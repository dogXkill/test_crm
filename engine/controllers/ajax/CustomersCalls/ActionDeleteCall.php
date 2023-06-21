<?php

class ActionDeleteCall extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}
		
		$this->db = Database::getInstance();
		$customersCallsClass = CustomersCallsClass::getInstance();
		
		$managers = UsersClass::getInstance()->getManagers();
		
		$customerId = isset($this->request->post['customerId']) ? intval($this->request->post['customerId']) : 0;
		$callId = isset($this->request->post['callId']) ? intval($this->request->post['callId']) : 0;
		
		$call = $customersCallsClass->getCall($customerId, $callId);
		if (!$call) {
			return array('status' => 404);
		}
		
		if (!$this->user->getAccountType() && $call['user_id'] != $this->user->uid) {
			return array('status' => 401);
		}
		
		$customersCallsClass->deleteCall($customerId, $callId);
		
		$calls = $customersCallsClass->getCallsByCustomerId($customerId);
		$template_part = $this->template->render('customersCalls/customers/callsList', compact('calls', 'managers'), true);
		
		$callsCount = count($calls);
		
		// Возвращаем успешный результат
		return array(
			'status' => 200,
			'template_part' => $template_part,
			'callId' => $callId,
			'callsCount' => $callsCount,
			'lastCall' => $callsCount > 0 ? date('d.m.Y H:i', strtotime($calls[0]['date'])) : '—'
		);
	}
	
}
