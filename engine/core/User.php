<?php

	final class User {

		private static $instance = null;

		private $db;

		private $logged = false;

		/**
		 * Массив данных пользователя
		 * useragent +
		 * ip +
		 * type - тип пользователя ('sup', 'meg', 'adm', 'mng', 'acc', 'oth')
		 * accountType - тип аккаунта (1 - админ или бухгалтер, иначе 0 - менеджер)
		 */
		private $data = array();

		public static function getInstance() {
			self::init();

			return self::$instance;
		}

		// Инициализация
		public static function init() {
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}
		}

		// При инициализации
		private function __construct() {
			$this->db = Database::getInstance();

			if (!session_id()) {
				session_start();
			}

			$this->data['useragent'] = $_SERVER['HTTP_USER_AGENT'];
			$this->data['ip'] = $_SERVER['REMOTE_ADDR'];

			// Пытаемся залогинить из сессии
			$this->tryAuth();

			if (isset($_GET['logout'])) {
				$this->logout();
			}
		}

		private function __clone() {}

		public function __get($property) {
			if (array_key_exists($property, $this->data)) {
				return $this->data[$property];
			}

			return null;
		}

		/**
		 * Попытка залогинить из куков
		 */
		private function tryAuth() {
			if ($this->isGuest() && isset($_POST['auth_in'])) {
				$params = array(
					'login' => $_POST['in_user'],
					'pass' => $_POST['in_pass']
				);

				$this->login($params);

				if ($this->isLogged()) {
					if (isset($_COOKIE['auto_redirect']) && $_COOKIE['auto_redirect'] == '1') {
						header('Location: /acc/query/');
					}

					setcookie('login_autocomplete', $this->getLogin(), 0, '/');
					setcookie('auto_redirect', '0', '/');
				}
			}

			// Попытка получить из куков данные авторизации
			$authData = $this->getAuthData();

			if ($authData != false) {
				$this->login($authData);
			}

			return false;
		}

		/*
		 * Берем authData из сессии или из куков
		 */
		private function getAuthData() {
			if (isset($_COOKIE['user_des']) && isset($_COOKIE['pass_des'])) {
				return array(
					'login' => $_COOKIE['user_des'],
					'pass' => $_COOKIE['pass_des']
				);
			}

			return false;
		}

		/*
		 * Логиним пользователя используя данные авторизации из кук
		 * TODO: вынести getUserByLogin в класс Users
		 */
		private function login($authData) {
			//extract($authData);

			$user = $this->getUser($authData);
			if ($user != false) {
				$this->processLogin($user);
			}
		}

		/*
		 * Получение пользователя по логину
		 */
		private function getUser($authData) {
			$user = $this->db->getRow("SELECT uid, login, email, type, pass, payment_edit_num, order_access, shop_access, tasks_access, plans_access, sprav_access, proizv_access, logistics_access, list_access, sotr_access, tabl_access, order_access_edit FROM users WHERE login = '" . $this->db->esc($authData['login']) . "' AND pass = '" . $this->db->esc($authData['pass']) . "' AND archive = '0'  LIMIT 1");

			return $user;
		}

		/*
		 * Программно авторизуем пользователя
		 * 1. Устанавливаем куки
		 * 2. Устанавливаем свойства объекта
		 */
		private function processLogin($user) {
			// Устанавливаем куки на 2 дня
			setcookie('user_des', $user['login'], CURRENT_TIME + 3600 * 24 * 14, '/');
			setcookie('pass_des', $user['pass'], CURRENT_TIME + 3600 * 24 * 14, '/');

			// Выставляем свойства объекта
			$this->setParams($user);
		}

		/*
		 * Программно выходим из акка, удаляем куку
		 */
		private function processLogout() {
			if ($this->isLogged()) {
				setcookie('login_autocomplete', $this->getLogin(), CURRENT_TIME + 3600 * 24 * 14, '/');
			}

			setcookie('user_des', '', CURRENT_TIME - 1, '/');
			setcookie('pass_des', '', CURRENT_TIME - 1, '/');
		}

		/*
		 * Выставляем свойства объекта
		 */
		private function setParams($user) {
			$this->logged = true;

			$this->data['uid'] = intval($user['uid']);
			$this->data['login'] = $user['login'];
			$this->data['email'] = $user['email'];
			$this->data['type'] = $user['type'];
			$this->data['accountType'] = in_array($user['type'], array('sup', 'acc', 'meg')) ? 1 : 0;
			$this->data['order_access_edit'] = $user['order_access_edit'];
		}

		/**
		 * Возвращает авторизован ли пользователь
		 */
		public function isLogged() {
			return $this->logged;
		}

		/**
		 * Возвращает гость ли пользователь
		 */
		public function isGuest() {
			return !$this->logged;
		}

		/*
		 * Авторизует пользователя используюя массив пользователя из БД. Предполагается,
		 * что данные пользователя уже валидированы и проверены.
		 */
		public function setUser($user) {
			$this->processLogin($user);
		}

		/*
		 * Выходит из аккаунта (затирает все куки)
		 */
		public function logout() {
			if ($this->isLogged()) {
				$this->logged = false;
				$this->processLogout();

				header('Location: /');
				die();
			}
		}

		public function getLogin() {
			if ($this->isLogged()) {
				return $this->data['login'];
			}

			return false;
		}

		public function getType() {
			if ($this->isLogged()) {
				return $this->data['type'];
			}

			return false;
		}

		public function getId() {
			if ($this->isLogged()) {
				return $this->data['uid'];
			}

			return false;
		}

		public function getEmail() {
			if ($this->isLogged()) {
				return $this->data['email'];
			}

			return false;
		}

		public function getAccountType() {
			if ($this->isLogged()) {
				return $this->data['accountType'];
			}

			return false;
		}

		public function isAdmin() {
			if ($this->isLogged()) {
				return (bool) $this->data['accountType'];
			}

			return false;
		}
        public function order_access_edit() {
			if ($this->isLogged()) {
				return $this->data['order_access_edit'];
			}

			return false;
		}
	}
