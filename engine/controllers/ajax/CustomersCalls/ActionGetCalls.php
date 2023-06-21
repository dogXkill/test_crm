<?php

class ActionGetCalls extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}

		$this->db = Database::getInstance();

		$customersCallsClass = CustomersCallsClass::getInstance();

		
		$filter = isset($this->request->post['filter']) ? $this->request->post['filter'] : false;
		
		$filters = array();
		$args = array();
		
		if ($filter != false) {
			if (isset($filter['manager'])) {
				$args['manager'] = $filters['manager'] = intval($filter['manager']);
			}
			
			if (isset($filter['periodFrom'])) {
				$args['periodFrom'] = $filters['periodFrom'] = $filter['periodFrom'];
			}
			if (isset($filter['periodTo'])) {
				$args['periodTo'] = $filters['periodTo'] = $filter['periodTo'];
			}
			
			if (isset($filter['result'])) {
				$tmp = explode(',', $filter['result']);
				
				$result_ids = array();
				foreach ($tmp as $value) {
					$value = intval(trim($value));
					
					if (isset(CustomersCallsConfig::$callsResults[$value])) {
						$result_ids[] = $value;
					}
				}
				
				if ($result_ids) {
					$args['result'] = $filters['result'] = $result_ids;
				}
			}
		}
		
		if (!$this->user->isAdmin()) {
			$args['manager'] = $this->user->getId();
			if (isset($filters['manager'])) {
				unset($filters['manager']);
			}
		}

		$callsCount = CustomersCallsClass::getInstance()->getCalls(array_merge($args, array('getCount' => true)));
		$totalPages = ceil($callsCount / CustomersCallsConfig::$defaultPerPage);

		// Страница
		if (isset($this->request->post['page'])) {
			$page = intval($this->request->post['page']);
			if ($page > 1) {
				if ($page > $totalPages) {
					$args['page'] = $totalPages;
				} else {
					$args['page'] = $page;
				}
			}
		}
		
		$calls = CustomersCallsClass::getInstance()->getCalls($args);

		// Генерируем пагинацию
		$params = array(
			'count' => $totalPages,
			'current' => isset($args['page']) ? $args['page'] : 1
		);
		$paginationHtml = $this->template->render('customersCalls/pagination', $params, true);
		
		// Генерируем список
		$managers = UsersClass::getInstance()->getManagers();
		$itemsHtml = $this->template->render('customersCalls/calls/list', compact('calls', 'managers'), true);
		
		
		// Генерируем ссылку на страницу
		// Собираем все фильтры (поиск, период, селекты) + текущая страница (если != 1)
		$linkArray = array();
		foreach (array('manager', 'periodFrom', 'periodTo', 'result') as $key) {
			if (isset($filters[$key])) {
				if (is_array($filters[$key])) {
					$linkArray[$key] = implode(',', $filters[$key]);
				} else {
					$linkArray[$key] = $filters[$key];
				}
			}
		}
		
		if (isset($args['page']) && $args['page'] > 1) {
			$linkArray['page'] = $args['page'];
		}
		
		return array(
			'status' => 200,
			'paginationHtml' => $paginationHtml,
			'itemsHtml' => $itemsHtml,
			'link' => http_build_query($linkArray),
			'callsCount' => $callsCount
		);
		
	}
	
}
