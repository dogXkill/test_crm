<!DOCTYPE HTML>

<html>

<head>
  <title>Untitled</title>
  <style type="text/css">
  <!--

  .ramka{
  border: 1px solid #000000;
  }
 .line{
 border:1px solid;
 position:absolute;
 width:4000px;
 height:4000px;
 -webkit-transform: rotate(45deg);

 }
.hline{
  border-bottom:1px solid;
  position:absolute;
}
.size{
font-size: 40px;
}
.form_text{
font-size: 40px;
}
.inp{
height: 40px;
width:100px;
font-size: 40px;
}
.but{
height: 50px;
width:350px;
font-size: 40px;
}
.box{
height: 30px;
width:30px;
}

  -->
  </style>
</head>
<script type="text/javascript" src="../acc/includes/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="../acc/includes/js/jquery-ui.js"></script>
<script>
function generate(){
k=56.7
//35.37
shirina = $('#shirina').val()*k
vysota = $('#vysota').val()*k
bok = $('#bok').val()*k
klapan = $('#klapan_inp').val()*k
podvorot = $('#podvorot_inp').val()*k
niz = $('#bok').val()*0.7*k

shirina_mm = $('#shirina').val()*10
vysota_mm = $('#vysota').val()*10
bok_mm = $('#bok').val()*10
klapan_mm = $('#klapan_inp').val()*10
podvorot_mm = $('#podvorot_inp').val()*10
niz_mm = $('#bok').val()*0.7*10

$('#pol_bok1_div').show()
$('#lico1_div').show()
$('#pol_bok2_div').show()
$('#pol_bok3_div').show()
$('#lico2_div').show()
$('#pol_bok4_div').show()


//рисуем клеевой клапан
$('#klapan_podv').css('width', klapan)
$('#klapan_podv').css('height', podvorot)
$('#klapan').css('width', klapan)
$('#klapan').css('height', vysota)
$('#klapan_dno').css('width', klapan)
$('#klapan_dno').css('height', niz)

//рисуем левую боковину
$('#bok_pol1_podv').css('width', bok/2)
$('#bok_pol1_podv').css('height', podvorot)
$('#bok_pol1').css('width', bok/2)
$('#bok_pol1').css('height', vysota)
$('#bok_pol1_dno').css('width', bok/2)
$('#bok_pol1_dno').css('height', niz)


//рисуем лицо
$('#lico_podv').css('width', shirina)
$('#lico_podv').css('height', podvorot)
$('#lico').css('width', shirina)
$('#lico').css('height', vysota)
$('#lico_dno').css('width', shirina)
$('#lico_dno').css('height', niz)

//рисуем середину двойную боковину
$('#bok_pol2_podv').css('width', bok/2)
$('#bok_pol2_podv').css('height', podvorot)
$('#bok_pol2').css('width', bok/2)
$('#bok_pol2').css('height', vysota)
$('#bok_pol2_dno').css('width', bok/2)
$('#bok_pol2_dno').css('height', niz)
$('#bok_pol3_podv').css('width', bok/2)
$('#bok_pol3_podv').css('height', podvorot)
$('#bok_pol3').css('width', bok/2)
$('#bok_pol3').css('height', vysota)
$('#bok_pol3_dno').css('width', bok/2)
$('#bok_pol3_dno').css('height', niz)

//рисуем второе лицо
$('#lico2_podv').css('width', shirina)
$('#lico2_podv').css('height', podvorot)
$('#lico2').css('width', shirina)
$('#lico2').css('height', vysota)
$('#lico2_dno').css('width', shirina)
$('#lico2_dno').css('height', niz)

//рисуем правую боковину
$('#bok_pol4_podv').css('width', bok/2)
$('#bok_pol4_podv').css('height', podvorot)
$('#bok_pol4').css('width', bok/2)
$('#bok_pol4').css('height', vysota)
$('#bok_pol4_dno').css('width', bok/2)
$('#bok_pol4_dno').css('height', niz)


//рисуем биговки под наклоном 45 градусов
$('#diag_1').css('top', vysota-bok/2+828)
$('#diag_2').css('top', vysota-bok/2+828)
$('#diag_3').css('top', vysota-bok/2+828)
$('#diag_3').css('left', 0-2000+bok/2)

//рисуем боковую биговку
$('#line_1').css('left', 0-klapan)
$('#line_1').css('width', klapan+bok/2+shirina+bok/2+3)
$('#line_1').css('top', vysota-bok/2)

if($('#polovina').prop('checked'))
{
$('#pol_bok1_div').show()
$('#lico1_div').show()
$('#pol_bok2_div').hide()
$('#pol_bok3_div').show()
$('#lico2_div').hide()
$('#pol_bok4_div').hide()
$('#diag_2').css('left', -2000+bok/2)
razv_shir =
razv_height
} else {
$('#diag_2').css('left', -2000)
}



}
function increase(){
$('#line_bok_pol1').css('width', "200")
}
</script>

<body>
<table>
<tr>
<td><span id="forma" class=form_text>
Ширина:
<input type="text" size=4 id="shirina" name="" value="25" class="inp" />
Высота:
<input type="text" size=4 id="vysota" name="" value="36" class="inp" />
Бок:
<input type="text" size=4 id="bok" name="" value="10" class="inp" />
 <br>
<label for="polovina">Половинка</label> <input type="radio" name="type_stamp" value="pol" id="polovina" class="box"/>
<label for="cely">Цельный</label> <input type="radio" name="type_stamp" value="celn" id="cely" class="box" checked/>
<br>
Клеевой плапан:
<input type="text" size=4 id="klapan_inp" name="" value="2" class="inp" />
Подворот:
<input type="text" size=4 id="podvorot_inp" name="" value="5" class="inp" />
Нижний клапан:
<input type="text" size=4 id="niz_inp" name="" value="5" class="inp" />
 <br>
 <input type="button" onclick="generate()" value="Сгенерить!" class="but"/>

 </span></td>
<td style="width:500px;" align=center><span class=size>Размер от ножа до ножа:<br>
<span id="razv_sh">22</span> x <span id="razv_vys">33</span> mm</span>
</td>
</tr>
</table>


<br><br>
<div style="width:1000%;left:50px;">
<div style="overflow: hidden;display:table;white-space: nowrap;">
<div id="shtamp" style="height:1000%;white-space: nowrap;">
<div style="display:inline-block;float: left;" id=klapan_div>
<div class="ramka" style="" id="klapan_podv"></div>
<div class="ramka" style="margin-top:-1px;" id="klapan"></div>
<div class="ramka" style="border: 1px solid #000000;margin-top:-1px;" id="klapan_dno"></div>
</div>

<div style="display:inline-block;float: left;" id=pol_bok1_div>
<div class="ramka" style="margin-left:-1px;" id="bok_pol1_podv"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;position:relative;" id="bok_pol1"><div class="hline" style="margin-top:-1px;margin-left:-1px;" id="line_1"></div><div class="line" style="left:-2000px;margin-left:-1px;" id="diag_1"></div></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="bok_pol1_dno"></div>
</div>

<div style="display:inline-block;float: left;" id=lico1_div>
<div class="ramka" style="margin-left:-1px;" id="lico_podv"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="lico"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="lico_dno"></div>
</div>

<div style="display:inline-block;float: left;" id=pol_bok2_div>
<div class="ramka" style="margin-left:-1px;" id="bok_pol2_podv"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="bok_pol2"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="bok_pol2_dno"></div>
</div>

<div style="display:inline-block;float: left;" id=pol_bok3_div>
<div class="ramka" style="margin-left:-1px;" id="bok_pol3_podv"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;position:relative;" id="bok_pol3"><div class=line style="left:-2000px;margin-left:-1px;" id="diag_2"></div></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="bok_pol3_dno"></div>
</div>
<div style="display:inline-block;float: left;" id=lico2_div>
<div class="ramka" style="margin-left:-1px;" id="lico2_podv"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="lico2"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="lico2_dno"></div>
</div>
<div style="display:inline-block;float: left;" id=pol_bok4_div>
<div class="ramka" style="margin-left:-1px;" id="bok_pol4_podv"></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;position:relative;" id="bok_pol4"><div class=line style="left:-2000px;margin-left:-1px;" id="diag_3"></div></div>
<div class="ramka" style="margin-top:-1px;margin-left:-1px;" id="bok_pol4_dno"></div>
</div>


</div>
</div>
</div>
<div id=metka4 style="width:25px;height:28px;border-top:1px solid;border-left:1px solid; margin-right: -35px;position:relative;z-index:10;display:none;"></div>
</body>

</html>