<?php

class AjaxController {
	
	private $request;
	private $user;
	
	// https://gist.github.com/phoenixg/5326222
	private $errorCodes = array(
        400 => array(
			'message' => 'Bad Request',
			'header' => 'HTTP/1.1 400 Bad Request',
		),
        401 => array(
			'message' => 'Unauthorized',
			'header' => 'HTTP/1.1 401 Unauthorized',
		),
        403 => array(
			'message' => 'Forbidden',
			'header' => 'HTTP/1.1 403 Forbidden',
		),
        404 => array(
			'message' => 'Not Found',
			'header' => 'HTTP/1.1 404 Not Found',
		),
    );
	
	protected $actions = array(
		'getPaymentsEditForm' => 'ActionGetPaymentsEditForm',
		'editPayment' => 'ActionEditPayment',
		'deletePayment' => 'ActionDeletePayment',
		'addPayment' => 'ActionAddPayment',
		'saveAccNumber' => 'ActionSaveAccNumber',
		'getQueries' => 'ActionGetQueries',
		'getQueriesSummary' => 'ActionGetQueriesSummary',
		'deleteQuery' => 'ActionDeleteQuery',
		'getDeleteForm'=>'ActionGetDeleteForm',
		'getDialogFiles'=>'ActionGetDialogFiles',
		'getIzmDeleteForm'=>'ActionIzmGetDeleteForm',
		'getCommentEditForm' => 'ActionGetCommentEditForm',
		'editComment' => 'ActionEditComment',
		'editDelete'=>'ActionEditDelete',
		'CancelPercentage' => 'ActionCancelPercentage',
		'setPrintManager' => 'ActionSetPrintManager',
        'ActionShipped' => 'ActionShipped',
		'getAmoCrmIdEditForm' => 'ActionGetAmoCrmIdEditForm',
		'editAmoCrmId' => 'ActionEditAmoCrmId',
		
		'querySend/searchCustomers' => 'QuerySend/SearchCustomers',
		'query_send_searchCustomers' => 'ActionSearchCustomers',
		
		'customersCalls/getCustomers' => 'CustomersCalls/ActionGetCustomers',
		'customersCalls/getCallsDialog' => 'CustomersCalls/ActionGetCallsDialog',
		'customersCalls/addCall' => 'CustomersCalls/ActionAddCall',
		'customersCalls/deleteCall' => 'CustomersCalls/ActionDeleteCall',
		
		'customersCalls/getCalls' => 'CustomersCalls/ActionGetCalls',
		
		'testGetNextClientWoNumber' => 'ActionTestPhone',
		'testSavePhones' => 'ActionTestPhoneSave'
	);
	
	protected $action;
	
	public function __construct() {
		$this->request = Engine::app()->request;
	}
	
	public function initAjax() {
		if ($this->request->isAjax) {
			if (!isset($this->request->post['action']) || $this->request->post['action'] == false) {
				$this->out($this->error(400));
			}
			
			$action = trim($this->request->post['action']);
			
			if (!isset($this->actions[$action])) {
				$this->out($this->error(404));
			}
			
			$this->action = $action;
			
			$class = $this->actions[$action];
			if (($pos = strrpos ($class, '/')) !== false) {
				$path = CONTROLLERSPATH . '/ajax/' . $class . '.php';

				if (file_exists($path)) {
					$class = substr($class, ($pos + 1));
					
					require_once($path);
				}
			}
			
			$actionObj = new $class();
			
			$result = $actionObj->run();
			
			if (is_array($result) && isset($result['status']) && isset($this->errorCodes[$result['status']])) {
				$this->out($this->error($result['status']));
			}

			$this->out($result);
		} else {
			$this->out($this->error(400));
		}
	}
	
	private function out($obj) {
		header('Content-type: application/json; charset=utf-8');
		if ($this->action == 'addPayment') {
			// header('Content-Type: application/json; charset=utf-8');
			// var_dump($obj);
			// var_dump(json_encode($obj));
			// var_dump(json_last_error());
			// var_dump(json_last_error_msg());
		}
		die(json_encode($obj));
	}
	
	private function error($code) {
		$error = $this->errorCodes[$code];
		
		return array(
			'code' => $code,
			'message' => $error['message']
		);
	}
	
}
