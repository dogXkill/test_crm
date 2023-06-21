<li><a href="?act=save_old_phones">сохранить колонки firm_tel и cont_tel в колонки firm_tel_old и cont_tel_old</a></li>
<li><a href="?act=save_old_phones_to_comment">сохранить колонки firm_tel и cont_tel в поле comment</a></li>
<li><a href="?act=stardartize_phones">стандартизировать телефоны (firm_tel, cont_tel) без сохранения</a></li>
<li><a href="?act=stardartize_phones&save=1">стандартизировать телефоны (firm_tel, cont_tel) с сохранением в БД</a></li>

<?

$act = $_GET["act"];
$save = $_GET["save"];
require_once("../acc/includes/db.inc.php");


if($act == "save_old_phones"){

   $q = "SELECT uid, firm_tel, cont_tel FROM clients WHERE 1 ";
    $phones = mysql_query($q);

    while($g =  mysql_fetch_assoc($phones)){
        $uid = $g["uid"];
        $firm_tel = $g["firm_tel"];
        $cont_tel = $g["cont_tel"];
                   mysql_query("UPDATE clients SET firm_tel_old = '$firm_tel', cont_tel_old = '$cont_tel' WHERE uid = '$uid'");
                   $count_r = $count_r+1;
    }
    echo mysql_error()."данные обновлены! затронуто: <b>".$count_r."</b> строк";
}

if($act == "save_old_phones_to_comment"){

   $q = "SELECT uid, firm_tel, cont_tel FROM clients WHERE 1 ";
    $phones = mysql_query($q);

    while($g =  mysql_fetch_assoc($phones)){
        $uid = $g["uid"];
        $firm_tel = $g["firm_tel"];
        $cont_tel = $g["cont_tel"];
        $all_phones = $firm_tel . " " . $cont_tel;
                   mysql_query("UPDATE clients SET comment = CONCAT(comment, '$all_phones') WHERE uid = '$uid'");
                   $count_r = $count_r+1;
        $all_phones =''; $firm_tel=''; $cont_tel='';
    }
    echo mysql_error()."данные обновлены! затронуто: <b>".$count_r."</b> строк";
}



if($act == "stardartize_phones"){

   if($save == '1'){$limit = '';}else{$limit = ' LIMIT 0,1000';}


   $q = "SELECT uid, firm_tel, cont_tel FROM clients WHERE 1 $limit";

    $phones = mysql_query($q);



    while($g =  mysql_fetch_assoc($phones)){

    $uid = $g["uid"];
    $firm_tel = $g["firm_tel"];
    $cont_tel = $g["cont_tel"];
    $firm_tel_prev = $firm_tel;
    $cont_tel_prev = $cont_tel;



     $firm_tel = preg_replace('/[^0-9]/', '', $firm_tel);
     $cont_tel = preg_replace('/[^0-9]/', '', $cont_tel);


     $fsf = substr($firm_tel, 0, 1);

        if ($fsf == '7' or $fsf == '8'){
             $firm_tel = substr($firm_tel, 1);
        }

     $fsc = substr($cont_tel, 0, 1);

        if ($fsc == '7' or $fsc == '8'){
             $cont_tel = substr($cont_tel, 1);
        }

        //удаляем первые 10 символов
        $firm_tel = mb_substr($firm_tel, 0, 10);
        $cont_tel = mb_substr($cont_tel, 0, 10);

            //телефоны если 10 символов, до добавляем +7
            if(strlen($firm_tel) == 10){$firm_tel = "+7".$firm_tel;}
            if(strlen($cont_tel) == 10){$cont_tel = "+7".$cont_tel;}

            //очевидный шлак удаляем
            if(strlen($firm_tel) < 7){$firm_tel = "";}
            if(strlen($cont_tel) < 7){$cont_tel = "";}



           if($save == '1'){
           mysql_query("UPDATE clients SET firm_tel = '$firm_tel', cont_tel = '$cont_tel' WHERE uid = '$uid'");
           $count_r = $count_r+1;
           }else{
            echo "$firm_tel_prev  <b>--></b> $firm_tel<br><br>";
            echo "$cont_tel_prev  <b>--></b> $cont_tel<br><br>";
           }

    }


}
if($save == '1'){
echo mysql_error()."данные обновлены! затронуто: <b>".$count_r."</b> строк";
 }

//делаем перебор емейлов или телефонов или ИНН  или р/с

//проверяем емейл , телефон ли, ИНН ли, р/c ли

//если ДА - смотрим сколько есть еще клиентов с таким параметром, пишем ID этих клиентов в массив

//если больше 1 то берем из них клиента с самым свежим ID запоминаем в переменную - newest_client_id

//смотрим заказы клиентов по ID из массива и меняем на самый свежий client_id

//

?>

