<?

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // прошлая дата
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
if ($user_access['sprav_access'] == '0' || empty($user_access['sprav_access']) || $user_access['proizv_access_edit']=='0') {
  header('Location: /');
}

// Запрос на типы изделия

$types = array();

$q = "SELECT * FROM `types` WHERE vis_stamps = 1 ORDER BY seq DESC";
$r = mysql_query($q);
while ($row = mysql_fetch_row($r))
{
  $type = array();
  $type['tid'] = $row[0];
  $type['type'] = $row[1];

  array_push($types, $type);
  unset($type);
}

	$stamp['folders']=md5(microtime() . rand(0, 9999));

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html" charset="windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.js"></script>
<script src="../../includes/js/jquery.cookie.js"></script>
<style type="text/css">

.spans{
border: 1px solid #336699;
background-color:white;
padding: 10px;
display:none;
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

<div class="title_razd" style="text-align: center;">
Создание штампа
</div>
<table align=center width="1200" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
  <tbody>
    <tr class="tab_query_tit"><td align="center"><button onclick='window.location.href="/acc/sprav/stamps/"' style="font-size:16px;">Список всех штампов</button></td></tr>
  </tbody>
</table>
<table id="stamp-edit-table" align=center width="1200" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
  <td align="center">
    <form action="" name="stampform" method="post" id="stamp-edit-form" enctype="multipart/form-data">
      <table cellspacing="3" cellpadding="4" border="0">
        <tbody>
        <!--  <tr>
            <td align="right">Название штампа:</td>
            <td><span class="err">*</span></td>
            <td><input name="stamp_name" id="stamp-name" size="30" type="text" value=""/></td>
          </tr>-->
          <tr>
            <td align="right">№:</td>
            <td><span class="err">*</span></td>
            <td><span id='prefix' style='width:25px;    display: inline-block;'></span><input name="stamp_number" id="stamp-number" type="text" size="30" value="" style="width:175px;height:30px;font-size:20px;"/></td>
          </tr>
          <tr>
            <td align="right">Ширина (см):</td>
            <td><span class="err">*</span></td>
            <td><input name="stamp_shir" id="stamp-shir" type="text" size="30" value="" style="width:200px;height:30px;font-size:20px;"/></td>
          </tr>
          <tr>
            <td align="right">Высота (см):</td>
            <td><!--<span class="err">*</span>--></td>
            <td><input name="stamp_vis" id="stamp-vis" type="text" size="30" value="" style="width:200px;height:30px;font-size:20px;"/></td>
          </tr>
          <tr>
            <td align="right">Бок (см):</td>
            <td><!--<span class="err" id="side_require">*</span>--></td>
            <td><input name="stamp_bok" id="stamp-bok" type="text" size="30" value="" style="width:200px;height:30px;font-size:20px;"/></td>
          </tr>
          <tr>
            <td align="right">Размер Х (мм):</td>
            <td><span class="err">*</span></td>
            <td><input name="stamp_size_x" id="stamp-size-x" type="text" size="30" value="" style="width:200px;height:30px;font-size:20px;"/></td>
          </tr>
          <tr>
            <td align="right">Размер Y (мм):</td>
            <td><span class="err">*</span></td>
            <td><input name="stamp_size_y" id="stamp-size-y" type="text" size="30" value="" style="width:200px;height:30px;font-size:20px;"/></td>
          </tr>
          <tr>
            <td align="right">Связь с другим штампом (№):</td>
            <td><span class="err"></span></td>
            <td><input name="stamp_another_stamp" id="stamp-another-stamp" type="text" size="30" value="" style="width:200px;height:30px;font-size:20px;"/></td>
          </tr>
          <tr>
            <td align="right">Тип изделия:</td>
            <td><span class="err"></span></td>
            <td>
              <select name="stamp_izd_type" class="users_tp" id="stamp-izd-type" style="width:300px;height:30px;font-size:20px;" onchange="change_type(this.value)">
                <?
                foreach ($types as $type => $char) {
                  if ($char['tid'] == '4') {
                    $selected = ' selected';
                  } else {
                    $selected = '';
                  }
                  ?><option value="<?= $char['tid'] ?>" <?=$selected?>><?=$char['type']?></option><?

                }
                ?>
              </select>
            </td>
          </tr>
                    <tr  class="type_4_show">
                        <td align='right' ?>Вклееная ручка:</td>
                        <td><span></span></td>
                        <td><input type='checkbox' id ='status_vk_r' ><label  for="status_vk_r" id="status_vk_r_text">Нет</label></td>
                    </tr>
                    <tr  class="type_4_show">
                        <td>Сколько изделий на штампе:</td>
                        <td></td>
                        <td>
                            <select id="kol_izd">
                                <?php 
                                  echo '<option value="0" selected>Выбрать</option>';
                                  echo '<option value="1" >0.5</option>';
                                    for ($i=2;$i<=13;$i++){
                                        $i_t=$i-1;
                                        
                                        echo "<option value={$i}>{$i_t}</option>";
                                    }
                                
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr  class="type_4_show">
                        <td align="right">Склейка:</td>
                        <td></td>
                        <td>
                            <select id="select_skleika">
                                
                                <option value="0" >Выбрать</option>
                                <option value="2" >Боковая склейка</option>
                                <option value="1" >Внутренняя склейка</option>
                            </select>
                        </td>
                    </tr>
          <tr>
            <td align="right">Фото:</td>
            <td><span class="err"></span></td>
            <td><input name="stamp_photo" id="stamp-photo" type="file" size="30" style="width:200px;height:30px;font-size:20px;"/></td>
          </tr>
          <tr>
            <td align="right">Каркас:</td>
            <td><span class="err"></span></td>
            <td><input name="stamp_canvas" id="stamp-canvas" type="file" size="30" style="width:200px;height:30px;font-size:20px;"/></td>
          </tr>

          <tr>
            <td align="right">Примечание:</td>
            <td><span class="err"></span></td>
            <td><textarea name="stamp_comment" id="stamp-comment" cols="30" rows="5" style="width:200px;height:60px;font-size:20px;"/></textarea></td>
          </tr>
        </tbody>
        </table>
            </form>
            <table>
              <tbody>
          <tr>
            <td align="center">
              <button class="users_frm_butt" onclick="check_stamp()">Создать</button>
            </td>
            <td><span></span></td>
            <td>
              <button class="users_frm_butt" onclick="backtolist()">Отмена</button>
            </td>
            </td>
          </tr>
        </tbody>
      </table>

  </td>
</table>

<script>
$(document).ready(function() {
$('body').on('input', '#stamp-number', function(){
	 $(this).val($(this).val().replace (/[А-Яа-я]\D/, '').replace(",","").replace(".",""));	
});
$('body').on('input', '#stamp-shir', function(){
	//this.value = this.value.replace(/[^0-9\.\,]/g, '').replace(",",".");
	$(this).val($(this).val().replace(/\,/g, '.'));
	$(this).val($(this).val().replace(/(?=(\d+\.\d{2})).+|(\.(?=\.))|([^\.\d])|(^\D)/gi, '$1').replace(",","."));
	
});
$('body').on('input', '#stamp-vis', function(){
	//this.value = this.value.replace(/[^0-9\.\,]/g, '').replace(",",".");
	$(this).val($(this).val().replace(/\,/g, '.'));
	$(this).val($(this).val().replace(/(?=(\d+\.\d{2})).+|(\.(?=\.))|([^\.\d])|(^\D)/gi, '$1').replace(",","."));
});
$('body').on('input', '#stamp-bok', function(){
	//$("#stamp-bok").on("input",function(e){
	//this.value = this.value.replace(/[^0-9\.\,]/g, '').replace(",",".");
	$(this).val($(this).val().replace(",","."));
	$(this).val($(this).val().replace(/\,/g, '.'));
	$(this).val($(this).val().replace(/(?=(\d+\.\d{2})).+|(\.(?=\.))|([^\.\d])|(^\D)/gi, '$1').replace(",","."));
});
});
  // проверка заполнения полей
  function check_stamp() {

    var errors = '';
    var stamp = document.stampform;

  /*  var name = stamp.stamp_name.value;
    if (name == null || name == '') {
      errors = errors + 'Не указано название! ';
    }*/

    var number = stamp.stamp_number.value;
    var izd_type = stamp.stamp_izd_type.value;

    var re=new RegExp('^[a-zA-Z0-9]+$');
    if(!re.test(number) && number !== ''){
    errors = errors + '\nНомер штампа не должен содержать кириллицу!';
    }

    if (number == null || number == '') {
      errors = errors + '\nНе указан номер штампа! ';
    }

    var shir = Number(stamp.stamp_shir.value);
    if (shir == null || shir == '') {
      errors = errors + '\nНе указана ширина! ';
    }
    if (isNaN(shir)) {
      errors = errors + '\nШирина не является числом! ';
    }

    var vis = Number(stamp.stamp_vis.value);
	/*
    if (vis == null || vis == '') {
      errors = errors + '\nНе указана высота! ';
    }
    if (isNaN(vis)) {
      errors = errors + '\nВысота не является числом! ';
    }
	*/
	if (vis != null || vis!=''){
			if (isNaN(vis)) {
				errors = errors + '\nВысота не является числом! ';
			}
		}
    // Бок
	var bok = stamp.stamp_bok.value;
		bok= String(bok).replace(",",".");
		bok = Number(bok);
		/*
		if (bok == null || bok == '') {
            errors = errors + '\nНе указан Бок! ';
        }
        if (isNaN(bok)) {
            errors = errors + '\nБок не является числом! ';
        }*/
		if (bok != null || bok!=''){
			if (isNaN(bok)) {
				errors = errors + '\nБок не является числом! ';
			}
		}
	/*
    var bok = stamp.stamp_bok.value;

    if (!is_another_type(izd_type) && !is_integer(bok)) {
      errors = errors + '\nБок не является числом! ';
    } else if (is_another_type(izd_type)) {
      if (bok !== '' && !is_integer(bok)) {
        // выбран тип штампа "другое" и чем-то заполнен
        errors = errors + '\nБок не является числом! ';
      }
    }
	*/
    var size_x = Number(stamp.stamp_size_x.value);
    if (size_x == null || size_x == '') {
      errors = errors + '\nНе указан Размер X! ';
    }
    if (isNaN(size_x)) {
      errors = errors + '\nРазмер X не является числом! ';
    }

    var size_y = Number(stamp.stamp_size_y.value);
    if (size_y == null || size_y == '') {
      errors = errors + '\nНе указан Размер Y! ';
    }
    if (isNaN(size_y)) {
      errors = errors + '\nРазмер Y не является числом! ';
    }

    var another_stamp = stamp.stamp_another_stamp.value;
    var re=new RegExp('^[a-zA-Z0-9]+$');
    if(!re.test(another_stamp) && another_stamp !== ''){
        errors = errors + '\nНомер штампа не должен содержать кириллицу!';
    }

    var comment = stamp.stamp_comment.value;

    if (errors == null || errors == '') {

      var file = stamp.stamp_photo.files[0];
      if (file) {
        var formData = new FormData();
        formData.append('photo', file);
		var nums='<? echo $stamp["folders"];?>';
        var url_params = 'photohandler.php?number=' +nums+'';
        $.ajax({
          url: url_params,
          data: formData,
          processData: false,
          contentType: false,
          type: 'POST',
          async: false,
          success: function(data) {
            if (data !== null && data !== '') {
              alert(data);
            }
          }
        });
      } else {

      }

      var canvas = stamp.stamp_canvas.files[0];
      if (canvas) {
        var formData = new FormData();
        formData.append('canvas', canvas);
		var nums='<? echo $stamp["folders"];?>';
        var url_params = 'canvashandler.php?number=' +nums+'';
        $.ajax({
          url: url_params,
          data: formData,
          processData: false,
          contentType: false,
          type: 'POST',
          async: false,
          success: function(data) {
            if (data !== null && data !== '') {
              alert(data);
            }
          }
        });
      }

		var nums='<? echo $stamp["folders"];?>';
    if ($("#status_vk_r").is(':checked')==true){var status_vk_r=1;}else{var status_vk_r=0;}
            var skleika_tip=$("#select_skleika").val();
            var kol_izd=$("#kol_izd").val();
      $.ajax({
        url: 'handler.php',
        method: 'POST',
        data: {new: 1, number: number,nums_folder:nums,shir: shir, vis: vis, bok: bok, size_x: size_x, size_y: size_y, another_stamp: another_stamp, izd_type: izd_type, comment: comment,status_vk_r:status_vk_r,skleika_tip:skleika_tip,kol_izd:kol_izd},
        dataType: 'html',
        async: false,
        success: function(data){
          if (data !== null && data !== '') {
            alert(data);
          } else {
            alert("Создано");
			setTimeout(() => {window.location = '/acc/sprav/stamps/'}, 1000);
          }
        }
      });

    } else {
      alert(errors);
      return false
    }
    errors == '';

  }

  function backtolist() {
    window.location = "/acc/sprav/stamps/";
  }
	$("body").on('change','#stamp-izd-type',function(e){
		//
    if ($(this).val()==4){
            $(".type_4_show").show();
        }else{
            $(".type_4_show").hide();
        }
		$.ajax({
            url: 'backend/stamp_info.php',
            method: 'POST',
            data: {id: $(this).val()},
            dataType: 'html',
            async: false,
            success: function(data){
               $("#prefix").html(data);
            }
        });
	});
  //status_vk_r
  $("body").on('change','#status_vk_r',function(e){
        if ($(this).is(':checked')==true){$("#status_vk_r_text").text("Да");}else{$("#status_vk_r_text").text("Нет");}
    });
</script>
<script src="../../includes/js/stamps.js"></script>
</body>
</html>
<? ob_end_flush(); ?>
