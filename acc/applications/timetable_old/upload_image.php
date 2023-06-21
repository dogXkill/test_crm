<?
 require_once("../../includes/db.inc.php");
 $image = $_POST["image"];
 $query="INSERT INTO capture (image) VALUES('".$image."')";
 mysql_query( $query );
 if (!mysql_error()){echo mysql_insert_id();}else{echo mysql_error();}
 ?>