<?php

class ActionGetCustomers extends AbstractAction {
	
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
			if (isset($filter['search']) && trim($filter['search']) != false) {
				$args['search'] = $filters['search'] = trim($filter['search']);
			}
			
			if (isset($filter['manager'])) {
				$args['manager'] = $filters['manager'] = intval($filter['manager']);
			}
			
			if (isset($filter['orderType'])) {
				$args['orderType'] = $filters['orderType'] = intval($filter['orderType']);
			}
			
			if (isset($filter['ordersCount'])) {
				$value = $filter['ordersCount'];
				
				if (preg_match('#[^\d]#', $filter['ordersCount']) || $value > 999 || $value < 1) {
					$filter['ordersCount'] = 0;
				} else {
					$args['ordersCount'] = $filters['ordersCount'] = $value;
				}
			}
			
			if (isset($filter['ordersPeriodFrom'])) {
				$args['ordersPeriodFrom'] = $filters['ordersPeriodFrom'] = $filter['ordersPeriodFrom'];
			}
			if (isset($filter['ordersPeriodTo'])) {
				$args['ordersPeriodTo'] = $filters['ordersPeriodTo'] = $filter['ordersPeriodTo'];
			}
			
			if (isset($filter['callsPeriodFrom'])) {
				$args['callsPeriodFrom'] = $filters['callsPeriodFrom'] = $filter['callsPeriodFrom'];
			}
			if (isset($filter['callsPeriodTo'])) {
				$args['callsPeriodTo'] = $filters['callsPeriodTo'] = $filter['callsPeriodTo'];
			}
			
			if (isset($filter['noCalls'])) {
				$args['noCalls'] = $filters['noCalls'] = $filter['noCalls'] ? 1 : 0;
			}

            if (isset($filter['status'])) {
                $args['status'] = $filters['status'] = $filter['status'] ?: 0;
            }
		}
		
		$sort = isset($this->request->post['sort']) ? $this->request->post['sort'] : false;
		if ($sort) {
			if (isset(QueriesConfig::$sortValues[$sort])) {
				$args['orderBy'] = $sort;
			}
		}

		$customersCount = $customersCallsClass->getCustomers(array_merge($args, array('getCount' => true)));
		$totalPages = ceil($customersCount / CustomersCallsConfig::$defaultPerPage);

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
		
		$args['getOrderItems'] = true;
		$customers = $customersCallsClass->getCustomers($args);
		

		// Генерируем пагинацию
		$params = array(
			'customersCount' => $customersCount,
			'count' => $totalPages,
			'current' => isset($args['page']) ? $args['page'] : 1
		);
		$paginationHtml = $this->template->render('customersCalls/pagination', $params, true);
		
		// Генерируем список
		$managers = UsersClass::getInstance()->getManagers();
		$itemsHtml = $this->template->render('customersCalls/customers/list', compact('customers', 'managers'), true);
		
		
		// Генерируем ссылку на страницу
		// Собираем все фильтры (поиск, период, селекты) + текущая страница (если != 1)
		$linkArray = array();
		foreach (array('search', 'manager', 'orderType', 'ordersPeriodFrom', 'status', 'ordersPeriodTo', 'callsPeriodFrom', 'callsPeriodTo', 'noCalls', 'ordersCount') as $key) {
			if (isset($filters[$key])) {
				$linkArray[$key] = $filters[$key];
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
			'customersCount' => $customersCount
		);
		
	}
	
}
