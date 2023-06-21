<?php

class ActionDeletePayment extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}
		
		$editAccess = false;
		if (in_array($this->user->uid, array(11, 12, 228, 199, 384))) {
			$editAccess = true;
		}
		
		$queryId = isset($this->request->post['queryId']) ? intval($this->request->post['queryId']) : 0;
		$paymentId = isset($this->request->post['paymentId']) ? intval($this->request->post['paymentId']) : 0;
		
		$this->db = Database::getInstance();
		$paymentsClass = PaymentsClass::getInstance();
		$queriesClass = QueriesClass::getInstance();
		
		$payment = $paymentsClass->getPayment($queryId, $paymentId);
		if (!$payment) {
			return array('status' => 404);
		}
		
		// Удаляем
		$query = sprintf("DELETE FROM payment_predm WHERE query_id = %d AND uid = %d", $queryId, $paymentId);
		$this->db->query($query);
		
		// Данные по платежу и запросу
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
			'summ' => $payment['sum_accounts'],
			'date' => date('d.m.Y', strtotime($payment['date_ready'])),
			'number' => $payment['number_pp'],
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
		$subject = Str::get('query/deletePaymentMailSubject', array('{client}' => $params['client']), false);
		// $subject = 'Удален ранее поступивший платеж от ' . $params['client'];
		$body = $this->template->render('mails/actionPaymentDelete', $params, true);
        $emailer = new EmailerHelper($params['mails'], $subject, $body);
        $emailer->send();
	}
}
