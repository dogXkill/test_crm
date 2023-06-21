 <?
 require_once("../../includes/db.inc.php");
$str = $_SERVER['QUERY_STRING'];
parse_str($str);

//обрабатываем произвольные наклейки
if($type == 'by_art_id'){

$get_uid = mysql_query("SELECT title, col_in_pack FROM plan_arts WHERE art_id = '$art_id'");
$get_uid = mysql_fetch_array($get_uid);
echo mysql_error();

echo $get_uid['title'].";".$get_uid['col_in_pack'];

}else{
?>

<html>
<head>
	<title>наклейка</title>
<script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
</head>

<body>
<?
$app = mysql_fetch_assoc(mysql_query("SELECT * FROM applications WHERE num_ord = '$num_ord'"));
echo mysql_error();

$izd_w = $app[izd_w];
$izd_v = $app[izd_v];
$izd_b = $app[izd_b];
$izd_color = $app[izd_color];
$izd_material = $app[izd_material];
$izd_lami = $app[izd_lami];
$art_id = $app[art_id];
$ClientName = $app[ClientName];
$app_type = $app[app_type];
$izd_type = $app[izd_type];
$col_in_pack = $app[col_in_pack];

//для формирования заголовка, получаем типы изделий в массив
$type = mysql_query("SELECT * FROM types");
while ( $row = mysql_fetch_array($type) ) {
$types[$row[0]] = $row[1];
}

//для формирования заголовка, получаем материалы в массив
$color = mysql_query("SELECT * FROM colours");
while ( $row = mysql_fetch_array($color)){$colors[$row[0]] = $row[1];}

//для формирования заголовка, получаем материалы в массив
$material = mysql_query("SELECT * FROM materials");
while ( $row = mysql_fetch_array($type)){$materials[$row[0]] = $row[1];}

//ламинация
if($izd_lami == "1"){$lam_text = " матовый";}
if($izd_lami == "2"){$lam_text = " глянцевый";}

//print_r($types);
if($izd_w > 0){$size = $izd_w;}
if(is_numeric($izd_v) and $izd_v !== "0"){$size .= "x".$izd_v;}
if(is_numeric($izd_b) and $izd_b !== "0"){$size .= "x".$izd_b;}
if($app_type == '2'){
$nakl_title = "<u>Арт.".$art_id."</u><br>";
$nakl_text = $types[$izd_type].", ".$size.", ".$colors[$izd_color]."".$materials[$izd_material]."".$lam_text;
}else{
$nakl_title = "<u>".$ClientName."</u><br>";
$nakl_text = $types[$izd_type].", ".$size.", ".$colors[$izd_color]."".$materials[$izd_material]."".$lam_text;
}

if ($qty == "30"){
$h1_size = "45";
$h3_size = "18";
$text_size = "15";
$rows = "10";
$cols = "3";
}
if ($qty == "21"){
$h1_size = "45";
$h3_size = "18";
$text_size = "15";
$rows = "7";
$cols = "3";
}
if ($qty == "15"){
$h1_size = "45";
$h3_size = "18";
$text_size = "15";
$rows = "5";
$cols = "3";
}
if ($qty == "9"){
$h1_size = "65";
$h3_size = "25";
$text_size = "20";
$rows = "3";
$cols = "3";
}
if ($qty == "4"){
$h1_size = "155";
$h3_size = "55";
$text_size = "35";
$rows = "2";
$cols = "2";
}
if ($qty == "2"){
$h1_size = "225";
$h3_size = "75";
$text_size = "45";
$rows = "1";
$cols = "1";
}
if ($qty == "1"){
$h1_size = "275";
$h3_size = "95";
$text_size = "45";
$rows = "1";
$cols = "1";
}

$tek_date = date("d.m.Y");
?>

<style type="text/css">
    <!--
h1{
font-size: <?=$h1_size;?>px;
font-weight: bold;
}
h3{
font-size: <?=$h3_size;?>px;
font-weight: bold;
}

td {
  font-size: <?=$text_size;?>px;
	border: dashed;
	border-width: 1px;
}
-->
</style>

<script>
function manual_nakl(){
    $("#programmed").toggle();
    $("#not_programmed").toggle();
    $("#art_id_general_span").toggle();

    $("#art_id_general_inp").focus();
}


function get_data(id){
               console.log(id)
    if(id !== ""){art_id = $("#input_"+id).val();}else{art_id = $("#art_id_general_inp").val();}


    num_ord= $("#num_ord_input_"+id).val();

            var save;
            save = $.ajax({
            type: "GET",
            url: 'index.php?type=by_art_id',
        	data : '&art_id='+art_id,
            success: function () {
            var resp = save.responseText

                resp = resp.split(";")
                title = resp[0];
                packed = resp[1];

                if(id !== ""){
                    $("#art_id_"+id).html(art_id);
                    $("#title_"+id).html(title);
                    $("#descr_"+id).show();
                    $("#input_span_"+id).hide();
                    $("#num_ord_input_"+id).hide();
                    if(num_ord){$("#num_ord_"+id).html(num_ord);}else{$("#num_ord_"+id).html("___________");}
                    if(packed){$("#pack_"+id).html(packed);}else{$("#pack_"+id).html("___________");}

                }else{
                   $(".fill_art_id").html(art_id);
                   $(".fill_title").html(title);
                   $(".descr").show();
                   $(".input_span").hide();
                   $(".num_ord_input").hide();
                   $(".num_ord_span").hide();
                   if(packed){$(".fill_pack").html(packed);}else{$(".fill_pack").html("___________");}

                }

}})


    }
</script>
<span onclick="manual_nakl()" style="font-size:13px; text-decoration:underline; cursor:pointer; padding: 7px;">произвольные наклейки</span><br>
<span id="art_id_general_span" style="display:none; border-style:dashed; border-width: 1px; padding: 7px; top: 15px; position:relative;">Введите номер артикула для всех наклеек, либо введите номер в каждое отдельное поле
<input type="text" size="5" id="art_id_general_inp" /> <input type="submit" value="OK" onclick="get_data('')"/></span>
<div id=programmed style="top: 15px; position:relative;">
<table width=1000 height=90% cellpadding="5" cellspacing=0 >
<?for ($j = 0; $j < $rows; $j++){?>
<tr>
<?for ($i = 0; $i < $cols; $i++) {?>
<td align=center>

<h1><?=$nakl_title;?></h1>
<h3><?=$nakl_text;?></h3>

Упаковка по <? if ($col_in_pack){echo $col_in_pack." шт";}else{?> ____________ шт<?}?>
<br>
заявка: <?=$num_ord;?>
</td><?}?>
</tr> <?}?>
</table>
</div>

<div id=not_programmed style="display:none;top: 25px; position:relative;">
<table width=999 height=100% cellpadding="5" cellspacing=0>
<?for ($j = 0; $j < $rows; $j++){?>
<tr>
<?for ($i = 0; $i < $cols; $i++) {?>
<td align=center width=333>
<span id="input_span_<?=$j."_".$i;?>" class="input_span" style="display:block">
арт. <input type="text" size="5" id="input_<?=$j."_".$i;?>" value=""/>
заявка # <input type="text" size="5" id="num_ord_input_<?=$j."_".$i;?>" value=""/>
<input type="submit" value="OK" onclick="get_data('<?=$j."_".$i;?>')"/>
</span>

<span id="descr_<?=$j."_".$i;?>" class="descr" style="display:none;">
<h1>Арт. <span id="art_id_<?=$j."_".$i;?>" class="fill_art_id"></span></h1>
<h3><span id="title_<?=$j."_".$i;?>" class="fill_title"></span></h3>
Упаковка по <span id="pack_<?=$j."_".$i;?>" class="fill_pack"></span> шт
<br>
<span class="num_ord_span">заявка: <span id="num_ord_<?=$j."_".$i;?>" class="num_ord"></span></span>
</span>

</td><?}?>
</tr> <?}?>
</table>
</div>


</body>
</html><?}?>
