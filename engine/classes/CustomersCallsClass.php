<?php

	class CustomersCallsClass {

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

		public function getCall($customerId, $callId) {
			$call = $this->db->getRow("SELECT * FROM clients_calls WHERE client_id = " . intval($customerId) . " AND id = " . intval($callId) . " LIMIT 1");

			return $call;
		}

		public function deleteCall($customerId, $callId) {
			$sql = sprintf("DELETE FROM clients_calls WHERE client_id = %d AND id = %d", $customerId, $callId);
			$this->db->query($sql);
		}

		public function getCallsByCustomerId($id) {
			$query = "SELECT * FROM clients_calls WHERE client_id = " . intval($id) . " ORDER BY date DESC";

			$calls = $this->db->getRows($query);

			return $calls;
		}

		public function getCustomers($params) {
			$where = array('1=1');

			$getCount = isset($params['getCount']) && $params['getCount'] ? true : false;

			if (isset($params['search'])) {
				$search = trim($params['search']);
				$or = array();
				$or[] = "c.short LIKE '%" . $this->db->esc($search) . "%'";
				$or[] = "c.name LIKE '%" . $this->db->esc($search) . "%'";

				$where[] = '(' . implode(' OR ', $or) . ')';
			}

			if (isset($params['manager'])) {
				$manager = intval($params['manager']);
				if ($manager != false) {
					$where[] = "c.user_id = " . $manager;
				}
			}

            if (isset($params['status'])) {
			 	$status = intval($params['status']);
			 	if ($status) {
			 		$where[] = "c.status_id = " . $status;
			 	}
			}

			if (isset($params['orderType'])) {
				$orderType = intval($params['orderType']);
				if ($orderType > 0) {
					$where[] = 'q.typ_ord = ' . $orderType;
				} else {
					$where[] = 'q.typ_ord NOT IN (1, 2, 3)';
				}
			}

			if (isset($params['ordersPeriodFrom'])) {
				$from = strtotime($params['ordersPeriodFrom']);
				if ($from != false) {
					$where[] = "q.date_query >= '" . date('Y-m-d 00:00:00', $from) . "'";
				}
			}
			if (isset($params['ordersPeriodTo'])) {
				$to = strtotime($params['ordersPeriodTo']);
				if ($to != false) {
					$where[] = "q.date_query <= '" . date('Y-m-d 23:59:59', $to) . "'";
				}
			}

			if (isset($params['callsPeriodFrom'])) {
				$from = strtotime($params['callsPeriodFrom']);
				if ($from != false) {
					$where[] = "cc.date >= '" . date('Y-m-d 00:00:00', $from) . "'";
				}
			}
			if (isset($params['callsPeriodTo'])) {
				$to = strtotime($params['callsPeriodTo']);
				if ($to != false) {
					$where[] = "cc.date <= '" . date('Y-m-d 23:59:59', $to) . "'";
				}
			}

			if (isset($params['noCalls'])) {
				if ($params['noCalls'] != false) {
					$where[] = "cc.date IS NULL";
				}
			}

			if (isset($params['ordersCount'])) {
				$value = intval($params['ordersCount']);
				if ($value > 0) {
					$where[] = "q.cnt >= " . $value;
				}
			}

			if ($getCount) {
				$q = "SELECT COUNT(c.uid)
					FROM clients c
					LEFT JOIN (SELECT cc1.* FROM (SELECT client_id, max(date) as maxdate FROM clients_calls GROUP BY client_id) cc INNER JOIN clients_calls cc1 ON cc1.client_id = cc.client_id AND cc1.date = cc.maxdate) cc ON cc.client_id = c.uid
					LEFT JOIN (SELECT q1.*, q.cnt FROM (SELECT client_id, max(date_query) as maxdate, COUNT(*) as cnt FROM queries GROUP BY client_id) q INNER JOIN queries q1 ON q1.client_id = q.client_id AND q1.date_query = q.maxdate) q ON q.client_id = c.uid
					WHERE " . implode(' AND ', $where);

				return intval($this->db->getVar($q));
			}

			$perPage = CustomersCallsConfig::$defaultPerPage;

			if (isset($params['page'])) {
				$offset = intval($params['page']) - 1;
				if ($offset < 0) {
					$offset = 0;
				}
			} else {
				$offset = 0;
			}

			$start = $offset * $perPage;

			$q = "
				SELECT
					c.*,
					q.prdm_sum_acc AS sum_last,
					q.uid AS order_id,
					q.typ_ord AS order_type,
					q.corsina_order_uid,
					q.corsina_order_num,
					cc.date AS last_call,
					q.date_query AS last_order,
                    q.uniq_id AS uniq_id,
					(SELECT sum(q.prdm_sum_acc) FROM queries q WHERE q.client_id = c.uid) AS sum_all,
					(SELECT COUNT(q.uid) FROM queries q WHERE q.client_id = c.uid) AS queries_count,
					(SELECT COUNT(cc.id) FROM clients_calls cc WHERE cc.client_id = c.uid) AS calls_count
				FROM clients c
				LEFT JOIN (SELECT cc1.* FROM (SELECT client_id, max(date) as maxdate FROM clients_calls GROUP BY client_id) cc INNER JOIN clients_calls cc1 ON cc1.client_id = cc.client_id AND cc1.date = cc.maxdate) cc ON cc.client_id = c.uid
				LEFT JOIN (SELECT q1.*, q.cnt FROM (SELECT client_id, max(date_query) as maxdate, COUNT(*) AS cnt FROM queries GROUP BY client_id) q INNER JOIN queries q1 ON q1.client_id = q.client_id AND q1.date_query = q.maxdate) q ON q.client_id = c.uid
				WHERE " . implode(' AND ', $where) . "
				AND q.deleted <> '1' ORDER BY q.date_query DESC
				LIMIT " . $start . ", " . $perPage . "
			";

			// echo $q;

			$clients = $this->db->getRows($q);

			if (isset($params['getOrderItems']) && $params['getOrderItems']) {
				if ($clients) {
					$ids = array_filter(array_column($clients, 'order_id'));

					if ($ids) {
						$items = QueriesClass::getInstance()->getItemsByQueryId($ids);

						foreach ($clients as $key => $client) {
							$order_id = intval($client['order_id']);

							if (isset($items[$order_id])) {
								$clients[$key]['items'] = $items[$order_id];
							} else {
								$clients[$key]['items'] = array();
							}
						}
					}
				}
			}

			return $clients;
		}

		public function getCalls($params) {
			$where = array('1=1');

			$getCount = isset($params['getCount']) && $params['getCount'] ? true : false;

			if (isset($params['manager'])) {
				$manager = intval($params['manager']);
				if ($manager != false) {
					$where[] = "cc.user_id = " . $manager;
				}
			}

			if (isset($params['periodFrom'])) {
				$from = strtotime($params['periodFrom']);
				if ($from != false) {
					$where[] = "cc.date >= '" . date('Y-m-d 00:00:00', $from) . "'";
				}
			}
			if (isset($params['periodTo'])) {
				$to = strtotime($params['periodTo']);
				if ($to != false) {
					$where[] = "cc.date <= '" . date('Y-m-d 23:59:59', $to) . "'";
				}
			}

			if (isset($params['result'])) {
				$result_ids = array_map('intval', $params['result']);
				$where[] = "cc.result_id IN (" . implode(',', $result_ids) . ")";
			}

			if ($getCount) {
				$q = "SELECT COUNT(cc.id) FROM clients_calls cc WHERE " . implode(' AND ', $where);
				return intval($this->db->getVar($q));
			}

			$perPage = CustomersCallsConfig::$defaultPerPage;

			if (isset($params['page'])) {
				$offset = intval($params['page']) - 1;
				if ($offset < 0) {
					$offset = 0;
				}
			} else {
				$offset = 0;
			}

			$start = $offset * $perPage;

			$q = "
			SELECT
				cc.*,
				c.short AS client_short
			FROM clients_calls cc
			INNER JOIN clients c ON c.uid = cc.client_id
			WHERE " . implode(' AND ', $where) . " ORDER BY cc.date DESC LIMIT " . $start . ", " . $perPage;
			// echo $q;

			$calls = $this->db->getRows($q);

			return $calls;
		}
	}
