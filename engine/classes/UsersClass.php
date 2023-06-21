<?php

	class UsersClass {
		
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
		
		public function getManagers() {
			// $query = "SELECT surname, uid FROM users WHERE type IN ('mng', 'adm', 'sup', 'meg') ORDER BY surname";
			// $managers = $this->db->getColIndexed($query);

			$query = "SELECT uid, surname, name, archive, doljnost FROM users WHERE (user_department = '6' OR user_department = '3' OR user_department = '1' OR user_department = '2' OR user_department = '9' OR doljnost = '8' OR type = 'sup' OR type = 'meg') AND surname != 'test' ORDER BY surname";
			$managers = $this->db->getRows($query, 'uid');

			return $managers;
		}
	}