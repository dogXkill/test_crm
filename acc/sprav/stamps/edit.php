<?
// Запрос на типы изделия
if ($user_access['sprav_access'] == '0' || empty($user_access['sprav_access']) || $user_access['proizv_access_edit']=='0') {
  header('Location: /');
}
$types = array();
/*
function check_prefix($mas,$zn){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['tid']==$zn){return $value['prefix'];}
	}
	return false;
}
*/
$q = "SELECT * FROM `types` WHERE vis_stamps = 1 ORDER BY seq DESC";
$r = mysql_query($q);
while ($row = mysql_fetch_row($r))
{
    $type = array();
    $type['tid'] = $row[0];
    $type['type'] = $row[1];
	$type['prefix']=$row[5];

    array_push($types, $type);
    unset($type);
}

// Файлы фотографий
$stamp['photos'] = array();

 if (!empty($stamp['photo'])) {
	$folder_search_photo=explode("/",$stamp['photo']);
	//print_r($folder_search_photo);
	$folders=$folder_search_photo[count($folder_search_photo)-2];
	$stamp['folders']=$folders;
	$path = __DIR__ . '/photo-stamps/' . $folders . '/';
	//echo $path;
	if (is_dir($path)!==false){
	$files = scandir($path);
	foreach ($files as $file_key => $file_name) {
		//echo $file_name;
		if ($file_name !== '.' && $file_name !== '..' && $file_name!='mini.png') {
		   array_push($stamp['photos'], $file_name);
		}
	}
	}
 }


// Файлы штампов

$stamp['canvas1'] = array();
$stamp['canvas_icons1'] = array();

 if (!empty($stamp['karkas'])) {
//$path = __DIR__ . '/canvas-stamps/' . $stamp['number'] . '/';
//$folder_search_photo=explode("/",$stamp['canvas']);
	//print_r($folder_search_photo);
	//$folders=$folder_search_photo[count($folder_search_photo)-2];
	if (empty($folders)){
		$folder_search_photo=explode("/",$stamp['karkas']);
		$folders=$folder_search_photo[count($folder_search_photo)-2];
		$stamp['folders']=$folders;
	}
	$path = __DIR__ . '/canvas-stamps/' . $folders . '/';
	//echo $path;
if (is_dir($path)!==false){
$files = scandir($path);
foreach ($files as $file_key => $file_name) {
    if ($file_name !== '.' && $file_name !== '..') {

        $extension_file = explode('.', $file_name);
        $extension_file = $extension_file[1];
        switch ($extension_file) {
            case 'cdr':
                $canvas_icon = '/acc/i/file_icons/cdr_icon.png';
                break;
            case 'pdf':
                $canvas_icon = '/acc/i/file_icons/pdf_icon.png';
                break;
            case 'ai':
                $canvas_icon = '/acc/i/file_icons/ai_icon.png';
                break;
            case 'eps':
                $canvas_icon = '/acc/i/file_icons/eps_icon.png';
                break;
        }
        if (empty($canvas_icon)) {
            $canvas_icon = '/acc/i/who.gif';
        }

        array_push($stamp['canvas1'], $file_name);
        array_push($stamp['canvas_icons1'], $canvas_icon);

        unset($canvas_icon);
    }
}
}
 }
 if (empty($stamp['folders'])){
	//нет файлов ,создаем папку по ключу к id
	$stamp['folders']=md5(microtime() . rand(0, 9999));
}
$prefix="";
$types_id=$stamp['izd_type'];
if ($types_id!=4){
$prefix=check_prefix($types,$types_id);
}
//обработка связанных (ловим по id номер)
$id_another_stamp=$stamp['another_stamp'];
if ($id_another_stamp!=''){
	if (strpos($id_another_stamp, ",") != false) {//более 1
		$num_anot='';
		$mas_another_id=explode(",",$id_another_stamp);
		foreach ($mas_another_id as $value){
			$q = "SELECT * FROM stamps WHERE id = '$value'";
			$r = mysql_query ($q);
			$arr = mysql_fetch_assoc($r);
			if ($num_anot==''){$num_anot=$arr['number'];}else{$num_anot.=",".$arr['number'];}
		}
	}else{
	  $num_anoth = "SELECT * FROM stamps WHERE id = '{$id_another_stamp}'";
	  $res_num_anoth = mysql_query($num_anoth);
	  $mas_anot = mysql_fetch_assoc($res_num_anoth);
	  $num_anot=$mas_anot['number'];
	}
}else{
	$num_anot='';
}
?>
<style>
    .err_photo {
        font-size: 12px;
    }
    .err_photo:hover {
        text-decoration: underline;
        cursor: pointer;
    }
	/*popup_stamps*/
.wrap {
  width: 100%;
  height: 100%;
  position: fixed;
  left: 0; top: 0; right: 0; bottom: 0;
  display:none;
}

.modal_t1 {
  position: absolute;
  left: 50%;
  top: 50%;
  display: block;
  transform: translate(-50%, -50%);
      z-index: 100;
    background-color: white;
    padding: 10px;
    border: 1px solid rgb(128, 128, 128);
    color: rgb(51, 51, 51);
    border-radius: 15px;
    font-weight: 600;
	width:480px;
	height:300px;
}
.content-modal{font-size:14px;}
.list_stamps{
	width:100%;
	height:240px;
	overflow-y:auto;
	    border: 1px solid #ccc;
    margin-top: 5px;
}
.list_stamps p{
		    padding: 5px;
    margin: 0px;
	font-weight:100;
}
.list_stamps p:hover{
	cursor:pointer;
	background-color:#ffa;
}
p.select_stamps b {
    width: 100px;
    display: inline-block;
}
.select_stamps span {
    width: 115px;
    display: inline-block;
}
#list_stamps_in1,
#list_stamps_in2,
#list_stamps_in3{
	font-size: 13px;
    padding: 6px 0 4px 10px;
    border: 1px solid #cecece;
    border-radius: 4px;
}
.img_list_stamp {
    position: absolute;
    top: 50%;
    left: 50%;
    margin: -125px 0 0 -125px;
    width: 250px;
    height: 250px;
	display:none;
}
.img_list_stamp img {
    width: 250px;
    height: 250px;
}
#error_anot{color:red;}
</style>
<div class="title_razd" style="text-align: center;">
    Редактирование штампа № <?=$stamp['number']?>
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
                        <td><input name="stamp_name" id="stamp-name" size="30" type="text" value="<?=$stamp['name']?>"/></td>
                    </tr>-->
                    <tr>
                        <td align="right">№:</td>
                        <td><span class="err">*</span></td>
                        <td><span id='prefix' style='width:25px;    display: inline-block;'><?php echo $prefix."&nbsp;";?></span><input name="stamp_number" id="stamp-number" type="text" size="30" value="<?=$stamp['number']?>" style="width:175px;height:30px;font-size:20px;" /></td>
                    </tr>
                    <tr>
                        <td align="right">Ширина (см):</td>
                        <td><span class="err">*</span></td>
                        <td><input name="stamp_shir" id="stamp-shir" type="text" size="30" value="<?=$stamp['shir']?>" style="width:200px;height:30px;font-size:20px;" /></td>
                    </tr>
                    <tr>
                        <td align="right">Высота (см):</td>
                        <td><!--<span class="err">*</span>--></td>
                        <td><input name="stamp_vis" id="stamp-vis" type="text" size="30" value="<?=$stamp['vis']?>" style="width:200px;height:30px;font-size:20px;"/></td>
                    </tr>
                    <tr>
                        <td align="right">Бок (см):</td>
                        <td><!--<span class="err" id="side_require"><?= $stamp['izd_type'] === '36' ? '' : '*'?></span>--></td>
                            <?php
                            // для типа штампа "другое" параметр бок необзательный и может быть не заполнен
                            if ($stamp['izd_type'] === '36' && $stamp['bok'] === '0') {
                                    $sideValue = '';
                            } else {
                                    $sideValue = $stamp['bok'];
                            }
                            ?>
                        <td><input name="stamp_bok" id="stamp-bok" type="text" size="30" value="<?=$sideValue?>" style="width:200px;height:30px;font-size:20px;"/></td>
                    </tr>
                    <tr>
                        <td align="right">Размер Х (мм):</td>
                        <td><span class="err">*</span></td>
                        <td><input name="stamp_size_x" id="stamp-size-x" type="text" size="30" value="<?=$stamp['size_x']?>" style="width:200px;height:30px;font-size:20px;"/></td>
                    </tr>
                    <tr>
                        <td align="right">Размер Y (мм):</td>
                        <td><span class="err">*</span></td>
                        <td><input name="stamp_size_y" id="stamp-size-y" type="text" size="30" value="<?=$stamp['size_y']?>" style="width:200px;height:30px;font-size:20px;"/></td>
                    </tr>
                    <tr>
                        <td align="right">Связь с другим штампом (№):</td>
                        <td><span class="err"></span></td>
                        <td><input type='hidden' id='stamp-another-stamp-id' value="<?=$stamp['another_stamp']?>">
						<input name="stamp_another_stamp" id="stamp-another-stamp"  type="text" size="30" value="<?=$num_anot?>" style="width:200px;height:30px;font-size:20px;"/>
						<button type="button" onclick="load_stamp_list();" style="    height: 30px;vertical-align: top;padding-bottom: 3px;padding-top: 3px;margin: 0px;">Реестр</button>
						<p id='error_anot'></p>
						</td>
                    </tr>
                    <tr>
                        <td align="right">Тип изделия:</td>
                        <td><span class="err"></span></td>
                        <td>
                            <select name="stamp_izd_type" class="users_tp" id="stamp-izd-type" style="width:300px;height:30px;font-size:20px;" onchange="change_type(this.value)">
                                 <option value="">не установлен</option>
                                <?
                                $tip_tek=0;
                                foreach ($types as $type => $char) {
                                    if (!empty($stamp['izd_type']) && $stamp['izd_type'] !== '0') {
                                        if ($char['tid'] == $stamp['izd_type']) {
                                            $selected = ' selected';
                                            $tip_tek=$char['tid'];
                                        } else {
                                            $selected = '';
                                        }
                                    } else {
                                        if ($char['tid'] == '4') {
                                            $selected = ' selected';
                                            $tip_tek=4;
                                        } else {
                                            $selected = '';
                                        }
                                    }

                                    ?><option value="<?= $char['tid'] ?>" <?=$selected?>><?=$char['type']?></option><?

                                }
                                ?>

                            </select>
                        </td>
                    </tr>
                    <?php if ($tip_tek==4){$css_dop='table-row';}else{$css_dop='none';}    
                    ?>
                    <tr style="display: <?=$css_dop;?>;" class="type_4_show">
                        <td align='right' ?>Вклееная ручка:</td>
                        <td><span></span></td>
                        <?php 
                            if ($stamp['status_vk_r']==1){$ch_status_vk='checked';$text_status_vk="Да";}else{$ch_status_vk='';$text_status_vk="Нет";}
                        ?>
                        <td><input type='checkbox' id ='status_vk_r' <?=$ch_status_vk;?>><label  for="status_vk_r" id="status_vk_r_text"><?=$text_status_vk;?></label></td>
                    </tr>
                    <tr style="display: <?=$css_dop;?>;" class="type_4_show">
                        <td>Сколько изделий на штампе:</td>
                        <td></td>
                        <td>
                            <select id="kol_izd" style='height:30px;font-size:20px;'>
                                <?php 
								
                                if ($stamp['kol_izd']==0){
                                    echo '<option value="0" selected>Выбрать</option>';
                                    echo '<option value="1" >0.33</option>';
									echo '<option value="2" >0.5</option>';
									 for ($i=2;$i<=13;$i++){
                                        $i_t=$i-1;
                                        
                                        echo "<option value={$i}>{$i_t}</option>";
                                    }
                                }else if ($stamp['kol_izd']==1){
                                    echo '<option value="0" >Выбрать</option>';
									echo '<option value="1" selected >0.33</option>';
                                    echo '<option value="2" >0.5</option>';
									 for ($i=2;$i<=13;$i++){
                                        $i_t=$i-1;
                                        
                                        echo "<option value={$i}>{$i_t}</option>";
                                    }
                                }else if ($stamp['kol_izd']==2){
                                    echo '<option value="0" >Выбрать</option>';
									echo '<option value="1"  >0.33</option>';
                                    echo '<option value="2" selected>0.5</option>';
									for ($i=2;$i<=13;$i++){
                                        $i_t=$i-1;
                                        
                                        echo "<option value={$i}>{$i_t}</option>";
                                    }
                                }else{
                                    echo '<option value="0" >Выбрать</option>';
                                    echo '<option value="1" >0.5</option>';
                                    for ($i=3;$i<=13;$i++){
                                        $i_t=$i-1;
                                        if ($stamp['kol_izd']==$i){echo "<option value={$i} selected>{$i_t}</option>";}else{
                                        echo "<option value={$i}>{$i_t}</option>";}
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr style="display: <?=$css_dop;?>;" class="type_4_show">
                        <td align="right">Склейка:</td>
                        <td></td>
                        <td>
                            <select id="select_skleika" style='height:30px;font-size:20px;'>
                                
                                <option value="0" <?php if ($stamp['skleika']==0){echo 'selected';}?>>Выбрать</option>
                                <option value="2" <?php if ($stamp['skleika']==2){echo 'selected';}?>>Боковая склейка</option>
                                <option value="1" <?php if ($stamp['skleika']==1){echo 'selected';}?>>Внутренняя склейка</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Фото:</td>
                        <td><span class="err"></span></td>
                        <td><input name="stamp_photo" id="stamp-photo" type="file" size="30" style="width:200px;height:30px;font-size:20px;"/></td>
                    </tr>
                    <?
                    if (!empty($stamp['photos']))
                    {
                        $count = 0;
                            foreach ($stamp['photos'] as $file_key => $file_name) {
                                $count = $count + 1;
                            ?>
                            <tr id="photo_cont_<?=$count?>">
                                <td align="right"><span id="photo_<?=$count?>" data-count="<?=$count?>" data-file="/<?=$stamp['folders']?>/<?=$file_name?>" class="err err_photo" onclick="deletephoto(this.id);">удалить</span></td>
                                <td><span></span></td>
                                <td><a href="/acc/sprav/stamps/photo-stamps/<?=$stamp['folders']?>/<?=$file_name?>" target="blank"><img style="height: 70px;" src="/acc/sprav/stamps/photo-stamps/<?=$stamp['folders']?>/<?=$file_name?>"></a></td>
                            </tr>
                            <?
                            }
                    }
                    ?>
                    <tr>
                        <td align="right">Каркас:</td>
                        <td><span class="err"></span></td>
                        <td><input name="stamp_canvas" id="stamp-canvas" type="file" size="30" style="width:200px;height:30px;font-size:20px;"/></td>
                    </tr>
                    <?
                    if (!empty($stamp['canvas1']))
                    {
                        $count = 0;
                            foreach ($stamp['canvas1'] as $file_key => $file_name) {
                                $count = $count + 1;
                            ?>
                            <tr id="canvas_cont_<?=$count?>">
                                <td align="right"><span id="canvas_<?=$count?>" data-count="<?=$count?>" data-file="/<?=$stamp['folders']?>/<?=$file_name?>" class="err err_photo" onclick="deletecanvas(this.id);">удалить</span></td>
                                <td><span></span></td>
                                <td><a href="/acc/sprav/stamps/canvas-stamps/<?=$stamp['folders']?>/<?=$file_name?>" target="blank"><img style="height: 70px;" src="<?=$stamp['canvas_icons1'][$file_key]?>"></a></td>
                            </tr>
                            <?
                            }
                    }
                    ?>
                    <tr>
                        <td align="right">Примечание:</td>
                        <td><span class="err"></span></td>
                        <td><textarea name="stamp_comment" id="stamp-comment" cols="30" rows="5" style="width:200px;height:60px;font-size:20px;"/><?=$stamp['comment']?></textarea></td>
                    </tr>
                </tbody>
                </table>
                        </form>
                        <table>
                            <tbody>
                    <tr>
                        <td align="center">
                            <button class="users_frm_butt" onclick="check_stamp()">Сохранить</button>
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
<div class="wrap">
  <div class="modal_t1" id='modal_shtamp' >
  <span style='    padding-bottom: 8px;    cursor: move;
    display: inline-block;'>Реестр штампов</span><img src="../../i/del.gif" width="20" align="right" height="20" alt="" style="cursor:pointer" onclick="show_hide_modal(this,'hide');">
  <div class='content-modal'>
  
  </div>
  </div>
</div>
<script>
//
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
$('body').on('input', '#stamp-another-stamp', function(){
	var stroka=$(this).val();
	 $(this).val($(this).val().replace (/[А-яа-я]\D/, '').replace(".",",").replace(/,+/g,','));
	 //var res=/[^а-я ]/g.exec($(this).val());
	 //console.log(res);
	 var num_another_stamp=$("#stamp-another-stamp").val();
				
				if (num_another_stamp==''){
					//обнулить id-ки
					$("#stamp-another-stamp-id").val("");
				}
});
 $('#stamp-another-stamp').on('keyup input', function() {
					 
					var t1 = $(this);
					 var t1_d = 1500;
					 clearTimeout($(t1).data('timer'));
					 $(this).data('timer', setTimeout(function(){
					 $(this).removeData('timer');
					 izm_id_anoth();
					 }, t1_d));
					});
});
//
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

        //var shir = Number(stamp.stamp_shir.value);
		var shir=stamp.stamp_shir.value;
		shir= String(shir).replace(",",".");
		shir = Number(shir);
        if (shir == null || shir == '') {
            errors = errors + '\nНе указана ширина! ';
        }
		//shir= String(shir).replace(",",".");
		//shie=Number(shir);
        if (isNaN(shir)) {
            errors = errors + '\nШирина не является числом! ';
        }

        var vis = stamp.stamp_vis.value;
		vis= String(vis).replace(",",".");
		vis = Number(vis);
        /*if (vis == null || vis == '') {
            errors = errors + '\nНе указана высота! ';
        }
        if (isNaN(vis)) {
            errors = errors + '\nВысота не является числом! ';
        }*/
		if (vis != null || vis!=''){
			if (isNaN(vis)) {
				errors = errors + '\nВысота не является числом! ';
			}
		}
        // Бок
        var bok = stamp.stamp_bok.value;
		bok= String(bok).replace(",",".");
		bok = Number(bok);
		if (bok != null || bok!=''){
			if (isNaN(bok)) {
				errors = errors + '\nБок не является числом! ';
			}
		}
		/*if (bok == null || bok == '') {
            errors = errors + '\nНе указан Бок! ';
        }
        if (isNaN(bok)) {
            errors = errors + '\nБок не является числом! ';
        }*/
		/*
        if (!is_another_type(izd_type) && isNaN(bok)) {
            errors = errors + '\nБок не является числом! ';
			console.log(1);
        } else if (is_another_type(izd_type)) {
            if (bok !== '' && !is_integer(bok)) {
                // выбран тип штампа "другое" и чем-то заполнен
                errors = errors + '\nБок не является числом! ';
            }
        }*/

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
       // var re=new RegExp('^[a-zA-Z0-9]+$');
        //if(!re.test(another_stamp) && another_stamp !== ''){
        //errors = errors + '\nНомер штампа не должен содержать кириллицу!';
        //}

        var comment = stamp.stamp_comment.value;

        if (errors == null || errors == '') {

            var file = stamp.stamp_photo.files[0];
            if (file) {
                var formData = new FormData();
                formData.append('photo', file);
				var nums='<? echo $stamp["folders"];?>';
				console.log(nums);
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
			var id_another_stamp=$("#stamp-another-stamp-id").val();
            $.ajax({
                url: 'handler.php',
                method: 'POST',
                data: {id: <?=$stamp['id']?>,nums_folder:nums, number: number, shir: shir, vis: vis, bok: bok, size_x: size_x, size_y: size_y, another_stamp: another_stamp,id_another_stamp:id_another_stamp, izd_type: izd_type, comment: comment,status_vk_r:status_vk_r,skleika_tip:skleika_tip,kol_izd:kol_izd},
                dataType: 'html',
                async: false,
                success: function(data){
                    if (data !== null && data !== '') {
                        alert(data);
                    } else {
						alert("Сохранено");
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

    function deletephoto(id) {
        var file_name = document.getElementById(id).getAttribute('data-file');
        var path = 'photo-stamps' + file_name;
        $.ajax({
            url: 'deletefile.php',
            method: 'POST',
            data: {path: path},
            dataType: 'html',
            async: false,
            success: function(data){
                var count = document.getElementById(id).getAttribute('data-count');
                document.getElementById('photo_cont_' + count).remove();
            }
        });
    }

    function deletecanvas(id) {
        var file_name = document.getElementById(id).getAttribute('data-file');
        var path = 'canvas-stamps' + file_name;
        $.ajax({
            url: 'deletefile.php',
            method: 'POST',
            data: {path: path},
            dataType: 'html',
            async: false,
            success: function(data){
                var count = document.getElementById(id).getAttribute('data-count');
                document.getElementById('canvas_cont_' + count).remove();
            }
        });

    }
	//$('body').on('input', '#stamp-bok', function(){
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
	function show_hide_modal(obj,tipe){
			console.log($(obj).parent('.wrap'));
			if (tipe=='hide'){
				$(obj).parents('.wrap').hide();
			}else if(tipe=='show'){
				$(obj).parents('.wrap').show();
			}
		}
	function load_stamp_list(){
			var tip_izd=$("#stamp-izd-type").val();
			
			$.ajax({
			 url: '../../applications/backend/show_list_stamp.php',
			 data: 'type=4',
			 dataType: 'html',
			 type: 'GET',
			 success: function(html) {
				 $("#modal_shtamp").find(".content-modal").html(html);
				 
				 //
				 show_hide_modal('#modal_shtamp','show');
				 //
				 $('#list_stamps_in1').on('keyup input', function() {
					 var t1 = $(this);
					 var t1_d = 500;
					 clearTimeout($(t1).data('timer'));
					 $(this).data('timer', setTimeout(function(){
					 $(this).removeData('timer');
					 change_sthamp();
					 }, t1_d));

					});
				 $('#list_stamps_in2').on('keyup input', function() {
					  var $this = $(this);
					 var $delay = 500;
					 clearTimeout($this.data('timer'));
					 this.data('timer', setTimeout(function(){
					 $this.removeData('timer');
					 change_sthamp();
					 }, $delay));
				 });
				 $('#list_stamps_in3').on('keyup input', function() {
					  var $this = $(this);
					 var $delay = 500;
					 clearTimeout($this.data('timer'));
					 this.data('timer', setTimeout(function(){
					 $this.removeData('timer');
					 change_sthamp();
					 }, $delay));
				 });
					 /*
					 $( ".list_stamps p" ).show();
					$( ".list_stamps p" ).each(function( index ) {
						strq=String($(this).data('num'));
						if (strq.indexOf($("#list_stamps_in1").val())==-1){
							$(this).hide();
						}
					});
					*/
					
				 //
			 }
			});
		}
		function change_sthamp(){
			$( ".list_stamps p" ).show();
			$( ".list_stamps p" ).each(function( index ) {
				strq=String($(this).data('num'));
				strq1=String($(this).data('vis'));
				strq2=String($(this).data('sh'));
				//console.log("strq:"+strq+":::"+strq.indexOf($("#list_stamps_in1").val()));
				///console.log("strq1:"+strq1+":::"+strq1.indexOf($("#list_stamps_in3").val()));
				//console.log("strq2:"+strq2+":::"+strq2.indexOf($("#list_stamps_in2").val()));
				if ((strq.indexOf($("#list_stamps_in1").val())==-1) ){
							$(this).hide();
				}
				if (strq1.indexOf($("#list_stamps_in3").val())==-1){$(this).hide();}
				if (strq2.indexOf($("#list_stamps_in2").val())==-1){
									$(this).hide();
								}
			});
		}
		$(document).on('click','.list_stamps p',function(){
			var num_another_stamp=$("#stamp-another-stamp").val();
				
				if (num_another_stamp==''){
					//обнулить id-ки
					$("#stamp-another-stamp-id").val("");
					//
					if (num_another_stamp==''){num_another_stamp=$(this).data('num');}else{num_another_stamp=num_another_stamp+","+$(this).data('num');}
					$("#stamp-another-stamp").val(String(num_another_stamp));
					var id_another_stamp=$("#stamp-another-stamp-id").val();
					if (id_another_stamp==''){id_another_stamp=$(this).data('id');}else{id_another_stamp=id_another_stamp+","+$(this).data('id');}
					$("#stamp-another-stamp-id").val(String(id_another_stamp));
					//
				}else{
					//проверка на повторный клик(удаление)
					var mas_zn=num_another_stamp.split(",");
					var id_another_stamp=$("#stamp-another-stamp-id").val();
					var mas_zn1=id_another_stamp.split(",");
					res= mas_zn.indexOf(String($(this).data('num')));
					if (res==-1){
						//не найден
						if (num_another_stamp==''){num_another_stamp=$(this).data('num');}else{num_another_stamp=num_another_stamp+","+$(this).data('num');}
					$("#stamp-another-stamp").val(String(num_another_stamp));
					var id_another_stamp=$("#stamp-another-stamp-id").val();
					if (id_another_stamp==''){id_another_stamp=$(this).data('id');}else{id_another_stamp=id_another_stamp+","+$(this).data('id');}
					$("#stamp-another-stamp-id").val(String(id_another_stamp));
					}else{
						//найден (удаляем из массивов и выводим новое)
						res1= mas_zn1.indexOf(String($(this).data('id')));
						 mas_zn.splice(res, 1);
						 mas_zn1.splice(res1, 1);
						 //console.log(mas_zn);
						 //console.log(mas_zn1);
						 var str1='';
						 var str2='';
						 for (var i=0;i<mas_zn.length;i=i+1){if (str1==''){str1=mas_zn[i];}else{str1=str1+","+mas_zn[i];}}
						 for (var i=0;i<mas_zn1.length;i=i+1){if (str2==''){str2=mas_zn1[i];}else{str2=str2+","+mas_zn1[i];}}
						 $("#stamp-another-stamp").val(String(str1));
						 $("#stamp-another-stamp-id").val(String(str2));
					}
					
				}
				
				
				
				show_hide_modal('#modal_shtamp','hide');
				
		 });
		  $(document).on('click','.list_stamps p a',function(){
			  e.preventDefault();
		  });
		 $(document).on('mouseover','.list_stamps p',function(){
			 
			 var img_src=String($(this).data('img'));
			 if (img_src!=""){
			 console.log(img_src);
			 $(".img_list_stamp").find('img').attr('src',img_src);
			 $(".img_list_stamp").show();
			 }
		 });
		  $(document).on('mouseout','.list_stamps p',function(){
			 
			 $(".img_list_stamp").find('img').attr('src',"");
			 $(".img_list_stamp").hide();
		 });
		 function izm_id_anoth(){
			 $.ajax({
			 url: '../../applications/backend/sear_stamps.php',
			 data: 'num_stamps='+$("#stamp-another-stamp").val().replace(',,',','),
			 dataType: 'json',
			 type: 'POST',
			 success: function(html) {
				 if (html.error!=''){
					 $("#stamp-another-stamp-id").val(html.id);
					 $("#error_anot").html('Не найдено:'+html.error+'(не будут сохранены)');
				 }else{
					 $("#error_anot").html('');
					 $("#stamp-another-stamp-id").val(html.id);
					 $("#stamp-another-stamp").val(html.num);
				}
				 
			 }
			 });
		 }
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
</script>
<script src="../../includes/js/stamps.js"></script>