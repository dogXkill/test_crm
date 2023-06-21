<?php

class AbstractController {
	
	protected $action = 'index';
	
	protected $ajaxActions = array();
	protected $ajaxAction;
	
	protected $request;
	protected $user;
	protected $template;
	
	protected $menu = array();
	
	public function __construct() {
		$this->request = Engine::app()->request;
		$this->user = Engine::app()->user;
		$this->template = Engine::app()->template;
	}
	
	public function runAction($action) {
		$this->action = $action;
		
		if ($this->request->isAjax()) {
			$this->runAjaxAction();
		} else {
			$this->runInlineAction();
		}
	}
	
	protected function runAjaxAction() {
		$request = $this->request->post;
		
		if (!isset($request['action']) || !isset($this->ajaxActions[$this->action]) || !in_array($request['action'], $this->ajaxActions[$this->action])) {
			$this->ajaxOutput($this->ajaxError(404));
			return;
		}
		
		if (!$this->checkAccess()) {
			$this->ajaxOutput($this->ajaxError(404));
			return;
		}
		
		$method = $request['action'] . 'AjaxAction';
		
		if (method_exists($this, $method)) {
			$this->$method();
		} else {
			$this->ajaxOutput($this->ajaxError(404));
			return;
		}
	}
	
	protected function runInlineAction() {
		if (!$this->checkAccess()) {
			header('Location: /');
			die();
		}
		
		$method = $this->action . 'Action';

		if (method_exists($this, $method)) {
			$this->$method();
		} else {
			die('Action <b>' . $this->action . '</b> does not exist');
		}
	}
	
	protected function checkAccess() {
		return $this->user->isLogged();
	}
	
	protected function ajaxOutput($obj) {
		echo json_encode($obj);
	}
	
	protected function ajaxError($code) {
		if ($code == 404) {
			return array('status' => 404, 'message' => 'Action not found!');
		}
	}
	
	protected function processMenu($active = false) {
		$menu = array();
		
		if ($this->menu) {
			foreach ($this->menu as $actionMethod => $params) {
				// Проверяем, показывать ли пункт меню текущему пользователю
				if (isset($params['access'])) {}
				
				$menu[] = array(
					'link' => $params['link'],
					'title' => $params['title'],
					'ico' => isset($params['ico']) ? $params['ico'] : false,
					'active' => $active == $actionMethod
				);
			}
		}
		
		return $menu;
	}
}
