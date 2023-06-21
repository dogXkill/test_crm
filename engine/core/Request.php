<?php

	final class Request {

		private static $instance = null;

		public $get = array();
		public $post = array();
		public $files = array();
		
		public $isPost = false;
		public $isGet = false;
		public $isAjax = false;

		public static function getInstance() {
			self::init();
			
			return self::$instance;
		}

		public static function init() {
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}
		}

		/*
		 * Инициализация класса.
		 * Определение всех необходимых переменных:
		 * - request_uri = /video/url_to_video?first_get=asdf
		 * - query_string = first_get=asdf
		 * - url = video/url_to_video
		 * - request_method = POST/GET
		 */
		private function __construct() {
			if ($_GET != false) {
				$this->get = $_GET;
			}

			if ($_POST != false) {
				$this->post = $_POST;
			}

			if ($_FILES != false) {
				$this->files = $_FILES;
			}
			
			$this->isGet = isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'GET');
			$this->isPost = isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
			$this->isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
		}

		public function isGet() {
			return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'GET');
		}

		public function isPost() {
			return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
		}

		public function isAjax() {
			return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
		}
	}