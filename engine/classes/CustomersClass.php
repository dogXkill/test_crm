<?php

	class CustomersClass {
		
		private static $instance = null;
		
		private $db;
		
		public static function getInstance() {
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}

			return self::$instance;
		}
		
		private function __construct() {
			$this->db = Engine::app()->db;
		}
		private function __clone() {}
		
		public function getCustomerById($id, $fields = false) {
			if (!$id) {
				return false;
			}
			
			if (!$fields) {
				$fields = array('*');
			}

			$customer = $this->db->getRow("SELECT " . implode(', ', $fields) . " FROM clients WHERE uid = " . intval($id) . " LIMIT 1");
			
			return $customer;
		}
	}