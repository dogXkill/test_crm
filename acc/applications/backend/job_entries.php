<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$str = $_SERVER['QUERY_STRING'];
parse_str($str);

//разбиваем на массив для дальнейшего использования
$str_arr = explode("&", $str);

//т.к. данные о тарифах хранятся в таблице и с появлением нового тарифа данные туда не попадают, проставляем данные в ручном режиме
function get_tek_rate($job){
  if($job == '14'){$tek_rate = '0.5';}
  if($job == '15'){$tek_rate = '0.17';}
  if($job == '16'){$tek_rate = '0.17';}
  if($job == '29'){$tek_rate = '0.12';}
  if($job == '31'){$tek_rate = '0.2';}
return $tek_rate;
}


if(!$items_on_page){$items_on_page = "200";}

if($act !== "get_sdelka" and $app_type !== NULL){


$arr_app_type = array();
foreach ($str_arr as $v) {
   $str_ar = explode("=", $v);
    if($str_ar[0] == "app_type" and $str_ar[1] !== ""){array_push($arr_app_type, ' a.app_type = ' . $str_ar[1]);}
}

  $arr_app_type = implode(' OR ', $arr_app_type);
  $sql_vst .= " AND (" . $arr_app_type . " ) ";


}


if(is_numeric($year) and $month == ""){
    $sql_vst .= " AND j.cur_time LIKE '$year-%' ";
}elseif(is_numeric($year) and $month !== "")
{
    $sql_vst .= " AND j.cur_time LIKE '$year-$month-%' ";
}

if(is_numeric($art_id)){$sql_vst .= " AND a.art_id = '$art_id' ";}

if(is_numeric($num_of_work)){$sql_vst .= " AND j.num_of_work = '$num_of_work' ";}

if(is_numeric($num_ord)){$sql_vst .= " AND j.num_ord = '$num_ord' ";}

if(is_numeric($otpravka_num)){$sql_vst .= " AND j.otpravka = '$otpravka_num' ";}

if($num_sotr){
//т.к. в строке может передаваться сразу несколько значений с одинаковым идентификатором, то parse_str тут не срабоает, приходиться обрабатывать массив вручную
//$str_arr = explode("&", $str);
$arr_num_sotr = array();
foreach ($str_arr as $v) {
   $str_ar = explode("=", $v);
    if($str_ar[0] == "num_sotr" and $str_ar[1] !== ""){array_push($arr_num_sotr, ' j.num_sotr = ' . $str_ar[1]);}
}

  $arr_num_sotr = implode(' OR ', $arr_num_sotr);
  $sql_vst .= " AND (" . $arr_num_sotr . ") ";




} else {
  $arr = array();
  $sotr_list = array();
  if ($user_access['account_access_dep'] !== '0' && $user_access['account_access_dep'] !== '') {
    $allowed_deps = explode('|', $user_access['account_access_dep']);
    $q = "SELECT job_id FROM users WHERE ";
    foreach ($allowed_deps as $key => $value) {
      array_push($arr, ' user_department = ' . $value);
    }
    $arr = implode(' OR ', $arr);
    $q .= $arr;
    $r = mysql_query($q);
    while($row = mysql_fetch_row($r))
    {
      array_push($sotr_list, 'j.num_sotr = ' . $row[0]);
    }
  } else {
    $q = "SELECT job_id FROM users WHERE user_department = " . $user_access['user_department'];
    $r = mysql_query($q);
    while($row = mysql_fetch_row($r))
    {
      array_push($sotr_list, 'j.num_sotr = ' . $row[0]);
    }

  }
  $sotr_list = implode(' OR ', $sotr_list);
  $sql_vst .= " AND (" . $sotr_list .")";

}

if($izd_type){

$izd_type_sotr = array();
foreach ($str_arr as $v) {
   $str_ar = explode("=", $v);
    if($str_ar[0] == "izd_type" and $str_ar[1] !== ""){array_push($izd_type_sotr, ' a.izd_type = ' . $str_ar[1]);}
}

  $izd_type_sotr = implode(' OR ', $izd_type_sotr);
  $sql_vst .= " AND (" . $izd_type_sotr . ") ";

 //   $sql_vst .= " AND a.izd_type = '$izd_type' ";

    }






if($job){

//т.к. в строке может передаваться сразу несколько значений с одинаковым идентификатором, то parse_str тут не срабоает, приходиться обрабатывать массив вручную

$arr_jobs = array();
foreach ($str_arr as $v) {
   $str_ar = explode("=", $v);
    if($str_ar[0] == "job" and $str_ar[1] !== ""){array_push($arr_jobs, ' j.job = ' . $str_ar[1]);}
}

  $arr_jobs = implode(' OR ', $arr_jobs);
  $sql_vst .= " AND (" . $arr_jobs . ") ";



} else {
  $jobs_allowed = explode('|', $user_access['jobs_access']);
  $arr = array();
  foreach ($jobs_allowed as $key => $value) {
    array_push($arr, ' j.job = ' . $value);
  }
  $arr = implode(' OR ', $arr);
  $sql_vst .= " AND (" . $arr . ") ";
}

if(is_numeric($izd_w)){$sql_vst .= " AND a.izd_w = '$izd_w' ";}

if(is_numeric($izd_v)){$sql_vst .= " AND a.izd_v = '$izd_v' ";}

if(is_numeric($izd_b)){$sql_vst .= " AND a.izd_b = '$izd_b' ";}



if($from){$sql_vst .= " AND j.cur_time >= '$from' ";}


if($to){


$to = date('Y-m-d',strtotime($to . "+1 days"));

$sql_vst .= " AND j.cur_time <= '$to' ";}


$en_q = "SELECT j.*, j.uid AS job_uid, a.* FROM job AS j, applications AS a WHERE 1 AND j.num_ord = a.num_ord $sql_vst ORDER BY j.cur_time DESC LIMIT 0,$items_on_page";
$en = mysql_query($en_q);

if($act !== "get_sdelka"){

//получаем имена сотрудников в массив
//$sotr = "SELECT job_id, surname, name FROM users WHERE (nadomn = '1' OR proizv = '1' OR administration = '1' OR user_group = '2') AND job_id > '0'";
$sotr = "SELECT uid, job_id, surname, name FROM users WHERE job_id > '0'";
$sotr = mysql_query($sotr);
$sotr_arr = array();
while($rows = mysql_fetch_assoc($sotr)){
$sotr_arr[$rows[job_id]] = $rows[surname]." ".$rows[name];
$users_arr[$rows[uid]] = $rows[surname]." ".$rows[name];
}

//собираем типы изделий в массив
$types = "SELECT tid, type FROM types ORDER BY seq DESC";
$types = mysql_query($types);
while ($r = mysql_fetch_array($types)){
    $types_arr[$r[0]] = $r[1];
}

//собираем типы этапов производства в массив
$job_names = "SELECT id, name FROM job_names ORDER BY seq DESC";
$job_names = mysql_query($job_names);
while ($r = mysql_fetch_array($job_names)){
    $job_names_arr[$r[0]] = $r[1];
}

//собираем типы материалов в массив
$izd_materials = "SELECT tid, type FROM materials ORDER BY seq DESC";
$izd_materials = mysql_query($izd_materials);
while ($r = mysql_fetch_array($izd_materials)){
    $izd_materials_arr[$r[0]] = $r[1];
}

?>

<span id=sql style="display:none;"><?=$en_q;?></span>
<table class=apps_tbl cellspacing=0 cellpadding=0 border=1 width=1500>
<tr>
  <th>номер сотр</th>
  <th>имя</th>
  <th>тип изделия</th>
  <th>заказ</th>
  <th>номер заявки</th>
  <th>участок производства</th>
  <th>количество</th>
  <th>цена</th>
  <th>стоимость</th>
  <th>дата и время</th>
  <th># отправки</th>
  <th>кто внес</th>
  <th>действия</th>
</tr>
<?

$total_sum = "0";
if ($en) {
    while ($r = mysql_fetch_array($en)) {
        $job_uid = $r['job_uid'];
        $cur_time = date('d.m.Y G:H', strtotime($r['cur_time']));
        $num_sotr = $r['num_sotr'];
        $who_entered = $r['who_entered'];
        $num_ord = $r['num_ord'];
        $uid_ord = $r['uid'];
        $dat_ord = date('d.m.Y', strtotime($r['dat_ord']));
        $art_id = $r['art_id'];
        $art_uid = $r['art_uid'];
        $izd_type = $r['izd_type'];
        $job = $r['job'];
        $nadomn = $r['nadomn'];
        $otpravka = $r['otpravka'];
        if($otpravka == '0'){$otpravka_txt = "";}else{$otpravka_txt = "<a href=\"https://crm.upak.me/acc/applications/sendings/edit.php?id=$otpravka\" target=_blank>$otpravka</a>";}

        $qty= $r['num_of_work'];
        $order_price = $r['order_price'];
        //если установлена цена по ордеру и это не выдача надомницу, то подставляем цену ордера и значок
        if (is_numeric($order_price) and $job !== '14') {
            $tek_rate = $order_price; $order = " <img src=\"../../../i/invoice16.png\" valign=absmiddle>";
        } else {
          //если это не ордер, то берем базовый тариф, заложенный в заявке, а на выдачу надомнику и ручки с клипсами проставляем тариф вручную
        $order = "";
        $tek_rate = round($r["rate_".$job],2);
            if ($tek_rate == "") {
                //не помимаю зачем пишем тут в констатнте, ведь в таблице applications уже имеются колонки rate_14 и rate_15. Но, если нет, то вероятно имеются ввиду какие то старые заявки, где этот тариф не задан.
                $tek_rate = get_tek_rate($job);
            }
        }

        if ($job == "4" and $nadomn == '1') {
            $house = " <img src=\"../../../i/house.png\" valign=absmiddle>";
        }

        $sum = round($qty*$tek_rate,2);
        $izd_material = $r['izd_material'];
        $izd_w = $r['izd_w'];
        $izd_v = $r['izd_v'];
        $izd_b = $r['izd_b'];

        $izd_material = $izd_materials_arr[$izd_material];

        if ($r['app_type'] == 1) {
            $app_type = $r['ClientName'];
        } elseif($r['app_type'] == 4) {
            $app_type = "шелкография: $r[ClientName]";
        } else {
            $app_type = "серийник арт.<a href=\"http://www.paketoff.ru/shop/view/?id=$art_uid\" style=\"font-weight:bold\" target=\"_blank\">$art_id</a>";
        }

        $zak_descr = "$app_type, $izd_w x $izd_v x $izd_b, $izd_material";?>
        <tr id="tr_<?=$job_uid;?>">
          <td><?=$num_sotr;?></td>
          <td><?=$sotr_arr[$num_sotr];?></td>
          <td><?=$types_arr[$izd_type];?></td>
          <td style="horizontal-align:left;"><?=$zak_descr;?></td>
          <td><a href="../edit.php?uid=<?=$uid_ord;?>" target="_blank"><?=$num_ord;?></a> <span class=date>от <?=$dat_ord;?></span></td>
          <td><?=$job_names_arr[$job]; echo $house; echo $order;?></td>
          <td align=center id="entry_qty_<?=$job_uid;?>">
              <span style="cursor:pointer" onclick="change_qty_form('<?=$job_uid;?>')" id="entry_qty_span_<?=$job_uid;?>"><?=$qty; $total_qty = $qty + $total_qty;?></span>
          </td>
          <td align=center><?=$tek_rate; $cnt = $cnt + 1; $tek_rate_total = $tek_rate+$tek_rate_total;?></td>
          <td align=center><?=$sum; $total_sum = $total_sum + $sum;?></td>
          <td class=date id="entry_date_<?=$job_uid;?>">
              <span style="cursor:pointer" onclick="change_date_form('<?=$job_uid;?>')" id="entry_date_span_<?=$job_uid;?>"><?=$cur_time;?></span>
              </td>
          <td align=center><?=$otpravka_txt;?></td>
          <td class=who><?=$users_arr[$who_entered];?></td>
          <td><img src="/acc/i/del.gif" width="20" height="20" onclick="del('<?=$job_uid;?>')" style="cursor:pointer;"></td>
        </tr>
        <?

        $house = "";
        $tek_rate = "";
        $order_price = "";
    }
} ?>
<tr style="font-weight:bold; background-color: #99CCFF">
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td align=center><?=round($total_qty,2);?></td>
  <td align=center>средн. <?if($cnt > 0) echo round($tek_rate_total/$cnt,2);?></td>
  <td align=center><?=round($total_sum,2);?></td>
  <td></td>
  <td></td>
  <td></td>
</tr>
</table><?
} else {
if ($en) {
    while ($r = mysql_fetch_array($en)) {
        $qty = $r['num_of_work'];
        $job = $r['job'];
        $order_price = $r['order_price'];
        if (is_numeric($order_price) and $job !== '14') {
            $tek_rate = $order_price;
        } else {
            //если это не ордер, то берем базовый тариф, заложенный в заявке, а на выдачу надомнику и ручки с клипсами проставляем тариф вручную
            $tek_rate = round($r["rate_" . $job], 2);
            if ($tek_rate == "") {
                $tek_rate = get_tek_rate($job);
            }
        }
        $sum = round($qty * $tek_rate, 2);
        $total_sum = $total_sum + $sum;
        $tek_rate = "";
        $order_price = "";
    }
}
 echo round($total_sum);
} ?>
