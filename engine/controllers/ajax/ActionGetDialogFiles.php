<?php

class ActionGetDialogFiles extends AbstractAction {
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}
		
		$queriesClass = QueriesClass::getInstance();
		
		$queryId = isset($this->request->post['queryId']) ? intval($this->request->post['queryId']) : 0;
		$query = $queriesClass->getQueryById($queryId);
		$tip=$this->request->post['tip'];
		if (!$query) {
			return array(
				'status' => 600,
				'message' => 'Запроса с ID=' . $queryId . ' не существует.'
			);
		}
		//ловим  по queryId cliend id ->email
		$client=$queriesClass->load_client_info($queryId);
		
		$params = array(
			'queryId' => $queryId,
			'query' => $query,
			'email'=>$client['email'],
			'tip'=>$tip,
			'name_client'=>$client["name"]
		);
		
		$template_part = $this->template->render('query/getDiaglogFiles', $params, true);
		
		// return array('status' => 200, 'template_part' => iconv("Windows-1251", "UTF-8//IGNORE", $template_part));
		return array('status' => 200, 'template_part' => $template_part);
	}
	
}
