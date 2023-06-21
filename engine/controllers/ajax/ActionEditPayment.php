<?php

class ActionEditPayment extends AbstractAction {

	protected $db;

	public function run() {
		// Только залогиненные могут выполнить
		/*if ($this->user->isGuest() || !in_array($this->user->uid, array(11, 12, 228, 199, 332, 384))) {
			return array('status' => 401);
		}*/
		if ($this->user->isGuest() ) {
			return array('status' => 401);
		}

		// return $this->request->post;

		$this->db = Database::getInstance();
		$paymentsClass = PaymentsClass::getInstance();
		$queriesClass = QueriesClass::getInstance();

		$queryId = isset($this->request->post['queryId']) ? intval($this->request->post['queryId']) : 0;
		$paymentId = isset($this->request->post['paymentId']) ? intval($this->request->post['paymentId']) : 0;

		$payment = $paymentsClass->getPayment($queryId, $paymentId);
		if (!$payment) {
			return array('status' => 404);
		}

		$summ = isset($this->request->post['payment']['summ']) ? floatval($this->request->post['payment']['summ']) : 0;
		$date = isset($this->request->post['payment']['date']) ? trim($this->request->post['payment']['date']) : '';
		$number = isset($this->request->post['payment']['number']) ? htmlspecialchars(strip_tags(trim($this->request->post['payment']['number']))) : '';

		$errors = array();

		if ($summ == false || $summ <= 0) {
			// $errors['summ'] = 'Введите сумму платежа. Сумма платежа должна быть больше 0.';
			$errors['summ'] = Str::get('query/paymentErrorSum');
		}

		$time = strtotime($date);
		if ($date == false) {
			// $errors['date'] = 'Введите дату платежа';
			$errors['date'] = Str::get('query/paymentErrorDateEmpty');
		} elseif ($time == false) {
			// $errors['date'] = 'Неправильный формат даты';
			$errors['date'] = Str::get('query/paymentErrorDateFormat');
		} elseif ($time > time()) {
			// $errors['date'] = 'Платеж не может быть проведен датой, позже чем сегодня';
			$errors['date'] = Str::get('query/paymentErrorDateFuture');
		}

		// Если есть ошибки - возвращаем их
		if ($errors) {
			return array(
				'status' => 600,
				'errors' => $errors
			);
		}

		// Обновляем платежку в базе
		$dbQuery = sprintf("UPDATE payment_predm SET sum_accounts = '%s', date_ready = '%s', number_pp = '%s' WHERE uid = %d", $summ, date('Y-m-d', $time), $this->db->esc($number), $paymentId);
		$this->db->query($dbQuery);

		// Собираем обновленные данные по платежу и запросу
		$data = $this->getData($queryId);

		// Вычисляем новые данные запроса и обновляем запрос
		$oplaceno = floatval($data['sum_accounts']);
		$dolg = floatval($data['prdm_sum_acc']) - $oplaceno;
		$dbQuery = sprintf("UPDATE queries SET prdm_opl = '%s', prdm_dolg = '%s' WHERE uid = %d", $oplaceno, $dolg, $queryId);
		$this->db->query($dbQuery);

        $data['mails'] = array_unique($data['mails']);

		// Отправляем оповещение
		$params = array(
			'queryId' => $queryId,
			'paymentId' => $paymentId,
			'summ' => $summ,
			'date' => date('d.m.Y', $time),
			'number' => $number,
			'client' => $data['short'],
			'dolg' => $dolg,
			'name' => $data['name'],
			'surname' => $data['surname'],
			'mails' => implode(',', $data['mails']),
            'crm_url' => SITE_URL
		);
		$this->sendEmail($params);

		// Возвращаем успешный результат
		return array(
			'status' => 200,
			'paymentId' => $paymentId,
			'oplaceno' => $oplaceno,
			'dolg' => $dolg
		);
	}

	private function getData($queryId) {
		$dbQuery = "SELECT q.prdm_sum_acc, u.surname, u.name, u.email, c.short, (SELECT SUM(sum_accounts) FROM payment_predm pp WHERE pp.query_id = q.uid) as sum_accounts
			FROM queries q
			INNER JOIN clients c ON q.client_id = c.uid
			INNER JOIN users u ON q.user_id = u.uid
			WHERE q.uid = " . intval($queryId) . " LIMIT 1";

		$data = $this->db->getRow($dbQuery);

		$mails = $this->db->getCol("SELECT DISTINCT email FROM mail");
		if ($mails == false) {
			$mails = array();
		}

		$data['mails'] = $mails;
		$data['mails'][] = $data['email'];

		return $data;
	}

	private function sendEmail($params) {
		$subject = Str::get('query/editPaymentMailSubject', array('{client}' => $params['client']), false);
		$body = $this->template->render('mails/actionPaymentEdit', $params, true);
        $emailer = new EmailerHelper($params['mails'], $subject, $body);
        $emailer->send();
	}

}
