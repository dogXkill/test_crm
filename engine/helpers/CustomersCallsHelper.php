<?php

	class CustomersCallsHelper {
		
		public static function printOrderCountCell($customer) {
			if ($customer['queries_count'] > 0) {
				echo '<b>' . $customer['queries_count'] . '</b>';
				echo '<br/>';
				echo '<span>' . number_format($customer['sum_all'], 2, '.', ' ') . ' ₽</span>';
			} else {
				echo '<span>0</span>';
			}
		}
		
		public static function printLastOrderCell($customer) {
			$time = strtotime($customer['last_order']);
			
			if ($time) {
				echo '<div class="showItemsHandle">';
				echo '<span><b>' . date('d.m.Y', strtotime($customer['last_order'])) . '</b></span>';
				echo '<br/>';
				echo '<span>' . number_format($customer['sum_last'], 2, '.', ' ') . ' ₽</span>';
				echo '</div>';
				
				$items = $customer['items'];
				
				if ($items != false) {
					?>
					<div class="itemsTooltip" id="items_<?=rand(1,4444444);?>">
						<h6>Предмет счета</h6>
						<div class="list">
							<?php foreach ($items as $item) { ?>
							<div><strong><?php echo $item['name']; ?></strong> / <?php echo $item['num']; ?> шт. / <?php echo $item['price']; ?> руб.</div>
							<?php } ?>
						</div>
					</div>
					<?php 
				}
			} else {
				echo '<span>—</span>';
			}
		}
		
		public static function printLastCallCell($customer) {
			$time = strtotime($customer['last_call']);
			echo '<span>' . ($time ? date('d.m.Y H:i', $time) : '—') . '</span>';
		}
		
		public static function printOrderTypeCell($customer) {
			if (is_null($customer['order_type'])) {
				echo '—';
				return;
			}
			
			$orderType = intval($customer['order_type']);

		   //	$cartOrderUid = intval($customer['corsina_order_uid']);
           $cartOrderUid = $customer['uniq_id'];
		   $cartOrderNum = $customer['corsina_order_num'];

           if($cartOrderNum == "" or $cartOrderNum == 0){$cartOrderNum = "";}else{$cartOrderNum = "--> ".$cartOrderNum;}

			echo '<a href="https://crm.upak.me/acc/query/query_send.php?show=' . $customer['order_id'] . '" target="_blank"><b>' . $customer['order_id'] . '</b></a>';
			
			switch ($orderType) {
				case 1:
					echo ' заказ';
					break;
				case 2:
					echo ' магазин';
					if ($cartOrderUid != false) {
						echo ' <a href="https://www.paketoff.ru/order/print?num='.$cartOrderUid.'" target="_blank" style="font-size:9px;font-weight:bold; text-decoration: none;">'.$cartOrderNum.'</a>';
					}
					break;
				case 3:
					echo ' готовые с лого';
					if ($cartOrderUid != false) {
						echo ' <a href="https://www.paketoff.ru/order/print?num='.$cartOrderUid.'" target="_blank" style="font-size:8px;font-weight:bold;">'.$cartOrderNum.'</a>';
					}
					break;
				default:
					echo ' <img title="Тип заказа: Пакетофф" src="/acc/i/button_green_16x16.png" width="16" height="16" alt="" />';
					break;
			}
		}
		
		public static function printCallResultSelect() {
			echo '<select>';
			echo '<option value=""></option>';
			foreach (CustomersCallsConfig::$callsResults as $key => $value) {
				echo '<option value="' . $key . '">' . $value . '</option>';
			}
			echo '</select>';
		}
		
		public static function printCallResultCell($call) {
			if ($call['result_id'] == 1) {
				echo $call['result_other'];
			} elseif (isset(CustomersCallsConfig::$callsResults[$call['result_id']])) {
				echo CustomersCallsConfig::$callsResults[$call['result_id']];
			} else {
				'—';
			}
		}
		
		public static function printCallActionsCell($call) {
			$user = User::getInstance();
			if ($user->getAccountType() || $call['user_id'] == $user->uid) {
				echo '<span><i class="far fa-trash-alt deleteCall"></i></span>';
			}
		}
		
	}


