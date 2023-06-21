<?
require_once("../includes/db.inc.php");
// ------------- СОХРАНЕНИЕ ВВЕДЕННОГО НОМЕРА СЧЕТА ---------------

	$tmp_id = $_GET['acc_id'];
	$tmp_acc = trim($_GET['set_acc']);
	if($tmp_id) {

		if( (strtolower($tmp_acc) == 'нет') || (strtolower($tmp_acc) == 'no') || ($tmp_acc == '-') ) {
            $tmp_acc="none";
			}
          if($tmp_acc == ""){$dt_ready = "date_ready=''"; $ready = "";}else{$dt_ready = "date_ready=NOW()"; $ready = "1";}

			$query = "UPDATE queries SET prdm_num_acc='$tmp_acc',".$dt_ready.",ready='$ready' WHERE uid='$tmp_id'";
			$q = mysql_query($query);
            echo mysql_error();
			if(!mysql_error()){echo "ok";}
            }

 ?>