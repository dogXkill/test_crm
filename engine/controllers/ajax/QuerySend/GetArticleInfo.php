<?php

class GetArticleInfo extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}

		$this->db = Database::getInstance();
		
		$article = isset($this->request->post['article']) ? intval($this->request->post['article']) : 0;
		
		// SELECT uid AS uid, title AS title, price AS price, sklad AS sklad, col_in_pack AS col_in_pack, price_our AS price_our FROM plan_arts WHERE art_id = '$art_id'
		
		// $query = "SELECT *
		// ";
		
		$query = "SELECT
					c.uid, c.short, c.name, c.legal_address, c.inn, c.kpp, c.rs_acc, c.bik, c.email, c.comment, c.cont_tel, c.firm_tel, COUNT(q.uid) as queries_count
				FROM clients c
				LEFT JOIN queries q ON q.client_id = c.uid
				WHERE 
					short LIKE '%" . $this->db->esc($searchString) . "%' 
				GROUP BY c.uid
				ORDER BY c.short";
		$clients = $this->db->getRows($query);
		
		// $query = "SELECT uid, client_id, date_query, prdm_sum_acc FROM queries WHERE client_id IN (" . implode(',', array_column($clients, 'uid') ) . ") ORDER BY date_query DESC GROUP BY client_id";
		$query = "SELECT uid, client_id, MAX(date_query) AS date_query, prdm_sum_acc FROM queries WHERE client_id IN (" . implode(',', array_column($clients, 'uid') ) . ") GROUP BY client_id";
		$last_orders = $this->db->getRows($query, 'client_id');
		
		array_walk($clients, function(&$client, $key, $queries) {
			$last_query = array();
			
			if (isset($queries[$client['uid']])) {
				$last_query['date'] = $queries[$client['uid']]['date_query'];
				$last_query['sum'] = $queries[$client['uid']]['prdm_sum_acc'];
			}
			
			$client['last_query'] = $last_query;
		}, $last_orders);
		
		$result = array(
			'status' => 200,
			'count' => count($clients)
		);
		
		if ($clients) {
			$result['html'] = $this->template->render('query/send_searchCustomersResult', array('clients' => $clients, 'searchString' => $searchString), true);
		}
		
		return $result;
	}
	
}
