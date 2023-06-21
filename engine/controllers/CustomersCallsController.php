<?php

class CustomersCallsController extends AbstractController {
	
	protected $menu = array(
		'indexAction' => array(
			'title' => 'Клиенты',
			'link' => '/crm/customersCalls/',
			'ico' => 'fas fa-users'
			// 'access' => array('mng', 'adm')
		),
		'callsAction' => array(
			'title' => 'Звонки',
			'link' => '/crm/customersCalls/calls.php',
			'ico' => 'fas fa-phone-alt'
		)
	);
	
	protected function indexAction() {
		header('Content-Type: text/html; charset=utf-8');
		
		$managers = UsersClass::getInstance()->getManagers();
		
		$vars = $this->request->get;
		
		$args = array();
		$filters = array();
		
		if (isset($vars['search']) && trim($vars['search']) != false) {
			$args['search'] = trim($vars['search']);
			$filters['search'] = $args['search'];
		}
		
		if (isset($vars['manager']) && isset($managers[intval($vars['manager'])])) {
			$args['manager'] = intval($vars['manager']);
			$filters['manager'] = $args['manager'];
		}
		
		if (isset($vars['orderType']) && isset(QueriesConfig::$filter_orderType[intval($vars['orderType'])])) {
			$args['orderType'] = intval($vars['orderType']);
			$filters['orderType'] = $args['orderType'];
		}
		
		if (isset($vars['ordersCount'])) {
				$value = $vars['ordersCount'];
				
				if (preg_match('#[^\d]#', $vars['ordersCount']) || $value > 999 || $value < 1) {
					
				} else {
					$args['ordersCount'] = $filters['ordersCount'] = $value;
				}
			}
		
		if (isset($vars['ordersPeriodFrom']) && strtotime($vars['ordersPeriodFrom']) != false) {
			$args['ordersPeriodFrom'] = $vars['ordersPeriodFrom'];
			$filters['ordersPeriodFrom'] = $args['ordersPeriodFrom'];
		}
		if (isset($vars['ordersPeriodTo']) && strtotime($vars['ordersPeriodTo']) != false) {
			$args['ordersPeriodTo'] = $vars['ordersPeriodTo'];
			$filters['ordersPeriodTo'] = $args['ordersPeriodTo'];
		}
		
		if (isset($vars['callsPeriodFrom']) && strtotime($vars['callsPeriodFrom']) != false) {
			$args['callsPeriodFrom'] = $vars['callsPeriodFrom'];
			$filters['callsPeriodFrom'] = $args['callsPeriodFrom'];
		}
		if (isset($vars['callsPeriodTo']) && strtotime($vars['callsPeriodTo']) != false) {
			$args['callsPeriodTo'] = $vars['callsPeriodTo'];
			$filters['callsPeriodTo'] = $args['callsPeriodTo'];
		}
		
		if (isset($vars['noCalls'])) {
			$args['noCalls'] = $filters['noCalls'] = $vars['noCalls'] ? 1 : 0;
		}

        if (isset($vars['status'])) {
            $args['status'] = $filters['status'] = $vars['status'] ?: 0;
        }
		
		$customersCount = CustomersCallsClass::getInstance()->getCustomers(array_merge($args, array('getCount' => true)));
		$totalPages = ceil($customersCount / CustomersCallsConfig::$defaultPerPage);
		
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
		
		$args['getOrderItems'] = true;
		
		$customers = CustomersCallsClass::getInstance()->getCustomers($args);
		
		$this->template->initTemplate();
		$this->template->enqueueScript('jquery');
		$this->template->enqueueScript('jquery-ui');
		$this->template->enqueueScript('jquery-ui-timepicker-addon');
		
		$this->template->enqueueScript('js-script', $this->template->getTemplateUrl() . '/js/customersCalls/customers_script.js', true);
		$this->template->enqueueScript('js-filters', $this->template->getTemplateUrl() . '/js/customersCalls/customers_filters.js', true);
		$this->template->enqueueStyle('jquery-ui');
		$this->template->enqueueStyle('jquery-ui-timepicker-addon');
		
		$this->template->enqueueStyle('font-awesome-5.12');
		$this->template->enqueueStyle('style-global', $this->template->getTemplateUrl() . '/css/global.css');
		$this->template->enqueueStyle('style-module', $this->template->getTemplateUrl() . '/css/customersCalls.css');
		$this->template->enqueueStyle('style-page', $this->template->getTemplateUrl() . '/css/customersCalls_customers.css');
		
		$pageParams = array(
			'user' => $this->user,
			'menu' => $this->template->render('customersCalls/menu', array('menuItems' => $this->processMenu(__FUNCTION__)), true),
			'managers' => $managers,
			'pagination' => $this->template->render('customersCalls/pagination', array('count' => $totalPages, 'current' => $page), true),
			'items' => $this->template->render('customersCalls/customers/list', compact('customers', 'managers'), true),
			'filters' => $filters,
			'customersCount' => $customersCount,
		);
		
		$layoutParams = array(
			'user' => $this->user,
			'old_name_curr_page' => 'customersCalls'
		);
		
		$this->template->printPage('customersCalls/customers/index', $pageParams, 'main', $layoutParams);
	}
	
	protected function callsAction() {
		header('Content-Type: text/html; charset=utf-8');
		
		$managers = UsersClass::getInstance()->getManagers();
		
		$vars = $this->request->get;
		
		$args = array();
		$filters = array();
		
		if (isset($vars['manager']) && isset($managers[intval($vars['manager'])])) {
			$args['manager'] = $filters['manager'] = intval($vars['manager']);
		}
		
		if (isset($vars['periodFrom']) && strtotime($vars['periodFrom']) != false) {
			$args['periodFrom'] = $filters['periodFrom'] = $vars['periodFrom'];
		}
		if (isset($vars['periodTo']) && strtotime($vars['periodTo']) != false) {
			$args['periodTo'] = $filters['periodTo'] = $vars['periodTo'];
		}
		
		if (isset($vars['result']) && isset($managers[intval($vars['manager'])])) {
			$args['manager'] = $filters['manager'] = intval($vars['manager']);
		}
		
		if (isset($vars['result'])) {
			$tmp = explode(',', $vars['result']);
			
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
		
		if (!$this->user->isAdmin()) {
			$args['manager'] = $this->user->getId();
			if (isset($filters['manager'])) {
				unset($filters['manager']);
			}
		}
		
		$callsCount = CustomersCallsClass::getInstance()->getCalls(array_merge($args, array('getCount' => true)));
		$totalPages = ceil($callsCount / CustomersCallsConfig::$defaultPerPage);
		
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
		
		$calls = CustomersCallsClass::getInstance()->getCalls($args);
		
		$this->template->initTemplate();
		$this->template->enqueueScript('jquery');
		$this->template->enqueueScript('jquery-ui');
		// $this->template->enqueueScript('jquery-ui-timepicker-addon');
		
		$this->template->enqueueScript('js-script', $this->template->getTemplateUrl() . '/js/customersCalls/calls_script.js', true);
		$this->template->enqueueScript('js-filters', $this->template->getTemplateUrl() . '/js/customersCalls/calls_filters.js', true);
		$this->template->enqueueStyle('jquery-ui');
		// $this->template->enqueueStyle('jquery-ui-timepicker-addon');
		
		$this->template->enqueueStyle('font-awesome-5.12');
		$this->template->enqueueStyle('style-global', $this->template->getTemplateUrl() . '/css/global.css');
		$this->template->enqueueStyle('style-module', $this->template->getTemplateUrl() . '/css/customersCalls.css');
		$this->template->enqueueStyle('style-page', $this->template->getTemplateUrl() . '/css/customersCalls_calls.css');
		
		$pageParams = array(
			'user' => $this->user,
			'menu' => $this->template->render('customersCalls/menu', array('menuItems' => $this->processMenu(__FUNCTION__)), true),
			'managers' => $managers,
			'pagination' => $this->template->render('customersCalls/pagination', array('count' => $totalPages, 'current' => $page), true),
			'items' => $this->template->render('customersCalls/calls/list', compact('calls', 'managers'), true),
			'filters' => $filters,
			'callsCount' => $callsCount,
		);
		
		$layoutParams = array(
			'user' => $this->user,
			'old_name_curr_page' => 'customersCalls'
		);
		
		$this->template->printPage('customersCalls/calls/index', $pageParams, 'main', $layoutParams);
	}
}
