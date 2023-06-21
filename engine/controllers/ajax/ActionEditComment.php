<?php

class ActionEditComment extends AbstractAction {
	
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
		$comment = isset($this->request->post['comment']) ? htmlspecialchars(strip_tags(trim($this->request->post['comment']))) : '';
		$user_name_full = isset($this->request->post['user_name_full']) ? htmlspecialchars(strip_tags(trim($this->request->post['user_name_full']))) : '';
		$query = $queriesClass->getQueryById($queryId);
		
		if (!$query) {
			return array(
				'status' => 600,
				'message' => 'Запроса с ID=' . $queryId . ' не существует.'
			);
		}
        $uid = $query['uid'];
        if($comment == "delete_all"){
            $dbQuery = sprintf("UPDATE queries SET note = '' WHERE uid = '$uid'");
            $this->db->query($dbQuery);
        } else{
		// Обновляем камент в базе
        $tek_time = date('d.m.Y H:i');
		$dbQuery = sprintf("UPDATE queries SET note = CONCAT(note, '%s') WHERE uid = %d", "<br><b><i>".$user_name_full."</i></b> <i>".$tek_time."</i>:".$this->db->esc($comment), $uid);
		$this->db->query($dbQuery);
         }


		// Возвращаем успешный результат
		return array(
			'status' => 200,
			'commentIsEmpty' => empty($comment) ? 1 : 0

		);
	}
	
}
