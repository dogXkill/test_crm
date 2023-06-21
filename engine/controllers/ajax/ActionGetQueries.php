<?php
ini_set('memory_limit', '256M');
class ActionGetQueries extends AbstractAction {
	
	protected $db;
	
	public function run() {
		// Только залогиненные могут выполнить
		if ($this->user->isGuest()) {
			return array('status' => 401);
		}

		$this->db = Database::getInstance();
		$queriesClass = QueriesClass::getInstance();
		
		$managers = UsersClass::getInstance()->getManagers();
		
		$filter = isset($this->request->post['filter']) ? $this->request->post['filter'] : false;
		
		$processedFilters = QueriesClass::processFilters($filter, compact('managers'));
		$args = $processedFilters['args'];
		$filters = $processedFilters['filters'];
		//print_r($args);

		
		$sort = isset($this->request->post['sort']) ? $this->request->post['sort'] : false;
		if ($sort) {
			if (isset(QueriesConfig::$sortValues[$sort])) {
				$args['orderBy'] = $sort;
			}
		}
		
		// Кол-во на страницу
		if (isset($_COOKIE['perPage'])) {
			$perPage = intval($_COOKIE['perPage']);
			if (in_array($perPage, QueriesConfig::$perPage) && $perPage != QueriesConfig::$defaultPerPage) {
				$args['perPage'] = $perPage;
			} else {
				setcookie('perPage', $perPage, time() - 1, '/');
			}
		}

		if (isset($this->request->post['perPage'])) {
			$perPage = intval($this->request->post['perPage']);
			if (in_array($perPage, QueriesConfig::$perPage)) {
				if ($perPage != QueriesConfig::$defaultPerPage) {
					setcookie('perPage', $perPage, time() + 3600 * 24 * 7, '/');
				} else {
					setcookie('perPage', $perPage, time() - 1, '/');
				}
				
				$args['perPage'] = $perPage;
			}
		}
		
		if (!$this->user->isAdmin()) {
			$args['manager'] = $this->user->getId();
			if (isset($filters['manager'])) {
				unset($filters['manager']);
			}
			
			if (isset($args['search'])) {
				unset($args['manager']);
			}
			
			if (isset($filters['printManager'])) {
				unset($filters['printManager']);
			}
		}
		
		$totalQueries = $queriesClass->getQueries(array_merge($args, array('getCount' => true)));
		//echo $totalQueries;
		$perPage = isset($args['perPage']) ? $args['perPage'] : QueriesConfig::$defaultPerPage;
		$totalPages = ceil($totalQueries / $perPage);
		//echo $totalPages;
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

		// Получаем позиции корзины для каждого запроса
		$args['getItems'] = true;
		
		// Получаем количество заказов на производство
		$args['getApps'] = true;
		if (isset($vars['izd'])) {
			//echo $vars['izd'];
		}
		//print_r($args);
		$queries = $queriesClass->getQueries($args);
		//print_r($queries);
		//echo count($queries);
		
		$flag_filt=0;
		//echo $flag_filt;
		if (isset($args['deleted'])){
			foreach (array('specialFilter', 'search', 'from', 'to', 'manager', 'orderType', 'deliveryType', 'paymentType', 'debt', 'printManager', 'amoCrm', 'shipped', 'deleted','izd','sdelka') as $key) {
				if (isset($filters[$key])) {
					$flag_filt=1;
				}
			}
		}
		//echo "ttt";
		
		//echo "ttt1";
		//print_r($queries_all);
		//echo $flag_filt;
		//print_r($args);
		if ($flag_filt==1 && ($args['deleted']==1 || $args['deleted']==2)){
			$queries_all=$queriesClass->getQueriesAlls($args);
			$reason=$queriesClass->getReasonQuery($queries_all);
		}else{
			//
			//$reason=$queriesClass->getReasonQuery($queries);
			//$queries_all= QueriesClass::getInstance()->getQueriesAlls($args);
				$reason=QueriesClass::getInstance()->getReasonQuery($queries);	
		}
		//}
		// print_r($queries);
		// Генерируем пагинацию
		$params = array(
			'count' => $totalPages,
			'current' => isset($args['page']) ? $args['page'] : 1
		);
		$paginationHtml = $this->template->render('query/pagination', $params, true);
		
		// Генерируем список
		//print_r($queries);
		$itemsHtml = $this->template->render('query/list', array('queries' => $queries, 'managers' => $managers), true);
		$itemsSummaryHtml = $this->template->render('query/listSummary', array('queries' => $queries), true);
		if (isset($args['deleted'])){
		$reasonHtml=$this->template->render('query/listReason', array('reason' => $reason), true);
		}
		// Генерируем ссылку на страницу
		// Собираем все фильтры (поиск, период, селекты) + текущая страница (если != 1)
		$linkArray = array();
		foreach (array('specialFilter', 'search', 'from', 'to', 'manager', 'orderType', 'deliveryType', 'paymentType', 'debt', 'printManager', 'amoCrm', 'shipped', 'deleted','izd','sdelka') as $key) {
			if (isset($filters[$key])) {
				$linkArray[$key] = $filters[$key];
			}
		}
		
		if (isset($args['orderBy'])) {
			$linkArray['orderBy'] = $args['orderBy'];
		}
		
		if (isset($args['page']) && $args['page'] > 1) {
			$linkArray['page'] = $args['page'];
		}
		if (isset($args['izd']))  {
			$linkArray['izd'] = $args['izd'];
		}
		if (isset($args['sdelka']))  {
			$linkArray['sdelka'] = $args['sdelka'];
		}
		
		/* perPage - нужно не сохранять в куках */
		
		return array(
			'status' => 200,
			'paginationHtml' => $paginationHtml,
			'itemsHtml' => $itemsHtml,
			'reasonHtml'=>$reasonHtml,
			'itemsSummaryHtml' => $itemsSummaryHtml,
			'link' => http_build_query($linkArray),
			'ac_us'=>$this->user->order_access_edit(),
			'flag'=>$flag_filt
			// 'z_totalPages' => $totalPages
			
			// 'orderTypes' => array_unique(array_column($queries, 'typ_ord')),
			// 'deliveryTypes' => array_unique(array_column($queries, 'deliv_id')),
			// 'debt' => array_unique(array_column($queries, 'prdm_dolg')),
			// 'date' => array_unique(array_column($queries, 'date_query')),
			// 'users' => array_unique(array_column($queries, 'user_id')),
		);
		
	}
	
}
