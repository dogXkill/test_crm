<?
//header('Content-Type: text/html; charset=utf-8');
require_once("../includes/db.inc.php");
//require_once("../includes/auth.php");
require_once("../includes/lib.php");
ini_set('max_execution_time', 300);

//записываем дату последней синхронизации в табличцу
function update_date ($type, $affected) {
	$now_date = date("Y-m-d H:i:s");
	$update_synch = mysql_query("UPDATE plan_synch SET date='$now_date' WHERE type='$type'");
	echo mysql_error();
	//echo "<img src=\"../../i/button_ok.png\" valign=absmiddle> синхронизировано: <strong>".$affected."</strong> записей. ".$now_date." ".$art_ids;
	
	/*if ($_GET["iframe"] == "1") {
		?>
		<br><img src="../../i/button_ok.png" valign=absmiddle> Заявки и продажи на производство синхрозизированы сайтом.
		<iframe src="/acc/plan/apps_synch.php" frameborder="0" style="width: 1px; height:1px;"></iframe>
		<?
	}*/
}

//$type = $_GET["type"];
$affected = 0;
//анализируем данные за последние 3года
$ago = date("Y-m-d h:i:s",strtotime("-2 year"));

$now_date = date("Y-m-d H:i:s");

//для упрощения и ускорения последующей обработки данных, приводим месяц и год продажи к упрощенному формату
//if ($type == "date_format") {
if (1) {
	$select = mysql_query("SELECT uid FROM queries WHERE date_query > '$ago'");

	while ($r =  mysql_fetch_array($select)) {
		$uid = $r["uid"];
		$format_date = mysql_query("UPDATE queries SET date_query_formatted = date_format(date_query, '%m.%Y') WHERE uid = '$uid'");

		if (mysql_affected_rows()) {
			$affected = $affected+1;
		}
	}

	//update_date($type, $affected);
	update_date("date_format", $affected);
}

//достаем все текущие товары из базы интернет магазина, новые добавляем, при совпадении обновляем
###if ($type == "web_goods") {
if (2) {

	###$update_check = $_POST["update_check"];
	###$actus = $_GET["actus"];
	
	//при запросе со страниц, проверяем, не делался ли синхрон час назад $actus проверяем если  задание на синхрон идет из планировщика
	//if ($update_check !== "1" && $actus !== "1") {
	if (3) {
		/* $update_check = mysql_query("SELECT date FROM plan_synch WHERE type='web_goods'");
		$update_date =  mysql_fetch_array($update_check);
		$update_date =  $update_date[0];
		$before = date("Y-m-d H:i:s",strtotime("-2 hour")); */
		
		###if ($before > $update_date) {
		###	/*echo "час назад ".$before." <br>обновлялось: ".$update_date;
		###	echo "<br>последний синхрон сделан больше часа назад";*/
		###	$actus = "1";
		###} else {
		###	/*echo "час назад ".$before." <br>обновлялось: ".$update_date;
		###	echo "<br>последний синхрон сделан меньше часа назад";*/
		###	$actus = "0";
		###}
	}

	###if ($actus == "1") {
	if (4) {
		//синхронизация с сайтом

		$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=goods");
		$ar = json_decode($ar, true);

		foreach ($ar as $a) {
			$fields = array(
				'uid' => $a["uid"],
				'art_id' => $a["art_id"],
				'title' => iconv('utf-8', 'cp1251', $a["title"]),
				'izd_type' => $a["izd_type"],
				'izd_w' => $a["izd_w"],
				'izd_v' => $a["izd_v"],
				'izd_b' => $a["izd_b"],
				'izd_color' => $a["izd_color"],
				'izd_material' => $a["izd_material"],
				'manufacturer' => $a["manufacturer"],
				'izd_lami' => $a["izd_lami"],
				'izd_ruchki' => $a["izd_ruchki"],
				'price' => $a["price"],
				'price_our' => $a["price_our"],
				'r_price_our' => $a["r_price_our"],
				'retail' => $a["retail"],
				'retail_price' => $a["retail_price"],
				'col_in_pack' => $a["col_in_pack"],
				'sklad' => $a["sklad"],
				'onn' => $a["onn"],
				'vip' => $a["vip"],
			);
			
			if ($uid !== "0") {
				$values = array();
				$updates = array();
				
				foreach ($fields as $key => $value) {
					$values[] = "'" . mysql_real_escape_string($value) . "'";
					$updates[] = $key . "='" . mysql_real_escape_string($value) . "'";
				}
				
				$sql = "INSERT INTO plan_arts (" . implode(', ', array_keys($fields)) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updates);
				
				$update = mysql_query($sql);
			}

			if (mysql_affected_rows()) {
				$affected = $affected+1;
			}
		}

		update_date("web_goods", $affected);
	}
}

###if ($type == "types") {
if (5) {
	//синхрон типов изделий
	$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=types", "r");
	$ar = json_decode($ar, true);
	
	foreach ($ar as $a) {
		$tid = $a["tid"];
		$type = $a["type"];
		$type = iconv('utf-8', 'cp1251', $type);
		$update = mysql_query("INSERT INTO types (tid,type) VALUES('$tid','$type') ON DUPLICATE KEY UPDATE tid='$tid',type='$type'");

		if (mysql_affected_rows()) {
			$affected = $affected + 1;
		}
	}
	
	$ar = "";

	//синхрон материалы
	$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=materials", "r");
	$ar = json_decode($ar, true);
	//print_r($ar);
	foreach ($ar as $a) {
		$tid = $a["tid"];
		$type = $a["type"];
		$type = iconv('utf-8', 'cp1251', $type);
		$update = mysql_query("INSERT INTO materials (tid,type) VALUES('$tid','$type') ON DUPLICATE KEY UPDATE tid='$tid',type='$type'");
		if (mysql_affected_rows()) {
			$affected = $affected+1;
		}
	}

	//синхрон производители
	$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=manufacturer", "r");
	$ar = json_decode($ar, true);
	//print_r($ar);
	foreach ($ar as $a) {
		$tid = $a["tid"];
		$type = $a["type"];
		$type = iconv('utf-8', 'cp1251', $type);
		$update = mysql_query("INSERT INTO manufacturer (tid,type) VALUES('$tid','$type') ON DUPLICATE KEY UPDATE tid='$tid',type='$type'");

		if (mysql_affected_rows()) {
			$affected = $affected + 1;
		}
	}

	//синхрон цветов изделий
	$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=colours", "r");
	$ar = json_decode($ar, true);
	//$cnt = 1;
	foreach ($ar as $a) {
		$cid = $a['cid'];
		$colour = $ar[$cid]['colour'];
		$colour = iconv('utf-8', 'cp1251', $colour);
		$html_colour = $ar[$cid]['html_colour'];
		$seq = $ar[$cid]['seq'];
		$update = mysql_query("INSERT INTO colours (cid,colour,html_colour,seq) VALUES('$cid','$colour','$html_colour','$seq') ON DUPLICATE KEY UPDATE cid='$cid',colour='$colour',html_colour='$html_colour',seq='$seq'");
		//$update_text = "INSERT INTO colours (cid,colour,html_colour,seq) VALUES('$cid','$colour','$html_colour','$seq') ON DUPLICATE KEY UPDATE colour='$colour',html_colour='$html_colour',seq='$seq'";
		if (mysql_affected_rows()) {
			$affected = $affected + 1;
		}
	}

	update_date("types", $affected);
}

###echo $update_text;
###echo $cid;
//print_r($ar[72][html_colour]);

//считаем проданные артикула, вычисляем количество месяцев, в которые они продавались, вносим данные в таблицу
###if ($type == "intra_sales") {
if (6) {

	//$ago = '2015-11-17 16:05:23';
	$select = mysql_query("SELECT a.art_num AS art_num, SUM(a.num) AS sold FROM obj_accounts AS a, queries AS b  WHERE b.date_query > '$ago' AND a.query_id = b.uid AND a.art_num <> '0' AND a.art_num <> 'd' AND a.art_num <> 'n' AND a.art_num <> ''  AND a.art_num <> 'н' GROUP BY a.art_num");
	$arts = '';
	while ($r =  mysql_fetch_array($select)) {
		$sold = $r["sold"];
		$art_num = $r["art_num"];

		//if($art_num!=="" and $sold > 0){
		//получаем только количество месяцев, в которых была продажа каждого артикула и только эти месяца участвуют в статистическом исследовании
		$months_distinct = mysql_query("SELECT COUNT(DISTINCT (b.date_query_formatted)) FROM obj_accounts AS a, queries AS b WHERE a.query_id = b.uid AND a.art_num = '$art_num' AND b.date_query > '$ago'");
		$months_distinct = mysql_fetch_array($months_distinct);
		$months_of_sales = $months_distinct[0];
		//}
		//else{$months=""; $sold="";}

		if ($months_of_sales > 0 and $sold > 0) {
			//вносим данные в таблицу, пишем сколько продано и месяцы продажи
			$update_sold = mysql_query("UPDATE plan_arts SET sold='$sold', months_of_sales='$months_of_sales' WHERE art_id = '$art_num'");

			if (mysql_affected_rows()) {
				$affected = $affected + 1;
				$arts .= $art_num . ", ";
			}
		}
	}
	
	update_date("intra_sales", $affected);
}


//собираем данные из таблицы с заявками на производство и учетом работы. Анализируем только те заявки, которые не старше одного года. Смотрим сколько упаковано.
//если хотя бы упаковано на 20% меньше планируемого тиража, значит мы еще ожидаем эти пакеты и вносим их в план артс накопительным образом
###if ($type == "app_stat") {
if (7) {
	//чтобы избежать повторногодобавления, предварительно очищаем столбец
	mysql_query("UPDATE plan_arts SET in_work = ''");

	//делаем выборку всех артикулов
	$select_arts = mysql_query("SELECT art_id FROM plan_arts WHERE 1");
	
	while ($r =  mysql_fetch_array($select_arts)) {
		$art_id = $r["art_id"];
		//ограничиваем заявки последним годом
		$dat_ord_ago = date("Y-m-d h:i:s",strtotime("-1 year"));
		//смотрим таблицу, если есть упакованные, то суммируем их и формируем массив, если нет упакованных, то это просто заявка которая еще не начата
		//в последствии, нужно будет обусловить добавление остатков наличием какого нибудь маркера в заявках, например "сырье куплено" и/или "заявка выполнена полностью"
		//также, необходимо создать заявки на закупку тех или иных артикулов, т.к. производится учет также внешних товаров
		$select = mysql_query("SELECT a.art_num AS art_num, a.tiraz AS tiraz, a.num_ord AS num_ord, SUM(b.num_of_work) AS upakovano, a.tiraz-SUM(b.num_of_work) AS ostatok FROM applications AS a, job AS b WHERE ((a.art_num = '$art_id' AND a.num_ord = b.num_ord AND b.job = '11') OR (a.art_num = '$art_id' AND b.uid = '0')) AND a.dat_ord > '$dat_ord_ago' GROUP BY a.num_ord");

		while ($r2 =  mysql_fetch_array($select)) {
			//тираж по заявке
			$tiraz = $r2["tiraz"];
			//сколько упаковано
			$upakovano = $r2["upakovano"];
			//остаток
			$ostatok = $r2["ostatok"];

			//если остаток больше хотя бы 20% от тиража, то мы предполагаем, что заказ еще не довыполнен и соответственно, мы добавляем этот остаток
			if ($ostatok  > $tiraz * 0.2) {
				$update = mysql_query("UPDATE plan_arts SET in_work = in_work + '$ostatok' WHERE art_id = '$art_id'");

				if (mysql_affected_rows()) {
					$affected = $affected + 1;
				}
			}
		}
	}
	
	update_date("app_stat", $affected);
}

//анализ аналогичных товаров из других групп. Берем каждый артикул, смотрим в каких он группах присутсвтует. Если находим другие товары в данной группе, то смотрим
//какая рентабельность у этих товаров и сколько их есть в наличии.
###if ($type == "group_analyse") {
if (8) {

	$select_goods = mysql_query("SELECT uid, grup FROM plan_arts WHERE vis = '1' AND grup <> '0'");
	
	while ($r =  mysql_fetch_array($select_goods)) {
		$current_uid = $r["uid"];
		$current_group = $r["grup"];
		//суммируем как товарный остаток, так и пакеты в производстве для всех товаров в данной группе, кроме текущего
		$get_sum_of_analogs = mysql_query("SELECT SUM(sklad) AS sklad, SUM(in_work) AS in_work FROM plan_arts WHERE grup = '$current_group' AND uid <> '$current_uid'");
		$s = mysql_fetch_array($get_sum_of_analogs);
		$group_sum = $s["sklad"] + $s["in_work"];
		$update = mysql_query("UPDATE plan_arts SET group_sum = '$group_sum' WHERE uid='$current_uid'");
		
		if (mysql_affected_rows()) {
			$affected = $affected + 1;
		}
	}
	
	update_date("group_analyse", $affected);
}

//считаем рентабельность выпуска тех или иных пакетов. Вычисляем среднюю месячную прибыль при условии их наличия. Вносим итог в БД
###if ($type == "calc_rent") {
if (9) {

	$select_rent = mysql_query("SELECT art_id, sold, months_of_sales, price, r_price_our FROM plan_arts WHERE sold > '0' OR months_of_sales > '0'");
	while ($r =  mysql_fetch_array($select_rent)) {
		$art_id = $r["art_id"];
		$sold = $r["sold"];
		$price = $r["price"];
		$r_price_our = $r["r_price_our"];
		$months_of_sales = $r["months_of_sales"];

		//echo $art_id." ".$sold

		//среднее количество проданных единиц в месяц
		$monthly_sales = $sold / $months_of_sales;
		//средняя маржа с единицы продукции
		$marja_unit = $price - $r_price_our;
		//средняя маржа в месяц
		$monthly_profit = $marja_unit * $monthly_sales;

		$update = mysql_query("UPDATE plan_arts SET monthly_sales = '$monthly_sales', marja_unit='$marja_unit', monthly_profit = '$monthly_profit' WHERE art_id='$art_id'");
		
		if (mysql_affected_rows()) {
			$affected = $affected + 1;
		}
	}
	
	update_date("calc_rent", $affected);
}

//print_r ($ar);

?>