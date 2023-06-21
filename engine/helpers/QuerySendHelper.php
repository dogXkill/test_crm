<?php

	class QuerySendHelper {
		
		public static function printPhones($phones) {
			if ($phones) {
				foreach ($phones as $phone) {
					if (trim($phone)) {
						echo '<span>' . trim($phone) . '</span>';
					}
				}
			}
		}
		
		public static function printClientDataTag($client) {
			// print_r($client);
			$attributes = array(
				'uid' => $client['uid'],
				'short' => $client['short'],
				'full' => $client['name'],
				'address' => $client['legal_address'],
				'inn' => $client['inn'],
				'kpp' => $client['kpp'],
				'rs' => $client['rs_acc'],
				'bik' => $client['bik'],
				'email' => $client['email'],
				'comment' => $client['comment']
			);
			
			$client['cont_tel'] = trim($client['cont_tel']);
			$client['firm_tel'] = trim($client['firm_tel']);
			
			$phones = array();
			if ($client['cont_tel']) $phones[] = $client['cont_tel'];
			if ($client['firm_tel']) $phones[] = $client['firm_tel'];
			
			$attributes['phones'] = json_encode($phones);
			
			foreach ($attributes as $key => $value) {
				$data[] = 'data-' . $key . '="' . htmlspecialchars($value) . '"';
			}
			
			echo '<div class="clientList_item_data" ' . implode(' ', $data) . '></div>';
		}
		
		public static function printCustomerSearchQueries($client) {
			if ($client['queries_count'] > 0) {
				echo '<span class="count">Кол-во заказов: <span>' . $client['queries_count']. '</span></span> <span class="last">Последний: <span>' . DatetimeHelper::format($client['last_query']['date'], 'd.m.Y') . '</span> на сумму <span>' . MoneyHelper::format($client['last_query']['sum']) . ' руб.</span></span>';
			} else {
				echo 'Этот клиент заказов не делал';
			}
		}
		
	}


