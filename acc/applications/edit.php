<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
ob_start();

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
if ($user_access['proizv_access'] == '0' || empty($user_access['proizv_access'])) {
  header('Location: /');
}

// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
  header("Location: /");
  exit;
}


$str = $_SERVER['QUERY_STRING'];
parse_str($str);

$tpus = $user_type;		// тип пользователя

// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

 // $uid = $_GET["uid"];

//получаем название клиента
 // $zakaz_id = $_GET["zakaz_id"];
  if(is_numeric($zakaz_id)){
  $res1 = mysql_fetch_array(mysql_query("SELECT client_id FROM queries WHERE uid='$zakaz_id'"));
  $client_id =  $res1["client_id"];
  $res2 = mysql_fetch_array(mysql_query("SELECT short FROM clients WHERE uid='$client_id'"));
  $client_name = $res2["short"];
  }
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Заявка на производство</title>
<link href="../style.css?t=<?php echo(microtime(true)); ?>" rel="stylesheet" type="text/css" />
<link href="../includes/new.css?t=<?php echo(microtime(true)); ?>" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="../includes/fonts/css/all.min.css">

</head>

<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>

<script>
var user_type = '<?=$user_type;?>';
var my_user_id = '<?=$user_id;?>';
</script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../includes/js/jquery-ui.css">
<body>
  <span id="all">
<? require_once("../templates/top.php"); ?>
<table align="center" width="100%" border="0">
  <tr>
    <td>
      <?$name_curr_page = 'apl_list';
      require_once("../templates/main_menu.php");?>
      <table width=80% align=center border="0" cellpadding="0" cellspacing="0" bgcolor="#F6F6F6">
      	<tr>
      		<td valign="top" align="center">
      			<table width="90%" border=0 cellspacing="0" cellpadding="0">
      				<tr id="top_menu">
          <td width="300"><a href="index.php" class="sublink icon_btn"><!--<img src="../../i/manufacture.png" width="24" height="24" alt=""  style="vertical-align: middle;"/>--><i class="fa-solid fa-square-list icon_btn_r21 icon_btn_blue"></i></a>
<a href="index.php" class="sublink">Список заявок</a></td>
      					<td width="300" align=center>
<a href="edit.php" class="sublink icon_btn"><!--<img src="../../i/manufacture.png" width="24" height="24" alt=""  style="vertical-align: middle;"/>--> </a><a href="edit.php" style="color: #0095EB;cursor:pointer">
<i class="fa-solid fa-file-pen icon_btn_r17 icon_btn_blue"></i>
Создать новую заявку</a>
</td>
<td width="300" align=center>
    <? if($uid){ ?>
<span onclick="open_job()"><!--<img src="../../i/journal.png" align="middle">--><i class="fa-solid fa-table-cells icon_btn_r21 icon_btn_blue" style='cursor:pointer;'></i></span> <span onclick="open_job()"  style="color: #0095EB;cursor:pointer;vertical-align: middle; text-decoration:underline">Работы выполненные по заявке</span>
<? } ?>
</td>
</tr>
<tr><td colspan=2 class=inputs>
<form id=forma action="" autocomplete="off" method="post">
<span id="everything">
<span id="user_id_span">
Инициатор заявки:<br>
<?$res = mysql_query("SELECT uid AS num_sotr, surname, name FROM users WHERE (user_department = '6' OR user_department = '4' OR user_department = '18' OR user_department = '1' OR user_department = '2' OR user_department = '18' OR user_department = '3') AND archive <> '1' ORDER BY surname ASC");?>
<select <?=(($tpacc)?'':'disabled="disabled"')?> style="width:220px" name="user_id" id="user_id" size="1">
<option value="">выбрать</option>
<?while($r = mysql_fetch_array($res)){?>
<option value="<?=$r['num_sotr']?>" <?=$sel;?>><?=$r['name']?> <?=$r['surname']?></option>
<?}?>
</select></span>

<span id="app_type_span">
Тип заявки:<br>
<select id=app_type name=app_type style="width:270px" onchange="show_hide_app_type_flds()" required>
<option value="">не выбран</option>
<option value="1">заказная продукция</option>
<option value="2">серийная продукция</option>
<option value="4">готовые с лого (шелкография)</option>
<option value="3" disabled="disabled">заказ у внешнего поставщика</option>
</select>
</span>



<span id="tiraz_span">
Тираж<sup>&lowast;</sup>:<br>
<input type="text" id="tiraz" name="tiraz" onkeyup="this.value=replace_num(this.value);" style="width:100px" required/><br>
</span>

<span id="num_ord_span">
Номер заявки:		&nbsp;Дата:<br>
<input type="text" readonly  style="width:100px" id=num_ord name=num_ord />

<input type="hidden" value="<?=$uid;?>" id=uid name=uid />
&nbsp;
<span id=data_ord name=data_ord ></span>
</span>
<span style="vertical-align:middle" id="plan_span"><input type="checkbox" value="1" name="plan_in" id="plan_in"/> <label for="plan_in" style="cursor:pointer">в план!</label></span>


<div id=art_id_span>Артикул<sup>&lowast;</sup>: <input type="text" id="art_id" name=art_id size=5  style="width:100px" onchange="get_art_info('get_data');get_art_info('check');check_new();" required/>

<span id=art_link></span>
<span id="new_art_but">
<?if(!$uid){?><input type="checkbox" id=art_id_new onchange="new_art_form();jump('art_id_new','1','izd_type');search_similar_art()"/> <label for="art_id_new" style="cursor:pointer">новый</label><?}?>
</span>
<span id=art_id_span_al class="span_al"></span>
<input type="hidden" id="art_uid" name="art_uid" value="" />
<span id=art_check></span>

</div>



<span id=old_title style="display:block"></span>

<div id=ClientName_span>
Название клиента<sup>&lowast;</sup>: <input type="text" id="ClientName" name=ClientName size=5 value="<?=$client_name;?>"  style="width:350px"/ required>
<?if($zakaz_id !== ""){?><img src="../i/rm_icon.gif" width="20" onclick="go_to_link()" style="cursor:pointer;" height=20" alt="" align="absmiddle"/><?}?>

<input type="hidden" id="zakaz_id" name=zakaz_id value="<?=$zakaz_id;?>"/><br>
Надпись или бренд на изделии: <input type="text" id="text_on_izd" name="text_on_izd"  value=""  style="width:350px"/ required><br>
</div>

<div id=preview_span>
<?php
$result = mysql_query("SELECT * FROM applications WHERE uid = {$uid}");
if ($result) {
    $arr = mysql_fetch_array($result);
    $app_user_id = $arr["user_id"];
    $preview_link = $arr["preview_link"];
	 $deadline = $arr['deadline'];
}

if (!empty($preview_link)) { ?>
  Ссылка на превью:
  <a href="<?=$preview_link?>" target="blank"><img src="../i/lupa.gif" width="20" height="20" style="cursor:pointer;" /></a>

  <?
}
?>

<?
if (isset ($uid) && !empty($uid)) {
  $preview_path = __DIR__ . '/preview_img/' . $uid;
  if (file_exists($preview_path) ) {
    $preview_files = scandir($preview_path);
    foreach ($preview_files as $key => $preview_file) {
      if ($preview_file !== '.' && $preview_file !== '..') {
        $preview_link = '/acc/applications/preview_img/' . $uid . '/' . $preview_file;
        $short_link = '/' . $uid . '/' . $preview_file;
        $show_preview = 1;
      }
    }
  }
  $result_files_path = __DIR__ . '/result_files_img/' . $uid;
  if (file_exists($result_files_path)) {
    $result_files = array();
    $result_files_dir = scandir($result_files_path);
    foreach ($result_files_dir as $key => $file) {
      if ($file !== '.' && $file !== '..') {
        $full_result_link = '/acc/applications/result_files_img/' . $uid . '/' . $file;
        $short_result_link = '/' . $uid . '/' . $file;
        array_push($result_files, array('full_result_link' => $full_result_link, 'short_result_link' => $short_result_link));
      }
    }
  }
}

?>
<style media="screen">
  .err_photo:hover {
    cursor: pointer;
    text-decoration: underline;
  }
</style>
<div style="display: flex;">
<div id=preview_file>
Файл превью: <input type="file" id="preview_photo" data-exist="<?echo ($show_preview == 1) ? 1 : 0 ;?>" data-changed="0" data-warned="0" data-uid="<?=$uid?>" name="preview_photo" style="width:350px">
<?
if ($show_preview == 1) {
  ?><div class="">

  </div>
  <div onclick="delete_preview();" id="preview_photo_del" data-file="<?=$short_link?>" class="err err_photo" style="position: absolute; padding-left: 10px; padding-top: 10px; z-index: 1;"><img style="height: 20px; width: auto;" src="/acc/i/del.gif"></img></div><a style="display: block;" href="<?=$preview_link?>" target="blank"><img id="preview_photo_img" src="<?=$preview_link?>" style="cursor:pointer; height: 150px; width: auto;"></a>
  <?
}
?>

</div>
<div id="result_files" style="display: block;">
      Файлы результата работ:
      <input id="result_files_input" data-changed="0" data-uid="<?=$uid?>" data-exist="0" type="file" multiple="multiple" name="result_files[]" accept="image"/>
      <div style="display: flex; flex-wrap: wrap;">
        <?
        if (!empty($result_files)) {
          foreach ($result_files as $key => $value) {
            ?>
              <div id="del_result_file_cont_<?=$key?>">
                <div class="del_result_file" data-key="<?=$key?>" style="position: absolute; padding-left: 10px; padding-top: 10px; z-index: 1; cursor: pointer;" data-file="<?=$value['short_result_link']?>">
                  <img title="Удалить изображение" style="height: 20px; width: auto;" src="/acc/i/del.gif" />
                </div>
                <a href="<?=$value['full_result_link']?>" target="_blank"><img src="<?=$value['full_result_link']?>" style="cursor:pointer; height: 150px; width: auto; padding: 5px;" title="Нажмите для просмотра изображения"></a>
              </div>
            <?
          }
        }
 
        ?>
      </div>
    </div>
</div>
</div>


<div id="izd_type_span" style='display: inline-block;'>
Тип продукции<sup>&lowast;</sup>:
<select id="izd_type" name=izd_type style="width:250px" onchange="jump('izd_type','1','tiraz');search_similar_art();hide_izd_type_flds();" required>
<?$get_types = mysql_query("SELECT * FROM types WHERE vis = 1 ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_types)){
$id = $gg["tid"];
if($id == "0"){$id = "";}
?>
<option value="<?=$id;?>"><?=$gg["type"];?></option>
<?}?>
</select>
<a href="https://www.paketoff.ru/admin/shop/types/" target="_blank"><img src="../../i/sprav.png" width="16" height="16" alt=""></a>
<input type="checkbox" name="vip" id="vip" value="1"> <label for="vip">VIP</label>

</div>
<div id="dressing_span" style='display: inline-block;'>
<input type="checkbox" name="dressing" id="dressing" value="1"> <label for="dressing">перевязка ручек</label>
</div>
<span style='display: block;' id=shelko_span_art>Артикул на который сделать нанесение: <input type="text" style="width:60px;height:30px;" id="shelko_art" name="shelko_art"/></span>

<div id="shelko_span" >
Шелкография:


<select id="shelko_num_colors" name="shelko_num_colors" style="width:150px" onchange="count_shelko_colors()">
<option value="">указать</option>
<option value="1">1+1</option>
<option value="2">2+2</option>
<option value="3">3+3</option>
<option value="4">1+0</option>
<option value="5">2+0</option>
<option value="6">3+0</option>
<option value="7">2+1</option>
<option value="8">3+1</option>
<option value="9">3+2</option> 
</select>
всего прокаток на изд. <input type="text" id="shelko_prokatok" name="shelko_prokatok" onkeyup="this.value=replace_num(this.value);" style="width:40px;"/>

<input type="checkbox" name="shelko_storon" id="shelko_storon" value="1"> <label for="shelko_storon">шелкография на стороне</label>

</div>

<div id=size_span style="line-height: 25px; white-space: nowrap">
Размер: <span id="jump_span"><input type="checkbox" id=jumpoff /> <label for="jumpoff" style="font-size:7px; cursor:pointer;">отключить прыжки</label></span>
Ш<sup>&lowast;</sup>: <input type="text" style="width:100px;" id="izd_w" name="izd_w" onkeyup="jump('izd_w','2','izd_v');this.value=replace_num(this.value);" onchange="search_similar_art()" size=3 required/>
<span id="izd_v_span">В<sup>&lowast;</sup>: <input type="text" style="width:100px;" id="izd_v" name="izd_v" onkeyup="jump('izd_v','2','izd_b');this.value=replace_num(this.value);" onchange="search_similar_art()" size=3 required/></span>
<span id="izd_b_span">Б: <input type="text" style="width:100px;" id="izd_b" name="izd_b" onkeyup="jump('izd_b','2','izd_material');this.value=replace_num(this.value);" onchange="search_similar_art()" size=3 required/></span>

<span id=stamp_num_span>штамп #: <input type="text" style="width:70px" id="stamp_num" name="stamp_num" value="" size=4/></span>
<button type="button" onclick="load_stamp_list();" style='    height: 30px;vertical-align: top;padding-bottom: 3px;padding-top: 3px;margin: 6px;'>Реестр</button>
<a href='#' style='display:none;'target="_blank"><img src="../../i/sprav.png" width="16" height="16" alt="" id='open_stamp'></a>
<span style="opacity:0.7;font-size:12px;" id="podvorot_klapan_span">
подворот (см): <input type="text" style="width:35px" id="podvorot" name="podvorot" onkeyup="this.value=replace_num(this.value);" value="5" size=3/>
</span>




</div>

<div id="izd_material_span">
Материал<sup>&lowast;</sup>:
<select id="izd_material" name="izd_material" style="width:250px" onchange="jump('izd_material','1','izd_gramm');search_similar_art()" required>
<option value="">не выбран</option>
<?php
$materials = mysql_query("SELECT * FROM materials WHERE show_apps = 1 ORDER BY seq ASC");
if ($materials) {
    while($r =  mysql_fetch_array($materials)){
        $id = $gg["tid"];
        if ($id == "0") {
            $id = "";
        }
        ?>
    <option value="<?=$r["tid"];?>"><?=$r["type"];?></option>
<?php }
}
?>
</select>
<a href="https://www.paketoff.ru/admin/shop/material/" target="_blank"><img src="../../i/sprav.png" width="16" height="16" alt=""></a>

<span id="izd_gramm_span">
грамм: <input type="text" style="width:50px" id="izd_gramm" name="izd_gramm" maxlength=4 onkeyup="jump('izd_gramm','3','paper_suppl');this.value=replace_num(this.value);" required/></span> 
<!--<input type="text" style="width:180px;height:30px;font-size:10px;" id="material_comment" name="material_comment" />-->


</div>

<div id="izd_lami_span">
Ламинация<sup>&lowast;</sup>:
<select id="izd_lami" name="izd_lami" style="width:250px" onchange="search_similar_art();jump('izd_lami','1','izd_color')" required>
<option value="">не выбран</option>
<?
$get = mysql_query("SELECT * FROM lamination ORDER BY tid ASC");
echo mysql_error();
while($gg =  mysql_fetch_array($get)){

$id = $gg["tid"];?>
<option value="<?=$id;?>"><?=$gg["type"];?></option>
<?$lami_arr .= "lami_arr[".$gg["tid"]."] = ".$gg["cost"]."\n";}?>
</select>

</div>

<div id="tisnenie_span">
<span>
Тиснение:
<select id="tisnenie" name="tisnenie" style="width:250px" onchange="jump('tisnenie','1','col_ottiskov_izd');">
<option value="">без тиснения</option>
<option value="2">1+1</option>
<option value="4">2+2</option>
<option value="6">3+3</option>
<option value="8">4+4</option>
<option value="1">1+0</option>
<option value="2">2+0</option>
<option value="3">3+0</option>
<option value="4">4+0</option>
<option value="5">5+0</option>
<option value="6">6+0</option>
<option value="7">7+0</option>
<option value="8">8+0</option>
</select></span>
<span id="tisnenie_dop_polya">
оттисков на 1 изд.: <input type="text" id="col_ottiskov_izd" name="col_ottiskov_izd"  style="width:50px"/>
коммент:
<input type="text" style="width:200px;height:30px;font-size:10px;" id="tisn_comment" name="tisn_comment"/></span>
</div>

<div id="paper_col_ext_span">
Цвет изделия<sup>&lowast;</sup>:
<select id="izd_color" name="izd_color" style="width:150px" onchange="jump('izd_color','1','izd_color_inn');search_similar_art();hlp('0','izd_color','izd_color_inn')">
<option value="">не выбран</option>
<?
$get_colors = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_colors)){
if($gg["cid"] !== "0")?>
<option value="<?=$gg["cid"];?>"><?=$gg["colour"];?></option>
<?}?>
</select>


<span id="color_pantone_span">
номер пантона: <input type="text" id="color_pantone" name="color_pantone" style="width:60px"/>
</span>

<span id="color_inn_span">
<span id="hlp_izd_color_span">
внутри: <span onclick="hlp('same','izd_color','izd_color_inn')" style="font-size:8px; cursor:pointer;">такой же</span> |
<span onclick="hlp('15','izd_color','izd_color_inn')" style="font-size:8px; cursor:pointer;">белый</span> |
<span onclick="hlp('23','izd_color','izd_color_inn')" style="font-size:8px; cursor:pointer;">коричневый</span>
<b>&rsaquo;</b></span>
<select id="izd_color_inn" name="izd_color_inn" style="width:150px">
<option value="">не выбран</option>
<?
$get_colors1 = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg1 =  mysql_fetch_array($get_colors1)){
if($gg1["cid"] !== "0")?>
<option value="<?=$gg1["cid"];?>"><?=$gg1["colour"];?></option>
<?}?>
</select>
<a href="https://www.paketoff.ru/admin/shop/colours/" target="_blank"><img src="../../i/sprav.png" width="16" height="16" alt=""></a>
</span>

</div>


<div id="paper_num_list_span">
<span id="sborka_type_span">
Тип сборки<sup>&lowast;</sup>:
<select id="sborka_type" name="sborka_type" style="width:100px" required>
<option value="">выбрать</option>
<option value="1">стандарт</option>
<option value="1.2">премиум</option>
<option value="1.4">VIP</option>
</select>
</span>


 собирается из<sup>&lowast;</sup>:
<select id="paper_num_list" name="paper_num_list" style="width:250px" required>
<option value="">выбрать</option>
<option value="1">из одного листа</option>
<option value="2">из двух листов</option>
</select>
<span id=paper_list_typ_span style="display:none">

<select id="paper_list_typ" name="paper_list_typ" style="width:250px">
<option value="1">листы одинаковые</option>
<option value="2">листы разные</option>
</select></span>
</div>

<div id="luve_span">
Люверсы:
<select id="luve" name="luve" style="width:250px" onchange="jump('luve','1','izd_ruchki');">
<option value="">без люверсов</option>
<option value="1">серебро</option>
<option value="2">золото</option>
<option value="3">черные</option>
<option value="4">красные</option>
<option value="5">синие</option>
<option value="6">другие (укажите в коментариях)</option>
</select>
</div>


<div id="izd_ruchki_span" style="padding-top:10px;">
<span>
Ручки:
<select id="izd_ruchki" name="izd_ruchki" style="width:180px" onchange="jump('izd_ruchki','1','hand_type');" required>
<option value="">выбрать</option>
<?
$get = mysql_query("SELECT * FROM ruchki ORDER BY seq ASC");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["tid"];?>"><?=$gg["type"];?></option>
<?}?>
</select>

<span id='hand_txt_bl'>
коммент:
<input type="text" style="width:250px;height:30px;font-size:10px;" id="hand_txt" name="hand_txt"/></span>
</span>
<br>
</div>




<span id="ruchki_dop_polya">

Тип ручек:
<select id="hand_type" name="hand_type" style="width:180px" onchange="jump('hand_type','1','hand_length');" required>
<option value="">выбрать</option>
<?
$get = mysql_query("SELECT * FROM hand_type");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["tid"];?>"><?=$gg["type"];?></option>
<?
$hand_types_arr .= "hand_types_arr[".$gg["tid"]."] = ".$gg["cost"]."\n";
}?>
</select>


Длина 1 ручки (включая узел):
<input type="text" style="width:50px" value="40" onkeyup="this.value=replace_num(this.value);" id="hand_length" name="hand_length" maxlength=4 required/>
Толщина/ширина - шнура/ленты мм:
<input type="text" style="width:50px" onkeyup="this.value=replace_num(this.value);" id="hand_thick" name="hand_thick" maxlength=4 required/>

Крепление ручек:
<select id="hands_krepl" name="hands_krepl" style="width:170px" onchange="jump('hands_krepl','','hand_color');" required>
<option value="">выбрать</option>
<?$get = mysql_query("SELECT * FROM hands_krepl ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["id"];?>"><?=$gg["name"];?></option>
<?}?>
</select>
<br>
Цвет ручек<sup>&lowast;</sup>:
<span id="hlp_hand_color_span">
<span onclick="hlp('same','izd_color','hand_color')" style="font-size:8px; cursor:pointer;">как пакет</span> |
<span onclick="hlp('15','','hand_color')" style="font-size:8px; cursor:pointer;">белый</span> |
<span onclick="hlp('16','','hand_color')" style="font-size:8px; cursor:pointer;">черный</span> |
<span onclick="hlp('23','','hand_color')" style="font-size:8px; cursor:pointer;">коричневый</span> |
<span onclick="hlp('18','','hand_color')" style="font-size:8px; cursor:pointer;">синий</span>
<b>&rsaquo;</b></span>
<select id="hand_color" name="hand_color" style="width:150px" onchange="jump('hand_color','','gluing_material');" required>
<option value="">не выбран</option>
<?
$get_colors = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_colors)){?>
<option value="<?=$gg["cid"];?>"><?=$gg["colour"];?></option>
<?}?>
</select>
</span>


<div id="strengt_bot_span">
Укрепление пакета:
<input type="checkbox" id="strengt_bot" name="strengt_bot" value="1" checked="checked"> <label for="strengt_bot" style="cursor:pointer;">дно</label>
<input type="checkbox" id="strengt_side" name="strengt_side" value="1" checked="checked"> <label for="strengt_side" style="cursor:pointer;">бок</label>
<select id="strengt_tip" name="strengt_tip" style="width:170px"  required>
	<option value="0">выбрать</option>
	<option value="1">обычный картон</option>
	<option value="2">двойной картон</option>
	<option value="3">КАПА картон</option>
</select>
</div>

<div id="gluing_material_span">
Клеим на<sup>&lowast;</sup>: <select id="gluing_material" name="gluing_material" style="width:280px" onchange="jump('gluing_material','1','pack');" required>
<option value="">не выбран</option>
<?$get = mysql_query("SELECT * FROM gluing_materials ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["id"];?>"><?=$gg["name"];?></option>
<?$glue_arr .= "glue_arr[".$gg["id"]."] = ".$gg["cost"]."\n";}?>
</select>
</div>
<br>
<span id="pack_span">
Упаковка: <select id="pack" name="pack" style="width:150px" onchange="jump('pack','1','col_in_pack');">
<?$get = mysql_query("SELECT * FROM upakovka ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["id"];?>"><?=$gg["name"];?></option>
<?}?>
</select> по <input type="text" style="width:50px"  onkeyup="this.value=replace_num(this.value);" value="25" id="col_in_pack" name="col_in_pack" maxlength=4/> шт
</span>
<br>

<div id="deadline_span">
Дедлайн (день готовности заказа): <input type="date" style="width:170px;height:40px;font-size:20px" value="" id="deadline" data-deadline="<?=$deadline?>" name="deadline" onchange="jump('deadline','1','spec_req');"/> <span style="font-size:10px;">* если нужен поэтапный график, то пишите его в комментарии</span>
</div> 



<span id=tarif_span class="tarif_span">Тариф: <span id="sborka_cost_span"></span></span>

<br>


<div id="utv_pech_list_span"><input type="checkbox" name="utv_pech_list" id="utv_pech_list" value="1"> <label for="utv_pech_list">утвердить печатный лист с менеджером-инициатором заявки при поступлении</label></div>
<div id="utv_got_izd_span"><input type="checkbox" name="utv_got_izd" id="utv_got_izd" value="1"> <label for="utv_got_izd">утвердить первое изделие с менеджером-инициатором заявки</label></div>

<div id="spec_req_span">
Комментарий (вносить все что не вошло в стандартную форму!):<br>
<textarea id="spec_req" name="spec_req" style="width:812px;height:150px;font-size:12px;"></textarea>
<br>

</div>
<div class="print_comment1" id="comments">

</div>
<br><br>
<div id=add_art_add_flds style="border: 1px #3399CC solid; border-radius: 5px; width:1000px; padding: 5px">
<b>Форма добавления на сайт:</b><br>
Отпускная цена: <input type="text" style="width:80px"  onkeyup="this.value=replace_num(this.value);"  value="" id="price" name="price" maxlength=8 required/>
Себестоимость рентабельная: <input type="text" style="width:80px"  onkeyup="this.value=replace_num(this.value);" value="" id="price_our" name="price_our" maxlength=8 required/><br>

<input type="checkbox" value="1" name="onn" id="onn" checked/> <label for="onn">отображать на сайте</label>
<input type="checkbox" name="print" id="print" value="type2"  checked> <label for="print">шелкография</label>
<input type="checkbox" name="show_when_zero" id="show_when_zero" value="1" > <label for="show_when_zero">отображать если 0</label>

<br>




<br>
Примечание к товару: <input style="width:450px" name="primechanie" id="primechanie" type="text" value="" maxlength="255">
<br>

<input onclick="add_art_site();" type="button" id="add_art_site_but" value="Добавить артикул на сайт!" style="width: 400px; cursor:pointer; height: 45px; font-size: 25px;">
<input type="button" value="Показать похожие" onclick="search_similar_art('1')" style="width: 300px; left:10px; cursor:pointer; height: 45px; font-size: 25px;"/>
<span id="new_art_span"></span>
</div>

<script>
var user_type = '<?=$user_type;?>';
var user_name = '<?=$user_access['name'];?>';
var user_surname = '<?=$user_access['surname'];?>';
var user_name_full= user_name+' '+user_surname;
</script>

<div id="org_param_span" style="border: 1px dotted; padding: 5px; width: 800px">
<b>Информация о материале (заполняется тем, кто заказывает материал):</b>



<br>
Формат листа, используемого для изготовления изделия:<br>
Сторона А<sup>&lowast;</sup>: <input type=text id="list_h" name="list_h" value="" style="width:50px" required> x Сторона B<sup>&lowast;</sup>: <input type=text id="list_w" name="list_w" value="" style="width:50px" required>
см вес 1 листа: <input type=text id="one_list_weight" value="" disabled style="width:100px">гр.



<div style="font-size:12px;"><span style="font-style: italic;display:none;" id=razvorot>примерный разворот - </span>
<span id="razvorot_cely"></span>
<span id="razvorot_half"></span></div>


Сколько изделий получается из 1 листа<sup>&lowast;</sup>:
<select id="isdely_per_list" name="isdely_per_list" style="width:100px" required>
<option value="">выбрать</option>
<option value="0.5">0.5</option>
<option value="0.67">0.67</option>
<?
$i = 1;
while ($i < 100) {
?>
<option value="<?=$i;?>"><?=$i;?></option>
<?$i = $i + 1;}?>
</select>
<br>
всего необходимо листов: <input type=text id="list_total" value="" disabled style="width:100px"> шт

общий вес материала: <input type=text id="list_weight" value="" disabled style="width:100px"> кг
 <br>
<span id="lami_storon_span">
<input type="checkbox" name="izd_lami_storon" id="izd_lami_storon" value="1"> <label for="izd_lami_storon">ламинация на стороне подрядчика</label>
</span><br>
<span id="izd_virub_span">
<input type="checkbox" name="izd_virub_storon" id="izd_virub_storon" value="1"> <label for="izd_virub_storon">вырубка на стороне подрядчика</label>
</span><br>
<span id="tisn_storon_span">
<input type="checkbox" name="izd_tisn_storon" id="izd_tisn_storon" value="1"> <label for="izd_tisn_storon">тиснение / конгрев на стороне подрядчика</label>
</span>

<!-- Штамп -->
<div style="font-weight: bold;">Необходимость заказать штамп (кто заказывает штамп)</div>
<div id="stamp_order_span" style="display: inline-block; vertical-align: top;">
    <select name="stamp_order" id="stamp_order" style="width:400px; font-size: 16px;">
        <option value="">выбрать</option>
        <option value="2">уже имеется (подтверждено производством)</option>
        <option value="4">заказывает менеджер проекта</option>
        <option value="5">заказывает производство</option>
    </select>
</div>
<span id="deadline_stamp_span" style="display: inline-block; vertical-align: top; line-height: 28px; margin-left: 2px; width:110px;">
    <input type="date" style="height:28px;font-size:16px; width: 115px;" value="" id="deadline_stamp" name="deadline_stamp"/>
</span>
<div style="display: inline-block; width: 245px; font-size: 14px; line-height: 14px; letter-spacing: -0.6px; margin-left: 5px;">Дата поставки <b>штампа</b> на производство (заполняет тот, кто заказывает)</div>
<!-- Конец: Штамп -->

<!-- Материал -->
<div style="font-weight: bold;">Ответственный за заказ материала</div>
<div id="resperson_material_span" style="display: inline-block; vertical-align: top;">
    <select id="resperson_material" name="resperson_material" style="width: 400px; font-size: 16px;" required>
        <option value="">выбрать</option>
        <option value="1">самостоятельно менеджер проекта</option>
        <option value="2">производственный отдел</option>
        <option value="3">материал имеется (подтверждено производством)</option>
    </select>
</div>
<span id="deadline_material_span" style="display: inline-block; vertical-align: top; line-height: 28px; margin-left: 2px;">
    <input type="date" style="width: 115px; height: 28px; font-size: 16px;" value="" id="deadline_material" name="deadline_material"/>
</span>
<div style="width: 273px; font-size: 14px; line-height: 14px; display: inline-block; letter-spacing: -0.7px;">Планируемая дата поставки <b>материала</b>: на производство (заполняет тот, кто заказывает)</div>

<!-- Печать -->
<div style="font-weight: bold;">Ответственный за заказ печати:</div>
<div id="resperson_pechat_span" style="display: inline-block; vertical-align: top;">
    <select id="resperson_pechat" name="resperson_pechat" style="width: 400px; font-size: 16px;" required>
        <option value="">выбрать</option>
        <option value="1">самостоятельно менеджер проекта</option>
        <option value="2">производственный отдел</option>
        <option value="3">материал имеется (подтверждено производством)</option>
		<option value="4">нет необходимости</option>
    </select>
</div>
<span id="deadline_pechat_span" style="display: inline-block; vertical-align: top; line-height: 28px; margin-left: 2px;">
    <input type="date" style="width: 115px; height: 28px; font-size: 16px;" value="" id="deadline_pechat" name="deadline_pechat"/>
</span>
<div id="deadline_pechat_span_1"style="width: 273px; font-size: 14px; line-height: 14px; display: inline-block; letter-spacing: -0.7px;">Планируемая дата поставки материала с <b>печатью</b> на производство (заполняет тот, кто заказывает)</div>
<!-- Конец: Материал -->

<!-- Шнур -->
</br>
<b>Необходимость заказать шнур / ленту</b>
<div style="margin-bottom: 5px; display: flex;">
    <div id="shnur_order_span">
        <select name="shnur_order" id="shnur_order" style="width:400px; height: 28px; font-size: 16px;">
            <option value="">выбрать</option>
            <option value="1">заказывает производство</option>
            <option value="2">уже имеется  (подтверждено производством)</option>
            <option value="0">в данном изделии ручек не предусмотрено</option>
        </select>
    </div>
</div>
<!-- Конец: Шнур -->

<div id="klishe_order_span" style="display:none;">
<select name="klishe_order" id="klishe_order" style="width:420px;">
<option value="">выбрать</option>
<option value="1">заказывает производство</option>
<option value="2">клише уже имеется (подтверждено производством)</option>
</select> необходимость заказать клише для конгрева / тиснения / фольги
</div>

</div>



<div id="tech_param_span" style="border: 1px dotted; padding: 5px; width: 800px">

<b>Технические параметры изделия (заполняется производством):</b><br>

Сколько изделий ламинируется за 1 прогон<sup>&lowast;</sup>:
<select id="lami_isdely_per_list" name="lami_isdely_per_list" style="width:100px" required>
<option value="">выбрать</option>
<option value="0.05">0.05 (1/20)</option>
<option value="0.06">0.06 (1/17)</option>
<option value="0.166">0.166 (1/6)</option>
<option value="0.20">0.20 (1/5)</option>
<option value="0.25">0.25 (1/4)</option>
<option value="0.33">0.33 (1/3)</option>
<option value="0.5">0.5 (1/2)</option>
<option value="0.67">0.67 (1/1.5)</option>
<?
$i = 1;
while ($i < 100) {
?>
<option value="<?=$i;?>"><?=$i;?></option>
<?$i = $i + 1;}?>
</select> всего прогонов: <input type=text id="lamin_total" value="" disabled style="width:100px"><br>


Сколько изделий вырубается за 1 удар<sup>&lowast;</sup>:
<select id="virub_isdely_per_list" name="virub_isdely_per_list" style="width:100px" required>
<option value="">выбрать</option>
<option value="0.05">0.05 (1/20)</option>
<option value="0.06">0.06 (1/17)</option>
<option value="0.166">0.166 (1/6)</option>
<option value="0.20">0.20 (1/5)</option>
<option value="0.25">0.25 (1/4)</option>
<option value="0.33">0.33 (1/3)</option>
<option value="0.5">0.5 (1/2)</option>
<option value="0.67">0.67 (1/1.5)</option>
<?
$i = 1;
while ($i < 100) {
?>
<option value="<?=$i;?>"><?=$i;?></option>
<?$i = $i + 1;}?>
</select> всего ударов: <input type=text id="virub_total" value="" disabled style="width:100px"><br>


Клеевой материал объем на изд: <input type=text id="gluing_material_per_izd" disabled style="width:100px"> цена на изд: <input type=text id="price_gluing_material_per_izd" disabled style="width:100px"><br>

<span id="no_sborka_span">
<input type="checkbox" name="no_sborka" id="no_sborka" value="1"> <label for="no_sborka">без сборки</label>
</span>




</div>


<span id="ss_span" style="border: 1px dotted; padding: 5px; width: 800px;<?if($user_type == 'mng'){echo "display:none;";}?> ">
    <input type="button" onclick="show_ss_form()"  style="height:30px" value="показать форму определения с/с">
    <span id=show_ss_form_span style="display:none;"><br>
<b>С/с серийной продукции (заполняется составителем заявки):</b>
<br>
Цена материала: <input type=text id="material_cost" name="material_cost" style="width:100px"  onkeyup="this.value=replace_zap(this.value);" required>

<select id="material_cost_type" name="material_cost_type" style="width:100px">
<option value="" selected="selected">выбрать</option>
<option value="per_list">за 1 лист</option>
<option value="per_tonn">за тонну</option>
</select> <span class="span_tip">может включать в себя печать</span>
<br>
Цена за материал на 1 изделие: <input type=text id="price_per_list" disabled style="width:100px"><br>
Стоимость печати на 1 изделие: <input type=text id="price_per_print" name="price_per_print" onkeyup="this.value=replace_zap(this.value);" style="width:100px">  <span class="span_tip">может включать в себя материал</span> <br>
Стоимость пленки для ламинирования (м2): <input type=text id="price_per_lami_film" disabled style="width:100px">
за 1 изделие включая работу: <input type=text id="price_per_lami" disabled style="width:100px"><br>
Стоимость вырубки (включая приладку): <input type=text id="price_per_virub" disabled style="width:100px"><br>
Стоимость сборки: <input type=text id="sborka_cost_oznak" disabled style="width:100px"><br>
Стоимость ручек на изделие: <input type=text id="price_per_ruchki" name="price_per_ruchki" onkeyup="this.value=replace_zap(this.value);" style="width:100px" value="3"> <span class="span_tip">вносится вручную исходи из текущих цен</span><br>
Организационные расходы: <input type=text id="orgrashodi_cost" name="orgrashodi_cost" value="1"  onkeyup="this.value=replace_zap(this.value);" style="width:100px"> <span class="span_tip">административные, складские и транспортные расходы</span><br>
Доп работы: <input type=text id="dopraboty_cost" value="" name="dopraboty_cost" onkeyup="this.value=replace_zap(this.value);" style="width:100px"> <span class="span_tip">упаковка, нарезка дна и боковин, вставка ручек, сверление</span><br>
Примерная валовая с/с изд: <input type=text id="r_price_our" name="r_price_our" style="background-color:#DDDDDD;width:100px" value=""> <span id="ss_site"></span> <span class="span_tip">для отображения с/с необходимо тщательно заполнить заявку</span>
<input type="button" value="сверить с/с с сайтом >>>" style="height:30px" onclick="get_art_info('compare_ss')"  id="compare_ss_but"/>
<span id="compare_ss_span" class="span_tip"></span><br>

<br>
</span>

<input onclick="get_art_info('compare_flds')" type="button" value="привести артикул на сайте в соответствие с заявкой>>>" id="compare_flds_but"  style="height:30px" />
 </span>




<table style="border: 1px dotted; width: 800px; " id="job_rate_box"><tr><td>
<?
$job_types = mysql_query("SELECT * FROM job_types ORDER BY seq ASC");
while($jt = mysql_fetch_array($job_types)){
$jt_id=$jt["id"];
?><span style="border-bottom: 1px dotted; font-size: 30px;cursor:pointer" onclick="show_tarif('<?=$jt_id;?>')"><?=$jt["name"];?></span><br>
<table width="450" cellpadding="1" cellspacing="1" id="<?=$jt_id;?>" style="border: 1px solid #909090; display:none; padding: 5px; border-collapse: collapse;"><?
$job_names = mysql_query("SELECT * FROM job_names WHERE job_type = '$jt_id' ORDER BY seq ASC");
while($jn = mysql_fetch_array($job_names)){
$nums_jobs = $nums_jobs.";".$jn["id"];
$rate_id = "rate_".$jn["id"];
$seq = $jn["seq"];
?>
<tr>
<td style="padding: 5px;"><?=$jn["name"];?></td>
<td style="padding: 5px;"><input class="tx" onkeyup="this.value=replace_zap(this.value);"  name="<?=$rate_id;?>" id="<?=$rate_id;?>" type="text" size="8" value="<?=$jn["price"];?>" /></td>
</tr>
<?}?></td></tr>
</table>
<?}?>

  <span onclick="open_job()" id="open_job_span" style="cursor:pointer;text-decoration:underline">выполненная работа по заявке</span>

</td></tr></table>


<br>


<span style="font-size:12px; text-decoration: underline; cursor:pointer" id="close_print_span" onclick="close_print()">назад к списку заявок</span>

<?
if ($user_access["proizv_access_edit"] == '2' || ($user_access["proizv_access_edit"] == '1' && $user_access["uid"] == $app_user_id) || !isset($_GET['uid'])) {
  ?>
<!--///-->
<div class="print_comment" id="comment">
<b>Комментарий к заявке №<span id="num_ord_comment_span"></span></b>
<div></div>
</div>
<div id="div_comment" onmouseup="end_drag()" onmousemove="dragIt(this,event)" style="background-color: rgb(255, 255, 255); padding: 5px; width: 550px; border: 1px solid rgb(0, 153, 204); position: absolute; z-index: 10000; left: 2030px; top: 319px;">
<span style="cursor:move;" onmousedown="start_drag(document.getElementById('div_comment'),event)">
<b>Комментарий к заявке №<span id="num_ord_comment_span"></span></b>
</span>
<div id="comment_div_text" style="width:530px; max-height:200; overflow:auto; padding: 3px; background-color: #F2F2F2">
<textarea name="" id="comment_text" style="width:530px;height:50px"></textarea>

<input type="hidden" value="8347" id="num_ord_comment"><br>
<span id="email_mas_otp1" style="display: inline;"><input type="checkbox" id="email_mas_otp_check1"><label for="email_mas_otp_check1">Отправить уведомления на почту?</label></span></br>
<input type="button" class="btn_big" onclick="comment_save(user_name_full)" value="Сохранить"> <input type="button" class="btn_big" onclick="comment_close()" value="Закрыть">
</div>
</div>

<!--///-->

  <input onclick="save_app();" type="button" id="save_but" value="Сохранить!" style="width: 400px; cursor:pointer; height: 70px; font-size: 30px;">

  <?
}
?>

<?if(is_numeric($uid)){?>
<input onclick="print_view();" type="button" id="print_view_but" value="Вид для печати!" style="width: 300px; cursor:pointer; height: 70px; font-size: 30px;">
<input onclick="printr();" type="button" id="print_but" value="Печать!" style="width: 200px; cursor:pointer; height: 50px; font-size: 20px;">

<?}?>
<div id="save_but_block_reason"></div>



</span>
</form>

</td></tr></table>
</td></tr></table>




<div id="similar_arts_div" style="position:fixed;top:20px;right:20px;width:500px;height:250px; background-color: white;display:none;"></div>
<input type="hidden" id="similar_arts_never_show" value="0" />
<div id="debug"></div>
<div class="wrap">
  <div class="modal" id='modal_shtamp' >
  <span style='    padding-bottom: 8px;    cursor: move;
    display: inline-block;'>Реестр штампов</span><img src="../../i/del.gif" width="20" align="right" height="20" alt="" style="cursor:pointer" onclick="show_hide_modal(this,'hide');">
  <div class='content-modal'>
  
  </div>
  </div>
</div>
<script src="../includes/application_edit.js?cache=<?=rand (1,1000000)?>"></script>


<script>
$('#modal_shtamp').draggable({
		start: function() {
            $('#modal_shtamp').css("transform","none");
			//$('.other_popup').css("height","0%");
        },
		stop: function() {
            //$('#modal_shtamp').css("transform","translate(-50%, -50%)");
			//$('.other_popup').css("height","0%");
        }
	});
var lami_arr = new Array();
<?echo $lami_arr;?>
var glue_arr = new Array();
<?echo $glue_arr;?>
var hand_types_arr = new Array();
<?=$hand_types_arr;?>

<?if(is_numeric($uid)){?>
//если указан Ид то делаем запрос и получаем данные заявки в массив
get_app_data(<?=$uid?>);
calc_ss();

<?}?>

<?if(is_numeric($zakaz_id) and $app_type == "1"){?>
show_hide_app_type_flds('1')
$("#app_type").val(1);
<?}else if(is_numeric($zakaz_id) and $app_type == "3"){?>
show_hide_app_type_flds('4')
$("#app_type").val(4);
<?}else if(!is_numeric($uid)){?>
$("#app_type").val(2);
block_save_button()
//если создаем заявку без привязки к заказу, то все поля кроме серийки надо заблочить
show_hide_app_type_flds('start')
<?}
if($user_type !== "sup"){?>
$("#app_type").prop("disabled", true);
<?}?>
$(document).click(function (e) {
	 if ($(e.target).closest("#modal_shtamp").length) {
        // клик внутри элемента
        return;
    }
	
	  // клик снаружи элемента
	  show_hide_modal('#modal_shtamp','hide');
  });
</script>

<span id=res></span>

</body>
</html>
<?ob_end_flush();?>
