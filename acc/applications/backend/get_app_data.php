<?
require_once("../../includes/db.inc.php");

//получаем из файла номер заявки, формируем строку и отправляем его обратно в edit.php для заполнения формы заявки
$uid = $_GET["uid"];
function zamen($str){return $str=str_replace("&","<||>",$str);}
$q = mysql_query("SELECT * FROM applications WHERE uid = '$uid'");

$app_arr = array();
    while($row =mysql_fetch_assoc($q))
    {
        $app_arr[] = $row;
    }
foreach ($app_arr[0] as $key => $value) {
	if ($key=='ClientName'){
		//$app_arr_new .= "&".$key."=".zamen($value);
	}else{
		//$app_arr_new .= "&".$key."=".$value;
	}
	$app_arr_new .= "&".$key."=".zamen($value);
}

echo $app_arr_new;
?>