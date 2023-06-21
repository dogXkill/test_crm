<?php

class ActionSaveAccNumber extends AbstractAction {

	protected $db;

	public function run() {
		// Только залогиненные и админы могут выполнить
		
		/*if ($this->user->isGuest() || !$this->user->getAccountType()) {
			return array('status' => 401);
		}*/

		$q = "SELECT payment_edit_num FROM users WHERE uid = " . $this->user->uid;
		$r = mysql_fetch_assoc(mysql_query($q));
		if ($r['payment_edit_num'] !== '1') {
			return array('status' => 401);
		}



		$this->db = Database::getInstance();
		$queriesClass = QueriesClass::getInstance();

		$queryId = isset($this->request->post['queryId']) ? intval($this->request->post['queryId']) : 0;
		$query = $queriesClass->getQueryById($queryId);

		if (!$query) {
			return array('status' => 404);
		}

		$number = isset($this->request->post['number']) ? htmlspecialchars(strip_tags(trim($this->request->post['number']))) : '';

		if (in_array(mb_strtolower($number), array('нет', 'no', '-'))) {
			$number = 'none';
		}

        $prdm_num_acc = $this->db->esc($number);

        if($prdm_num_acc !== ""){$shipped = '1';}else{$shipped = '0';}

		$dbQuery = sprintf("UPDATE queries SET prdm_num_acc = '%s', date_ready = '%s', ready = %d, shipped = %d WHERE uid = %d", $prdm_num_acc, date('Y-m-d H:i:s'), empty($number) ? 0 : 1, $shipped, $queryId);
		// echo $dbQuery;
		// var_dump($this->db->affectedRows());
		$this->db->query($dbQuery);

		// Возвращаем успешный результат
		return array(
			'status' => 200,
			'number' => $number
		);
	}

}
