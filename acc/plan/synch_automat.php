<?
//header('Content-Type: text/html; charset=utf-8');
require_once("../includes/db.inc.php");
//require_once("../includes/auth.php");
require_once("../includes/lib.php");
ini_set('max_execution_time', 300);

//���������� ���� ��������� ������������� � ��������
function update_date ($type, $affected) {
	$now_date = date("Y-m-d H:i:s");
	$update_synch = mysql_query("UPDATE plan_synch SET date='$now_date' WHERE type='$type'");
	echo mysql_error();
	//echo "<img src=\"../../i/button_ok.png\" valign=absmiddle> ����������������: <strong>".$affected."</strong> �������. ".$now_date." ".$art_ids;
	
	/*if ($_GET["iframe"] == "1") {
		?>
		<br><img src="../../i/button_ok.png" valign=absmiddle> ������ � ������� �� ������������ ���������������� ������.
		<iframe src="/acc/plan/apps_synch.php" frameborder="0" style="width: 1px; height:1px;"></iframe>
		<?
	}*/
}

//$type = $_GET["type"];
$affected = 0;
//����������� ������ �� ��������� 3����
$ago = date("Y-m-d h:i:s",strtotime("-2 year"));

$now_date = date("Y-m-d H:i:s");

//��� ��������� � ��������� ����������� ��������� ������, �������� ����� � ��� ������� � ����������� �������
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

//������� ��� ������� ������ �� ���� �������� ��������, ����� ���������, ��� ���������� ���������
###if ($type == "web_goods") {
if (2) {

	###$update_check = $_POST["update_check"];
	###$actus = $_GET["actus"];
	
	//��� ������� �� �������, ���������, �� ������� �� ������� ��� ����� $actus ��������� ����  ������� �� ������� ���� �� ������������
	//if ($update_check !== "1" && $actus !== "1") {
	if (3) {
		/* $update_check = mysql_query("SELECT date FROM plan_synch WHERE type='web_goods'");
		$update_date =  mysql_fetch_array($update_check);
		$update_date =  $update_date[0];
		$before = date("Y-m-d H:i:s",strtotime("-2 hour")); */
		
		###if ($before > $update_date) {
		###	/*echo "��� ����� ".$before." <br>�����������: ".$update_date;
		###	echo "<br>��������� ������� ������ ������ ���� �����";*/
		###	$actus = "1";
		###} else {
		###	/*echo "��� ����� ".$before." <br>�����������: ".$update_date;
		###	echo "<br>��������� ������� ������ ������ ���� �����";*/
		###	$actus = "0";
		###}
	}

	###if ($actus == "1") {
	if (4) {
		//������������� � ������

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
	//������� ����� �������
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

	//������� ���������
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

	//������� �������������
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

	//������� ������ �������
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

//������� ��������� ��������, ��������� ���������� �������, � ������� ��� �����������, ������ ������ � �������
###if ($type == "intra_sales") {
if (6) {

	//$ago = '2015-11-17 16:05:23';
	$select = mysql_query("SELECT a.art_num AS art_num, SUM(a.num) AS sold FROM obj_accounts AS a, queries AS b  WHERE b.date_query > '$ago' AND a.query_id = b.uid AND a.art_num <> '0' AND a.art_num <> 'd' AND a.art_num <> 'n' AND a.art_num <> ''  AND a.art_num <> '�' GROUP BY a.art_num");
	$arts = '';
	while ($r =  mysql_fetch_array($select)) {
		$sold = $r["sold"];
		$art_num = $r["art_num"];

		//if($art_num!=="" and $sold > 0){
		//�������� ������ ���������� �������, � ������� ���� ������� ������� �������� � ������ ��� ������ ��������� � �������������� ������������
		$months_distinct = mysql_query("SELECT COUNT(DISTINCT (b.date_query_formatted)) FROM obj_accounts AS a, queries AS b WHERE a.query_id = b.uid AND a.art_num = '$art_num' AND b.date_query > '$ago'");
		$months_distinct = mysql_fetch_array($months_distinct);
		$months_of_sales = $months_distinct[0];
		//}
		//else{$months=""; $sold="";}

		if ($months_of_sales > 0 and $sold > 0) {
			//������ ������ � �������, ����� ������� ������� � ������ �������
			$update_sold = mysql_query("UPDATE plan_arts SET sold='$sold', months_of_sales='$months_of_sales' WHERE art_id = '$art_num'");

			if (mysql_affected_rows()) {
				$affected = $affected + 1;
				$arts .= $art_num . ", ";
			}
		}
	}
	
	update_date("intra_sales", $affected);
}


//�������� ������ �� ������� � �������� �� ������������ � ������ ������. ����������� ������ �� ������, ������� �� ������ ������ ����. ������� ������� ���������.
//���� ���� �� ��������� �� 20% ������ ������������ ������, ������ �� ��� ������� ��� ������ � ������ �� � ���� ���� ������������� �������
###if ($type == "app_stat") {
if (7) {
	//����� �������� ��������������������, �������������� ������� �������
	mysql_query("UPDATE plan_arts SET in_work = ''");

	//������ ������� ���� ���������
	$select_arts = mysql_query("SELECT art_id FROM plan_arts WHERE 1");
	
	while ($r =  mysql_fetch_array($select_arts)) {
		$art_id = $r["art_id"];
		//������������ ������ ��������� �����
		$dat_ord_ago = date("Y-m-d h:i:s",strtotime("-1 year"));
		//������� �������, ���� ���� �����������, �� ��������� �� � ��������� ������, ���� ��� �����������, �� ��� ������ ������ ������� ��� �� ������
		//� �����������, ����� ����� ���������� ���������� �������� �������� ������ ������ ������� � �������, �������� "����� �������" �/��� "������ ��������� ���������"
		//�����, ���������� ������� ������ �� ������� ��� ��� ���� ���������, �.�. ������������ ���� ����� ������� �������
		$select = mysql_query("SELECT a.art_num AS art_num, a.tiraz AS tiraz, a.num_ord AS num_ord, SUM(b.num_of_work) AS upakovano, a.tiraz-SUM(b.num_of_work) AS ostatok FROM applications AS a, job AS b WHERE ((a.art_num = '$art_id' AND a.num_ord = b.num_ord AND b.job = '11') OR (a.art_num = '$art_id' AND b.uid = '0')) AND a.dat_ord > '$dat_ord_ago' GROUP BY a.num_ord");

		while ($r2 =  mysql_fetch_array($select)) {
			//����� �� ������
			$tiraz = $r2["tiraz"];
			//������� ���������
			$upakovano = $r2["upakovano"];
			//�������
			$ostatok = $r2["ostatok"];

			//���� ������� ������ ���� �� 20% �� ������, �� �� ������������, ��� ����� ��� �� ���������� � ��������������, �� ��������� ���� �������
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

//������ ����������� ������� �� ������ �����. ����� ������ �������, ������� � ����� �� ������� ������������. ���� ������� ������ ������ � ������ ������, �� �������
//����� �������������� � ���� ������� � ������� �� ���� � �������.
###if ($type == "group_analyse") {
if (8) {

	$select_goods = mysql_query("SELECT uid, grup FROM plan_arts WHERE vis = '1' AND grup <> '0'");
	
	while ($r =  mysql_fetch_array($select_goods)) {
		$current_uid = $r["uid"];
		$current_group = $r["grup"];
		//��������� ��� �������� �������, ��� � ������ � ������������ ��� ���� ������� � ������ ������, ����� ��������
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

//������� �������������� ������� ��� ��� ���� �������. ��������� ������� �������� ������� ��� ������� �� �������. ������ ���� � ��
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

		//������� ���������� ��������� ������ � �����
		$monthly_sales = $sold / $months_of_sales;
		//������� ����� � ������� ���������
		$marja_unit = $price - $r_price_our;
		//������� ����� � �����
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