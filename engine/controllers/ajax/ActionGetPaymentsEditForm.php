<?php

class ActionGetPaymentsEditForm extends AbstractAction {

	public function run() {
		// Только залогиненные могут выполнить

		if ($this->user->isGuest()) {
			return array('status' => 401);
		}
		$queriesClass = QueriesClass::getInstance();

		$queryId = isset($this->request->post['queryId']) ? intval($this->request->post['queryId']) : 0;
		$query = $queriesClass->getQueryById($queryId);

		$editAccess = false;

		$q = "SELECT order_access_payment FROM users WHERE uid = " . $this->user->uid;
		$r = mysql_fetch_assoc(mysql_query($q));
		$orderAccessPayment = $r['order_access_payment'];

		if ($orderAccessPayment == '2') {
			$editAccess = true;
		}

		if ($orderAccessPayment == '1') {
			$q = "SELECT user_id FROM queries WHERE uid = $queryId";
			$r = mysql_fetch_assoc(mysql_query($q));
			if ($r['user_id'] == $this->user->uid) {
				$editAccess = true;
			} else {
				$editAccess = false;
			}
		}

		/*if (in_array($this->user->uid, array(11, 12, 228, 199, 384, 332))) {
			$editAccess = true;
		}*/

		if (!$query) {
			return array(
				'status' => 600,
				'message' => 'Запроса с ID=' . $queryId . ' не существует.'
			);
		}

		$payments = QueriesClass::getInstance()->getPaymentsByQueryId($queryId);

		$params = array(
			'editAccess' => $editAccess,
			'payments' => $payments,
			'queryId' => $queryId,
			'query' => $query,
		);

		$template_part = $this->template->render('query/editPaymentsForm', $params, true);

		// return array('status' => 200, 'template_part' => iconv("Windows-1251", "UTF-8//IGNORE", $template_part));
		return array('status' => 200, 'template_part' => $template_part);
	}

}
