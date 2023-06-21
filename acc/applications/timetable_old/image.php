<html>

<img src="<?
if ( isset( $_GET['id'] ) ) {
  $id = (int)$_GET['id'];
  if ( $id > 0 ) {
  require_once("../../includes/db.inc.php");
    $query = "SELECT `image` AS image FROM `capture` WHERE id='$id'";
    $res = mysql_query($query);
    if ( mysql_num_rows( $res ) == 1 ) {
      $image = mysql_fetch_array($res);
      echo $image['image'];
    }
  }
}
echo mysql_error()?>" alt="" />

<?// echo $image['image']; ?>

</html>