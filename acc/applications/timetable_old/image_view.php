<!DOCTYPE HTML>

<html>

<head>
  <title>Untitled</title>
</head>

<body>

<span style="background: url(<?
if ( isset( $_GET['id'] ) ) {
  // Здесь $id номер изображения
  $id = (int)$_GET['id'];
  //echo $id;
  if ( $id > 0 ) {
  require_once("../../includes/db.inc.php");
    $query = "SELECT `image` AS image FROM `capture` WHERE id=4";
    $res = mysql_query($query);
    if ( mysql_num_rows( $res ) == 1 ) {
      $image = mysql_fetch_array($res);
      echo $image['image'];
    }
  }
}

echo mysql_error()?>) top left no-repeat; height:100px; width:150px;"></span>

</body>

</html>