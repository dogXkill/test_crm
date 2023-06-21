<?php

abstract class AbstractAction {
	
	public $error = false;
	
	protected $request;
	protected $user;
	protected $template;
	
	public function __construct() {
		$this->request = Engine::app()->request;
		$this->user = Engine::app()->user;
		$this->template = Engine::app()->template;
	}
	
	public function run() {}
	
}
