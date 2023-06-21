<?php

class ActionTestPhoneSave extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}

		$this->db = Database::getInstance();
		
		$uid = isset($this->request->post['uid']) ? intval($this->request->post['uid']) : 0;
		
		$client = $this->db->getRow("SELECT uid, short, name, legal_address, postal_address, cont_tel, firm_tel, temp_phone FROM clients WHERE temp_phone IS NULL AND uid > " . $uid);
		
		if (!$client) {
			return array('status' => 499, 'message' => 'Нет такого клиента');
		}
		
		$phones = isset($this->request->post['phones']) ? $this->request->post['phones'] : array();
		
		if (!$phones) {
			return array('status' => 499, 'message' => 'Вы не ввели ни одного номера телефона для этого клиента');
		}
		
		$errors = array();
		
		foreach ($phones as $key => $phone) {
			if (!preg_match('/^[78]\d{10}$/', $phone) && !preg_match('/^[78]\d{10}\#\d{1,5}$/', $phone)) {
				$errors[] = array(
					'phoneKey' => $key,
					'phone' => $phone,
					'message' => 'Номер имеет пеправильный формат. Формат [7|8]XXXXXXXXXX или [7|8]XXXXXXXXXX#XXXXX, где после знака # указывается добавочный номер состоящий из 1-5 цифр.'
				);
			}
		}
		
		if ($errors) {
			$text = '';
			foreach ($errors as $error) {
				$text .= '<div><strong>' . $error['phone'] . '</strong>: ' . $error['message'] . '</div>';
			}
			
			return array('status' => 499, 'message' => $text);
		}
		
		$this->db->query("UPDATE clients SET temp_phone = '" . $this->db->esc(json_encode($phones)) . "' WHERE uid = " . $uid);
		
		return array(
			'status' => 200,
			'count' => $this->db->getVar("SELECT COUNT(uid) FROM clients WHERE temp_phone IS NULL")
		);
		
	}
	
}
