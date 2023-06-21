<?php

class FixController extends AbstractController {
	
	protected function phoneNumberAction() {
		header('Content-Type: text/html; charset=utf-8');
		
		$this->template->initTemplate();
		$this->template->enqueueScript('jquery');
		$this->template->enqueueScript('jquery-ui');
		$this->template->enqueueScript('fixScript', $this->template->getTemplateUrl() . '/js/fix.js', true);
		// $this->template->enqueueScript('queriesFilters', $this->template->getTemplateUrl() . '/js/queriesFilters.js', true);
		$this->template->enqueueStyle('jquery-ui');
		$this->template->enqueueStyle('font-awesome');
		// $this->template->enqueueStyle('styles', $this->template->getTemplateUrl() . '/css/styles.css');
		$this->template->enqueueStyle('fix-styles', $this->template->getTemplateUrl() . '/css/fix.css');
		
		$db = Database::getInstance();
		
		$count = $db->getVar("SELECT COUNT(uid) FROM clients WHERE temp_phone IS NULL");

		$pageParams = array(
			'user' => $this->user,
			'count' => $count
		);
		
		$layoutParams = array(
			'user' => $this->user,
			'old_name_curr_page' => ''
		);
		
		$this->template->printPage('fix/phoneNumber', $pageParams, 'main', $layoutParams);
	}
	
	protected function formatPhoneNumbersAction() {
		header('Content-Type: text/plain; charset=utf-8');
		
		$this->db = Database::getInstance();
		
		$this->preformatPhones();
		
		$i = 90000;
		$count = array(
			'bad' => 0,
			'one' => 0,
			'two' => 0
		);
		
		do {
			$clients = $this->db->getRows("SELECT * FROM clients WHERE uid < " . $i . " AND temp_phone IS NULL ORDER BY uid DESC LIMIT 100");
			if ($clients) {
				foreach ($clients as $client) {
					$tel = trim($client['cont_tel']);
					$tel = trim($tel, '+,?:.');
					$tel = str_replace('–', '-', $tel);
					$tel = str_replace(array('[', ']'), array('(', ')'), $tel);

					$res = array();
					
					// echo $client['firm_tel'] . ' | ' . $tel . PHP_EOL;
					
					// Возможно несколько телефонов
					if (mb_strpos($tel, ',') !== false) {
						
						// echo $tel . PHP_EOL;
						$tels = explode(',', $tel);
						// var_dump($tels);
						if (count($tels) == 2) {
							$tels[0] = trim($tels[0]);
							$tels[1] = trim($tels[1]);
							
							$tel1 = $this->parse($tels[0]);
							$tel2 = $this->parse($tels[1]);
							
							// echo $client['cont_tel'] . ' - ' . $client['uid'] . ' - ' . $client['legal_address'] . ' - ' . $client['postal_address'] .  PHP_EOL;
							// var_dump([$tel, [$tels[0], $tel1], [$tels[1], $tel2]]);
							// var_dump([$tel, $tel1, $tel2]);
							
							if ($tel1 && $tel2) {
								$res = array($tel1, $tel2);
							} else {
								// echo implode(' | ', $tels) . PHP_EOL;
							}
						}
					} else {
						$oldTel = $tel;
						$tel = $this->parse($tel);
						
						if ($tel != false) {
							$res[] = $tel;
						} else {
							echo $client['cont_tel'] . PHP_EOL;
							// echo $oldTel . PHP_EOL;
						}
					}
					
					// Если нихрена не нашли
					if (count($res) == 0) {
						$count['bad']++;
						echo $client['cont_tel'] .  PHP_EOL;
						// echo $client['cont_tel'] . ' - ' . $client['uid'] . ' - ' . $client['legal_address'] . ' - ' . $client['postal_address'] .  PHP_EOL;
					}

					// Если нашли один номер
					if (count($res) == 1) {
						$count['one']++;
						
						#$this->update($client, json_encode($res));
					}
					
					// Если нашли 2 номера
					if (count($res) == 2) {
						$count['two']++;
						
						#$this->update($client, json_encode($res));
					}
				
				
				
					// $this->update($client, $tel);
					
					
					$i = $client['uid'];
				}
			}
		} while ($clients != false);
		
		foreach ($count as $key => $val) {
			echo $key . ': ' . $val . PHP_EOL;
		}
	}
	
	private function preformatPhones() {
		$this->preUpdate('8 /4842/ 705-007', '8-4842-705-007');
		$this->preUpdate('8.903.744-03-36', '8-903-744-03-36');
		$this->preUpdate('8 (968) 94?335?94', '8 (968) 94-335-94');
		$this->preUpdate('+7 916 199 67 60  +7 499 394 49 24', '7 916 199 67 60,  7 499 394 49 24'); // 8 шт
		$this->preUpdate('+7 925 844 32 75 +7 926 658 80 40', '+7 925 844 32 75, +7 926 658 80 40'); // 1 шт
		$this->preUpdate('8(499)677-5857 +7 (977)554-32-37', '8(499)677-5857, 7 (977)554-32-37'); // 4 шт
		$this->preUpdate('7 499 681-1512 | М. +7 985 760-4724', '7 499 681-1512, 7 985 760-4724'); // 3 шт
		$this->preUpdate('+7 (926) 053-60-63 +7 (495) 777-15-15', '7 (926) 053-60-63, 7 (495) 777-15-15');
		$this->preUpdate('+7 (495) 984 28 28 +7 (916) 712 86 54', '+7 (495) 984 28 28, +7 (916) 712 86 54');
		$this->preUpdate('7 925 076 09 35 +79263417721', '7 925 076 09 35, +79263417721');
		$this->preUpdate('+7(495) 642-32-17 +7(977) 899-877-0', '+7(495) 642-32-17, +7(977) 899-877-0');
		$this->preUpdate('7 999 801 27 25 +7(499) 271 67 68', '7 999 801 27 25, +7(499) 271 67 68');
		$this->preUpdate('7-965-444-33-44 Моб. 8-925-801-16-36', '7-965-444-33-44, 8-925-801-16-36');
		$this->preUpdate('Тел.: +7 (495) 150-06-91 Моб.: +7- 905- 558-08-31', '7 (495) 150-06-91, 7-905-558-08-31');
		$this->preUpdate('8 977 804 12 64  8 495 580 97 77', '8 977 804 12 64, 8 495 580 97 77');
		$this->preUpdate('+7 (985) 352-29-45 +7 (495) 725-95-35', '+7 (985) 352-29-45, +7 (495) 725-95-35');
		$this->preUpdate(' +7 (495) 136 50 47 M +7 (968) 534-11-41', ' +7 (495) 136 50 47, +7 (968) 534-11-41');
		$this->preUpdate('7 926 152 71 78? 7 499 703 33 94', '7 926 152 71 78, 7 499 703 33 94');
		$this->preUpdate('8-495-744-10-24/8-495-675-02-02', '8-495-744-10-24, 8-495-675-02-02');
		$this->preUpdate('8-(495)-744-00-50 Моб.: 8-916-178-55-46', '8-(495)-744-00-50, 8-916-178-55-46');
		$this->preUpdate('Тел.: +7(499) 703-45-17 Moб.: +7(963) 663-55-82', '7(499) 703-45-17, 7(963) 663-55-82');
		$this->preUpdate('o  +7 495 966 2141  m +7 903 662 4112', '7 495 966 2141, 7 903 662 4112');
		$this->preUpdate('+7 499 322 47 44  +7 926 550 08 05', '+7 499 322 47 44,  +7 926 550 08 05');
		$this->preUpdate('8 (499) 579-91-96 8 (925) 04-34-173', '8 (499) 579-91-96, 8 (925) 04-34-173');
		$this->preUpdate('раб. +7 (495) 221 12 64 моб.+7 (917) 506 18 04', '7 (495) 221 12 64, 7 (917) 506 18 04');
		$this->preUpdate('+79038888000 ; +7 967 299-98-89', '+79038888000, +7 967 299-98-89');
		$this->preUpdate('тел.: +7.495.363.45.82  моб. +7.985.643.04.20', '+7-495-363-45-82, +7-985-643-04-20');
		$this->preUpdate('+79265312048  +79151714633', '+79265312048,  +79151714633');
		$this->preUpdate('Раб. +7 (495) 225-57-71 Моб. +7 (903) 775-24-55', '+7 (495) 225-57-71, +7 (903) 775-24-55');
		$this->preUpdate('7 916 140-16-68          916 140-16-85', '7 916 140-16-68, 7916 140-16-85');
		$this->preUpdate('8 929 666 34 51 / 8 495 645 69 80', '8 929 666 34 51, 8 495 645 69 80');
		$this->preUpdate('791652589939265690801', '79165258993, 79265690801');
		$this->preUpdate('8(4242) 24-46-85; +7 (924) 139-68-88', '8 4242 24-46-85, +7 (924) 139-68-88');
		$this->preUpdate('  моб: +7(962) 937-37-14 тел:  +7(495) 142-15-22', 'моб: +7(962) 937-37-14 тел:  +7(495) 142-15-22');
		$this->preUpdate('моб: +7(962) 937-37-14 тел:  +7(495) 142-15-22', '+7(962) 937-37-14,  +7(495) 142-15-22');
		$this->preUpdate('+7 495 620 58 99 | м.: +7 925 514 61 84', '+7 495 620 58 99, +7 925 514 61 84');
		$this->preUpdate('+7 495 505 70 72? 8-916-380-73-06', '+7 495 505 70 72, 8-916-380-73-06');
		$this->preUpdate('+ 7 (916) 329-60-05    + 7 (977) 264-72-94', '+ 7 (916) 329-60-05, + 7 (977) 264-72-94');
		$this->preUpdate('Тел.: +7 (495) 981-81-81 Моб.: 8(916)847-16-73', '+7 (495) 981-81-81, 8(916)847-16-73');
		$this->preUpdate('тел: 8(8332)38-41-50; 8(919)524-22-20', '8(8332)38-41-50, 8(919)524-22-20');
		$this->preUpdate('7 (861) 290 59 55 Моб.: +7 (928) 404 81 41', '7 (861) 290 59 55, +7 (928) 404 81 41');
		$this->preUpdate('+7 (926) 595 88 40 +7 (499) 238 95 11', '+7 (926) 595 88 40, +7 (499) 238 95 11');
		$this->preUpdate('(495) 666-00-96/(495) 666-00-95', '7(495) 666-00-96, 7(495) 666-00-95');
		$this->preUpdate('+7 (495) 958-4293 Mob. +7 (916) 090-9399', '+7 (495) 958-4293, +7 (916) 090-9399');
		
	}
	
	private function preUpdate($old, $new) {
		$query = "UPDATE clients SET cont_tel = '" . $this->db->esc($new) . "' WHERE cont_tel = '" . $this->db->esc($old) . "'";
		$res = $this->db->query($query);
		echo $query . PHP_EOL . $this->db->affectedRows() . PHP_EOL . PHP_EOL;
	}
	
	private function update($client, $new_val) {
		$query = "UPDATE clients SET temp_phone = '" . $this->db->esc($new_val) . "' WHERE uid = " . $client['uid'];
		$res = $this->db->query($query);
	}
	
	private function parse($originalTel, $recurse = false) {

		
		if (mb_strlen($originalTel) == 11 && !preg_match('/[^\d]/', $originalTel)) {
			if (in_array(substr(trim($originalTel), 0, 1), array(8, 7))) {
				return trim($originalTel);
			}
		}
		
		
		
		
		$tel = $originalTel;
		$tel = str_replace(array(' ', '+', '(', ')', '-'), '', $tel);
		if (mb_strlen($tel) == 11 && !preg_match('/[^\d]/', $tel)) {
			if (in_array(substr($tel, 0, 1), array(8, 7))) {
				return $tel;
			}
		}
		
		
		$tel = $originalTel;
		$tel = str_replace(array(' ', '-'), '', $tel);
		if (mb_strlen($tel) == 12 && !preg_match('/[^\d\(\)]/', $tel) && preg_match('/^\(\d{3}\)/', $tel)) {
			$tel = '7' . str_replace(array('(', ')'), '', $tel);
			return $tel;
		}
		
		
		
		
		
		$tel = $originalTel;
		$tel = str_replace(array(' ', '-'), '', $tel);
		if (mb_strlen($tel) == 10) {
			$regexp = '/^(900|901|902|903|904|905|906|908|909|910|911|912|913|914|915|916|917|918|919|920|921|922|923|924|925|926|927|928|929|930|931|932|933|934|936|937|938|939|941|950|951|952|953|954|955|956|958|960|961|962|963|964|965|966|967|968|969|970|971|977|978|980|981|982|983|984|985|986|987|988|989|991|992|993|994|995|996|997|999)/';
			if (!preg_match('/^(495|496|499|811|424|845|472|487|861|843|815|347|346)/', $tel) && !preg_match($regexp, $tel)) {

			} else {
				$tel = '7' . $tel;
				return $tel;
			}
		}
		
		
		
		
		$tel = $originalTel;
		$tel = str_replace(array(' ', '-'), '', $tel);
		if (mb_strlen($tel) == 12 && preg_match('/^\((\d\d\d)\d\)/', $tel)) {
			$tel = '7' . str_replace(array('(', ')'), '', $tel);
			return $tel;
		}
		
		if (!$recurse) {
			$tel = $originalTel;
			$tel = preg_replace('/[^0-9]*$/', '', $tel);
			$tel = preg_replace('/^[^0-9]*/', '', $tel);
			$preTel = $tel;
			if (($tel = $this->parse($tel, true)) != false) {
				return $tel;
			} else {
				// echo $originalTel . ' |---| ' . $tel . PHP_EOL;
			}
		}
		
		// echo $client['cont_tel'] . ' - ' . $client['uid'] . ' - ' . $client['legal_address'] . ' - ' . $client['postal_address'] .  PHP_EOL;
		// echo $client['cont_tel'] . PHP_EOL;

		return false;
	}

	protected function showDoublesAction() {
		header('Content-Type: text/html; charset=utf-8');
		
		$this->template->initTemplate();
		$this->template->enqueueScript('jquery');
		$this->template->enqueueScript('jquery-ui');
		$this->template->enqueueScript('fixShowDoublesScript', $this->template->getTemplateUrl() . '/js/fixShowDoubles.js', true);
		// $this->template->enqueueScript('queriesFilters', $this->template->getTemplateUrl() . '/js/queriesFilters.js', true);
		$this->template->enqueueStyle('jquery-ui');
		$this->template->enqueueStyle('font-awesome');
		// $this->template->enqueueStyle('styles', $this->template->getTemplateUrl() . '/css/styles.css');
		$this->template->enqueueStyle('fixShowDoublesStyles', $this->template->getTemplateUrl() . '/css/fixShowDoubles.css');
		
		$db = Database::getInstance();
		
		$pageParams = array(
			'user' => $this->user
		);
		
		$layoutParams = array(
			'user' => $this->user,
			'old_name_curr_page' => ''
		);
		
		$this->template->printPage('fix/showDoubles', $pageParams, 'main', $layoutParams);
	}

	public function mergeClientsByInnAction() {
		header('Content-Type: text/plain; charset=utf-8');
		
		if (!isset($_GET['process'])) die('Нужен параметр');
		
		$excludeFieldsFromMerge = array('uid', 'user_id', 'sphere', 'sphere_other', 'celi', 'celi_other', 'potrebnost', 'kak_uznali', 'kak_uznali_other', 'del', /* 'temp_phone', */ 'orders', 'last_order');
		
		$this->db = Database::getInstance();
		
		// $this->deleteTestData();
		$this->updateData();

		// Исключение из мерджа inn
		$exclude = array('', '712150062245', '7701632441', '7702375772', '7706413620', '7707083893', 'ИНН 7707083893', '7710353606', '7717090804', '7717664618', '7719236470', '7727651047', '7727707469', '7728168971', '7744001433', '9909056286');
		
		
		$q = "SELECT COUNT(*) AS cnt, inn FROM clients WHERE inn NOT IN ('" . implode("', '", $exclude) . "') GROUP BY inn HAVING cnt > 1 ORDER BY `inn`";
		$rows = $this->db->getRows($q);
		
		foreach ($rows as $row) {
			$q = "SELECT c.*, (SELECT COUNT(uid) FROM queries q WHERE q.client_id = c.uid) AS orders, (SELECT date_query FROM queries q1 WHERE q1.client_id = c.uid ORDER BY date_query DESC LIMIT 1) AS last_order FROM clients c WHERE c.inn = '" . $this->db->esc($row['inn']) . "' ORDER BY last_order DESC";
			$doubles = $this->db->getRows($q);
			
			$masterProfile = $doubles[0];
			
			$mergedFields = array();
			$ids = array();
			
			echo '====================================================================================================' . PHP_EOL;
			echo 'Master profile: ' . $masterProfile['short'] . ' (INN: ' . $masterProfile['inn'] . ') (дублей: ' . $row['cnt'] . ')' . ' (заказов: ' . array_sum(array_column($doubles, 'orders')) . ')' . PHP_EOL;
			
			// ПРОВЕРИТЬ может какой-то дубль уже смерган и нужно из merged_fields данные перекинуть на новый мастер профиль
			
			echo '▼------------------------------------'.PHP_EOL;
			foreach ($doubles as $index => $double) {
				echo $double['short'] . PHP_EOL;
				if ($index == 0) continue;
				
				// Проходимся по всем полям и определяем, что нужно мержить
				foreach ($double as $field => $value) {
					if (in_array($field, $excludeFieldsFromMerge)) continue;
					
					if (!isset($mergedFields[$field])) {
						$values = array();
					} else {
						$values = $mergedFields[$field];
					}
					
					if (!in_array(trim($value), $values) && trim($value) != trim($masterProfile[$field]) && !empty(trim($value))) {
						$values[] = trim($value);
					}
					
					if ($values != false) {
						$mergedFields[$field] = $values;
					}
				}
				
				$ids[] = intval($double['uid']);
			}
			
			echo '▲------------------------------------'.PHP_EOL;
			
			// Удаляем профили-дубли
			$this->deleteDoubles($ids);
			
			// Перекидываем ордеры дублей на мастер профиль
			$this->updateQueriesClients($ids, intval($masterProfile['uid']));
			
			// Вставляем в merged_fields поля, которые не совпадают с мастер-профилем
			$this->insertMergedFields($mergedFields, intval($masterProfile['uid']));
			
			echo PHP_EOL;
		}
		
	}

	public function mergeClientsByTempPhoneAction() {
		header('Content-Type: text/plain; charset=utf-8');
		
		if (!isset($_GET['process'])) die('Нужен параметр');
		
		$excludeFieldsFromMerge = array('uid', 'user_id', 'sphere', 'sphere_other', 'celi', 'celi_other', 'potrebnost', 'kak_uznali', 'kak_uznali_other', 'del', /* 'temp_phone', */ 'orders', 'last_order');
		
		$this->db = Database::getInstance();
		
		// $this->deleteTestData();
		$this->updateData();
		
		// Исключение из мерджа
		$exclude = array('["74959250006","79647004573"]', '');
		// $exclude = array('');
		
		$q = "SELECT COUNT(*) AS cnt, temp_phone FROM clients WHERE temp_phone NOT IN ('" . implode("', '", $exclude) . "') GROUP BY temp_phone HAVING cnt > 1 AND temp_phone IS NOT NULL ORDER BY temp_phone";
		$rows = $this->db->getRows($q);
		// die($q);
		
		foreach ($rows as $row) {
			$q = "SELECT c.*, (SELECT COUNT(uid) FROM queries q WHERE q.client_id = c.uid) AS orders, (SELECT date_query FROM queries q1 WHERE q1.client_id = c.uid ORDER BY date_query DESC LIMIT 1) AS last_order FROM clients c WHERE c.temp_phone = '" . $this->db->esc($row['temp_phone']) . "' ORDER BY last_order DESC";
			$doubles = $this->db->getRows($q);
			
			$masterProfile = $doubles[0];
			
			$mergedFields = array();
			$ids = array();
			
			echo '====================================================================================================' . PHP_EOL;
			echo 'Master profile: ' . $masterProfile['temp_phone'] . ' (дублей: ' . $row['cnt'] . ')' . ' (заказов: ' . array_sum(array_column($doubles, 'orders')) . ')' . PHP_EOL;
			
			echo '▼------------------------------------'.PHP_EOL;
			foreach ($doubles as $index => $double) {
				echo $double['short'] . PHP_EOL;
				if ($index == 0) continue;
				
				// Проходимся по всем полям и определяем, что нужно мержить
				foreach ($double as $field => $value) {
					if (in_array($field, $excludeFieldsFromMerge)) continue;
					
					if (!isset($mergedFields[$field])) {
						$values = array();
					} else {
						$values = $mergedFields[$field];
					}
					
					if (!in_array(trim($value), $values) && trim($value) != trim($masterProfile[$field]) && !empty(trim($value))) {
						$values[] = trim($value);
					}
					
					if ($values != false) {
						$mergedFields[$field] = $values;
					}
				}
				
				$ids[] = intval($double['uid']);
			}
			
			echo '▲------------------------------------'.PHP_EOL;
			
			// Удаляем профили-дубли
			$this->deleteDoubles($ids);
			
			// Перекидываем ордеры дублей на мастер профиль
			$this->updateQueriesClients($ids, intval($masterProfile['uid']));
			
			// Перекидываем merged_fields дублей (если раньше смержились) на мастер профиль
			$mergedFields = $this->performMergedFields($ids, $mergedFields, intval($masterProfile['uid']));
			
			// Вставляем в merged_fields поля, которые не совпадают с мастер-профилем
			$this->insertMergedFields($mergedFields, intval($masterProfile['uid']));
			
			echo PHP_EOL;
		}
		
	}
	
	public function mergeClientsByEmailAction() {
		header('Content-Type: text/plain; charset=utf-8');
		
		if (!isset($_GET['process'])) die('Нужен параметр');
		
		$excludeFieldsFromMerge = array('uid', 'user_id', 'sphere', 'sphere_other', 'celi', 'celi_other', 'potrebnost', 'kak_uznali', 'kak_uznali_other', 'del', /* 'temp_phone', */ 'orders', 'last_order', 'dogovor_num');

		$this->db = Database::getInstance();

		// $this->deleteTestData();
	   //	$this->updateData();
		
	   //	$exclude = array('', 'mariakirvas@ya.ru', 'asmazhilo@prp.ru', 'production@lastorystore.com', 'print-lider@yandex.ru', '8629370@mail.ru', 'lyapinak2000@gmail.com', 'svetabr@allinpr.ru', 'zakaz@chuvstvarings.com', 'zakupki@kancprom.ru', 'tegin@tegin.com', 'thetresors@gmail.com', 'tamara.evstafieva@hotmail.com');

		$q = "SELECT COUNT(*) AS cnt, email FROM clients WHERE 1 GROUP BY email HAVING cnt > 1 AND email IS NOT NULL ORDER BY email";
		$rows = $this->db->getRows($q);
		  $this->db->esc($row['email'])
		foreach ($rows as $row) {
			$q = "SELECT c.*, (SELECT COUNT(uid) FROM queries q WHERE q.client_id = c.uid) AS orders, (SELECT date_query FROM queries q1 WHERE q1.client_id = c.uid ORDER BY date_query DESC LIMIT 1) AS last_order FROM clients c WHERE c.email = '" . $this->db->esc($row['email']) . "' ORDER BY last_order DESC";
			$doubles = $this->db->getRows($q);
			
			$masterProfile = $doubles[0];
			
			$mergedFields = array();
			$ids = array();

           // $email = $masterProfile['email'];

           // if($email == "fdsfdsfdsfdsfdfdssdfsdcc"){
			
			echo '====================================================================================================' . PHP_EOL;
			echo 'Master profile: ' . $masterProfile['email'] . ' (дублей: ' . $row['cnt'] . ')' . ' (заказов: ' . array_sum(array_column($doubles, 'orders')) . ')' . PHP_EOL;

			echo '▼------------------------------------'.PHP_EOL;
			foreach ($doubles as $index => $double) {
				echo $double['short'] . PHP_EOL;
				if ($index == 0) continue;
				
				// Проходимся по всем полям и определяем, что нужно мержить
				foreach ($double as $field => $value) {
					if (in_array($field, $excludeFieldsFromMerge)) continue;
					
					if (!isset($mergedFields[$field])) {
						$values = array();
					} else {
						$values = $mergedFields[$field];
					}
					
					if (!in_array(trim($value), $values) && trim($value) != trim($masterProfile[$field]) && !empty(trim($value))) {
						$values[] = trim($value);
					}
					
					if ($values != false) {
						$mergedFields[$field] = $values;
					}
				}
				
				$ids[] = intval($double['uid']);
			}
			
			echo '▲------------------------------------'.PHP_EOL;
			
			// Удаляем профили-дубли
			$this->deleteDoubles($ids);
			
			// Перекидываем ордеры дублей на мастер профиль
			$this->updateQueriesClients($ids, intval($masterProfile['uid']));
			
			// Перекидываем merged_fields дублей (если раньше смержились) на мастер профиль
			$mergedFields = $this->performMergedFields($ids, $mergedFields, intval($masterProfile['uid']));
			
			// Вставляем в merged_fields поля, которые не совпадают с мастер-профилем
			$this->insertMergedFields($mergedFields, intval($masterProfile['uid']));
           // }
          //  else {
           //     echo "not valid emails $email <br>";
           // }
			echo PHP_EOL;
		}
	}


    private function isValidEmail($email){
     $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";

     if (eregi($pattern, $email)){
        return true;
     }
     else {
        return false;
     }
    }

	private function deleteTestData() {
		// Сначала удалим тестовых пользователей
		// Всех с testtest@paketoff.ru и 1302@paketoff.ru
		$q = "DELETE FROM clients WHERE email IN ('testtest@paketoff.ru', '1302@paketoff.ru', '117@paketoff.ru', 'pavel@paketoff.ru')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		// Всех, где inn=1
		$q = "DELETE FROM clients WHERE inn IN ('1', '11111', '1111', '2222', '54543', '42342342')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		// Всех, где temp_phone = ["79032479926"]
		$q = "DELETE FROM clients WHERE temp_phone IN ('[\"79032479926\"]')";
		$this->db->query($q);
		echo $q . PHP_EOL;
	}
	
	private function updateData() {
		// Приводим к едиому виду inn
		$q = "UPDATE clients SET inn = '7814517150', kpp = '781401001' WHERE inn = 'ИНН 7814517150'";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET inn = '7715815318', kpp = '771501001', rs_acc = '4070281013806001' WHERE inn = 'ИНН 7715815318'";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET inn = '7701704946' WHERE inn = 'ИНН 7701704946'";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET inn = '7709878655' WHERE inn = '7709878655 Свидетельство сер. 77 № 012171838 выдано 30.05.2011г. МИФНС № 46 по г. Москве'";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET inn = '' WHERE inn = '-'";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		// Обновляем поле email
		$q = "UPDATE clients SET email = '' WHERE email IN ('нет', '1')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET email = 'o.kiseleva@teca.ru' WHERE email IN ('Панькова')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET email = 'irina.shchukina@drive-event.ru' WHERE email IN ('Ирина Щукина <irina.shchukina@drive-event.ru>')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET email = 'myname@doraemon.ru' WHERE email IN ('info@bonsay.ru')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET email = 'trz1992@gmail.com' WHERE email IN ('trz1992@mail.ru')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		$q = "UPDATE clients SET email = 'bobrovanastasija@gmail.com' WHERE email IN ('Анастасия Боброва <bobrovanastasija@gmail.com>')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		$q = "UPDATE clients SET email = 'nastya.sufab@mail.ru' WHERE email IN ('Анастасия Егорченкова <nastya.sufab@mail.ru>')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		$q = "UPDATE clients SET email = 'Alena.Busygina@RT.RU' WHERE email IN ('Бусыгина Алена Михайловна <Alena.Busygina@RT.RU>')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		$q = "UPDATE clients SET email = 'gu@riolprint.ru' WHERE email IN ('Наида Гаджиева [gu@riolprint.ru]')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		$q = "UPDATE clients SET email = 'olimporihiro@yandex.ru' WHERE email IN ('ООО\" Олимп плюс\" Маликов <olimporihiro@yandex.ru>')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		$q = "UPDATE clients SET email = 'ra6x9@yandex.ru' WHERE email IN ('РА 6х9 <ra6x9@yandex.ru>')";
		$this->db->query($q);
		echo $q . PHP_EOL;
		
		
	
	}
	
	private function deleteDoubles($ids) {
		$q = "DELETE FROM clients WHERE uid IN (" . implode(', ', $ids) . ")";
		$this->db->query($q);
		echo $q . PHP_EOL;
	}
	
	private function updateQueriesClients($ids, $masterProfileUid) {
		$q = "SELECT uid FROM queries WHERE client_id IN (" . implode(', ', $ids) . ")";
		$orders = $this->db->getCol($q);
		
		if ($orders != false) {
			$q = "UPDATE queries SET client_id = " . $masterProfileUid . " WHERE uid IN (" . implode(', ', $orders) . ")";
			$this->db->query($q);
			echo $q . PHP_EOL;
		}
	}
	
	private function performMergedFields($ids, $mergedFields, $masterProfileUid) {
		$q = "SELECT field, value FROM clients_merged_fields WHERE client_id IN (" . implode(', ', array_merge($ids, array($masterProfileUid))) . ")";
		$res = $this->db->getRows($q);
		
		$availableMergedFields = array();
		if ($res != false) {
			foreach ($res as $mf) {
				if (!isset($availableMergedFields[$mf['field']])) {
					$availableMergedFields[$mf['field']] = array();
				}
				
				$availableMergedFields[$mf['field']][] = $mf['value'];
			}
		}
		
		if ($availableMergedFields != false) {
			foreach ($mergedFields as $field => $values) {
				if (isset($availableMergedFields[$field])) {
					foreach ($values as $key => $value) {
						if (in_array($value, $availableMergedFields[$field])) {
							unset($mergedFields[$field][$key]);
						}
					}
					
					if ($mergedFields[$field] == false) {
						unset($mergedFields[$field]);
					} else {
						$mergedFields[$field] = array_values($mergedFields[$field]);
					}
				}
			}
			
			$q = "UPDATE clients_merged_fields SET client_id = " . $masterProfileUid . " WHERE client_id IN (" . implode(', ', $ids) . ")";
			$this->db->query($q);
			echo $q . PHP_EOL;
			echo '>>>>>>>>>';
		}
		
		return $mergedFields;
	}
	
	private function insertMergedFields($mergedFields, $masterProfileUid) {
		if ($mergedFields != false) {
			$q = "INSERT INTO clients_merged_fields VALUES" . PHP_EOL;
			foreach ($mergedFields as $key => $values) {
				foreach ($values as $value) {
					$q .= "(NULL, " . $masterProfileUid . ", '" . $key . "', '" . $this->db->esc($value) . "')," . PHP_EOL;
				}
			}
			
			$q = trim(trim($q), ',') . ';';
			
			$this->db->query($q);
			
			echo $q . PHP_EOL;
		};
	}
	
	private function arrayRecursiveDiff($aArray1, $aArray2) {
	  $aReturn = array();

	  foreach ($aArray1 as $mKey => $mValue) {
		if (array_key_exists($mKey, $aArray2)) {
		  if (is_array($mValue)) {
			$aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
			if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
		  } else {
			if ($mValue != $aArray2[$mKey]) {
			  $aReturn[$mKey] = $mValue;
			}
		  }
		} else {
		  $aReturn[$mKey] = $mValue;
		}
	  }
	  return $aReturn;
	}
	
	protected function makeCSVAction() {
		header('Content-Type: text/plain; charset=utf-8');
		
		$db = Database::getInstance();
		
		$menagers = array();
		$query_types = array(
			0 => 'Другой',
			1 => 'Под заказ',
			2 => 'Магазин',
			3 => 'Магазин с лого'
		);
		
		$q = "SELECT
					*,
					(SELECT sum(q.prdm_sum_acc) FROM queries q WHERE q.client_id = c.uid) AS sum_all,
					(SELECT COUNT(q.uid) FROM queries q WHERE q.client_id = c.uid) AS queries_count,
					(SELECT q.date_query FROM queries q WHERE q.client_id = c.uid ORDER BY q.date_query DESC LIMIT 1) AS last_order,
					(SELECT q.prdm_sum_acc FROM queries q WHERE q.client_id = c.uid ORDER BY q.date_query DESC LIMIT 1) AS sum_last,
					(SELECT q.user_id FROM queries q WHERE q.client_id = c.uid ORDER BY q.date_query DESC LIMIT 1) AS menager_last
				FROM clients c
				HAVING last_order > '" . date('Y-m-d H:i:s', strtotime('-7 years')) . "' ORDER BY last_order ASC";
				
		$clients = $db->getRows($q);
		
		echo '"Клиент";"Компания";"Контактное лицо";"Телефоны грязные";"Телефоны чистые";"Количество заказов";"Дата последнего заказа";"Год";"Месяц";"День";"Менеджер";"Сумма всех заказов";"Сумма последнего заказа";"Тип последнего заказа";"Содержимое последнего заказа";"Email";"Адрес"' . PHP_EOL;
		
		foreach ($clients as $client) {
			$row = array();

			$tels = array();
			if ($client['cont_tel'] != false) {
				$tels[] = $client['cont_tel'];
			}
			
			if ($client['firm_tel'] != false) {
				$tels[] = $client['firm_tel'];
			}
			
			$phones = json_decode($client['temp_phone']);
			
			$row['client_name'] = $client['short'];
			$row['client_company_name'] = $client['name'];
			$row['cont_pers'] = $client['cont_pers'];
			$row['phones_raw'] = implode(', ', $tels);
			$row['phones'] = $phones ? implode(', ', $phones) : '';
			$row['queries_count'] = $client['queries_count'];
			$row['last_query_date'] = date('d.m.Y', strtotime($client['last_order']));
			$row['last_query_year'] = date('Y', strtotime($client['last_order']));
			$row['last_query_month'] = date('m', strtotime($client['last_order']));
			$row['last_query_day'] = date('d', strtotime($client['last_order']));
			
			
			if (isset($menagers[$client['menager_last']])) {
				$row['last_query_menager'] = $menagers[$client['menager_last']];
			} else {
				$menagers[$client['menager_last']] = $row['last_query_menager'] = $db->getVar("SELECT surname FROM users WHERE uid = " . intval($client['menager_last']));
			}
			
			$row['all_queries_sum'] = str_replace('.', ',', $client['sum_all']);
			$row['last_query_sum'] = str_replace('.', ',', $client['sum_last']);
			
			$last_query_type = $db->getVar("SELECT typ_ord FROM queries WHERE client_id = " . $client['uid']. " ORDER BY date_query DESC LIMIT 1");
			$row['last_query_type'] = $query_types[$last_query_type];
			
			$query_id = $db->getVar("SELECT uid FROM queries WHERE client_id = " . $client['uid']. " ORDER BY date_query DESC LIMIT 1");
			$items = $db->getRows("SELECT * FROM obj_accounts WHERE query_id = " . $query_id . " ORDER BY nn");
			
			$itemsString = [];
			foreach ($items as $item) {
				$itemsString[] = $item['name'] . ' / ' . $item['num'] . ' шт. / ' . $item['price'] . 'руб.';
			}
			
			
			$row['last_query_items'] = mb_substr(implode("\n", $itemsString), 0, 250);
			$row['email'] = $client['email'];
			$row['address'] = $client['legal_address'];
			
			
			$row = array_map(function($elem) { return str_replace(PHP_EOL, "\n", addslashes($elem)); }, $row);
			
			
			// echo PHP_EOL;
			echo '"';
			echo implode('";"', $row);
			echo '"' . PHP_EOL;
		}

		
		
		
		
		// Кто заказывал за последние 7 лет.

		// Имя|Последний заказ|Телефон(ы)|Кол-во заказов|Дата последнего заказа|Менеджер который вел заказ|Сумма всех заказов|Сумма последнего заказа|Емайл|Адрес
	}
}

/*
Delete clients:
email:
nickolya.didenko@gmail.com (или inn = 42342342)
testtest@paketoff.ru
1302@paketoff.ru

inn:
1
---------------------------------
Exclude:
''
-
712150062245
7701632441
7702375772
7706413620
7707083893
7710353606
7717090804
7717664618
7719236470
7727651047
7727707469
7728168971
7744001433
9909056286



*/
