<?php

	class PaymentsClass {
		
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
		
		public function getPayment($queryId, $paymentId) {
			$payment = $this->db->getRow("SELECT * FROM payment_predm WHERE query_id = " . intval($queryId) . " AND uid = " . intval($paymentId) . " LIMIT 1");
			
			return $payment;
		}
		
		public function deletePayment($id) {
			$this->db->query("DELETE FROM payment_predm WHERE uid = " . intval($id));
			
			return true;
		}
	}