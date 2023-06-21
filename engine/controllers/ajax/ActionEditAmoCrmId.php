<?php

class ActionEditAmoCrmId extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}
		
		// return $this->request->post;
		
		$this->db = Database::getInstance();
		$queriesClass = QueriesClass::getInstance();
		
		$queryId = isset($this->request->post['queryId']) ? intval($this->request->post['queryId']) : 0;
		$amoCrmId = isset($this->request->post['amoCrmId']) ? trim($this->request->post['amoCrmId']) : 0;
		
		$query = $queriesClass->getQueryById($queryId);
		
		if (!$query) {
			return array(
				'status' => 600,
				'message' => 'Запроса с ID=' . $queryId . ' не существует.'
			);
		}
		
		$error = false;
				
		if (preg_match('#[^0-9]$#is', $amoCrmId)) {
			$error = 'Идентификатор AMO может содержать только цифры';
		} elseif (!empty($amoCrmId) && strlen($amoCrmId) < 4) {
			$error = 'Идентификатор AMO должен быть длиной больше 4-х цифр';
		}
		
		if ($error) {
			return array(
				'status' => 600,
				'message' => $error
			);
		}
		
		// Обновляем платежку в базе
		$dbQuery = sprintf("UPDATE queries SET amo_crm_id = '%s' WHERE uid = %d", $amoCrmId, $query['uid']);
		$this->db->query($dbQuery);
		
		// Возвращаем успешный результат
		return array(
			'status' => 200,
			'empty' => empty($amoCrmId) ? 1 : 0
		);
	}
	
}
