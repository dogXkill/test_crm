<?php

class ActionTestPhone extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}

		$this->db = Database::getInstance();
		
		$uid = isset($this->request->post['uid']) ? intval($this->request->post['uid']) : 0;
		
		$data = $this->db->getRow("SELECT uid, short, name, legal_address, postal_address, cont_tel, firm_tel FROM clients WHERE temp_phone IS NULL AND uid > " . $uid);

		return array(
			'status' => 200,
			'data' => $data
		);
		
	}
	
}
