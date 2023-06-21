<?php


$month = array(
	'������',
	"�������",
	"�����",
	"������",
	"���",
	"����",
	"����",
	"�������",
	"��������",
	"�������",
	"������",
	"�������"
	);
$month_sel = array(
	'������',
	"�������",
	"����",
	"������",
	"���",
	"����",
	"����",
	"������",
	"��������",
	"�������",
	"������",
	"�������"
	);




function isint($v, $min = -2147483648, $max = 2147483647)
{
  return ((string) intval($v) === (string) $v and $v >= $min and $v <= $max);
}




function check_date($date)
{
  $res = false;

  /*if(preg_match("/^(2[0-9]{3})-([0-1][0-9])-([0-3][0-9])$/", $date, $matches))
  {
    $res = checkdate($matches[2], $matches[3], $matches[1]);
  }*/

  if(preg_match("/^([0-3][0-9])-([0-1][0-9])-(2[0-9]{3})$/", $date, $matches))
  {
    $res = checkdate($matches[2], $matches[1], $matches[3]);
  }

  return $res;
}

function format_date($date)
{
  $res = false;

  if(preg_match("/^([0-3][0-9])-([0-1][0-9])-(2[0-9]{3})$/", $date, $matches))
  {
    $res = $matches[3] . "-" . $matches[2] . "-" . $matches[1];
  }

  return $res;
}

function unformat_date($date)
{
  $res = false;

  if(preg_match("/^(2[0-9]{3})-([0-1][0-9])-([0-3][0-9])$/", $date, $matches))
  {
    $res = $matches[3] . "-" . $matches[2] . "-" . $matches[1];
  }

  return $res;
}



//���������� ���� ��������� ������������� �� synch.php � app_stat.php � �������
function update_date($type, $affected) {
	$now_date = date("Y-m-d H:i:s");
	$update_synch = mysql_query("UPDATE plan_synch SET date='$now_date' WHERE type='$type'");
	$now_date_for_print = date("d.m.Y H:i");
	echo "<img src=\"../../i/button_ok.png\" valign=absmiddle> ���������: <strong>".$affected."</strong> �������. ".$now_date_for_print." ".$art_ids;

}

//������� ���� ��������� ������������� � plan/index.php
function renewal_date($type){
$get_update = mysql_query("SELECT DATE_FORMAT(date, '%d.%m.%Y %H:%i') FROM plan_synch WHERE type='$type'");
$get_update = mysql_fetch_array($get_update);
echo "���������: ".$get_update[0];
}

?>
