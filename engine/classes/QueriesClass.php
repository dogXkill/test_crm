<?php
ini_set('memory_limit', '256M');
	class QueriesClass {

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

		public static function processFilters($vars = array(), $params = array()) {
			$args = array();
			$filters = array();

			if ($vars) {
				$managers = isset($params['managers']) ? $params['managers'] : array();
				if (!$managers) {
					// trigger_error('В метод не передан массив с менеджерами', E_USER_ERROR);
				}

                if (isset($vars['from']) && strtotime($vars['from'])) {
                    $args['from'] = $vars['from'];
                    $filters['from'] = $args['from'];
                }

                if (isset($vars['to']) && strtotime($vars['to'])) {
                    $args['to'] = $vars['to'];
                    $filters['to'] = $args['to'];
                }

                if (isset($vars['manager']) && isset($managers[intval($vars['manager'])])) {
                    $args['manager'] = intval($vars['manager']);
                    $filters['manager'] = $args['manager'];
                }

                if (isset($vars['debt']) && in_array(intval($vars['debt']) , array(0, 1, 2))) {
                    $args['debt'] = intval($vars['debt']);
                    $filters['debt'] = $args['debt'];
                }
				if (isset($vars['izd'])) {
                    $args['izd'] = $vars['izd'];
                    $filters['izd'] = $args['izd'];
                }
				if (isset($vars['sdelka'])) {
                    $args['sdelka'] = $vars['sdelka'];
                    $filters['sdelka'] = $args['sdelka'];
                }
				if (isset($vars['specialFilter']) && in_array($vars['specialFilter'], ['k-dostavke', 'k-proizvodstvu', 'booking_ended'])) {
					if ($vars['specialFilter'] == 'k-dostavke') {
						$args['orderType'] = 2;
						$args['deliveryType'] = [8, 2];
                        $args['noCourierTask'] = true;
                        $args['prdm_num_acc'] = true;
                        $args['shipped'] = true;
                        if (!isset($args['from'])) {
                            $args['from'] = date('d.m.Y', strtotime('-3 months'));
                        }
					} elseif ($vars['specialFilter'] == 'k-proizvodstvu') {
						$args['orderType'] = [1, 3];
						$args['noApplications'] = true;
                        if (!isset($args['from'])) {
                            $args['from'] = date('d.m.Y', strtotime('-6 months'));
                        }
					} elseif ($vars['specialFilter'] == 'booking_ended') {
                        $args['orderType'] = [2, 3];
                        $args['booking_ended'] = true;
                        $args['prdm_num_acc'] = true;
                        $args['shipped'] = true;
                    }

					$filters['specialFilter'] = $vars['specialFilter'];
				} else {
					if (isset($vars['search']) && trim($vars['search']) != false) {
						$args['search'] = trim($vars['search']);
						$filters['search'] = $args['search'];
					}

					if (isset($vars['orderType']) && isset(QueriesConfig::$filter_orderType[intval($vars['orderType'])])) {
						$args['orderType'] = intval($vars['orderType']);
						$filters['orderType'] = $args['orderType'];
					}

					if (isset($vars['deliveryType']) && isset(QueriesConfig::$filter_deliveryType[intval($vars['deliveryType'])])) {
						$args['deliveryType'] = intval($vars['deliveryType']);
						$filters['deliveryType'] = $args['deliveryType'];
					}

					if (isset($vars['paymentType']) && isset(QueriesConfig::$filter_paymentType[intval($vars['paymentType'])])) {
						$args['paymentType'] = intval($vars['paymentType']);
						$filters['paymentType'] = $args['paymentType'];
					}

					if (isset($vars['printManager']) && in_array(intval($vars['printManager']) , array(0, 1))) {
						$args['printManager'] = intval($vars['printManager']);
						$filters['printManager'] = $args['printManager'];
					}


				 /*	if (isset($vars['amoCrm']) && in_array(intval($vars['amoCrm']) , array(0, 1))) {
						$args['amoCrm'] = intval($vars['amoCrm']);
						$filters['amoCrm'] = $args['amoCrm'];
					}   */

					if (isset($vars['deleted']) && isset(QueriesConfig::$filter_deleted[intval($vars['deleted'])])) {
						$args['deleted'] = intval($vars['deleted']);
						$filters['deleted'] = $args['deleted'];
					}
				}
			}

			return compact('args', 'filters');
		}

		public function getQueries($params) {
			$where = array('1=1');

			if (isset($params['deleted'])) {
				$where[] = 'q.deleted = ' . $params['deleted'];
			} else {
				$where[] = 'q.deleted = 0';
			}

			$getCount = isset($params['getCount']) && $params['getCount'] ? true : false;
			$getSummary = isset($params['getSummary']) && $params['getSummary'] ? true : false;

			if (isset($params['search'])) {
				$search = trim($params['search']);
				$or = array();
				$or[] = "c.short LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.name LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "q.uid LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "q.corsina_order_num LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.cont_pers LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.email LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.gen_dir LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.legal_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.postal_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.deliv_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.inn LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.cont_tel LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.firm_tel LIKE '%" . $this->db->esc($search) . "%'";

				$where[] = '(' . implode(' OR ', $or) . ')';
			}

			if (isset($params['orderType'])) {
                $orderType = $params['orderType'];
			   //	if ($orderType > 0) {
               if (is_array($params['orderType'])) {
                    $orderTypes = array_unique(array_map('intval', $params['orderType']));
                	if (!in_array(0, $orderType)) {
						$where[] = 'typ_ord IN (' . implode(', ', $orderTypes) . ')';
					}
					//$where[] = 'typ_ord = ' . $orderType;
			   	}
                else if($orderType > 0){$where[] = 'typ_ord = ' . $orderType; }
                else {
					$where[] = 'typ_ord NOT IN (1, 2, 3)';
				}
			}



           if (isset($params['booking_ended'])) {

			   		$where[] = " q.booking_till < DATE(NOW()) AND booking_till <> '0000-00-00' ";

			}

            if (isset($params['prdm_num_acc']) or isset($params['shipped'])) {

					$where[] = " (q.prdm_num_acc = '' OR  q.prdm_num_acc = '0') AND q.shipped='0'  ";

			}

			if (isset($params['deliveryType'])) {
				if (is_array($params['deliveryType'])) {
					$deliveryTypes = array_unique(array_map('intval', $params['deliveryType']));
					if (!in_array(0, $deliveryTypes)) {
						$where[] = 'deliv_id IN (' . implode(', ', $deliveryTypes) . ')';
					}
				} else {
					$deliveryType = intval($params['deliveryType']);
					if ($deliveryType > 0) {
						$where[] = 'deliv_id = ' . $deliveryType;
					} else {
						$not_in = array_filter(array_keys(QueriesConfig::$filter_deliveryType), function($index) { return $index > 0 ? true : false; });
						$where[] = 'deliv_id NOT IN (' . implode(', ', $not_in) . ')';
					}
				}
			}

			if (isset($params['paymentType'])) {
				$paymentType = intval($params['paymentType']);
				if ($paymentType > 0) {
					$where[] = 'form_of_payment = ' . $paymentType;
				} else {
					$where[] = 'form_of_payment NOT IN (1, 2, 3, 4)';
				}
			}

			if (isset($params['debt'])) {
				$debt = intval($params['debt']);
				if ($debt == 0) {
					// $where[] = 'prdm_dolg = 0';
					$where[] = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) = 0';
				} else if ($debt == 1){
					// $where[] = 'CAST(prdm_dolg AS DECIMAL(10, 2)) > 0';
					$where[] = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) > 0';
				} else {
                    $where[] = '((CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) > 0) AND CAST(q.prdm_opl AS DECIMAL(10, 2)) > 0';
				}
			}

			if (isset($params['CancelPercentage'])) {
				$printManager = intval($params['CancelPercentage']);
				if ($printManager == 0) {
					$where[] = 'q.CancelPercentage = 0';
				} else {
					$where[] = 'q.CancelPercentage = 1';
				}
			}


			if (isset($params['printManager'])) {
				$printManager = intval($params['printManager']);
				if ($printManager == 0) {
					$where[] = 'q.print_manager = 0';
				} else {
					$where[] = 'q.print_manager = 1';
				}
			}

		   /*	if (isset($params['amoCrm'])) {
				$amoCrm = intval($params['amoCrm']);
				if ($amoCrm == 0) {
					$where[] = "q.amo_crm_id = ''";
				} else {
					$where[] = "q.amo_crm_id != ''";
				}
			}*/

			if (isset($params['noCourierTask']) && $params['noCourierTask']) {
				$where[] = 'q.courier_task_id = 0';
			}

			if (isset($params['noApplications']) && $params['noApplications']) {
				// $where[] = '(SELECT COUNT(uid) FROM applications a WHERE a.zakaz_id = q.uid) = 0';
				$where[] = 'NOT EXISTS (SELECT 1 FROM applications a WHERE a.zakaz_id IS NOT NULL AND a.zakaz_id = q.uid)';
			}

			if (isset($params['from'])) {
				$from = strtotime($params['from']);
				if ($from != false) {
					$where[] = "date_query >= '" . date('Y-m-d 00:00:00', $from) . "'";
				}
			}

			if (isset($params['to'])) {
				$to = strtotime($params['to']);
				if ($to != false) {
					$where[] = "date_query <= '" . date('Y-m-d 23:59:59', $to) . "'";
				}
			}

			if (isset($params['manager'])) {
				$manager = intval($params['manager']);
				if ($manager != false) {

					$man_q = "SELECT order_access_type FROM users WHERE uid = $manager";
					$man_r = mysql_fetch_assoc(mysql_query($man_q));
					$order_access_type = $man_r['order_access_type'];

				 //	if ($order_access_type == '1' && !isset($_GET['search'])) {
					 	$where[] = "q.user_id = " . $manager;
				  //	}
                    //elseif ($order_access_type == '0') {
				   //		$where[] = "q.user_id = 999999999";
					//}

				}
			}
			//тип изделия
			$dop="";
/*				$dop ="LEFT JOIN obj_accounts AS obj_ac ON obj_ac.query_id = q.uid";
				//echo $dop;
				$where[]="obj_ac.tip_izd=".$params['izd'];
			}*/
			if (isset($params['izd'])) {
				//проверяем на несколько значений (разбиваем по запятой)
				$izd_temp=explode(",",$params['izd']);
				//если count ==1 - одиночное,иначе несколько элементов
				$dop ="LEFT JOIN obj_accounts AS obj_ac ON obj_ac.query_id = q.uid";
				//echo $dop;
				if ($params['izd']==0){
					$zap="(obj_ac.tip_izd=0 OR obj_ac.tip_izd='')";
				}else{
					if (count($izd_temp)>1){
						$zap="";
						foreach ($izd_temp as $vals){
							$zap.="obj_ac.tip_izd=".$vals." OR ";
						}
						$zap=substr($zap,0,-3);
						
						//echo $zap;
					}else{
						$zap="obj_ac.tip_izd=".$params['izd'];
					}
				}
				$where[]=$zap;
			}
			if (isset($params['sdelka'])) {
				$sdelka_temp=explode(",",$params['sdelka']);
				if ($dop==""){
					$dop ="LEFT JOIN obj_accounts AS obj_ac ON obj_ac.query_id = q.uid";
				}
				if ($params['sdelka']==0){
					$zap="(obj_ac.tip_sdelki=0 OR obj_ac.tip_sdelki='')";
				}else{
					if (count($sdelka_temp)>1){
						$zap="";
						foreach ($sdelka_temp as $vals){
							$zap.="obj_ac.tip_sdelki=".$vals." OR ";
						}
						$zap=substr($zap,0,-3);
						
						//echo $zap;
					}else{
						$zap="obj_ac.tip_sdelki=".$params['sdelka'];
					}
				}
				//echo $zap;
				$where[]=$zap;
			}
			//print_r($params['sdelka']);

			if (isset($params['perPage'])) {
				$perPage = intval($params['perPage']);
			} else {
				$perPage = QueriesConfig::$defaultPerPage;
			}
			//echo $perPage;
			if (isset($params['page'])) {
				$offset = intval($params['page']) - 1;
				if ($offset < 0) {
					$offset = 0;
				}
			} else {
				$offset = 0;
			}

			$start = $offset * $perPage;

			if ($getCount) {
				$query = "SELECT
						COUNT(q.uid)
					FROM queries q
					INNER JOIN clients AS c ON q.client_id = c.uid
					LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
					".$dop."
					WHERE
						" . implode(' AND ', $where);
                        //  echo $query;
				return intval($this->db->getVar($query));
			}

			if ($getSummary) {
				$subQuery = "SELECT
								COUNT(q.uid) AS 'count',
								SUM(CAST(q.prdm_sum_acc AS DECIMAL(10, 2))) AS amount,
								SUM(CAST(q.prdm_opl AS DECIMAL(10, 2))) AS paid
							FROM queries q
							INNER JOIN clients AS c ON q.client_id = c.uid
							LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
							".$dop."
							WHERE " . implode(' AND ', $where);

			   $query = "SELECT count, amount, paid, (amount - paid) AS debt FROM (" . $subQuery . ") sub";
				//echo $query;
			   return $this->db->getRow($query);
			}

			$orderBy = 'q.ready, q.type, q.date_query DESC';
			if (isset($params['orderBy']) && isset(QueriesConfig::$sortValues[$params['orderBy']])) {
				$sort = QueriesConfig::$sortValues[$params['orderBy']];

				if ($sort['field'] == 'date') {
					$orderBy = 'q.date_query';
				} elseif ($sort['field'] == 'summ') {
					$orderBy = 'CAST(q.prdm_sum_acc AS DECIMAL(10, 2))';
				} elseif ($sort['field'] == 'debt') {
					// $orderBy = 'CAST(q.prdm_dolg AS DECIMAL(10, 2))';
					$orderBy = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2)))';
				}

				if ($sort['order'] == 'desc') {
					$orderBy .= ' DESC';
				}

			}



			$user_name = $_COOKIE['user_des'];
			$user_pass = $_COOKIE['pass_des'];
			$order_type_q = "SELECT order_access_type, uid FROM users WHERE login = '$user_name' AND pass = '$user_pass'";
			$user_fields = mysql_fetch_assoc(mysql_query("$order_type_q"));
			if ($_COOKIE['order_access_type'] == '1') {
				if (!isset($_GET['search'])) {
					//$where[] = "q.user_id = " . $user_fields['uid'];
				}
			} elseif ($_COOKIE['order_access_type'] == '0') {
			//	$where[] = "q.user_id = 999999999";
			}



			$query = "SELECT SQL_CALC_FOUND_ROWS
					q.*,
					c.name AS client,
					c.short,
					c.cont_pers,
					ct.done
				FROM queries q
				INNER JOIN clients AS c ON q.client_id = c.uid
				LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
				".$dop."
				WHERE
					" . implode(' AND ', $where) . "
				ORDER BY " . $orderBy . "
				LIMIT " . $start . ", " . $perPage;
			//echo $query;
			$queries = $this->db->getRows($query);
			//echo count($queries);
			//print_r($queries);
			if ((isset($params['getItems']) && $params['getItems']) || (isset($params['getApps']) && $params['getApps'])) {
				if ($queries) {
					$ids = array_column($queries, 'uid');

					if ($params['getItems']) {
						$items = $this->getItemsByQueryId($ids);
					}

					if ($params['getApps']) {
						$apps = $this->getAppsByQueryId($ids);
					}

					foreach ($queries as $key => $query) {
						$query_id = intval($query['uid']);
						if (isset($items[$query_id])) {
							$queries[$key]['items'] = $items[$query_id];
						} else {
							$queries[$key]['items'] = array();
						}

						if (isset($apps[$query_id])) {
							$queries[$key]['apps'] = $apps[$query_id];
						} else {
							$queries[$key]['apps'] = array();
						}
					}
					
					//удаляем дубли для параметров tip_izd/tip_sdelki
					if ($dop!=""){
						$queries = array_unique($queries, SORT_REGULAR);
					}
					//
				}
			}
			//if (isset($params['deleted'])) {
			//ловим комментарий к удалению
			foreach ($queries as $k=>$item){
				$id_qu=$item['uid'];
				$res = $this->db->query("SELECT * FROM queries_comment WHERE queryId={$id_qu}");
				$row_n = mysqli_num_rows($res);
					 if ($row_n){
						 $row = $res->fetch_assoc();
						 $queries[$k]['tip_reason']=$row['tip_reason'];
						 $queries[$k]['comment']=$row['comment'];
					 }else{
						 $queries[$k]['tip_reason']=99;
						 $queries[$k]['comment']='-';
					 }
			}
			//}
			return $queries;
		}
		public function getQueriesAll($params){
			$where = array('1=1');

			if (isset($params['deleted'])) {
				$where[] = 'deleted =1 OR deleted=2';
			} else {
				$where[] = 'deleted = 0';
			}

			$getCount = isset($params['getCount']) && $params['getCount'] ? true : false;
			$getSummary = isset($params['getSummary']) && $params['getSummary'] ? true : false;

			if (isset($params['search'])) {
				$search = trim($params['search']);
				$or = array();
				$or[] = "c.short LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.name LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "q.uid LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "q.corsina_order_num LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.cont_pers LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.email LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.gen_dir LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.legal_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.postal_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.deliv_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.inn LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.cont_tel LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.firm_tel LIKE '%" . $this->db->esc($search) . "%'";

				$where[] = '(' . implode(' OR ', $or) . ')';
			}

			if (isset($params['orderType'])) {
                $orderType = $params['orderType'];
			   //	if ($orderType > 0) {
               if (is_array($params['orderType'])) {
                    $orderTypes = array_unique(array_map('intval', $params['orderType']));
                	if (!in_array(0, $orderType)) {
						$where[] = 'typ_ord IN (' . implode(', ', $orderTypes) . ')';
					}
					//$where[] = 'typ_ord = ' . $orderType;
			   	}
                else if($orderType > 0){$where[] = 'typ_ord = ' . $orderType; }
                else {
					$where[] = 'typ_ord NOT IN (1, 2, 3)';
				}
			}



           if (isset($params['booking_ended'])) {

			   		$where[] = " q.booking_till < DATE(NOW()) AND booking_till <> '0000-00-00' ";

			}

            if (isset($params['prdm_num_acc']) or isset($params['shipped'])) {

					$where[] = " (q.prdm_num_acc = '' OR  q.prdm_num_acc = '0') AND q.shipped='0'  ";

			}

			if (isset($params['deliveryType'])) {
				if (is_array($params['deliveryType'])) {
					$deliveryTypes = array_unique(array_map('intval', $params['deliveryType']));
					if (!in_array(0, $deliveryTypes)) {
						$where[] = 'deliv_id IN (' . implode(', ', $deliveryTypes) . ')';
					}
				} else {
					$deliveryType = intval($params['deliveryType']);
					if ($deliveryType > 0) {
						$where[] = 'deliv_id = ' . $deliveryType;
					} else {
						$not_in = array_filter(array_keys(QueriesConfig::$filter_deliveryType), function($index) { return $index > 0 ? true : false; });
						$where[] = 'deliv_id NOT IN (' . implode(', ', $not_in) . ')';
					}
				}
			}

			if (isset($params['paymentType'])) {
				$paymentType = intval($params['paymentType']);
				if ($paymentType > 0) {
					$where[] = 'form_of_payment = ' . $paymentType;
				} else {
					$where[] = 'form_of_payment NOT IN (1, 2, 3, 4)';
				}
			}

			if (isset($params['debt'])) {
				$debt = intval($params['debt']);
				if ($debt == 0) {
					// $where[] = 'prdm_dolg = 0';
					$where[] = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) = 0';
				} else if ($debt == 1){
					// $where[] = 'CAST(prdm_dolg AS DECIMAL(10, 2)) > 0';
					$where[] = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) > 0';
				} else {
                    $where[] = '((CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) > 0) AND CAST(q.prdm_opl AS DECIMAL(10, 2)) > 0';
				}
			}

			if (isset($params['CancelPercentage'])) {
				$printManager = intval($params['CancelPercentage']);
				if ($printManager == 0) {
					$where[] = 'q.CancelPercentage = 0';
				} else {
					$where[] = 'q.CancelPercentage = 1';
				}
			}


			if (isset($params['printManager'])) {
				$printManager = intval($params['printManager']);
				if ($printManager == 0) {
					$where[] = 'q.print_manager = 0';
				} else {
					$where[] = 'q.print_manager = 1';
				}
			}

		   /*	if (isset($params['amoCrm'])) {
				$amoCrm = intval($params['amoCrm']);
				if ($amoCrm == 0) {
					$where[] = "q.amo_crm_id = ''";
				} else {
					$where[] = "q.amo_crm_id != ''";
				}
			}*/

			if (isset($params['noCourierTask']) && $params['noCourierTask']) {
				$where[] = 'q.courier_task_id = 0';
			}

			if (isset($params['noApplications']) && $params['noApplications']) {
				// $where[] = '(SELECT COUNT(uid) FROM applications a WHERE a.zakaz_id = q.uid) = 0';
				$where[] = 'NOT EXISTS (SELECT 1 FROM applications a WHERE a.zakaz_id IS NOT NULL AND a.zakaz_id = q.uid)';
			}

			if (isset($params['from'])) {
				$from = strtotime($params['from']);
				if ($from != false) {
					$where[] = "date_query >= '" . date('Y-m-d 00:00:00', $from) . "'";
				}
			}

			if (isset($params['to'])) {
				$to = strtotime($params['to']);
				if ($to != false) {
					$where[] = "date_query <= '" . date('Y-m-d 23:59:59', $to) . "'";
				}
			}

			if (isset($params['manager'])) {
				$manager = intval($params['manager']);
				if ($manager != false) {

					$man_q = "SELECT order_access_type FROM users WHERE uid = $manager";
					
					$man_r = mysql_fetch_assoc(mysql_query($man_q));
					$order_access_type = $man_r['order_access_type'];

				 //	if ($order_access_type == '1' && !isset($_GET['search'])) {
					 	$where[] = "q.user_id = " . $manager;
				  //	}
                    //elseif ($order_access_type == '0') {
				   //		$where[] = "q.user_id = 999999999";
					//}

				}
			}



			if (isset($params['perPage'])) {
				$perPage = intval($params['perPage']);
			} else {
				$perPage = QueriesConfig::$defaultPerPage;
			}

			if (isset($params['page'])) {
				$offset = intval($params['page']) - 1;
				if ($offset < 0) {
					$offset = 0;
				}
			} else {
				$offset = 0;
			}
			//тип изделия
			$dop="";
/*				$dop ="LEFT JOIN obj_accounts AS obj_ac ON obj_ac.query_id = q.uid";
				//echo $dop;
				$where[]="obj_ac.tip_izd=".$params['izd'];
			}*/
			if (isset($params['izd'])) {
				//проверяем на несколько значений (разбиваем по запятой)
				$izd_temp=explode(",",$params['izd']);
				//если count ==1 - одиночное,иначе несколько элементов
				$dop ="LEFT JOIN obj_accounts AS obj_ac ON obj_ac.query_id = q.uid";
				//echo $dop;
				if ($params['izd']==0){
					$zap="(obj_ac.tip_izd=0 OR obj_ac.tip_izd='')";
				}else{
					if (count($izd_temp)>1){
						$zap="";
						foreach ($izd_temp as $vals){
							$zap.="obj_ac.tip_izd=".$vals." OR ";
						}
						$zap=substr($zap,0,-3);
						
						//echo $zap;
					}else{
						$zap="obj_ac.tip_izd=".$params['izd'];
					}
				}
				$where[]=$zap;
			}
			if (isset($params['sdelka'])) {
				$sdelka_temp=explode(",",$params['sdelka']);
				if ($dop==""){
					$dop ="LEFT JOIN obj_accounts AS obj_ac ON obj_ac.query_id = q.uid";
				}
				if ($params['sdelka']==0){
					$zap="(obj_ac.tip_sdelki=0 OR obj_ac.tip_sdelki='')";
				}else{
					if (count($sdelka_temp)>1){
						$zap="";
						foreach ($sdelka_temp as $vals){
							$zap.="obj_ac.tip_sdelki=".$vals." OR ";
						}
						$zap=substr($zap,0,-3);
						
						//echo $zap;
					}else{
						$zap="obj_ac.tip_sdelki=".$params['sdelka'];
					}
				}
				//echo $zap;
				$where[]=$zap;
			}
			$start = $offset * $perPage;

			if ($getCount) {
				$query = "SELECT
						COUNT(q.uid)
					FROM queries q
					INNER JOIN clients AS c ON q.client_id = c.uid
					LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
					WHERE
						" . implode(' AND ', $where);
                        //echo $query;
				return intval($this->db->getVar($query));
			}

			if ($getSummary) {
				$subQuery = "SELECT
								COUNT(q.uid) AS 'count',
								SUM(CAST(q.prdm_sum_acc AS DECIMAL(10, 2))) AS amount,
								SUM(CAST(q.prdm_opl AS DECIMAL(10, 2))) AS paid
							FROM queries q
							INNER JOIN clients AS c ON q.client_id = c.uid
							LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
							".$dop."
							WHERE " . implode(' AND ', $where);

			   $query = "SELECT count, amount, paid, (amount - paid) AS debt FROM (" . $subQuery . ") sub";

			   return $this->db->getRow($query);
			}

			$orderBy = 'q.ready, q.type, q.date_query DESC';
			if (isset($params['orderBy']) && isset(QueriesConfig::$sortValues[$params['orderBy']])) {
				$sort = QueriesConfig::$sortValues[$params['orderBy']];

				if ($sort['field'] == 'date') {
					$orderBy = 'q.date_query';
				} elseif ($sort['field'] == 'summ') {
					$orderBy = 'CAST(q.prdm_sum_acc AS DECIMAL(10, 2))';
				} elseif ($sort['field'] == 'debt') {
					// $orderBy = 'CAST(q.prdm_dolg AS DECIMAL(10, 2))';
					$orderBy = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2)))';
				}

				if ($sort['order'] == 'desc') {
					$orderBy .= ' DESC';
				}

			}



			$user_name = $_COOKIE['user_des'];
			$user_pass = $_COOKIE['pass_des'];
			$order_type_q = "SELECT order_access_type, uid FROM users WHERE login = '$user_name' AND pass = '$user_pass'";
			$user_fields = mysql_fetch_assoc(mysql_query("$order_type_q"));
			if ($_COOKIE['order_access_type'] == '1') {
				if (!isset($_GET['search'])) {
					//$where[] = "q.user_id = " . $user_fields['uid'];
				}
			} elseif ($_COOKIE['order_access_type'] == '0') {
			//	$where[] = "q.user_id = 999999999";
			}



			$query = "SELECT SQL_CALC_FOUND_ROWS
					q.*,
					c.name AS client,
					c.short,
					c.cont_pers,
					ct.done
				FROM queries q
				INNER JOIN clients AS c ON q.client_id = c.uid
				LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
				".$dop."
				WHERE
					" . implode(' AND ', $where) . "
				ORDER BY " . $orderBy ;
		 //  	 echo $query;

			$queries = $this->db->getRows($query);

			if ((isset($params['getItems']) && $params['getItems']) || (isset($params['getApps']) && $params['getApps'])) {
				if ($queries) {
					$ids = array_column($queries, 'uid');

					if ($params['getItems']) {
						$items = $this->getItemsByQueryId($ids);
					}

					if ($params['getApps']) {
						$apps = $this->getAppsByQueryId($ids);
					}

					foreach ($queries as $key => $query) {
						$query_id = intval($query['uid']);
						if (isset($items[$query_id])) {
							$queries[$key]['items'] = $items[$query_id];
						} else {
							$queries[$key]['items'] = array();
						}

						if (isset($apps[$query_id])) {
							$queries[$key]['apps'] = $apps[$query_id];
						} else {
							$queries[$key]['apps'] = array();
						}
					}
				}
			}
			//if (isset($params['deleted'])) {
			//ловим комментарий к удалению
			foreach ($queries as $k=>$item){
				$id_qu=$item['uid'];
				$res = $this->db->query("SELECT * FROM queries_comment WHERE queryId={$id_qu}");
				$row_n = mysqli_num_rows($res);
					 if ($row_n){
						 $row = $res->fetch_assoc();
						 $queries[$k]['tip_reason']=$row['tip_reason'];
						 $queries[$k]['comment']=$row['comment'];
					 }else{
						 $queries[$k]['tip_reason']=99;
						 $queries[$k]['comment']='-';
					 }
			}
			//}
			return $queries;
		}
		public function getQueriesAlls($params){
			
			$where = array('1=1');

			if (isset($params['deleted'])) {
				//$where[] = 'deleted = ' . $params['deleted'];
				$where[]='(q.deleted = 1 OR q.deleted=2)';
			} else {
				$where[] = 'q.deleted = 0';
			}

			$getCount = isset($params['getCount']) && $params['getCount'] ? true : false;
			$getSummary = isset($params['getSummary']) && $params['getSummary'] ? true : false;
			
			if (isset($params['search'])) {
				$search = trim($params['search']);
				$or = array();
				$or[] = "c.short LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.name LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "q.uid LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "q.corsina_order_num LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.cont_pers LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.email LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.gen_dir LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.legal_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.postal_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.deliv_address LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.inn LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.cont_tel LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.firm_tel LIKE '%" . $this->db->esc($search) . "%'";

				$where[] = '(' . implode(' OR ', $or) . ')';
			}

			if (isset($params['orderType'])) {
                $orderType = $params['orderType'];
			   //	if ($orderType > 0) {
               if (is_array($params['orderType'])) {
                    $orderTypes = array_unique(array_map('intval', $params['orderType']));
                	if (!in_array(0, $orderType)) {
						$where[] = 'typ_ord IN (' . implode(', ', $orderTypes) . ')';
					}
					//$where[] = 'typ_ord = ' . $orderType;
			   	}
                else if($orderType > 0){$where[] = 'typ_ord = ' . $orderType; }
                else {
					$where[] = 'typ_ord NOT IN (1, 2, 3)';
				}
			}



           if (isset($params['booking_ended'])) {

			   		$where[] = " q.booking_till < DATE(NOW()) AND booking_till <> '0000-00-00' ";

			}

            if (isset($params['prdm_num_acc']) or isset($params['shipped'])) {

					$where[] = " (q.prdm_num_acc = '' OR  q.prdm_num_acc = '0') AND q.shipped='0'  ";

			}
			
			if (isset($params['deliveryType'])) {
				if (is_array($params['deliveryType'])) {
					$deliveryTypes = array_unique(array_map('intval', $params['deliveryType']));
					if (!in_array(0, $deliveryTypes)) {
						$where[] = 'deliv_id IN (' . implode(', ', $deliveryTypes) . ')';
					}
				} else {
					$deliveryType = intval($params['deliveryType']);
					if ($deliveryType > 0) {
						$where[] = 'deliv_id = ' . $deliveryType;
					} else {
						$not_in = array_filter(array_keys(QueriesConfig::$filter_deliveryType), function($index) { return $index > 0 ? true : false; });
						$where[] = 'deliv_id NOT IN (' . implode(', ', $not_in) . ')';
					}
				}
			}

			if (isset($params['paymentType'])) {
				$paymentType = intval($params['paymentType']);
				if ($paymentType > 0) {
					$where[] = 'form_of_payment = ' . $paymentType;
				} else {
					$where[] = 'form_of_payment NOT IN (1, 2, 3, 4)';
				}
			}

			if (isset($params['debt'])) {
				$debt = intval($params['debt']);
				if ($debt == 0) {
					// $where[] = 'prdm_dolg = 0';
					$where[] = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) = 0';
				} else if ($debt == 1){
					// $where[] = 'CAST(prdm_dolg AS DECIMAL(10, 2)) > 0';
					$where[] = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) > 0';
				} else {
                    $where[] = '((CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2))) > 0) AND CAST(q.prdm_opl AS DECIMAL(10, 2)) > 0';
				}
			}

			if (isset($params['CancelPercentage'])) {
				$printManager = intval($params['CancelPercentage']);
				if ($printManager == 0) {
					$where[] = 'q.CancelPercentage = 0';
				} else {
					$where[] = 'q.CancelPercentage = 1';
				}
			}


			if (isset($params['printManager'])) {
				$printManager = intval($params['printManager']);
				if ($printManager == 0) {
					$where[] = 'q.print_manager = 0';
				} else {
					$where[] = 'q.print_manager = 1';
				}
			}

		   /*	if (isset($params['amoCrm'])) {
				$amoCrm = intval($params['amoCrm']);
				if ($amoCrm == 0) {
					$where[] = "q.amo_crm_id = ''";
				} else {
					$where[] = "q.amo_crm_id != ''";
				}
			}*/

			if (isset($params['noCourierTask']) && $params['noCourierTask']) {
				$where[] = 'q.courier_task_id = 0';
			}

			if (isset($params['noApplications']) && $params['noApplications']) {
				// $where[] = '(SELECT COUNT(uid) FROM applications a WHERE a.zakaz_id = q.uid) = 0';
				$where[] = 'NOT EXISTS (SELECT 1 FROM applications a WHERE a.zakaz_id IS NOT NULL AND a.zakaz_id = q.uid)';
			}

			if (isset($params['from'])) {
				$from = strtotime($params['from']);
				if ($from != false) {
					$where[] = "date_query >= '" . date('Y-m-d 00:00:00', $from) . "'";
				}
			}

			if (isset($params['to'])) {
				$to = strtotime($params['to']);
				if ($to != false) {
					$where[] = "date_query <= '" . date('Y-m-d 23:59:59', $to) . "'";
				}
			}

			if (isset($params['manager'])) {
				$manager = intval($params['manager']);
				if ($manager != false) {

					$man_q = "SELECT order_access_type FROM users WHERE uid = $manager";
					$man_r = mysql_fetch_assoc(mysql_query($man_q));
					$order_access_type = $man_r['order_access_type'];

				 //	if ($order_access_type == '1' && !isset($_GET['search'])) {
					 	$where[] = "q.user_id = " . $manager;
				  //	}
                    //elseif ($order_access_type == '0') {
				   //		$where[] = "q.user_id = 999999999";
					//}

				}
			}
			


			if (isset($params['perPage'])) {
				$perPage = intval($params['perPage']);
			} else {
				$perPage = QueriesConfig::$defaultPerPage;
			}

			if (isset($params['page'])) {
				$offset = intval($params['page']) - 1;
				if ($offset < 0) {
					$offset = 0;
				}
			} else {
				$offset = 0;
			}

			$start = $offset * $perPage;
			//тип изделия
			//print_r($params);
			$dop="";
			if (isset($params['izd'])) {
				//проверяем на несколько значений (разбиваем по запятой)
				$izd_temp=explode(",",$params['izd']);
				//если count ==1 - одиночное,иначе несколько элементов
				$dop ="LEFT JOIN obj_accounts AS obj_ac ON obj_ac.query_id = q.uid";
				//echo $dop;
				if ($params['izd']==0){
					$zap="(obj_ac.tip_izd=0 OR obj_ac.tip_izd='')";
				}else{
					if (count($izd_temp)>1){
						$zap="";
						foreach ($izd_temp as $vals){
							$zap.="obj_ac.tip_izd=".$vals." OR ";
						}
						$zap=substr($zap,0,-3);
						
						//echo $zap;
					}else{
						$zap="obj_ac.tip_izd=".$params['izd'];
					}
				}
				$where[]=$zap;
			}
			if (isset($params['sdelka'])) {
				$sdelka_temp=explode(",",$params['sdelka']);
				if ($dop==""){
					$dop ="LEFT JOIN obj_accounts AS obj_ac ON obj_ac.query_id = q.uid";
				}
				if ($params['sdelka']==0){
					$zap="(obj_ac.tip_sdelki=0 OR obj_ac.tip_sdelki='')";
				}else{
					if (count($sdelka_temp)>1){
						$zap="";
						foreach ($sdelka_temp as $vals){
							$zap.="obj_ac.tip_sdelki=".$vals." OR ";
						}
						$zap=substr($zap,0,-3);
						
						//echo $zap;
					}else{
						$zap="obj_ac.tip_sdelki=".$params['sdelka'];
					}
				}
				//echo $dop;
				$where[]=$zap;
			}
			
			if ($getCount) {
				$query = "SELECT
						COUNT(q.uid)
					FROM queries q
					INNER JOIN clients AS c ON q.client_id = c.uid
					LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
					".$dop."
					WHERE
						" . implode(' AND ', $where);
                        //  echo $query;
				return intval($this->db->getVar($query));
			}
			//echo "getSL:$getSummary";
			
			if ($getSummary) {
				$subQuery = "SELECT
								COUNT(q.uid) AS 'count',
								SUM(CAST(q.prdm_sum_acc AS DECIMAL(10, 2))) AS amount,
								SUM(CAST(q.prdm_opl AS DECIMAL(10, 2))) AS paid
							FROM queries q
							INNER JOIN clients AS c ON q.client_id = c.uid
							LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
							".$dop."
							WHERE " . implode(' AND ', $where);
				
			   $query = "SELECT count, amount, paid, (amount - paid) AS debt FROM (" . $subQuery . ") sub";
				//echo "test:".$query;
			   return $this->db->getRow($query);
			}
			
			$orderBy = 'q.ready, q.type, q.date_query DESC';
			if (isset($params['orderBy']) && isset(QueriesConfig::$sortValues[$params['orderBy']])) {
				$sort = QueriesConfig::$sortValues[$params['orderBy']];

				if ($sort['field'] == 'date') {
					$orderBy = 'q.date_query';
				} elseif ($sort['field'] == 'summ') {
					$orderBy = 'CAST(q.prdm_sum_acc AS DECIMAL(10, 2))';
				} elseif ($sort['field'] == 'debt') {
					// $orderBy = 'CAST(q.prdm_dolg AS DECIMAL(10, 2))';
					$orderBy = '(CAST(q.prdm_sum_acc AS DECIMAL(10, 2)) - CAST(q.prdm_opl AS DECIMAL(10, 2)))';
				}

				if ($sort['order'] == 'desc') {
					$orderBy .= ' DESC';
				}

			}


			
			$user_name = $_COOKIE['user_des'];
			$user_pass = $_COOKIE['pass_des'];
			$order_type_q = "SELECT order_access_type, uid FROM users WHERE login = '$user_name' AND pass = '$user_pass'";
			$user_fields = mysql_fetch_assoc(mysql_query("$order_type_q"));
			if ($_COOKIE['order_access_type'] == '1') {
				if (!isset($_GET['search'])) {
					//$where[] = "q.user_id = " . $user_fields['uid'];
				}
			} elseif ($_COOKIE['order_access_type'] == '0') {
			//	$where[] = "q.user_id = 999999999";
			}



			$query = "SELECT SQL_CALC_FOUND_ROWS
					q.*,
					c.name AS client,
					c.short,
					c.cont_pers,
					ct.done
				FROM queries q
				INNER JOIN clients AS c ON q.client_id = c.uid
				LEFT JOIN courier_tasks AS ct ON q.courier_task_id = ct.id
				".$dop."
				WHERE
					" . implode(' AND ', $where) . "
				ORDER BY " . $orderBy  ;
		 //  	 echo $query;
			//echo $query;
			//echo 'tttttt';
			$queries = $this->db->getRows($query,'uid');
			//$queries=$this->db->getRows1($query);
			//echo 'tttttt';
			//print_r($queries);
			if ((isset($params['getItems']) && $params['getItems']) || (isset($params['getApps']) && $params['getApps'])) {
				if ($queries) {
					$ids = array_column($queries, 'uid');

					if ($params['getItems']) {
						$items = $this->getItemsByQueryId($ids);
					}

					if ($params['getApps']) {
						$apps = $this->getAppsByQueryId($ids);
					}

					foreach ($queries as $key => $query) {
						$query_id = intval($query['uid']);
						if (isset($items[$query_id])) {
							$queries[$key]['items'] = $items[$query_id];
						} else {
							$queries[$key]['items'] = array();
						}

						if (isset($apps[$query_id])) {
							$queries[$key]['apps'] = $apps[$query_id];
						} else {
							$queries[$key]['apps'] = array();
						}
					}
				}
			}
			if ($dop!=""){
						$queries = array_unique($queries, SORT_REGULAR);
					}
			//print_r($queries);
			//if (isset($params['deleted'])) {
			//ловим комментарий к удалению
			foreach ($queries as $k=>$item){
				$id_qu=$item['uid'];
				$res = $this->db->query("SELECT * FROM queries_comment WHERE queryId={$id_qu}");
				$row_n = mysqli_num_rows($res);
					 if ($row_n){
						 $row = $res->fetch_assoc();
						 $queries[$k]['tip_reason']=$row['tip_reason'];
						 $queries[$k]['comment']=$row['comment'];
					 }else{
						 $queries[$k]['tip_reason']=99;
						 $queries[$k]['comment']='-';
					 }
			}
			//}
			//print_r($queries);
			return $queries;
		}
		public function getReasonQuery($mas){
			$reason=array();
			$k=0;
			$reason['tip'][0]=0;
			$reason['tip'][1]=0;
			$reason['tip'][2]=0;
			$reason['tip'][3]=0;
			$reason['tip'][4]=0;
			$reason['tip'][5]=0;
			$reason['tip'][6]=0;
			$reason['tip'][7]=0;
			$reason['tip'][8]=0;
			$reason['tip'][99]=0;
			foreach ($mas as $k=>$item){
				$id_qu=$item['uid'];
				
				$res = $this->db->query("SELECT * FROM queries_comment WHERE queryId={$id_qu}");
				
					
					 $row_n = mysqli_num_rows($res);
					 if ($row_n){
						
						$row = $res->fetch_assoc();
						//$reason[$k]['uid']=$id_qu;
						//$reason[$k]['tip']=$row['tip_reason'];
						$reason['tip'][$row['tip_reason']]=$reason['tip'][$row['tip_reason']]+1;
						//$k++;
					 }else{
						 $reason['tip'][99]=$reason['tip'][99]+1;
					 }
				
				
			}
			
			return $reason;
		}
		public function getReasonQueryId($id_qu){
			$res = $this->db->query("SELECT * FROM queries_comment WHERE queryId={$id_qu}");
			$row_n = mysqli_num_rows($res);
					 if ($row_n){
						$row = $res->fetch_assoc();
						return $row;
					 }else{return false;}
		}
		public function getItemsByQueryId($ids) {
			$items = array();

			if (!is_array($ids)) {
				$ids = array($ids);
			}

			$ids = array_map('intval', $ids);

			$res = $this->db->query("SELECT * FROM obj_accounts WHERE query_id IN (" . implode(', ', $ids).") ORDER BY nn");
			if ($res) {
				while ($row = $res->fetch_assoc()) {
					$query_id = intval($row['query_id']);
					if (!isset($items[$query_id])) {
						$items[$query_id] = array();
					}

					$items[$query_id][] = $row;
				}
			}

			return $items;
		}

		public function getAppsByQueryId($ids) {
			$apps = array();

			if (!is_array($ids)) {
				$ids = array($ids);
			}

			$ids = array_map('intval', $ids);

			$res = $this->db->query("SELECT uid, zakaz_id FROM applications WHERE zakaz_id IN (" . implode(', ', $ids) . ")  ORDER BY dat_ord");
			if ($res) {
				while ($row = $res->fetch_assoc()) {
					$query_id = intval($row['zakaz_id']);
					if (!isset($apps[$query_id])) {
						$apps[$query_id] = array();
					}

					$apps[$query_id][] = $row['uid'];
				}
			}

			return $apps;
		}

		public function getPaymentsByQueryId($id) {
			$query = "SELECT * FROM payment_predm WHERE query_id = " . intval($id) . " ORDER BY date_ready ASC";

			$payments = $this->db->getRows($query);

			return $payments;
		}

		public function getQueryById($id, $fields = false) {
			if (!$id) {
				return false;
			}

			if (!$fields) {
				$fields = array('*');
			}

			$query = $this->db->getRow("SELECT " . implode(', ', $fields) . " FROM queries WHERE uid = " . intval($id) . " LIMIT 1");

			return $query;
		}

		public function getQueryData($id) {
			$query = "SELECT q.prdm_sum_acc, u.surname, u.name, u.email, c.short, (SELECT SUM(sum_accounts) FROM payment_predm pp WHERE pp.query_id = q.uid) as sum_accounts
			FROM queries q
			INNER JOIN clients c ON q.client_id = c.uid
			INNER JOIN users u ON q.user_id = u.uid
			WHERE q.uid = " . intval($id) . " LIMIT 1";

			$data = $this->db->getRow($query);

			return $data;
		}

		public function update($queryId, $params) {
			if (!$params) {
				return false;
			}
				$query = mysql_query("UPDATE queries SET prdm_opl='$oplaceno', prdm_dolg='$dolg' WHERE uid='$query_id'");

			$set = array();
			foreach ($params as $key => $value) {
				$set[] = $key . ' = ' . $this->db->esc($value);
			}

			$query = "UPDATE queries SET " . implode(', ', $set) . " WHERE uid = " . intval($queryId);

			$this->db->query($query);

			return true;
		}
		
		
		public function load_client_info($queryId){
			$sql="SELECT * FROM `queries` WHERE `uid` = {$queryId}";
			$res = $this->db->query($sql);
			if ($res) {
				while ($row = $res->fetch_assoc()) {
					$client_id=$row['client_id'];
				}
			}
			$sql="SELECT * FROM `clients` WHERE `uid` = {$client_id}";
			$res = $this->db->query($sql);
			if ($res) {
				$row = $res->fetch_assoc();
				$client=$row;
				/*while ($row = $res->fetch_assoc()) {
					$email=$row['email'];
			}*/
			}
			return $client;
		}
		public function load_types(){
			$sql="SELECT * FROM `types`";
			$res = $this->db->query($sql);
			if ($res) {
				while ($row = $res->fetch_assoc()) {
					$types[$row[tid]]=$row;
				}
				return $types;
			}else{return false;}
		}

	}
