<?php

class ActionAddCall extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}
		
		$this->db = Database::getInstance();
		$customersClass = CustomersClass::getInstance();
		$customersCallsClass = CustomersCallsClass::getInstance();
		
		$managers = UsersClass::getInstance()->getManagers();
		
		$customerId = isset($this->request->post['customerId']) ? intval($this->request->post['customerId']) : 0;
		$customer = $customersClass->getCustomerById($customerId);
		
		if (!$customer) {
			return array('status' => 404);
		}
		
		$date = isset($this->request->post['call']['date']) ? trim($this->request->post['call']['date']) : 0;
		$result_id = isset($this->request->post['call']['result']) ? intval($this->request->post['call']['result']) : '';
		$customResult = isset($this->request->post['call']['customResult']) ? htmlspecialchars(strip_tags(trim($this->request->post['call']['customResult']))) : '';
		$comment = isset($this->request->post['call']['comment']) ? htmlspecialchars(strip_tags(trim($this->request->post['call']['comment']))) : '';
		
		$errors = array();
		
		$time = strtotime($date);
		if ($date == false) {
			$errors['date'] = 'Введите дату звонка';
		} elseif ($time == false) {
			$errors['date'] = 'Неправильный формат даты';
		} elseif ($time > strtotime(date('d.m.Y', strtotime('+1 day')))) {
			$errors['date'] = 'Звонок нельзя добавить, позже чем сегодня';
		}
		
		if (!$result_id) {
			$errors['result'] = 'Выберите результат звонка';
		}
		
		if ($result_id == 1 && !$customResult) {
			$errors['customResult'] = 'Введите результат звонка';
		}
		
		if ($result_id != 1) {
			$customResult = '';
		}
		
		// Если есть ошибки - возвращаем их
		if ($errors) {
			return array(
				'status' => 600,
				'errors' => $errors
			);
		}
		
		// Добавляем звонок в базу
		$sql = sprintf("INSERT INTO clients_calls (client_id, user_id, date, result_id, result_other, comment) VALUES (%d, %d, '%s', %d, '%s', '%s')", $customerId, $this->user->uid, date('Y-m-d H:i:s', $time), $result_id, $this->db->esc($customResult), $this->db->esc($comment));
		$this->db->query($sql);
		
		// ID созданного звонка
		$callId = $this->db->insertedId();
		
		$calls = $customersCallsClass->getCallsByCustomerId($customerId);
		$template_part = $this->template->render('customersCalls/customers/callsList', compact('calls', 'managers'), true);

		// Возвращаем успешный результат
		return array(
			'status' => 200,
			'template_part' => $template_part,
			'callId' => $callId,
			'callsCount' => count($calls),
			'lastCall' => date('d.m.Y H:i', strtotime($calls[0]['date']))
		);
	}
	
}
