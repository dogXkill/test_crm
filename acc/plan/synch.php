<?

$type = $_GET["type"];
//header('Content-Type: text/html; charset=utf-8');
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
require_once("app_stat.php");
require_once("booked.php");
require_once("web_goods.php");

ini_set('max_execution_time', 300);


$affected = 0;
//����������� ������ �� ��������� 3����
$ago = date("Y-m-d h:i:s",strtotime("-2 year"));

$now_date = date("Y-m-d H:i:s");



//��� ��������� � ��������� ����������� ��������� ������, �������� ����� � ��� ������� � ����������� �������
if ($type == "date_format") {
    $select = mysql_query("SELECT uid FROM queries WHERE date_query > '$ago'");

    while($r = mysql_fetch_array($select)) {
        $uid = $r["uid"];
        $format_date = mysql_query("UPDATE queries SET date_query_formatted = date_format(date_query, '%m.%Y') WHERE uid = '$uid'");

        if (mysql_affected_rows()) {
            $affected = $affected+1;
        }
    }
    update_date($type, $affected);
}




//������� ��� ������� ������ �� ���� �������� ��������, ����� ���������, ��� ���������� ���������
if ($type == "web_goods") {

       web_goods();
}






//������� ��������� ��������, ��������� ���������� �������, � ������� ��� �����������, ������ ������ � �������
if($type == "intra_sales"){

//$ago = '2015-11-17 16:05:23';
$select = mysql_query("SELECT a.art_num AS art_num, SUM(a.num) AS sold FROM obj_accounts AS a, queries AS b  WHERE b.date_query > '$ago' AND a.query_id = b.uid AND a.art_num <> '0' AND a.art_num <> 'd' AND a.art_num <> 'n' AND a.art_num <> ''  AND b.deleted = '0' AND a.art_num <> '�' GROUP BY a.art_num");
while($r =  mysql_fetch_array($select)){
$sold = $r["sold"];
$art_num = $r["art_num"];


//if($art_num!=="" and $sold > 0){
//�������� ������ ���������� �������, � ������� ���� ������� ������� �������� � ������ ��� ������ ��������� � �������������� ������������
$months_distinct = mysql_query("SELECT COUNT(DISTINCT (b.date_query_formatted)) FROM obj_accounts AS a, queries AS b WHERE a.query_id = b.uid AND a.art_num = '$art_num' AND b.date_query > '$ago'");
$months_distinct = mysql_fetch_array($months_distinct);
$months_of_sales = $months_distinct[0];
//}
//else{$months=""; $sold="";}

if($months_of_sales > 0 and $sold > 0){
//������ ������ � �������, ����� ������� ������� � ������ �������
$update_sold = mysql_query("UPDATE plan_arts SET sold='$sold', months_of_sales='$months_of_sales' WHERE art_id = '$art_num'");

if(mysql_affected_rows()){
$affected = $affected+1;
$arts .= $art_num.", ";
}
}
}
update_date($type, $affected);
}





//�������� ������ �� ������� � �������� �� ������������ � ������ ������. ����������� ������ �� ������, ������� �� ������ ������ ����. ������� ������� ���������.
//���� ���� �� ��������� �� 20% ������ ������������ ������, ������ �� ��� ������� ��� ������ � ������ �� � ���� ���� ������������� �������
if($type == "app_stat"){


app_stat();


 }



//��������� ���������� � �����
if($type == "booked"){

booked();

}



//������� �������������� ������� ��� ��� ���� �������. ��������� ������� �������� ������� ��� ������� �� �������. ������ ���� � ��

if($type == "calc_rent"){

$select_rent = mysql_query("SELECT art_id, sold, months_of_sales, price, r_price_our FROM plan_arts WHERE sold > '0' OR months_of_sales > '0'");
while($r =  mysql_fetch_array($select_rent)){
$art_id = $r["art_id"];
$sold = $r["sold"];
$price = $r["price"];
$r_price_our = $r["r_price_our"];
$months_of_sales = $r["months_of_sales"];

//echo $art_id." ".$sold

//������� ���������� ��������� ������ � �����
$monthly_sales = $sold/$months_of_sales;
//������� ����� � ������� ���������
$marja_unit = $price - $r_price_our;
//������� ����� � �����
$monthly_profit = $marja_unit * $monthly_sales;

$update = mysql_query("UPDATE plan_arts SET monthly_sales = '$monthly_sales', marja_unit='$marja_unit', monthly_profit = '$monthly_profit' WHERE art_id='$art_id'");
if(mysql_affected_rows()){
$affected = $affected+1;
}
}
update_date($type, $affected);
}

//print_r ($ar);


