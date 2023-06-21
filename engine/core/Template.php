<?php

	class Template {

		private static $instance = null;


		// Абсолютный путь до папки со вьюхами
		protected $viewsPath;

		/**
		 * Абсолютный путь и ссылка на папку с ресурсами
		 */
		protected $assetsPath;
		protected $assetsUrl;

		/**
		 * Зарегистрированные скрипты и стили
		 */
		protected $registeredScripts;
		protected $registeredStyles;

		/**
		 * Скрипты и стили для вывода
		 */
		protected $app_styles = array();
		protected $app_scripts = array();
		protected $app_scripts_footer = array();

		/*
		 * СЕО-данные страницы
		 */
		protected $app_seo;

		/**
		 * Скомпилированные части страницы
		 */
		protected $app_header;
		protected $app_footer;
		protected $app_content;

		public static function getInstance() {
			self::init();

			return self::$instance;
		}

		public static function init() {
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}
		}

		// При инициализации
		private function __construct() {
			$this->viewsPath = TEMPLATEPATH;
			$this->path = ASSETSPATH;
			$this->url = str_replace(ABSPATH, '', ASSETSPATH);
		}

		private function __clone() {}

		public function initTemplate() {
			// Регистрация стандартных скриптов
			$this->registerScript('jquery', $this->url . '/libs/jquery/js/jquery-3.4.1.min.js', false);
			$this->registerScript('jquery-ui', $this->url . '/libs/jquery/js/jquery-ui-1.12.1.min.js', false);
			$this->registerScript('jquery-ui-timepicker-addon', $this->url . '/libs/jquery-ui-timepicker-addon-1.6.3/js/jquery-ui-timepicker-addon.min.js', false);
			$this->registerStyle('jquery-ui', $this->url . '/libs/jquery/css/jquery-ui-1.12.1.min.css', false);
			$this->registerStyle('jquery-ui-timepicker-addon', $this->url . '/libs/jquery-ui-timepicker-addon-1.6.3/css/jquery-ui-timepicker-addon.min.css', false);
			//$this->registerStyle('font-awesome', $this->url . '/libs/font-awesome-4.7.0/css/font-awesome.min.css', false);
			//$this->registerStyle('font-awesome-5.10', $this->url . '/libs/fontawesome-5.10.0-web/css/all.min.css', false);
			//$this->registerStyle('font-awesome-5.12', $this->url . '/libs/fontawesome-5.12.0-web/css/all.min.css', false);
			// $this->registerScript('jquery', 'local:/lib/jquery-3.2.1.min.js', false);
			// $this->registerScript('jquery', 'external:/lib/jquery-3.2.1.min.js', false);

			// Инициализация шаблона (регистрация нужных скриптов)
			/* $initFile = $this->getTemplatePath() . '/_init.php';
			if (file_exists($initFile)) {
				require_once($initFile);
			} */
		}

		public function render($file, $params = array(), $return = false) {
			// print_r(array_keys($params));
			extract($params);

			ob_start();

			include($this->viewsPath . '/' . $file . '.tpl.php');

			if ($return) {
				return ob_get_clean();
			} else {
				echo ob_get_clean();
			}

			return true;
		}

		/**
		 * Выводим страницу
		 */
		public function printPage($page, $page_params = array(), $layout = 'main', $layout_params = array()) {
			$layout = 'layouts/' . $layout;

			if (!file_exists($this->viewsPath . '/'. $layout . '.tpl.php')) {
				exit('Layout <b>' . $this->viewsPath . '/' . $layout . '.tpl.php</b> not found!');
			}

			if (!file_exists($this->viewsPath . '/' . $page . '.tpl.php')) {
				exit('View file <b>' . $this->viewsPath . '/' . $page . '.tpl.php</b> not found!');
			}

			// Рендерим $app_header
			$this->renderHeader();

			// Рендерим $app_content;
			$this->renderContent($page, $page_params);

			// Рендерим $app_footer
			$this->renderFooter();

			$layout_params['app_header'] = $this->app_header;
			$layout_params['app_content'] = $this->app_content;
			$layout_params['app_footer'] = $this->app_footer;

			$this->render($layout, $layout_params);
		}

		/*
		 * Строит head-теги
		 */
		private function renderHeader() {
			$rn = PHP_EOL;
			$t = "\t";

			$app_header = '';

			// СЕО-теги
			// Title
			$title = isset($this->app_seo['title']) ? $this->app_seo['title'] : SITENAME;
			$app_header .= '<title>' . $title . '</title>' . $rn;

			// Description если есть
			$description = isset($this->app_seo['description']) ? $this->app_seo['description'] : '';
			if ($description != false) {
				$app_header .= $t. '<meta name="description" content="' . $description . '" />' . $rn;
			}

			// Keywords если есть
			$keywords = isset($this->app_seo['keywords']) ? $this->app_seo['keywords'] : '';
			if ($keywords != false) {
				$app_header .= $t . '<meta name="keywords" content="' . $keywords . '" />' . $rn;
			}



			// Подключаемые стили
			if ($this->app_styles) {
				foreach ($this->app_styles as $style) {
					$app_header .= $t . '<link rel="stylesheet" href="' . $style['src'] . '?cache=' . rand() . '" type="text/css" media="all" />' . $rn;
				}
			}

			// Подключаемые скрипты
			if ($this->app_scripts) {
				foreach ($this->app_scripts as $script) {
					$app_header .= $t . '<script type="text/javascript" charset="utf-8" src="' . $script['src'] . '?cache=' . rand() . '"></script>' . $rn;
				}
			}

			$this->app_header = $app_header;
		}

		/**
		 * Строит футер (скрипты для футера)
		 */
		private function renderFooter() {
			//$rn = "\r\n";
			$rn = PHP_EOL;

			$app_footer = '';

			// Подключаемые в футере скрипты
			if ($this->app_scripts_footer) {
				foreach ($this->app_scripts_footer as $script) {
					$app_footer .= '<script type="text/javascript" charset="utf-8" src="' . $script['src'] . '?cache=' . rand() . '"></script>' . $rn;
				}
			}

			$this->app_footer = $app_footer;
		}

		/*
		 * Строит контент страницы
		 */
		public function renderContent($file, $params = array()) {
			$app_content = $this->render($file, $params, true);
			$this->app_content = $app_content;
		}

		/*
		 * Подключает скрипт к странице
		 */
		public function enqueueScript($handle, $src = false, $in_footer = true) {
			if (!isset($this->registeredScripts[$handle])) {
				if ($src == false) {
					return false;
				}

				$this->registerScript($handle, $src, $in_footer);
			}

			$script = $this->registeredScripts[$handle];

			if ($script['in_footer'] != false && !isset($this->app_scripts_footer[$script['handle']])) {
				$this->app_scripts_footer[$script['handle']] = $script;
			} elseif ($script['in_footer'] == false && !isset($this->app_scripts[$script['handle']])) {
				$this->app_scripts[$script['handle']] = $script;
			}

			return true;
		}

		/*
		 * Отключает скрипт со страницы
		 */
		public function dequeueScript($handle, $in_footer = true) {
			if ($in_footer) {
				if (isset($this->app_scripts_footer[$handle])) {
					unset($this->app_scripts_footer[$handle]);
				}
			} else {
				if (isset($this->app_scripts[$handle])) {
					unset($this->app_scripts[$handle]);
				}
			}

			return true;
		}

		/*
		 * Подключает стиль к странице
		 */
		public function enqueueStyle($handle, $src = false) {
			if (!isset($this->registeredStyles[$handle])) {
				if ($src == false) {
					return false;
				}

				$this->registerStyle($handle, $src);
			}

			$style = $this->registeredStyles[$handle];
			$this->app_styles[$style['handle']] = $style;

			return true;
		}

		/*
		 * Регистрирует js-скрипт. Возвращает true/false
		 */
		public function registerScript($handle, $src, $in_footer = true) {
			if (!isset($this->registeredScripts[$handle])) {
				$this->registeredScripts[$handle] = array(
					'handle' => $handle,
					'src' => $src,
					'in_footer' => $in_footer
				);

				return true;
			}

			return false;
		}

		/*
		 * Регистрирует css-скрипт. Возвращает true/false
		 */
		public function registerStyle($handle, $src) {
			if (!isset($this->registeredStyles[$handle])) {
				$this->registeredStyles[$handle] = array(
					'handle' => $handle,
					'src' => $src
				);

				return true;
			}

			return false;
		}

		/**
		 * Возвращает абсолютный путь до темы
		 */
		public function getTemplatePath() {
			return $this->path;
		}

		/*
		 * Возвращает ссылку до папки темы
		 */
		public function getTemplateUrl() {
			return $this->url;
		}

		/**
		 * Устанавливает сео-поле
		 */
		public function setSeo($param, $value) {
			$this->app_seo[$param] = $value;
		}

	}
