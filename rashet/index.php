<? require_once("../acc/includes/db_calc.inc.php");    ?>

<html>

<head>
<title>Untitled</title>


  <style type="text/css">
  <!--
  .calc_tbl{
    font-family: arial;
    font-size:14px;
  }
  .calc_text{
    font-family: arial;
    font-size:14px;
    width: 150px;
    height: 30px;
  }
  .calc_err{
    font-family: arial;
    font-size:12px;
    color:red;
  }
  .calc_sm{
    font-family: arial;
    font-size:10px;
    color: #808080;
    font-style: italic;
  }
  .cals_sl_1{
    font-family: arial;
    font-size:14px;
    width: 150px;
    height: 30px;
  }
  .cals_sl_2{
    font-family: arial;
    font-size:14px;
    width: 300px;
    height: 30px;
  }
  .calc_vid{
    font-family: arial;
    font-size:8px;
    color: #808080;

  }
.calc_bag_view_title{
   border: 1px solid #A0A0A0;
    top:-10px;
    font-size:14px;
    font-family:arial;
    background-color:#FFFFFF;
    border-radius: 4px;
    height:10px;
    padding: 4px;
    position: relative;
    left:20px;
}
.calc_bag_view{
   border: 1px solid #A0A0A0;
   border-radius: 4px;
   width:650px;
   height:350px;
   margin: 4px;
   padding:4px;
}
.calc_create_obj{
   top:4px;
   left:620px;
   background-image: url(img/create.png);
   background-repeat: no-repeat;
   width:24px;
   height:24px;
   position: relative;
   cursor:pointer;
}
.calc_obj_title{
   border: 1px dashed #A0A0A0;
    top:-10px;
    font-size:14px;
    font-family:arial;
    background-color:#FFFFFF;
    border-radius: 4px;
    height:10px;
    padding: 4px;
    position: relative;
    left:20px;
}
.calc_obj{
   border: 1px dashed #A0A0A0;
   border-radius: 4px;
   width:650px;
   height: 480px;
   margin: 4px;
   padding:4px;
}

.calc_link{
  width: 205px;
  font-family: arial;
  font-size:16px;
  text-decoration: underline;
  color: #0095EB;
  cursor:pointer;
  float: middle;
  display:table;
}
.calc_link:hover{
  width: 205px;
  font-family: arial;
  font-size:16px;
  text-decoration: underline;
  color: #101010;
  cursor:pointer;
  float: middle;
  display:table;
}

.calc_link_box{
   width: 209px;
   height: 209px;
   position: relative;
   text-align: center;
   float: left;
   display:table;
   border: 1px solid #DBDBDB;
}
.calc_link_box:hover{
   width: 209px;
   height: 209px;
   position: relative;
   text-align: center;
   float: left;
   display:table;
   border: 1px solid #909090;
   background-color: #EFEFEF;
   color: #101010;
}

.draggable
 {
   width: 210px;
   height: 210px;
   position: relative;
   cursor:move;
   text-align: center;
   float: left;
   display:table;

 }
 .child{
  display:table-cell;vertical-align: middle;
 }


 .draggable:hover
 {
   width: 210px;
   height: 210px;
   position: relative;
   cursor:move;
   text-align: center;
   background-color: #EFEFEF;
   background-image: url(rotate.png);
   background-repeat: no-repeat;
 }



 .draggable_new_obj
  {
   width: 210px;
   height: 210px;
   position: relative;
   cursor:move;
   text-align: center;
   float: left;
   display:table;
 }

.box{white-space:nowrap;}

.ui-rotatable-handle {
    height: 210px;
    width: 18px;
    left: 0px;
    left: 2px;
    top: 2px;
    position:absolute;
}
.draggable_descr{
    height: 210px;
    width: 210px;
    font-family: arial;
    font-size:10px;
    color: #EFEFEF;
    top:2px;
    position:absolute;
}
.draggable_descr:hover
{
    height: 210px;
    width: 210px;
    font-family: arial;
    font-size:10px;
    color: #909090;
    top:2px;
    position:absolute;
}




  -->
  </style>

<script type="text/javascript" src="../acc/includes/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../acc/includes/js/jquery-ui.js"></script>
<script src="rotatable.js"></script>
<script type="text/javascript">
$(function() {
    $( "#draggable" ).draggable().rotatable();
    $( "#draggable1" ).draggable().rotatable();
    $( "#draggable2" ).draggable().rotatable();
    $( "#draggable3" ).draggable().rotatable();
    $( "#draggable4" ).draggable().rotatable();
    $( "#draggable5" ).draggable().rotatable();
    $( "#draggable6" ).draggable().rotatable();
    $( "#draggable7" ).draggable().rotatable();
});


function preview(){
//получаем размеры пакета
size = $("#bag_sizes option:selected").val();
size = size.split(';')
size = size[1].split('x')
width = size[0]
height = size[1]
bok = size[2]
$( "#lico-size" ).html(width+"х"+height+"см")
$( "#bok-size" ).html(bok+"х"+height+"см")
//меняем масштаб
k = 5
new_width = (width*k).toFixed()
new_height = (height*k).toFixed()
new_bok = bok*k.toFixed()
$('#lico').css('width', new_width)
$('#lico').css('height', new_height)

//рисуем лицо
var canvas = document.getElementById("Canvas");
if (canvas.getContext)
    {
    otstup = 4
    start = (new_width/2).toFixed()
    radius = (new_width/5).toFixed()
    var ctx = canvas.getContext("2d");
    ctx.beginPath();
    ctx.canvas.height = new_height;
    ctx.canvas.width = new_width;
   if($('#print_type').val()=="1")
    {
    $('#shelk_logo').hide();
    $('#shelko_colors').hide();
    $('#Canvas').css('backgroundImage', 'url("img/cmyk1.jpg")');
    $('#print_type_text').html("Офсетная печать CMYK 4 цвета");
    }
    else{
    $('#shelk_logo').hide();
    $('#shelk_colors').hide();
    $('#shelko_colors').hide();

    var gradient = ctx.createLinearGradient(0,0,new_width,0);
    gradient.addColorStop(0,"#E2E2E2");
    gradient.addColorStop(1,"white");
    ctx.fillStyle = gradient;
    ctx.fillRect(0,0,new_width,new_height);
    $('#print_type_text').html("");
   if($('#print_type').val()=="2")
    {
   $('#Canvas').css('backgroundImage', 'url("")');
   k_width = new_width/4
   logo_font_size=k_width.toFixed();
   $('#shelk_logo').css('font-size', logo_font_size+'px');
   $('#shelk_logo').show();
   $('#print_type_text').html("Шелкография (трафаретная печать) <span id=shelko_num_colors></span>");
   $('#shelko_colors').show();
   $('#shelk_colors_inp').focus();
    }
    }
    ctx.lineWidth = "1";
    ctx.strokeRect(0,0,new_width,new_height);
    ctx.lineWidth = "2.5";
    ctx.strokeStyle="black"; // Синяя линия
    handle_length = radius*2/5*1,57
    ctx.arc(start,otstup,radius,0,Math.PI,false);   // Mouth (clockwise)
    ctx.stroke();
    }

//рисуем бок
$('#bok').css('width', new_bok)
$('#bok').css('height', new_height)

var canvas_bok = document.getElementById("Canvas_bok");
if (canvas_bok.getContext)
    {
    half = new_bok/2
    var ctx = canvas_bok.getContext("2d");
    ctx.beginPath();
    ctx.lineWidth = "1";
    ctx.strokeStyle="#A0A0A0"; // Синяя линия
    ctx.canvas.height = new_height;
    ctx.canvas.width = new_bok;
    if($('#print_type').val()=="1")
    {
    $('#Canvas_bok').css('backgroundImage', 'url("img/cmyk1.jpg")');
    }
    else{
    var gradient = ctx.createLinearGradient(0,0,half,0);
    gradient.addColorStop(0,"#E3E3E3");
    gradient.addColorStop(1,"white");
    ctx.fillStyle = gradient;
    ctx.fillRect(0,0,new_height,new_height);
  }

    ctx.moveTo(0,0); //Начало пути
    ctx.strokeRect(0,0,new_bok,new_height);
    ctx.moveTo(half,0);
    ctx.lineTo(half,new_height-half);
    ctx.lineTo(new_bok,new_height);
    ctx.moveTo(half,new_height-half);
    ctx.lineTo(0,new_height);
    ctx.stroke();
    }

if($('#ruchki').val()>"0"){
  $('#dlina_ruchki').show()

} else {
  $('#dlina_ruchki').hide()
}


}

function calc(){

//проверка данных
qty = $('#qty').val();
bag_sizes = $('#bag_sizes').val();
matherials = $('#matherials').val();
print_type = $('#print_type').val();
lamination = $('#lamination').val();
luvers = $('#luvers').val();
ruchki = $('#ruchki').val();
dlina_ruchki = $('#dlina_ruchki_inp').val();


if(qty < 25){
 $('#qty_err').html("<br>25 это минимальное количество. Если Вам нужно меньше пакетов, то заказывать прийдется все равно 25шт");
 $('#qty').select();
 return false;
} else { $('#qty_err').html(""); }

if(qty > 10000){
 $('#qty_err').html("<br>С помощью нашей программы можно подсчитать максимум 10 000 пакетов. <br>Если вас не устроила цена или вам нужно больше пакетов, пишите нам на sales@paketoff.ru");
 $('#qty').select();
 return false;
} else { $('#qty_err').html(""); }

var geturl;
    geturl = $.ajax({
    type: "GET",
    url: 'calc.php',
	data : '&qty='+qty+'&bag_sizes='+bag_sizes+'&matherials='+matherials+'&print_type='+print_type+'&lamination='+lamination+'&luvers='+luvers+'&ruchki='+ruchki+'&dlina_ruchki='+dlina_ruchki,
    success: function () {
    resp = geturl.responseText
    $('#cost').html(resp);
}
})

}


function create_obj(){

//$('#new_obj').html("<span><div  id=\"draggable\" class=\"draggable\"><strong>Размер (см)</strong> ширина: <input type=\"text\" size=2 value=\"\" id=\"new_obj_width\"/> высота: <input type=\"text\" size=2 value=\"\" id=\"new_obj_height\"/></div></span>");

$('#new_obj').fadeIn("300")
}


</script>



</head>


<body onload="preview()">
 <br><br>
 <h2>Онлайн расчет бумажных пакетов</h2>
<table width="950" border=0 cellpadding=5 class=calc_tbl>
<tr>
<td width=300 valign=top>
Тираж:<br>
<input type="text" maxlength=5 name=qty id=qty class="calc_text" value="1000"/> <br>
<span class="calc_sm">min 25 max 10000 шт</span>
<span class="calc_err" id="qty_err"></span>
<br><br>
Размер:
<br>
<span class="calc_sm">ширина х высота х бок</span> <br>
<select name="bag_sizes" id="bag_sizes" class="cals_sl_1" onchange="preview()">
<?$r_bag_sizes = mysql_query("SELECT * FROM r_bag_sizes ORDER BY width ASC");
while($r_bag_size = mysql_fetch_array($r_bag_sizes)) { ?>
<option value="<?=$r_bag_size['id']?>;<?=$r_bag_size['width']?>x<?=$r_bag_size['height']?>x<?=$r_bag_size['side']?>" <?if ($r_bag_size['selected'] == "1"){echo "selected";}?>><?=$r_bag_size['width']?> x <?=$r_bag_size['height']?> x <?=$r_bag_size['side']?></option>
<? } echo mysql_error();?>
</select>
  <br> <br>
Материал: <br>
<select name="matherials" id="matherials" class="cals_sl_2">
<option value="">мелованная бумага 200гр</option>
<option value="">плотный бело-коричневый крафт 135гр</option>
</select>
 <br> <br>

Печать:  <br>
<select name="print_type" id="print_type" class="cals_sl_1" onchange="preview()">
<option value="0">без печати</option>
<option value="1">офсетная CMYK</option>
<option value="2">шелкография</option>
</select>
<span id=shelko_colors style="display:none;"><br>
сколько цветов с каждой стороны<br>
<select name="shelk_colors_inp" id="shelk_colors_inp" class="cals_sl_1">
<option value="">1+0</option>
<option value="">2+0</option>
</select>
</span>
 <br> <br>

Ламинация: <br>
<select name="lamination" id="lamination" class="cals_sl_1">
<option value="">без ламинации</option>
<option value="">матовая</option>
<option value="">глянцевая</option>
</select>
<br> <br>

Люверсы: <br>
<select name="luvers" id="luvers" class="cals_sl_1">
<option value="">не нужны</option>
<option value="">серебряные</option>
<option value="">золотые</option>
</select>
 <br> <br>

Ручки: <br>
<select name="ruchki" id="ruchki" class="cals_sl_1" onchange="preview()">
<option value="0">без ручек</option>
<option value="1">шнур 5мм</option>
<option value="2">шнур 6мм</option>
</select>
<span id=dlina_ruchki style="display:none;"><br>
длина ручек (см)<br>
<input type="text" maxlength=3 value="35" name=dlina_ruchki_inp id=dlina_ruchki_inp class="calc_text"/> <br>

</span>

<br><br>
<input type="submit" style="font-size:30px; width:250px; height:38px;" onclick="calc()" value="Расчитать!" />
<br>
<span id=cost></span>
</td>
<td width=650>

<div id=bag_view class="calc_bag_view">
<span class="calc_bag_view_title">Лицо и бок пакета в реальных пропорциях</span>
<div style="vertical-align:middle;top: 5%;position:relative;">
<table border=0 cellspacing=10 align=center>
<tr>
<td align=center class=calc_vid>лицо пакета<br><span id=lico-size></span></td>
<td align=center class=calc_vid>вид пакета сбоку<br><span id=bok-size></span></td>
</tr>
<tr>

<td align=center>

<div id="Canvas_podl" style="text-align:center;position:relative;"><canvas id="Canvas" style="position:relative; opacity:0.7; z-index:1"></canvas>
<div id="shelk_logo" style="position:absolute;width:100%;height:100%; text-align:middle; z-index:1000; top:0px;display:none;"><span style="border: 1px solid; padding: 4px; top:40%; width:50%; position:relative; font-weight: bold;">лого</span></div>
</div>
<div id="print_type_text" style="position:absolute;font-size:10px;color:black;"></div>
</td>
<td align=center>

<canvas id="Canvas_bok" style="position:relative; opacity:0.7; z-index:1"></canvas>


</td>
</tr>
</table></div>

</div>


<div class="calc_obj" id=obj1>
<span class="calc_obj_title">Перетаскивайте предметы в пакет (можно вращать)</span>
<div class="calc_create_obj" onclick="create_obj()"></div>
<span id=new_obj style="display:none"><div id="draggable" class="draggable"><div><div class="draggable_descr_new_obj">
<strong>Размер (см)</strong>
<br>ширина: <input type="text" size=2 value="" id="new_obj_width"/> <br>
высота: <input type="text" size=2 value="" id="new_obj_height"/></div></div></div></span>

<span><div id="draggable" class="draggable"><div class="child"><img src="img/shampagne.gif" width="59" height="160" alt=""><div class="draggable_descr">бутылка шампанского<br>высота: 32см</div></div></div></span>
<span><div id="draggable1" class="draggable"><div class="child"><img src="img/mozart.gif" width="185" height="97" alt=""><div class="draggable_descr">коробка конфет<br>ширина: 37 высота 19,5 глубина 3,5см</div></div></div></span>
<span><div id="draggable2" class="draggable"><div class="child"><img src="img/dress.gif" width="200" height="146" alt=""><div class="draggable_descr">вечернее платье<br>ширина: 40см высота 30см</div></div></div></span>
<span><div id="draggable3" class="draggable"><div class="child"><img src="img/obuv_sm.gif" width="155" height="78" alt=""><div class="draggable_descr">обувная коробка<br>ширина: 31 высота 15 глубина 11см</div></div></div></span>
<span><div id="draggable4" class="draggable"><div class="child"><img src="img/krujka.gif" width="60" height="60" alt=""><div class="draggable_descr">кружка<br>диаметр: 8см, высота: 10см</div></div></div></span>
<span><div id="draggable5" class="draggable"><div class="child"><img src="img/myach.gif" width="115" height="115" alt=""><div class="draggable_descr">футбольный мяч<br>диаметр: 22см</div></div></div></span>
</div>

</td>

</tr>



</table>


</body>

</html>