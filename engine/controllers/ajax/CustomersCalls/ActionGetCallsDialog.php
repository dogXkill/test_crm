<?php

class ActionGetCallsDialog extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}
		
		$managers = UsersClass::getInstance()->getManagers();
		
		$customersClass = CustomersClass::getInstance();
		$customersCallsClass = CustomersCallsClass::getInstance();
		
		$customerId = isset($this->request->post['customerId']) ? intval($this->request->post['customerId']) : 0;
		$customer = $customersClass->getCustomerById($customerId);
		
		if (!$customer) {
			return array(
				'status' => 600,
				'message' => 'Клиента с ID=' . $customerId . ' не существует.'
			);
		}
		
		$calls = $customersCallsClass->getCallsByCustomerId($customerId);
		
		$callsList = $this->template->render('customersCalls/customers/callsList', compact('calls', 'managers'), true);
		$template_part = $this->template->render('customersCalls/customers/callsDialog', compact('callsList', 'customerId', 'customer'), true);
		
		return array('status' => 200, 'template_part' => $template_part);
	}
	
}
