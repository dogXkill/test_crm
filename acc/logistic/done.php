<?php
$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

// ���� 1 -����� ��� ���������, ����� 0 - ��������
$tpacc = (($user_type == 'sup') || ($user_type == 'meg') || ($user_type == 'adm') || ($user_type == 'acc')) ? 1 : 0;

// ----- ������� �� ������� ���� ������ �������� ---------
if(!$auth || (!$tpacc && $user_type != 'mng')) {
    header("Location: /");
    exit;
}

if(isset($_GET["id"]) and isint($_GET["id"], 1))
{
  $sql = "UPDATE courier_tasks SET done = 1 WHERE id = " . $_GET["id"];
  if($result = mysql_query($sql))
  {
  }
}

header("Location: /acc/logistic/index.php");
?>