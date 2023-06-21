<?php
define("_DB", "rashet");
define("_User", "root");
define("_Passwd", "");
define("_HostName", "localhost");



if (!mysql_connect(_HostName, _User, _Passwd)){
	error(__FILE__, __LINE__, mysql_error());
	exit;
}


mysql_query ("set character_set_client='cp1251'");
mysql_query ("set character_set_results='cp1251'");
mysql_query ("set collation_connection='cp1251_general_ci'");

mysql_select_db(_DB);
?>
