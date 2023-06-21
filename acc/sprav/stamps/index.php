<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //пїЅпїЅпїЅпїЅ пїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅ
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
require_once("../../includes/lib/img_prev.php");
if ($user_access['sprav_access'] == '0' || empty($user_access['sprav_access'])) {
  header('Location: /');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="windows-1251" />
<title>Штампы</title>
<link href="../../includes/css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<!--<link href="../../includes/css/font-awesome.min.css" rel="stylesheet" type="text/css" />-->
<link href="../../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
<!--<script type="text/javascript" src="../../includes/js/jquery-ui.js"></script>-->
<script src="../../includes/js/jquery.cookie.js"></script>
<script src="../../includes/js/jquery-ui.min.js"></script>
<script src="../../includes/js/n_jquery.dataTables.min.js" charset="windows-1251"></script>
<script src="../../includes/js/dataTables.fixedHeader.min.js" charset="windows-1251"></script>

<link rel="stylesheet" href="../../includes/fonts/css/all.min.css" type="text/css" media="all">

<style type="text/css">

.spans{
  border: 1px solid #336699;
  background-color:white;
  padding: 10px;
  display:none;
}

.stamp_delete {
  cursor: pointer;
}


.stamps_select {
  width: 200px;
  padding: 3px 0px 3px 0px;
  border: 1px solid #cecece;
  background: white;
  border-radius: 4px;
  font-size: 20px;
}
.stamps_select_number {
  width: 80px;
  padding: 3px 0px 3px 0px;
  border: 1px solid #cecece;
  background: white;
  border-radius: 4px;
  font-size: 20px;
  text-align: center;
}
.stamps_input_descr {
  color: black;
  font-size: 18px;
  line-height: normal;
}
.sorting:before {
  content: "^" !important;
}
.sorting:after {
  content: "v" !important;
}
table.dataTable thead>tr>td.sorting_desc:after,
table.dataTable thead>tr>td.sorting_asc:after {
  content: "v" !important;
}
table.dataTable thead>tr>td.sorting_asc:before ,
table.dataTable thead>tr>td.sorting_desc:before
 {  content: "^" !important;}
 .dataTables_scrollHead{
  position: sticky!important;
    top: 0px;
    z-index: 999;
 }
 table.fixedHeader-floating {
  background-color: white;
}

table.fixedHeader-locked {
  position: absolute !important;
  background-color: white;
}

@media print {
  table.fixedHeader-floating {
    display: none;
  }
}
</style>
</head>
<body>
<script type="text/javascript" src="../../includes/js/wz_tooltip.js"></script>
<? require_once("../../templates/top.php"); ?>
   <?
      $name_curr_page = 'sprav';
      require_once("../../templates/main_menu.php");

       $part = $_GET["part"];
       ?>

<?require_once("../../templates/spravmenu.php");?>

<?
 
function check_prefix($mas,$zn){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['tid']==$zn){return $value['prefix'];}
	}
	return false;
}
function check_prefix_zn($mas,$zn){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['prefix']==$zn){return $value['tid'];}
	}
	return false;
}
$stamps = array();
// izd_types

$types = array();

$q = "SELECT * FROM `types` WHERE vis_stamps = 1 ORDER BY seq DESC";
$r = mysql_query($q);
while ($row = mysql_fetch_row($r))
{
  $type = array();
  $type['tid'] = $row[0];
  $type['type'] = $row[1];
  $type['prefix'] = $row[5];
  array_push($types, $type);
  unset($type);
}
//
include("filters.php");
if (empty($sort_order)){
	//$sort_order=' deleted=0';
	//$sort_where=' WHERE';
}else{
	//$sort_order.=' AND deleted=0';
}
$q = "SELECT * FROM stamps" . $sort_where . $sort_order;
//echo $q;
$r = mysql_query($q);

while ($row = mysql_fetch_row($r) )
{
  $stamp = array();

  $stamp['ID'] = $row[0];
  $stamp['NUMBER'] = $row[1];
  $stamp['NAME'] = $row[2];
  $stamp['SHIR'] = $row[3];
  $stamp['VIS'] = $row[4];
  $stamp['BOK'] = $row[5];
  $stamp['SIZE_X'] = $row[6];
  $stamp['SIZE_Y'] = $row[7];
  $stamp['ANOTHER_STAMP'] = $row[8];
  $id_another_stamp= $row[8];
if ($id_another_stamp!=''){
$num_anoth = "SELECT * FROM stamps WHERE id = '{$id_another_stamp}'";
  $res_num_anoth = mysql_query($num_anoth);
  $mas_anot = mysql_fetch_assoc($res_num_anoth);
  $stamp['NUM_ANOTHER']=$mas_anot['number'];
}else{
	$stamp['NUM_ANOTHER']='';
}
	$stamp['TYPE'] = $row[9];
  $izd_q = "SELECT * FROM types WHERE vis_stamps = 1 AND tid = $row[9]";
  $izd_r = mysql_query($izd_q);
  $izd_arr = mysql_fetch_assoc($izd_r);

  $stamp['IZD_TYPE'] = $izd_arr['type'];
  $stamp['KARKAS'] = $row[11];

  if ($row[12] == 1) {
    $stamp['SKLEIKA'] = 'Внутренняя';
  } elseif ($row[12] == 2) {
    $stamp['SKLEIKA'] = 'Внешняя';
  } else {
    $stamp['SKLEIKA'] = '';
  }

  $stamp['TEXT'] = $row[13];
  $stamp['PHOTO']=$row[10];
  $stamp['CANVAS'] =$row[11];
  $stamp['CREATED_AT'] = empty($row[14]) ? '' : date('d.m.Y H:i:s', strtotime($row[14]));
  $stamp['CREATE_USER'] = $row[15];
  $stamp['status_vk_r'] = $row[18];
  $prefix="";
$types_id=$stamp['TYPE'];
if ($types_id!=4){
$prefix=check_prefix($types,$types_id);
}else{$prefix="";}
$stamp['PREFIX']=$prefix;
//photo 


  if (!empty($stamp['PHOTO'])) {
      //$stamp['PHOTO_ICON'] = "/acc/sprav/stamps/photo-stamps/" .$prefix.'/'. $stamp['NUMBER'] . "/" . $stamp['PHOTO'];
	  $stamp['PHOTO_ICON']="/acc/sprav". $stamp['PHOTO'];
	  $pyt="/home/crmu660633/test.upak.me/docs/acc/sprav/";
	  $pyt1=explode("/",$stamp['PHOTO']);
	  if (file_exists($pyt."stamps/photo-stamps/".$pyt1[3]."/mini.png")) {
		  //существует
	  }else{
	  $image = new Thumbs($pyt. "".$stamp['PHOTO']);
		$image->thumb(200, 200);
		$image->reduce(200, 200);
		$image->save($pyt."stamps/photo-stamps/".$pyt1[3]."/mini.png");

		//$image->cut(200,200);
		//$stamp['PHOTO_ICON']=$image->output();
	  }
		$stamp['PHOTO_ICON']="/acc/sprav/stamps/photo-stamps/".$pyt1[3]."/mini.png";
		//echo $iamge;
  } else {
      $stamp['PHOTO_ICON'] = '/acc/i/who.gif';
  }
  
//canvas

if (!empty($stamp['CANVAS'])) {
      //$stamp['PHOTO_ICON'] = "/acc/sprav/stamps/photo-stamps/" .$prefix.'/'. $stamp['NUMBER'] . "/" . $stamp['PHOTO'];
	  $stamp['CANVAS']="/acc/sprav". $stamp['CANVAS'];
	  
  } else {
      $stamp['CANVAS'] = null;
	  
  }
   $extension = explode('.', $stamp['CANVAS']);
      $extension = $extension[1];
      switch ($extension) {
          case 'cdr':
              $stamp['EXTENSION_ICON'] = '/acc/i/file_icons/cdr_icon.png';
              break;
          case 'ai':
              $stamp['EXTENSION_ICON'] = '/acc/i/file_icons/ai_icon.png';
              break;
          case 'eps':
              $stamp['EXTENSION_ICON'] = '/acc/i/file_icons/eps_icon.png';
              break;
          case 'pdf':
              $stamp['EXTENSION_ICON'] = '/acc/i/file_icons/pdf_icon.png';
              break;
      }
	 if (empty($stamp['EXTENSION_ICON']) ) {
		$stamp['EXTENSION_ICON'] = '/acc/i/who.gif';
		$stamp['EXTENSION_ICON']=null;
	 }
array_push($stamps, $stamp);
unset($stamp);
}
 
//

if (isset($_GET['with_photo']) ) {
  foreach ($stamps as $key => $value) {
    /*if (empty($value['PHOTO'])) {
      unset($stamps[$key]);
    }*/
	if (!empty($value['PHOTO']) && ($value['PHOTO']!= '/acc/i/who.gif')) {
      unset($stamps[$key]);
    }
  }
}

if (isset($_GET['with_canvas']) ) {
  foreach ($stamps as $key => $value) {
    if (!empty($value['CANVAS']) && ($value['CANVAS']!= '/acc/i/who.gif')) {
      unset($stamps[$key]);
    }
  }
}


//users 
$q="SELECT * FROM `users` ORDER BY `uid` ASC";
$r = mysql_query($q);
$mas_name = array();
while ($row = mysql_fetch_row($r))
{
  $mas_name[$row[0]]['name'] = $row[6]." ".$row[5];
}

if (!isset($_GET['edit']))
{
	$destination_img="mini.jpg";
	
//$new_img = compress($value['PHOTO_ICON'], $destination_img, 90);

?>
<div class="title_razd" style="text-align: center;">
  Штампы
</div>
<table style="width: 100%;" id="headings_1" align=center cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
  <tbody>
    <tr class="tab_query_tit"><?php if (($user_access['proizv_access']==1) && ($user_access['proizv_access_type']==2) && ($user_access['proizv_access_edit']==2)){?><td><button onclick='window.location.href="/acc/sprav/stamps/new.php"' style="font-size:16px;">Создать новый штамп</button></td><?php }?></tr>
  </tbody>
</table>
<table id="headings_2" style="width: 100%;" align=center cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
  <tbody>
    <tr class="tab_query_tit">
      <td>
        <div>
          <select id="filter_type" class="stamps_select" onchange="use_filters();">
            <option data-type="0" selected>Тип изделия</option>
            <?

            foreach ($types as $type_key => $type_value) {
              ?>
              <option id="type_<?=$type_value['tid']?>" data-type="<?=$type_value['tid']?>"><?=$type_value['type']?></option>
              <?
            }
            ?>
          </select>
          <input id="filter_number" class="stamps_select_number" type="text" name="filter_number" value="" placeholder="№ штампа" onchange="use_filters();">
          <input id="filter_shir" class="stamps_select_number" type="text" name="filter_shir" value="" placeholder="Ширина" onchange="use_filters();">
          <input id="filter_vis" class="stamps_select_number" type="text" name="filter_vis" value="" placeholder="Высота" onchange="use_filters();">
          <input id="filter_bok" class="stamps_select_number" type="text" name="filter_bok" value="" placeholder="Бок" onchange="use_filters();">
          <input id="filter_photo" type="checkbox" name="filter_with_photo" value="" onchange="use_filters();"><label for="filter_photo" class="stamps_input_descr">без фото</label> 
          <input id="filter_canvas" type="checkbox" name="filter_with_canvas" value="" onchange="use_filters();"><label for="filter_canvas" class="stamps_input_descr">без каркаса</label>
          <input id="filter_vk_r" type="checkbox" name="filter_vk_r" value="" onchange="use_filters();"><label for="filter_vk_r" class="stamps_input_descr">вклееная ручка</label>  
          <select id="filter_sklei" class="stamps_select" onchange="use_filters();">
          <option value="0" selected >Склейка</option>
                                <option value="2" >Боковая склейка</option>
                                <option value="1" >Внутренняя склейка</option>
          </select>
          <a href="/acc/sprav/stamps/">Сбросить фильтры</a>&nbsp;&nbsp;&nbsp;
		  <a href="#" id='link_archive'>Архив</a>&nbsp;&nbsp;&nbsp;
		  <a href="#" id='link_print'>Наклейки</a>
        </div>
      </td>

    </tr>
  </tbody>
</table>
<table id="stamps-table" align=center style="width: 100%;" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <thead>
      <tr class="tab_query_tit">
        <td class="tab_query_tit" align="center">Номер</td>
        <td class="tab_query_tit" align="center">Ширина (см)</td>
        <td class="tab_query_tit" align="center">Высота (см)</td>
        <td class="tab_query_tit" align="center">Бок (см)</td>
        <td class="tab_query_tit" align="center">Размер X (мм)</td>
        <td class="tab_query_tit" align="center">Размер Y (мм)</td>
        <td class="tab_query_tit" align="center">Связь с другим штампом</td>
        <td class="tab_query_tit" align="center">Тип изделия</td>
        <td class="tab_query_tit" align="center">Фото</td>
        <td class="tab_query_tit" align="center">Каркас</td>
        <td class="tab_query_tit" align="center">Склейка</td>
        <td class="tab_query_tit" align="center">Примечание</td>
		<td class="tab_query_tit" align="center">Кто добавил</td>
        <td class="tab_query_tit" align="center">Дата добавления</td>
        <td class="tab_query_tit" align="center">Действия</td>
      </tr>
    </thead>
      <tbody id='list_stamp'>
      <?
	  if (count($stamps)>=1){
      foreach ($stamps as $key => $value) {
        ?>
        <tr id="stamp_cont_<?=$value['ID']?>">
          <td class="tab_td_marg" align="center" data-sort="<?=$value['NUMBER']?>">
		  <input type='checkbox' class='ch_print' data-id-print="<?=$value['ID']?>">
		  <?php if (($user_access['proizv_access']==1) && ($user_access['proizv_access_type']==2) && ($user_access['proizv_access_edit']==2)){?>
			  <span style='color:gray;font-size:13px;'><?=$value['PREFIX']?></span><a href="/acc/sprav/stamps?edit=<?=$value['ID']?>" style="font-weight:bold;"><?=$value['NUMBER']?></a>
			  <?php
		  }else{
			  ?>
			  <span style="font-weight:bold;"><span style='color:gray;font-size:13px;'><?=$value['PREFIX']?></span><?=$value['NUMBER']?></span>
			  <?php
		  }?>
		  </td>
          <td class="tab_td_norm" align="center"><?=$value['SHIR']?></td>
          <td class="tab_td_norm" align="center"><?=$value['VIS']?></td>
          <td class="tab_td_norm" align="center"><?=$value['BOK']?></td>
          <td class="tab_td_norm" align="center"><?=$value['SIZE_X']?></td>
          <td class="tab_td_norm" align="center"><?=$value['SIZE_Y']?></td>
          <td class="tab_td_norm" align="center"><a href="/acc/sprav/stamps?edit=<?=$value['ANOTHER_STAMP']?>"><?=$value['NUM_ANOTHER']?></a></td>
          <td class="tab_td_norm" align="center"><?=$value['IZD_TYPE']?></td>
          <td class="tab_td_norm" align="center">
            <?php if (!empty($value['PHOTO'])) {?>
                <a href="/acc/sprav<?=$value['PHOTO']?>" target="blank">
                <img  style="width: 30px;" src="<?=$value['PHOTO_ICON']?>">
            </a>
            <?php } else { ?>
			<i class="fa-sharp fa-regular fa-image-slash" style="color: #19b834;font-size:21px;"></i>
                <!--<img style="width: 30px;" src="/acc/i/who.gif">-->
            <?php } ?>
          </td>
          <td class="tab_td_norm" align="center">
              <?php if (!empty($value['CANVAS'])) { ?>
              <a href="/acc/sprav<?=$value['CANVAS']?>" target="blank">
                <img style="width: 30px;" src="<?=$value['EXTENSION_ICON']?>">
              </a>
              <?php } else { ?>
                  <!--<img style="width: 30px;" src="/acc/i/who.gif">-->
				  <i class="fa-sharp fa-regular fa-image-slash" style="color: #19b834;font-size:21px;"></i>
              <?php } ?>
          </td>
          <td class="tab_td_norm" align="center"><?=$value['SKLEIKA']?></td>
          <td class="tab_td_norm" align="center"><?=$value['TEXT']?></td>
		  <?php $name_create=$mas_name[$value['CREATE_USER']]['name'];?>
		  <td class="tab_td_norm" align="center"><?=$name_create?></td>
		  <?php $mydate = strtotime($value['CREATED_AT']); ?>
          <td class="tab_td_norm" align="center" data-sort="<?=$mydate?>"><?=$value['CREATED_AT']?></td>
          <td class="tab_td_norm" align="center">
		  <a href="#" class='app_link' id="show_app_<?=$value['ID']?>" data-id="<?=$value['ID']?>"><i class="fa-light fa-shutters" style="color: #000000;font-size:21px;vertical-align:text-bottom;"></i></a>
				<?php if (($user_access['proizv_access']==1) && ($user_access['proizv_access_type']==2) && ($user_access['proizv_access_edit']==2)){?>
				<!--<img class="stamp_delete" id="stamp_delete_<?=$value['ID']?>" data-number="<?=$value['ID']?>" src="/acc/i/del.gif" onclick="deletestamp(this.id);">-->
			 
			  <i class="fa fa-times fa-light fa-shutters stamp_delete" aria-hidden="true" id="stamp_delete_<?=$value['ID']?>" onclick="deletestamp(this.id);" data-number="<?=$value['ID']?>" style="
    float: right;
    color: red;
    font-size: 20px;
    font-weight: 900;
"></i>
				<?php } ?>
          </td>
          <?

        ?>
        </tr>
        <?
      }
	  }else{echo "<td colspan='14' style='text-align:center;'><h3>по вашему запросу ничего не найдено</h3></td>";}
      ?>
    </tbody>

</table>
<div class='modal_app' style='display:none;'><i class="fa fa-times close_modal_link" aria-hidden="true" style="
    float: right;
    color: red;
    font-size: 20px;
    font-weight: 900;
"></i>
<div class='list_application'></div>
</div>
<script>
  function deletestamp(id) {
    var number = document.getElementById(id).getAttribute('data-number');
    //if (confirm('Уверены, что хотите удалить?')) {
      $.ajax({
        url: 'archivestamp.php',
        method: 'POST',
        data: {number: number,tip:1},
        dataType: 'json',
        async: false,
        success: function(data){
			if (data.result==1){
				$('#stamp_cont_' + number).fadeOut();
			}
        }
      });
    //}
  }
 function restart_table(){
    //$("#table_zakaz").DataTable().destroy();
    //$("#table_zakaz").DataTable().draw();
    $("#list_stamp").DataTable().rows().invalidate().draw(false);


  }
  function use_filters() {
	  var url=location.href;
			  let urls = new URL(url);
    filters = '';
    use_filters = 0;
    var type = document.getElementById('filter_type').selectedOptions[0].getAttribute('data-type');
    if (type) {
      if (type !== 0) {
        filters = filters + '&izd_type=' + type;
		urls.searchParams.set('izd_type',type);
      }else{urls.searchParams.delete('izd_type');}
    }else{urls.searchParams.delete('izd_type');}
    //
    
    var sklei = document.getElementById('filter_sklei').value*1;
    if (sklei) {
      if (sklei !== 0) {
        filters = filters + '&sklei=' + sklei;
		urls.searchParams.set('sklei',sklei);
      }else{urls.searchParams.delete('sklei');}
    }else{urls.searchParams.delete('sklei');}
	//
    var number = document.getElementById('filter_number').value;
    if (number) {
      filters = filters + '&number=' + number;
	  urls.searchParams.set('number',number);
    }else{urls.searchParams.delete('number');}
	//
    var vis = document.getElementById('filter_vis').value;
    if (vis) {
      filters = filters + '&vis=' + vis;
	  urls.searchParams.set('vis',vis);
    }else{urls.searchParams.delete('vis');}
	//
    var shir = document.getElementById('filter_shir').value;
    if (shir) {
      filters = filters + '&shir=' + shir;
	  urls.searchParams.set('shir',shir);
    }else{urls.searchParams.delete('shir');}
	//
    var bok = document.getElementById('filter_bok').value;
    if (bok) {
      filters = filters + '&bok=' + bok;
	  urls.searchParams.set('bok',bok);
    }else{urls.searchParams.delete('bok');}
	//
    var photo = document.getElementById('filter_photo').checked;
    if (photo == true) {
      filters = filters + '&with_photo=0';
	  urls.searchParams.set('with_photo',0);
    }else{urls.searchParams.delete('with_photo');}
	//
    var canvas = document.getElementById('filter_canvas').checked;
    if (canvas == true) {
      filters = filters + '&with_canvas=0';
	  urls.searchParams.set('with_canvas',0);
    }else{urls.searchParams.delete('with_canvas');}
	//
    var vk_r=document.getElementById("filter_vk_r").checked;
    if (vk_r == true) {
      filters = filters + '&vk_r=1';
	  urls.searchParams.set('vk_r',0);
    }else{urls.searchParams.delete('vk_r');}
  //
    if (filters !== null && filters !== '') {
      filters = '?use_filters=1' + filters;
	   urls.searchParams.set('use_filters',1);
    }else{urls.searchParams.delete('use_filters');}
   //window.location = filters;
   console.log(urls.href);
   history.pushState(null, null, urls.href);
   $.ajax({
		  type: "GET",
		  dataType : 'html',
		  url: "index_ajax.php"+filters,
		  //data: "search_text="+text_search,
		  success: function(data){
			  //$('#stamps-table').DataTable().clear().destroy();
        $('#stamps-table').DataTable().destroy();
			  $("#list_stamp").html(data);
			 $('#stamps-table').DataTable({
    "scrollX": true,
    "scrollY": false,
    "searching": false,
    "paging": false,
    "autoWidth": false,
    "info": false,
    "orderClasses": true,
    "ordering": true,
    "fixedHeader":true,
	"order": [[ 13, "desc" ]],
  });
			  
			  
			console.log(data);
			
			
			
		}
		//new 
		
		});

  }

  <?
  if (isset($_GET['izd_type'])) {
    $izd_type = $_GET['izd_type'];
    if ($izd_type !== '0') {
      echo "document.getElementById('type_" . $izd_type . "').setAttribute('selected', '');";
    }
  }
  if (isset($_GET['number'])) {
    $number = $_GET['number'];
    echo "document.getElementById('filter_number').value=" . $number . ';';
  }
  if (isset($_GET['shir'])) {
    $shir = $_GET['shir'];
    echo "document.getElementById('filter_shir').value=" . $shir . ';';
  }
  if (isset($_GET['vis'])) {
    $vis = $_GET['vis'];
    echo "document.getElementById('filter_vis').value=" . $vis . ';';
  }
  if (isset($_GET['bok'])) {
    $bok = $_GET['bok'];
    echo "document.getElementById('filter_bok').value=" . $bok . ';';
  }
  if (isset($_GET['with_photo'])) {
    echo "document.getElementById('filter_photo').checked = true;";
  }
  if (isset($_GET['with_canvas'])) {
    echo "document.getElementById('filter_canvas').checked = true;";
  }
  ?>
</script>
<?
} else {
  if ($_GET['edit']) {

    $stamp_id = $_GET['edit'];
    $q = "SELECT * FROM stamps WHERE id = '$stamp_id'";
    $r = mysql_query($q);
    $stamp = mysql_fetch_assoc($r);
    if(!empty($stamp['id'])) {
      include ('edit.php');
    } else {
      header('Location: /acc/sprav/stamps');
    }
 } else {
   header('Location: /acc/sprav/stamps');
 }
}
?>



<script defer type="text/javascript" charset="windows-1251">
function filterTable() {
  if ($('#stamps-table')) {
	  
  $('#stamps-table').DataTable({
    "scrollX": true,
    "scrollY": false,
    "searching": false,
    "paging": false,
    "autoWidth": false,
    "info": false,
    "orderClasses": true,
    "ordering": true,
	"order": [[ 13, "desc" ]],
  });

  }
}

setTimeout(filterTable, 100);
//

$('body').on('input', '#filter_vis', function(){
	//this.value = this.value.replace(/[^0-9\.\,]/g, '').replace(",",".");
	$(this).val($(this).val().replace(/\,/g, '.'));
	$(this).val($(this).val().replace(/(?=(\d+\.\d{2})).+|(\.(?=\.))|([^\.\d])|(^\D)/gi, '$1').replace(",","."));
});
$('body').on('input', '#filter_shir', function(){
	//this.value = this.value.replace(/[^0-9\.\,]/g, '').replace(",",".");
	$(this).val($(this).val().replace(/\,/g, '.'));
	$(this).val($(this).val().replace(/(?=(\d+\.\d{2})).+|(\.(?=\.))|([^\.\d])|(^\D)/gi, '$1').replace(",","."));
});
$('body').on('input', '#filter_bok', function(){
	//this.value = this.value.replace(/[^0-9\.\,]/g, '').replace(",",".");
	$(this).val($(this).val().replace(/\,/g, '.'));
	$(this).val($(this).val().replace(/(?=(\d+\.\d{2})).+|(\.(?=\.))|([^\.\d])|(^\D)/gi, '$1').replace(",","."));
});
$('body').on('click',"#link_archive",function(e){
	 var url=location.href;
			  let urls = new URL(url);
			  if (urls.search==''){tip="?";}else{tip="&";}
	url=urls.protocol+"//"+urls.host+""+urls.pathname+"archive.php"+urls.search+""+tip+"deleted=1";
	window.location.href=url;
	//console.log(url);
});
$('body').on('click',"#link_print",function(e){
	//let arr_print = new Array();
	var str_ids='';
	$('.ch_print').each(function(i){
		if ($(this).prop('checked')==true){
			//console.log($(this).attr('data-id-print'));
			//arr_print.push($(this).attr('data-id-print'));
			//if (str_ids!=''){
			str_ids=str_ids+''+$(this).attr('data-id-print')+'|';
			//}
		}
	});
	if (str_ids!=''){
		//console.log(arr_print);
		window.open('print.php?ids='+str_ids);
		}
});
//
$('body').on('click',".app_link",function(e){
	e.preventDefault();
	 var number = $(this).attr('data-id').split("_");
    //if (confirm('Уверены, что хотите удалить?')) {
      $.ajax({
        url: 'backend/show_application.php',
        method: 'POST',
        data: {id: number[0],izd_type:number[1],tip:1},
        async: false,
        success: function(data){
			console.log(data);
			if (data!=""){
			$(".list_application").html(data);
			$(".modal_app").show();
			}else{
				
			}
        }
      });
});
//open_applist
$('body').on('click',".open_applist",function(e){
	var id_list=$(this).attr('data-id-list');
	window.open('http://test.upak.me/acc/applications/edit.php?uid='+id_list);
});
$('body').on('click',".close_modal_link",function(e){
	$(".list_application").html("");
	$(".modal_app").hide();
});
</script>
<script>
$(document).ready(function(){
	$(".modal_app").hide();
});
</script>

</body>
</html>
<? ob_end_flush(); ?>
