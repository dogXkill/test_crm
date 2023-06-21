<?php


define("_DB", "");
define("_User", "");
define("_Passwd", "");
define("_HostName", "");

if (!mysql_connect(_HostName, _User, _Passwd)){
	error(__FILE__, __LINE__, mysqli_error());
	exit;
}


mysql_query ("set character_set_client='cp1251'");
mysql_query ("set character_set_results='cp1251'");
mysql_query ("set collation_connection='cp1251_general_ci'");

mysql_select_db(_DB);
?>
