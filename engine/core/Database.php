<?php
ini_set('memory_limit', '256M');
//ini_set("max_execution_time", "-1");
    class Database {

        private static $instance = null;

        private $mysqli;

        // Получение единственного экземпляра этого класса
        public static function getInstance() {
            self::init();

            return self::$instance;
        }

        // Инициализация класса
        public static function init() {
            if (self::$instance === null) {
                self::$instance = new self();
            }

            return true;
        }

        private function __clone() {}

        private function __construct() {
            $this->mysqli = @new \mysqli(MYSQLI_HOST, MYSQLI_USER, MYSQLI_PASS, MYSQLI_DB);

            if ($this->mysqli->connect_errno)
                exit('Ошибка соединения с базой данных');

            $this->mysqli->query("SET lc_time_names = 'ru_RU'");
            $this->mysqli->set_charset("utf8");
        }

        /**
         * Простой запрос
         *
         * @param string $query
         *
         * @return bool|mysqli_result
         */
        public function query($query) {
            $res = $this->mysqli->query($query);

            return $res;
        }

        /**
         * Возвращает значение первой ячейки первой строки набора или false если нет данных
         *
         * @param string $query
         *
         * @return mixed
         */
        public function getVar($query) {
            $res = $this->query($query);

            if ($res) {
                $row = $res->fetch_row();
                if ($row) {
                    return $row[0];
                }
            }

            return false;
        }

        /**
         * Возвращает простой, одномерный массив с первыми значениями всех строк набора или false если нет данных
         *
         * @param $query
         *
         * @return array|bool
         */
        public function getCol($query) {
            $res = $this->query($query);

            if ($res) {
                $results = array();
                while ($row = $res->fetch_row()) {
                    $results[] = $row[0];
                }

                return $results;
            }

            return false;
        }

        public function getColIndexed($query) {
            $res = $this->query($query);

            if ($res) {
                $results = array();
                while ($row = $res->fetch_row()) {
                    if (isset($row[1])) {
                        $results[$row[1]] = $row[0];
                    }
                }

                return $results;
            }

            return false;
        }

        /**
         * Возвращает первую строку набора в виде ассоциативного массива или false если нет данных
         *
         * @param $query
         *
         * @return array|bool
         */
        public function getRow($query) {
            $res = $this->query($query);

            if ($res) {
                $row = $res->fetch_assoc();

                if ($row != false) {
                    return $row;
                }
            }

            return false;
        }

        /**
         * Возвращает массив строк набора. Можно индексировать по любому полю.
         *
         * @param $query
         * @param bool|false $index_by
         *
         * @return array|bool
         */
        public function getRows($query, $index_by = false) {
			
            $res = $this->query($query);

            $results = array();

            if ($res) {
				//echo count($res->fetch_assoc());
                while ($row = $res->fetch_assoc()) {
                    if ($index_by && isset($row[$index_by])) {
                        $results[$row[$index_by]] = $row;
                    } else {
                        $results[] = $row;
                    }
                }
            }
			//print_r($results);
            return $results;
        }
		public function getRows1($query){
			 $res = $this->query($query);

            $results = array();
			 while ($row = $res->fetch_assoc()) {
				 $results[] = $row;
			 }
			 return $results;
		}

        /*
         * Вставка в базу
         */
        public function insert($table, $data, $format = array()) {

        }

        /*
         * Подготовка запроса функцией prepare
         */
        public function prepare($query, $values) {
            return vsprintf($query, $values);
        }

        /*
         * Очистка перед внесением в базу
         */
        public function esc($var) {
            return $this->mysqli->real_escape_string($var);
        }

        /*
         * Сколько затронуло ошибок строк
         */
        public function affectedRows() {
            return $this->mysqli->affected_rows;
        }
		/*
		*
		*/
		public function num_rows() {
            return $this->mysqli->num_rows;
        }
		public function error_text(){
			return $this->mysqli->error;
		}
        /*
         * Возвращает id последней вставленной записи
         */
        public function insertedId() {
            return $this->mysqli->insert_id;
        }



    }