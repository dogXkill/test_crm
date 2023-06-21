<?
require_once("../../includes/db.inc.php");
function check_prefix($mas,$zn){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['tid']==$zn){return $value['prefix'];}
	}
	return false;
}
$uid = $_GET["uid"];
$q = "SELECT * FROM applications WHERE uid = '$uid'";
$ord = mysql_query($q);
$ord = mysql_fetch_assoc($ord);
$num_ord = $ord[num_ord];
$ClientName = $ord[ClientName];
$text_on_izd = $ord[text_on_izd];
$art_id = $ord[art_id];
$izd_w = $ord[izd_w];
$izd_v = $ord[izd_v];
$izd_b = $ord[izd_b];
$tiraz = $ord[tiraz];
$stamp_order = $ord[stamp_order];
$klishe_order = $ord[klishe_order];
$shnur_order = $ord[shnur_order];
$stamp_order_status = $ord[stamp_order_status];
$stamp_arrival_date = $ord['stamp_arrival_date'] !== '' ? date('d.m.Y', strtotime($ord['stamp_arrival_date'])) : '';
$klishe_order_status = $ord[klishe_order_status];
$shnur_order_status = $ord[shnur_order_status];
$stamp_num = $ord[stamp_num];
$izd_type=$ord[izd_type];

$resperson_material = $ord[resperson_material];
if($resperson_material == ""){$resperson_material = 0;}
$resperson_pechat = $ord[resperson_pechat];
if($resperson_pechat == ""){$resperson_pechat = 0;}
$deadline_stamp = $ord['deadline_stamp'];
$deadline_material  = $ord[deadline_material];
$deadline_pechat = $ord[deadline_pechat];
//$resperson_material_arr = array("<spad class=red_alert>не указано!</span>","самостоятельно менеджер", "принт менеджер", "в наличии");
//делаем из номера id 
$q1 = "SELECT * FROM stamps WHERE number = '$stamp_num' AND izd_type='$izd_type'";
$ord1 = mysql_query($q1);
$ord1 = mysql_fetch_assoc($ord1);
$id_stamp=$ord1[id];
//
$resperson_material_arr = array(
"<spad class=red_alert>не указано!</span>",
"самостоятельно менеджер",
"производственный отдел",
"в наличии",
"нет необходимости");
$resperson_material_pechat_arr = array("<spad class=red_alert>не указано!</span>","самостоятельно менеджер", "производственный отдел", "в наличии","нет необходимости");

$material_supplier_comment = $ord[material_supplier_comment];
$material_arrival_date = $ord[material_arrival_date];
$material_arrival_comment = $ord[material_arrival_comment];


if($ClientName !== ""){$ord_text = "<b>заказ</b>: $ClientName ($text_on_izd)";}
if($art_id !== 0 and $art_id !== ""){$ord_text = "<b>серийка</b> артикул: $art_id";}


//функция должна подтягивать в массив все типы, которые хранятся в бд
function get_all_types(){
//список названий таблиц, которые нам надо подгрузить в массивы
$getting_types = "types,materials,lamination,job_names";
$getting_types_arr = explode(",", $getting_types);

foreach ($getting_types_arr as $val) {

$get = mysql_query("SELECT * FROM ".$val);

while($g =  mysql_fetch_array($get)){
$id = $g["0"];
$arr[$val][$id] .= $g["1"];
}}
return $arr;
}
$arr = get_all_types();


$izd_type=$arr[types][$ord[izd_type]];
$izd_material=$arr[materials][$ord[izd_material]];
if($ord[izd_lami] !== "3"){$lami_vst = "ламинация ";}
$izd_lami = $lami_vst.$arr[lamination][$ord[izd_lami]];
$title = "$ord_text <br> $izd_type $ord[izd_w]x$ord[izd_v]x$ord[izd_b], тираж $ord[tiraz]шт, $izd_material, $izd_lami";
?>

<img src="../../i/del.gif" width="20" align=right height="20" alt="" style="cursor:pointer"  onclick="show_app_info(<?=$uid?>)">

<div style="position: relative; height: 30px; width: 600px;">
	<h3 class='title_popup' style='position: absolute; top: -25px; right: 80px'>Организационная карта заказа</h3>
</div>

<div>
<?
$jpg = $_SERVER{'DOCUMENT_ROOT'}. '/acc/applications/preview_img/'.$uid.'/1.jpg';
$png = $_SERVER{'DOCUMENT_ROOT'}. '/acc/applications/preview_img/'.$uid.'/1.png';


if (file_exists($jpg)) {
	echo "<img style='width: 150px' src=\"/acc/applications/preview_img/$uid/1.jpg\">";
}
if (file_exists($png)) {
	echo "<img style='width: 150px' src=\"/acc/applications/preview_img/$uid/1.png\">";
}
?>
</div>

<span>Заявка <b><?=$num_ord;?></b></span><br>




<?=$title;?>




 <br><br>
<table style='width: 100%;' class="apps_tbl" border=1 cellspacing=0 cellpadding=3 align=center>
    <tr><td colspan=2>
        <span id="resperson_material_span" style="">
<?php if ($deadline_stamp !== "") {
    $tmp = explode('-', $deadline_stamp);
    $year = $tmp[0];
    $mnt = $tmp[1];
    $dat = $tmp[2];
    $deadline_stamp = "$dat.$mnt.$year";
}

if ($deadline_material !== "") {
    $tmp = explode('-', $deadline_material);
    $year = $tmp[0];
    $mnt = $tmp[1];
    $dat = $tmp[2];
    $deadline_material = "$dat.$mnt.$year";
} else {
    $deadline_material = "<span class='red_alert'>не указан!</span>";
} 
if ($deadline_pechat !== "") {
    $tmp = explode('-', $deadline_pechat);
    $year = $tmp[0];
    $mnt = $tmp[1];
    $dat = $tmp[2];
    $deadline_pechat = "$dat.$mnt.$year";
} else {
    $deadline_pechat = "<span class='red_alert'>не указан!</span>";
} 
echo $resperson_material."|".$resperson_pechat;

?>
      <form action="" id="app_status_form_<?=$uid;?>">
        <table style="width:600" cellspacing=0 border=0  class='table_app'>
            <tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Ответственный за заказ материала:</td>
                <td style="text-align:left;font-size:15px;"><b><?=$resperson_material_arr[$resperson_material];?></b></td>
            </tr>
			<?php if ($resperson_material!=3){?>
			<tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Планируемая дата поставки материала:</td>
                <td style="text-align:left;font-size:15px;"><b><?=$deadline_material;?></b></td>
            </tr>
			<?php }?>
			 <tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Ответственный за заказ печати:</td>
                <td style="text-align:left;font-size:15px;"><b><?=$resperson_material_arr[$resperson_pechat];?></b></td>
            </tr>
			<?php if ($resperson_pechat!=3){?>
            <tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Планируемая дата поставки материала с печатью: </td>
                <td style="text-align:left;font-size:15px;"><b><? if ($resperson_pechat==4){echo "";}else{echo $deadline_pechat;}?></b></td>
            </tr>
			<?php }?>
			<!--
			<?php if ($resperson_material!=3){?>
            <tr>
                <td style="text-align:left;font-size:14px;font-weight:700;">Материал заказан в (заполняет <?=$resperson_material_arr[$resperson_material];?>):</td>
                <td style="text-align:left;font-size:13px;"><input type="text" class='input_app' autocomplete="off" onchange="save_app_status('<?=$uid;?>')" style="width:250px;" value="<?=$material_supplier_comment?>" maxlength="255" id="material_supplier_comment" name="material_supplier_comment"/></td>
            </tr>
			<?php } ?>
			-->
            <tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Материал прибыл на производство:</td>
                <td style="text-align:left;font-size:13px;"><input type="date" class='input_app'autocomplete="off" onchange="save_app_status('<?=$uid;?>')" style="width:170px;height:40px;font-size:18px" value="<?=$material_arrival_date?>" id="material_arrival_date" name="material_arrival_date"/></td>
            </tr>
            <tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Комментарий по материалу от производства:</td>
                <td style="text-align:left;font-size:13px;"><input type="text" class='input_app' autocomplete="off" onchange="save_app_status('<?=$uid;?>')" style="width:250px;" value="<?=$material_arrival_comment?>" maxlength="255" id="material_arrival_comment" name="material_arrival_comment"/></td>
            </tr>

            <tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Штамп:</td>
                <td style="text-align:left;font-size:15px;">
                    <?php if ($stamp_order == "0") {
                        echo "<span class=red_alert>не указано!</span>";
                    } else if ($stamp_order == "1") {
                        echo "<b>штамп не нужен</b>";
                    } else if ($stamp_order == "2") {
			$stampLink = "/acc/sprav/stamps/?edit=$id_stamp";
                        echo "<b>штамп уже имеется</b> № <a href='$stampLink'>".$stamp_num . "</a>";
                    } else if(in_array($stamp_order, ['3', '4', '5'])) { ?>
                <select name="stamp_order_status" id="stamp_order_status" style="width:150px;height:20px;" onchange="if (this.value === '2') { $('#stamp_arrival_date').show(); } else { $('#stamp_arrival_date').hide(); } save_app_status('<?=$uid;?>')">
                <option value="0" <?if($stamp_order_status=="0"){echo "selected";}?>>пока не заказан</option>
                <option value="1" <?if($stamp_order_status=="1"){echo "selected";}?>>заказан</option>
                <option value="2" <?if($stamp_order_status=="2"){echo "selected";}?>>получен</option>
                </select>
                <?php } ?>
                </td>
            </tr>

            <?php // если штамп получен или в наличии, то скрываем поле с планируемой доставкой, чтобы значение при изменении статуса все равно сохранялось, а не затиралось ?>
            <tr style="display: <?= (in_array($stamp_order, ['3', '4', '5']) && $stamp_order_status !== "2") ? 'table-row' : 'none' ?>">
                <td style="text-align:left;font-size:14px;font-weight:300;">Планируемая дата поставки штампа на производство:</td>
                <?php if ($deadline_stamp != '') { ?>
                <td style="text-align:left;font-size:15px;">
                    <b><?= $deadline_stamp ?></b>
                    <input type="hidden" value="<?= $ord['deadline_stamp'] ?>" id="deadline_stamp" name="deadline_stamp" />
                </td>
                <?php } else { ?>
                <td style="text-align:left;font-size:13px;"><input type="date" autocomplete="off" onchange="save_app_status('<?=$uid;?>')" style="width:170px;height:40px;font-size:18px" value="" id="deadline_stamp" name="deadline_stamp"/></td>
                <?php } ?>
            </tr>

            <tr id="stamp_arrival_date" <?= ($stamp_order == 2 || $stamp_order_status === "2") ? '' : 'style="display: none;"' ?>>
                <td style="text-align:left;font-size:14px;font-weight:300;">Штамп прибыл на производство:</td>
                <?php if ($stamp_arrival_date != '') { ?>
                <td style="text-align:left;font-size:15px;">
                    <b><?= $stamp_arrival_date ?></b>
                    <input type="hidden" value="<?= $ord['stamp_arrival_date'] ?>" id="stamp_arrival_date" name="stamp_arrival_date" />
                </td>
                <?php } else { ?>
                <td style="text-align:left;font-size:13px;"><input type="date" autocomplete="off" onchange="save_app_status('<?=$uid;?>')" style="width:170px;height:40px;font-size:18px" value="" id="stamp_arrival_date" name="stamp_arrival_date"/></td>
                <?php } ?>
            </tr>

           <?php if ($klishe_order !== "0") { ?>
            <tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Клише для тиснения/конгрева/фольга:</td>
                <td style="text-align:left;font-size:15px;"><?if($klishe_order == "0"){echo "<span class=red_alert>не указано!</span>";}else if($klishe_order == "2"){echo "<b>клише уже имеется</b>";}else if($klishe_order == "1"){?>
                <select name="klishe_order_status" id="stamp_order_status" style="width:150px;height:20px;" onchange="save_app_status('<?=$uid;?>')">
                <option value="0" <?if($klishe_order_status=="0"){echo "selected";}?>>пока не заказано</option>
                <option value="1" <?if($klishe_order_status=="1"){echo "selected";}?>>заказано</option>
                <option value="2" <?if($klishe_order_status=="2"){echo "selected";}?>>получено</option>
                </select>
                <?}?></td>
            </tr>
            <?}?>

            <?if($shnur_order !== "0"){?>
            <tr>
                <td style="text-align:left;font-size:14px;font-weight:300;">Шнур / лента:</td>
                <td style="text-align:left;font-size:14px;"><?if($shnur_order == "0"){echo "<span class=red_alert>не указано!</span>";}else if($shnur_order == "2"){echo "<b>шнур уже имеется</b>";}else if($shnur_order == "1"){?>
                <select name="shnur_order_status" id="stamp_order_status" style="width:150px;height:20px;" onchange="save_app_status('<?=$uid;?>')">
                <option value="0" <?if($shnur_order_status=="0"){echo "selected";}?>>пока не заказан</option>
                <option value="1" <?if($shnur_order_status=="1"){echo "selected";}?>>заказан</option>
                <option value="2" <?if($shnur_order_status=="2"){echo "selected";}?>>получен</option>
                </select>
                <?}?></td></tr>
                <?}?>
        </table>
    </form>


</span>
    </td></tr>
<tr>
<th align=center><b>Название</b></th>
<th align=center><b>Количество выполнено</b></th>
</tr><?
$select_ord = "SELECT job, SUM(num_of_work) FROM job WHERE num_ord='$num_ord' GROUP BY job ORDER BY job ASC";
$select_ord = mysql_query($select_ord);
while($rows = mysql_fetch_row($select_ord)){
$job_id = $rows[0];
$job_name=$arr[job_names][$job_id];
?>
<tr>
<td align=center class="tab_td_norm"><?=$job_name?></td>
<td align=center class="tab_td_norm"><?=$rows[1]?></td>
</tr>

<?}?>
<tr><td colspan=2>
<?if(mysql_num_rows($select_ord) == "0"){?>
По данному заказу работы пока не вносилось!
<?}else{?>
<a href="/acc/applications/count/index.php?num_ord=<?=$num_ord;?>" target="_blank"><!--<img src="../../i/journal.png" align="middle">--><i class="fa-solid fa-table-cells icon_btn_r21 icon_btn_blue"></i></a>
<a href="/acc/applications/count/index.php?num_ord=<?=$num_ord;?>" class=sublink target="_blank">посмотреть в журнале</a>
<?}?>
</td></tr>
</table>

<script>
$( function() {$("#app_info_<?=$uid?>").draggable(); } );
 $(document).mouseup( function(e){ // событие клика по веб-документу
		var div = $("#app_info_<?=$uid?>"); // тут указываем ID элемента
		if ( !div.is(e.target) // если клик был не по нашему блоку
		    && div.has(e.target).length === 0 ) { // и не по его дочерним элементам
			div.hide(); // скрываем его
		}
	});
</script>