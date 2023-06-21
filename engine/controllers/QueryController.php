<?php

class QueryController extends AbstractController {
	
	protected $ajaxActions = array(
		'index' => array('createOrder')
	);
	
	protected function indexAction() {
		header('Content-Type: text/html; charset=utf-8');
		
		$managers = UsersClass::getInstance()->getManagers();
		$types_izd=QueriesClass::getInstance()->load_types();
		$vars = $this->request->get;
		
		$processedFilters = QueriesClass::processFilters($vars, compact('managers'));
		$args = $processedFilters['args'];
		$filters = $processedFilters['filters'];
		


		if (isset($_COOKIE['perPage'])) {
			$perPage = intval($_COOKIE['perPage']);
			if (in_array($perPage, QueriesConfig::$perPage) && $perPage != QueriesConfig::$defaultPerPage) {
				$args['perPage'] = $perPage;
			} else {
				setcookie('perPage', $perPage, 0, '/');
			}
		}
		
		if (isset($vars['orderBy']) && isset(QueriesConfig::$sortValues[$vars['orderBy']])) {
			$args['orderBy'] = $vars['orderBy'];
		}
		
		if (!$this->user->isAdmin()) {
			$args['manager'] = $this->user->getId();
			if (isset($filters['manager'])) {
				unset($filters['manager']);
			}
			
			if (isset($args['search']) && $args['search'] != false) {
				unset($args['manager']);
			}
			
			if (isset($filters['printManager'])) {
				unset($filters['printManager']);
			}
		}

		$queriesCount = QueriesClass::getInstance()->getQueries(array_merge($args, array('getCount' => true)));
		$perPage = isset($args['perPage']) ? $args['perPage'] : QueriesConfig::$defaultPerPage;
		$totalPages = ceil($queriesCount / $perPage);

		// ?page=x. Если x > $totalPages и x < 1 - то выводим первую старницу
		$page = 1;
		if (isset($vars['page'])) {
			$page = intval($vars['page']);
			if ($page < 1 || $page > $totalPages) {
				$page = 1;
			}
		}

		if ($page > 1) {
			$args['page'] = $page;
		}
		if (isset($vars['izd'])) {
			//echo $vars['izd'];
		}
		$args['getApps'] = true;
		$args['getItems'] = true;
		$queries = QueriesClass::getInstance()->getQueries($args);
		if (isset($args['deleted'])){
			foreach (array('specialFilter', 'search', 'from', 'to', 'manager', 'orderType', 'deliveryType', 'paymentType', 'debt', 'printManager', 'amoCrm', 'shipped', 'deleted','izd','sdelka') as $key) {
				if (isset($filters[$key])) {
					$flag_filt=1;
				}
			}
		}
		if (isset($args['deleted'])){
			if ($flag_filt==1){
				$queries_all= QueriesClass::getInstance()->getQueriesAlls($args);
				$reason=QueriesClass::getInstance()->getReasonQuery($queries_all);				
			}else{
				//$reason=QueriesClass::getInstance()->getReasonQuery($queries);
				$queries_all= QueriesClass::getInstance()->getQueriesAlls($args);
				$reason=QueriesClass::getInstance()->getReasonQuery($queries_all);	
			}
			
			
			
		}
		// $temp = array_column($queries, 'apps');
		
		$this->template->initTemplate();
		$this->template->enqueueScript('jquery');
		$this->template->enqueueScript('jquery-ui');
		$this->template->enqueueScript('queriesScript', $this->template->getTemplateUrl() . '/js/queriesScript.js', true);
		$this->template->enqueueScript('queriesFilters', $this->template->getTemplateUrl() . '/js/queriesFilters.js', true);
		$this->template->enqueueStyle('jquery-ui');
		$this->template->enqueueStyle('font-awesome');
		$this->template->enqueueStyle('styles', $this->template->getTemplateUrl() . '/css/styles.css?1');
		

		$pageParams = array(
			'user' => $this->user,
			'managers' => $managers,
			'types_izd' => $types_izd,
			'pagination' => $this->template->render('query/pagination', array('count' => $totalPages, 'current' => $page), true),
			'items' => $this->template->render('query/list', array('queries' => $queries, 'managers' => $managers), true),
			'summary' => $this->template->render('query/listSummary', array('queries' => $queries), true),
			'table_reason'=>$this->template->render('query/listReason', array('reason' => $reason), true),
			'perPage' => $perPage,
			'filters' => $filters,
			'flag_filt'=>$flag_filt,
			'sort' => isset($args['orderBy']) ? QueriesConfig::$sortValues[$args['orderBy']] : false
		);
		
		$layoutParams = array(
			'user' => $this->user,
			'old_name_curr_page' => 'query_list'
		);
		
		$this->template->printPage('query/index', $pageParams, 'main', $layoutParams);
	}
	
	protected function sendAction() {
		header('Content-Type: text/html; charset=utf-8');
		
		$this->template->initTemplate();
		$this->template->enqueueScript('jquery');
		$this->template->enqueueScript('jquery-ui');
		// $this->template->enqueueScript('script-helpers', $this->template->getTemplateUrl() . '/js/helpers.js');
		$this->template->enqueueScript('script-querySendEntities', $this->template->getTemplateUrl() . '/js/querySend_entities.js', true);
		$this->template->enqueueScript('script-querySend', $this->template->getTemplateUrl() . '/js/querySend.js', true);
		// $this->template->enqueueScript('queriesFilters', $this->template->getTemplateUrl() . '/js/queriesFilters.js', true);
		// $this->template->enqueueStyle('jquery-ui');
		$this->template->enqueueStyle('font-awesome-5.12');
		$this->template->enqueueStyle('style-global', $this->template->getTemplateUrl() . '/css/global.css');
		$this->template->enqueueStyle('style-page', $this->template->getTemplateUrl() . '/css/query_send.css');
		
		$pageParams = array(
			'user' => $this->user
		);
		
		$layoutParams = array(
			'user' => $this->user,
			'old_name_curr_page' => 'query_list'
		);
		
		$this->template->printPage('query/send', $pageParams, 'main', $layoutParams);
	}
	
}
