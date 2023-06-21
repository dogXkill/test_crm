<?php

	class QueryListHelper {

		public static function printItems($items) {
			echo '<i class="fa fa-list showOrderItems" style="font-size: 14px; color: #666; margin-right: 5px;"></i>';

			if ($items != false) {
				?>

				<div class="itemsTooltip" id="items_<?=rand(1,4444444);?>">
					<h6>Предмет счета</h6>
					<div class="list">
						<?foreach ($items as $item) { ?>
						<div><strong><? echo $item['name']; ?></strong> / <? echo $item['num']; ?> шт. / <? echo $item['price']; ?> руб.</div>
						<?} ?>
					</div>
				</div>
				<?
			} else {

			}
		}

		public static function printOrderType($obj) {
			$orderType = intval($obj['typ_ord']);

			$cartOrderUid = intval($obj['corsina_order_uid']);
			$cartOrderNum = intval($obj['corsina_order_num']);
            $uniq_id = $obj['uniq_id'];
			echo '<b>' . $obj['uid'] . '</b>';

			switch ($orderType) {
				case 1:
					echo ' заказ';
					break;
				case 2:
					echo ' магазин';
					if ($cartOrderUid != false) {
						echo ' <a href="https://www.paketoff.ru/order/print?num='.$uniq_id.'" target="_blank" style="font-size:9px;font-weight:bold; text-decoration: none;"> <i class="fa fa-cart-plus" style="font-size: 14px; color: #666; margin-right: 5px;"></i> '.$cartOrderNum.'</a>';
					}
					break;
				case 3:
					echo ' готовые с лого';
					if ($cartOrderUid != false) {
						echo ' <a href="https://www.paketoff.ru/order/print?num='.$uniq_id.'" target="_blank" style="font-size:8px;font-weight:bold;"> <i class="fa fa-cart-plus" style="font-size: 14px; color: #666; margin-right: 5px;"></i> '.$cartOrderNum.'</a>';
					}
					break;
				default:
					echo ' <img title="Тип заказа: Пакетофф" src="/acc/i/button_green_16x16.png" width="16" height="16" alt="" />';
					break;
			}
		}

		public static function printPaymentType($obj) {
			$paymentType = $obj['form_of_payment'];
            $zakaz_id = $obj['query_id'];

			switch ($paymentType) {


                case 1:
					?><i class="fa-light fa-money-bill" style="font-size: 14px; color: #666; margin-right: 5px;" title='Наличные'></i><?
					break;
				case 2:
					?><i class="fa fa-university" style="font-size: 14px; color: #666; margin-right: 5px;" title='Безнал'></i><?
					break;
				case 3:
					?><i class="fa fa-newspaper" style="font-size: 14px; color: #666; margin-right: 5px;" title='Квитанция'></i><?
					break;
				case 4:
					?><i class="fa fa-credit-card-alt" style="font-size: 14px; color: #666; margin-right: 5px;" title="По карте" aria-hidden="true"></i><?
					break;
				default:
					?>—<?
					break;
			}

			echo '<input type="hidden" id="opl_method_' . $obj['uid'] . '" value="' . $paymentType . '" />';
		}



    	public static function printPaymentLink($obj) {
			$uniq_id = $obj;

			echo '<a href="https://www.paketoff.ru/pay/?uniq_id='.$uniq_id.'" target="_blank"><i class="fa fa-credit-card-alt" style="font-size: 14px; color: #666; margin-right: 5px;" title="По карте" aria-hidden="true"></i></a>';
		}



        public static function printAmount($obj) {
            $query_id = $obj['uid'];
            $amount = $obj['prdm_sum_acc'];

            if ($amount) {
                $span = '<span title="Сумма счета" class="list_sum" id="acc_sum_'.$query_id.'">';
                $span .= MoneyHelper::format($amount);
                $span .= '</span>';
                echo $span. ($obj['skidka'] != '0' ? ' <b class="query_discount" title="Размер скидки">'.$obj['skidka'].'%</b>' : '');
            } else {
                echo '<span class="list_sum">---</span>';
            }
        }

		public static function printPayments($obj) {
			$summ = floatval($obj['prdm_opl']);

			echo '<span class="editPaymentsOpenButton" title="Редактировать список платежей">' . MoneyHelper::format($summ) . '</span>';
		}

		public static function printDate($date) {
			$timestamp = strtotime($date);

			if ($timestamp == false) {
				echo '<span class="dateValue" title="' . $date . '">—</span>';
				return;
			}

			$day = date('d', $timestamp);
			$month = DatetimeHelper::getRusMonth(date('m', $timestamp));
			$year = date('Y', $timestamp);
			$time = date('H:i', $timestamp);

			echo '<span class="dateValue" title="' . sprintf('%s %s %s %s', $day, $month, $year, $time) .'">';
			echo $day . ' ' . $month;
			if (date('Y') != $year) {
				echo ' ' . $year;
			}
			echo '</span>';
		}




		public static function printAccNumber($obj) {
			//$isAcc = Engine::app()->user->getAccountType();
            $userId = Engine::app()->user->uid;
			$q = "SELECT payment_edit_num FROM users WHERE uid = " . $userId;
			$r = mysql_fetch_assoc(mysql_query($q));
			$isAcc = $r['payment_edit_num'];


			$accNumber = $obj['prdm_num_acc'];
			$value = $accNumber !== '0' && $accNumber != false ? $accNumber : '';

			// echo '<input id="acc_inp_'.$obj['uid'].'" type=text class=inp_hint_acc style="width:45px;" value="'.$value.'" maxlength=50 />';
			echo '<input type="text" data-query-id="' . $obj['uid'] . '" value="' . $value . '" maxlength=50 ' . (($isAcc == '1') ? '' : 'readonly ') . '/>';
			echo '<span><i class="fa fa-cog fa-spin"></i></span>';
		}

		public static function printActionButtons($obj) {


            $userId = Engine::app()->user->uid;
            $query_user_id = $obj['user_id'];
			$q = "SELECT payment_edit_num, order_access_edit, shipped_edit FROM users WHERE uid = " . $userId;
			$r = mysql_fetch_assoc(mysql_query($q));
			$payment_edit_num_dostup = $r['payment_edit_num'];
			$order_access_edit_dostup = $r['order_access_edit'];
			$shipped_edit = $r['shipped_edit'];
			// AMO CRM
			$activeClass = $obj['amo_crm_id'] ? 'active' : '';
			if ($obj['amo_crm_id']){$activeClass_sdelka_amo='active';}else{$activeClass_sdelka_amo='no_active';}
			$title = $obj['amo_crm_id'] ? $obj['amo_crm_id'] : 'Добавить сделку AMO';
			echo '<span title="' . $title . '" class="actionEditAmoCrmId ' . $activeClass_sdelka_amo . '"><i class="fa fa-link"></i></span>' . PHP_EOL;

        if($order_access_edit_dostup == 2) {
         //отменить начисление %
         		$activeClass = $obj['CancelPercentage'] ? 'active' : '';
				$class = $obj['CancelPercentage'] ? 'fas fa-percent' : 'fas fa-percent';

				$onLabel = 'Отменить начисление % (в каменты записать причину)';
				$offLabel = 'Позволить начислить %';

				$title = $obj['CancelPercentage'] ? $offLabel : $onLabel;
				echo '<span title="'.$title.'" class="actionCancelPercentage ' . $activeClass . '" data-on-label="' . $onLabel . '" data-off-label="' . $offLabel . '"><i class="fa ' . $class . '"></i></span>' . PHP_EOL;



			// Принт менеджер
		 //	по умолчанию разрешаем управлять принт менеджером тем, у кого есть доступ к редактированию всех заказов

			 /*	$activeClass = $obj['print_manager'] ? 'active' : '';
				$class = $obj['print_manager'] ? 'fa-user' : 'fa-user-o';

				$onLabel = 'Включить PrintManager';
				$offLabel = 'Выключить PrintManager';

				$title = $obj['print_manager'] ? $offLabel : $onLabel;
				echo '<span title="'.$title.'" class="actionPrintManager ' . $activeClass . '" data-on-label="' . $onLabel . '" data-off-label="' . $offLabel . '"><i class="fa ' . $class . '"></i></span>' . PHP_EOL;
         */

         	}

			// Производство
			$appsCount = count($obj['apps']);
			$addLink = '/acc/applications/edit.php?app_type='.$obj['typ_ord'].'&zakaz_id='.$obj['uid'];
			if ($appsCount  == 0) {

            if($obj['prdm_opl'] > 0 and ($obj['typ_ord'] == '1' or $obj['typ_ord'] == '3')){$class = 'noapp needapp';}else{$class = 'noapp';}
				$addTitle = 'Создать заявку на производство';
				$viewLink = '#';
				$viewTitle = '';
			} elseif ($appsCount == 1) {
				$class = 'oneapp';
				$addTitle = 'Добавить еще одну заявку на производство';
				$viewLink = '/acc/applications/edit.php?zakaz_id='.$obj['uid'].'&uid=' . $obj['apps'][0];
				$viewTitle = 'Просмотреть заявку на производство';
			} elseif ($appsCount > 1) {
				$class = 'moreapps';
				$addTitle = 'Добавить еще одну заявку на производство';
				$viewLink = '/acc/applications/?search=everywhere&zakaz_id='. $obj['uid'];
				$viewTitle = 'Просмотреть заявки на производство';
			}

			echo '<div class="actionApp ' . $class . '"><span><i class="fa fa-cogs"></i> ' . $appsCount . '</span><a href="' . $viewLink . '" class="view"  target="_blank" title="' . $viewTitle . '"><i class="fa fa fa-eye"></i></a><a href="' . $addLink . '"  target="_blank" class="add" title="' . $addTitle . '"><i class="fa fa fa-plus"></i></a></div>' . PHP_EOL;

			

			// Доставка
			if (intval($obj['courier_task_id'])) {
				$link = '/acc/logistic/courier_tasks.php?id=' . $obj['courier_task_id'];
				$title = 'Просмотреть заявку на курьера';
				$class = 'active';
			} else {
				$link = '/acc/logistic/courier_tasks.php?query_id=' . $obj['uid'];
				$title = 'Создать заявку на доставку';
				$class = '';
			}
			echo '<a href="' . $link . '" title="'  . $title . '" class="actionDelivery ' . $class . '"><i class="fa fa-truck"></i></a>' . PHP_EOL;





        //обестачиваем кнопку если тип заказа не определен или это заказная позиция
        if($obj['typ_ord'] == '1' or $obj['typ_ord'] == '0'){echo "<span class='actionShippedDisabledType'><i class='fa fa-lock'></i></span>" . PHP_EOL;}

        else{

        $class_name = 'actionShipped';

        if($obj['shipped'] == '1'){
         $class_extra = 'actionShippedGreen';

         if($obj['time_shipped'] == '0000-00-00 00:00:00'){$time_shipped = 'НЕ УКАЗАНО';}
         else{$time_shipped = date("d.m.Y H:i", strtotime($obj['time_shipped']));}

         $message = 'title="Отгружено ' . $time_shipped . ' (ставится складом в момент проведения накладной в 1С)"';

        }
        else
        {


        $booking_till = date("d.m.Y", strtotime($obj['booking_till']));
        $tek_time = date("d.m.Y");


        if(strtotime($tek_time) > strtotime($booking_till)){$class_extra = "actionShippedRed";}else{$class_extra = "";}

         if($obj['booking_till'] !== '0000-00-00'){
         $message = 'title="Бронь до ' . $booking_till . ' включительно  (ставится складом в момент проведения накладной в 1С)"';
         }

        }


          //если есть доступ к отмечанию отгрузок то кнопка активная, если нет, то нет
        if($shipped_edit == '1'){
         //отметить отгрузку складом
        echo "<span $message class='actionShipped $class_extra'><i class='fa fa-lock'></i></span>" . PHP_EOL;
        }else{
        echo "<span $message class='actionShippedDisabled $class_extra'><i class='fa fa-lock'></i></span>" . PHP_EOL;
        }

        }



			// Комментарий к заказу
		  	$activeClass = $obj['note'] ? 'active' : '';
			$class = $obj['note'] ? 'fa-comment' : 'fa-comment';
			$title = $obj['note'] ? $obj['note'] : 'Добавить комментарий';
			echo '<span title="'.$title.'" class="actionComment ' . $activeClass . '"><i class="fa ' . $class . '"></i></span>' . PHP_EOL;

			// Удаление\Пометка на удаление
			$show = true;

            $restore_button = '<span title="Восстановить" class="actionDelete restore"><i class="fa fa-undo"></i></span>';
            $delete_button = '<span title="Удалить" class="actionDelete"><i class="fa fa-trash"></i><i class="fa fa-cog fa-spin"></i></span>';

        if($order_access_edit_dostup == 2) {

          if (!$obj['deleted']) {
            $restore_button = '';
            }
         if ($obj['deleted'] == 1) {
            $delete_button = '';
            }
         } else {

         if ($obj['deleted'] == 1) {
            $delete_button = '';
            $restore_button = '';
            }
         else if ($obj['deleted'] == 2) {
            $delete_button = '';
                }
         else {
            $restore_button = '';
            }

            if($userId <> $query_user_id){$delete_button = ''; $restore_button = '';}

         }

         echo $delete_button;    echo $restore_button;

           }



		public static function printDebt($obj) {
			$summ = floatval($obj['prdm_sum_acc']);
			$paid = floatval($obj['prdm_opl']);

			$debt = round($summ - $paid, 2);

			// echo '<span title="Долг">' . number_format($debt, 2, '.', '') . '</span>';
			echo '<span title="Долг">' . $debt . '</span>';
		}

        public static function printPdfFiles($uid)
        {
			$pdf_icon='<i class="fa-duotone fa-file-pdf fa-lg" style="--fa-primary-color: #fafafa; --fa-primary-opacity: 1; --fa-secondary-color: #de1212; --fa-secondary-opacity: 0.8;"></i>';
			$word_icon='<i class="fa-duotone fa-file-word fa-lg" style="--fa-primary-color: #fafafa; --fa-secondary-color: #005eff; --fa-secondary-opacity: 1;"></i>';
			$invoice_btn="<span title='PDF скачать или отправить' class='open_popup pdf_popup' id='pdf_{$uid}'>{$pdf_icon}</span>" . PHP_EOL;
            // Скачать счет в PDF
            $invoice = '<a href=/acc/backend/invoice_pdf.php?qid=' . $uid . ' title="Скачать счет в PDF">{$pdf_icon}</a>' . PHP_EOL;
            //echo $invoice;
			echo $invoice_btn;
            // Скачать накладную в PDF
            //$waybill = '<a href=/acc/backend/waybill_pdf.php?qid=' . $uid . ' title="Скачать накладную в PDF"><i class="fa fa-file-excel-o"></i></a>' . PHP_EOL;
            $waybill_btn="<span title='Скачать накладную в PDF' class='open_popup pdf1_popup' id='pdf1_{$uid}'>{$pdf_icon}</span>" . PHP_EOL;
			echo $waybill_btn;
						// Скачать договора  в word
           // $waybill = '<a href=/acc/files/load_word.php?qid=' . $uid . ' title="Скачать договор в Word"><i class="fa fa-file-word-o"></i></a>' . PHP_EOL;
            $waybill_btn="<span title='Скачать договор в Word' class='open_popup word_popup' id='word_{$uid}'>{$word_icon}</span>" . PHP_EOL;
			
			echo $waybill_btn;
        }
		public static function printCookieAccess(){
			return $_COOKIE['statistics_access'];
		}

	}
