<?php

	final class Engine {

		private static $app = null;
		
		private $coreClasses = array(
			'Database' => 'Database.php',
			'Request' => 'Request.php',
			'User' => 'User.php',
			'Template' => 'Template.php',
			'AbstractController' => 'AbstractController.php',
			'AbstractAction' => 'AbstractAction.php',
			'Str' => 'Str.php'
		);
		
		private $loadedStrings = array();
		
		public $db;
		public $user;
		public $template;
		public $request;

		public static function app() {
			self::init();

			return self::$app;
		}

		// Инициализация
		public static function init() {
			if (is_null(self::$app)) {
				self::$app = new self();
			}
		}

		// При инициализации
		private function __construct() {
			spl_autoload_register(array($this, 'includeClass'));
			
			$this->db = Database::getInstance();
			$this->request = Request::getInstance();
			$this->user = User::getInstance();
			$this->template = Template::getInstance();
		}

		private function __clone() {}

		/**
		 * Функция автозагрузки классов
		 *
		 * @param string $class
		 */
		private function includeClass($class) {
			// Проверим класс в ядре
			if (isset($this->coreClasses[$class])) {
				$file = COREPATH . '/' . $this->coreClasses[$class];
			} elseif (strpos($class, 'Controller') !== false) {
				$file = CONTROLLERSPATH . '/' . $class . '.php';
			} elseif (strpos($class, 'Class') !== false) {
				$file = CLASSESPATH . '/' . $class . '.php';
			} elseif (strpos($class, 'Config') !== false) {
				$file = CONFIGSPATH . '/' . $class . '.php';
			} elseif (strpos($class, 'Helper') !== false) {
				$file = HELPERSPATH . '/' . $class . '.php';
			} elseif (strpos($class, 'Action') === 0) {
				$file = CONTROLLERSPATH . '/ajax/' . $class . '.php';
			}
			
			if (isset($file) && file_exists($file)) {
				require_once($file);
			}
		}
		
		public function str($string, $params = array()) {
			if (strpos($string, '/') === false) {
				return '';
			}
			
			list($cat, $index) = explode('/', $string);
			
			if (isset($this->loadedStrings[$cat])) {
				$strings = $this->loadedStrings[$cat];
			} else {
				$file = STRINGSPATH . '/' . $cat . '.php';
				
				$strings = array();
				
				if (file_exists($file)) {
					$strings = require_once($file);
				}
				
				$this->loadedStrings[$cat] = $strings;
			}
			
			if (isset($strings[$index])) {
				if ($params != false) {
					return str_replace(array_keys($params), array_values($params), $strings[$index]);
				} else {
					return $strings[$index];
				}
			}
			
			return '';
		}
		
		public static function debug($obj) {
			echo '<pre>';
			print_r($obj);
			echo '</pre>';
		}
	}