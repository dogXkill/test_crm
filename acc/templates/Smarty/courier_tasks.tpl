<script type="text/javascript" src="../includes/js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/lang/calendar-ru.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar-setup.js"></script>

<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script src="../includes/js/jquery.ui.datepicker-courier-ru.js"></script>
<link rel="stylesheet" href="../includes/js/jquery-ui.css">
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=cc64a72b-8539-43fc-85b0-cdf3d05fc153" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script type="text/javascript">

{literal}
jQuery(document).ready(function(){
$("#map").click(function(e){
    var offset = $(this).offset();
        var x = e.pageX - offset.left - 7;
        var y = e.pageY - offset.top - 7;
        x = x.toFixed(0);
        y = y.toFixed(0);
        document.getElementById('map_x').value = x;
        document.getElementById('map_y').value = y;
        document.getElementById('map_point').style.left = "" + x + "px";
        document.getElementById('map_point').style.top = "" + y + "px";
});
var flag_adress_yandex=0;
var coord_yandex=0;

})

function init_point()
{
{/literal}
{if $content.map_x}

    document.getElementById('map_point').style.left = "" +  {$content.map_x} + "px";
    document.getElementById('map_point').style.top = "" + {$content.map_y} + "px";

{/if}
{literal}
}

</script> {/literal}

<form action="{if $content}courier_tasks.php?id={$smarty.get.id}&query_id={$content.query_id}{/if}" id=forma method="post">

{if $smarty.get.id or $queries}

{if $content.query_id}<input type="hidden" name="query_id" value="{$smarty.get.query_id}" />{/if}
<table border="0" cellspacing="5" cellpadding="5" width=588>
<tr><td class="tab_first_col">Инициатор *</td><td class="tab_two_col"><select type="text" name="user_id" class="cour_wdfull_sel">{foreach from=$users item=item}<option value="{$item.id}"{if ($content.user_id and $content.user_id eq $item.id) or (!$content.user_id and $item.login eq $smarty.cookies.user_des)} selected{/if}>{$item.name}</option>{/foreach}</select>
</td></tr>
<tr><td class="tab_first_col">Цель *</td><td class="tab_two_col"><textarea name="text" id=text style="height:70px;" class="cour_wdfull">{if $content}{$content.text}{else}{/if}</textarea></td></tr>
<tr><td class="tab_first_col">
Адрес доставки *</td><td class="tab_two_col"><textarea name="address" id=adress  class="cour_wdfull_adress">{if $content}{$content.address}{/if}</textarea>

<a href="" target="_blank" id="maps_google_a" onclick="openmap();">
<img src="../i/search.png" border="0" alt="" valign="absmiddle" style="cursor: pointer" onclick="search('leg_add')"></a>


{literal}
<script>


function check_form(){

    if ($('#text').val() == "")
    {$('#text').focus()
}
    else if ($('#adress').val() == ""){
    $('#adress').focus()
}
    else if ($('#courier_id_sel').val() == "")
    {
    alert("Необходимо выбрать водителя")
        $('#courier_id_sel').focus()
}
    else if ($('#opl_voditel').val() == ""){
    alert("Необходимо назначить водителю оплату. Если ее нет, поставьте просто 0")
    $('#opl_voditel').focus()
}
        else if ($('#contact_name').val() == "")
    {$('#contact_name').focus()
}
    else if ($('#contact_phone').val() == "")
    {$('#contact_phone').focus()
}


    else {
 $('#forma').submit()
    }

}
function openmap(){
var address_val=$("#adress").val();
var url_maps=encodeURI("https://maps.google.com?daddr="+address_val);
event.preventDefault();
 window.open(url_maps, '_blank').focus();
}

function check_deliv_possibility(){
deliv_id = '{/literal}{$content.deliv_id}{literal}';
prdm_sum_acc = '{/literal}{$content.prdm_sum_acc}{literal}';
prdm_opl = '{/literal}{$content.prdm_opl}{literal}';
prdm_dolg = prdm_sum_acc - prdm_opl;


if(prdm_dolg > 0 && deliv_id == '12'){alert("Самовывоз из шоурума разрешен только после поступления полной оплаты от клиента!")}

}

check_deliv_possibility()
</script>

{/literal}
</td></tr>
<tr>
<td></td>
<td>{if $content.query_id}
Привязка к заказу <b><a href="/acc/query/query_send.php?show={$content.query_id}" target=_blank>{$content.query_id}</a></b>
Сумма заказа: <b>{$content.prdm_sum_acc} р.</b> {if ($content.prdm_dolg > "-1") }долг: <b>{$content.prdm_dolg} р.</b>
<input type="hidden" name="prdm_sum_acc" id="prdm_sum_acc" value="{$content.prdm_sum_acc}"/>
{/if} Форма оплаты:
<b>{if ($content.form_of_payment == "")}нет данных{/if}
{if ($content.form_of_payment == "0") }нет данных{/if}
{if ($content.form_of_payment == "1") }наличные <img src="/i/cash.png" width="16" height="16" alt="" onmouseover="Tip('Наличные');" align="absmiddle">{/if}
{if ($content.form_of_payment == "2") }безнал по счету <img src="/i/invoice16.png" width="16" height="16" alt="" onmouseover="Tip('Безнал по счету');" align="absmiddle">{/if}
{if ($content.form_of_payment == "3") }безнал по квитанции <img src="/i/kvit16.png" width="16" height="16" alt="" onmouseover="Tip('По квитанции');" align="absmiddle">{/if}
{if ($content.form_of_payment == "4") }по карте{/if}</b>

<br>
{if ($content.form_of_payment == "2" and ($content.prdm_dolg > 0 or $content.prdm_dolg == "")) }
<span style="font-size:15px;font-weight:bold; border: 1px solid; color: #FF0000; padding: 5px;">От клиента оплата поступила не полностью или не отмечена. Вы уверены что стоит отгружать?</span>{/if}
<br>
<br>

{else}
<br>
<b>Нет привязки к заказу</b>
{/if}

Наличные к получению водителем:
<input type=text name="cash_payment" id="cash_payment" value="
{if (!$content.cash_payment and ($content.form_of_payment == "1" or $content.form_of_payment == "4") and $content.cash_payment !== "0")}
{if ($content.prdm_opl)}{$content.prdm_dolg}
{else}
{$content.prdm_sum_acc}{/if}

{/if}
{$content.cash_payment}
" size=6 maxlength=6> оплата водителю: <input type=text name="opl_voditel" id="opl_voditel" value="{$content.opl_voditel}" size=6 maxlength=6>



</td>
</tr>
<input type='hidden' value='{$content.maps_1}' id='maps_1'/>
<input type='hidden' value='{$content.maps_2}' id='maps_2'/>
<tr><td class="tab_first_col">На карте</td><td class="tab_two_col">

<!--
<div id=map style="margin-top:5px;border: 1px solid;background-image: url(../i/moscow_map.png);background-repeat: no-repeat;width:792px;height:784px;"><img src="../i/top.png" border="0" id="map_point" style="position:relative;z-index:100;"/></div>
--><div id=map_g style="margin-top:5px;"></div>

{literal}
<script>
function load_maps(){
ymaps.ready(function () {
    myGeocoder = ymaps.geocode($('#adress').val());
myGeocoder.then(
    function (res) {
        //alert('Координаты объекта :' + res.geoObjects.get(0).geometry.getCoordinates());
		flag_adress_yandex=1;
		coord_yandex=res.geoObjects.get(0).geometry.getCoordinates();
		var firstGeoObject = res.geoObjects.get(0);
		kor_address=firstGeoObject.getAddressLine();
		console.log(coord_yandex);
		
    //
    var myMap = new ymaps.Map('maps', {
            center: [coord_yandex[0],coord_yandex[1]],
            zoom: 9
        }, {
            searchControlProvider: 'yandex#search'
        }),

        // Создаём макет содержимого.
        MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
            '<div style="color: #FFFFFF; font-weight: bold;">$[properties.iconContent]</div>'
        ),

        myPlacemark = new ymaps.Placemark(myMap.getCenter(), {
            hintContent: 'Собственный значок метки',
            balloonContent: 'Это красивая метка'
        }, {
            // Опции.
          // Необходимо указать данный тип макета.
          iconLayout: 'default#image',
          // Своё изображение иконки метки.
          iconImageHref: 'http://test.upak.me/acc/i/myIcon.gif',
          // Размеры метки.
          iconImageSize: [30, 42],
          // Смещение левого верхнего угла иконки относительно
          // её "ножки" (точки привязки).
          iconImageOffset: [-5, -38]
        });



    myMap.geoObjects
        .add(myPlacemark)
        ;
    //
    },
    function (err) {
        //alert('Ошибка');
		flag_adress_yandex=0;
    }
);
//

});

}
//load_maps();
</script>
{/literal}
<input type="hidden" name="map_x" value="{$content.map_x}" id="map_x" /><input type="hidden" name="map_y" value="{$content.map_y}" id="map_y" />
<div id="maps"></div>

{literal}
<script>



$("#adress").change(function() {
$("#maps").html("");
	load_maps();
	//отправка данных на запись
	  $.ajax({
    url: "/acc/backend/load_save_maps.php",
    data: {coord_yandex:coord_yandex,kor_address:$("#adress").val(),text:kor_address},
    cache: false,
    success: function(html){
      if (html==1){
        //ok
        console.log("изменено");
		

      }else{
      //alert("Ошибка");
      console.log(html);
    }
	
    },error:function(html){
      //alert("Ошибка");
      console.log(html);
    }
    });
	//
});

</script>
{/literal}

</td>

</tr>
<tr style="display:none;"><td class="tab_first_col">Ближайшее метро</td><td class="tab_two_col"><select type="text" name="metro_id" ><option></option>{foreach from=$metro item=item}<option value="{$item.id}"{if $content and $content.metro_id eq $item.id} selected{/if}>{$item.name}</option>{/foreach}</select></td></tr>
<tr><td class="tab_first_col">Контактное лицо *</td><td class="tab_two_col"><input type="text" id=contact_name class="cour_wdfull_cnt" name="contact_name"{if $content} value="{$content.contact_name}"{/if}  size=100/></td></tr>
<tr><td class="tab_first_col">Контактный телефон *</td><td class="tab_two_col"><input type="text" id=contact_phone class="cour_wdfull_cnt" name="contact_phone"{if $content} value="{$content.contact_phone}"{/if}  size=100 /></td></tr>
<tr><td class="tab_first_col">Адрес отправки (при отправке через транспортную компанию)</td><td class="tab_two_col"><textarea name="address_real" class="cour_wdfull">{if $content}{$content.address_real}{/if}</textarea></td></tr>
<tr><td class="tab_first_col">Предполагаемая дата поездки *</td><td class="tab_two_col">
<input type="text" name="date" class="frm_wdfull_date" value="{if $content.date}{$content.date}{else}{$date}{/if}{if $content.st_date}{$content.st_date}{else}{$st_date}{/if}" id="date" />
<span onclick="set_date('0')" style="text-decoration: underline; cursor: pointer">сегодня</span> &nbsp;&nbsp;&nbsp;
<span onclick="set_date('1')" style="text-decoration: underline; cursor: pointer">завтра</span>
&nbsp;&nbsp;&nbsp;
<span onclick="set_date('2')" style="text-decoration: underline; cursor: pointer">послезавтра</span>
&nbsp;&nbsp;&nbsp;
<span onclick="set_date('3')" style="text-decoration: underline; cursor: pointer">после-послезавтра</span>
{literal}
<script type="text/javascript">
$("#date").datepicker($.datepicker.regional[ "ru" ]);

function set_date(days){
$("#date").datepicker("setDate", '+'+days);
}

function change_id(){

cour_sev_val = $("#courier_id_sel").val();
cour_sev_val = cour_sev_val.split(';')

driver_id = cour_sev_val[0]
driver_base_tarif = cour_sev_val[1]

$("#opl_voditel").val(driver_base_tarif);
$("#courier_id").val(driver_id);

}

function set_first_point(){
 $("#first_point_span").toggle();
 $("#first_point").val("");
 $("#first_point").focus();
}
</script>
{/literal}
</td></tr>
<tr><td class="tab_first_col">Предполагаемый исполнитель *</td><td class="tab_two_col">
<select type="text" id=courier_id_sel name="courier_id_sel"  class="cour_wdfull_sel" onchange="change_id()">

<option value="">выберите водителя</option>
{foreach from=$couriers item=item}<option value="{$item.id};{$item.base_tarif}" {if $content and $content.courier_id eq $item.id} selected{/if}>{$item.name}</option>{/foreach}</select>

<input type="hidden" name="courier_id" id=courier_id value="{$content.courier_id}"/>
</td></tr>

{if ($content.typ_ord==2 || $content.typ_ord==3)}
<tr><td class='tab_first_col'>Товар</td>
<td class='tab_two_col'>
<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;white-space: break-spaces;" border=1 cellspacing=0 cellpadding=5 width=500>
{foreach from=$queries item=item}
<tr>
<td><p>{$item.name}</p></td>
<td>{$item.col} шт</td>
<td>{if $item.num_col}склад {$item.num_col} {/if}</td>
</tr>
{/foreach}
</table>
</td></tr>
{/if}
<tr><td class="tab_first_col">Комментарий</td>
<td class="tab_two_col" >
<textarea name="comment" class="cour_wdfull_comm" id="comment_pole">

{$content.comment}
</textarea><div class='print_comment'>
{$content.comment}
</div></td>

</tr>

<tr><td class="tab_first_col"><label for="first_point_chk">Первая точка!</label></td>
<td class="tab_two_col" align="left">
<input type="checkbox" id="first_point_chk" onchange="set_first_point()" name="first_point_chk"{if $content and $content.first_point} checked{/if} />
<span id="first_point_span" style="display:{if !$content.first_point}none{/if}">успеть до:<input type="text" value="{$content.first_point}" name="first_point" id="first_point" style="width:200px;" maxlength="255"/></span>
</td>
</tr>
<tr><td colspan="2" align="center"><input type="button" onclick="check_form(); return false;" value="{if $content and !$queries}Изменить задание{else}Добавить задание{/if}" style="width:500px;font-size:30px;height:50px;" /></td></tr>
</table>
</form>
{literal}
<script type="text/javascript">  /*
        Calendar.setup({
                inputField     :    "date",      // id of the input field
        button         :    "date",   // trigger for the calendar (button ID)

        });   */
		function obr_comment(obj,t_font_size,max_f3){
			var temp_str=$(obj).val().split('\n').length;
			//console.log(temp_str);
			font_s=$('#comment_pole').css('font-size').split('px');
			if (temp_str<4){
				//меньше 4 строк  ,то x3 font-size
				t_font_size=$('#comment_pole').css('font-size').split('px');
				//console.log(t_font_size[0]+"|"+max_f3);
				if (t_font_size[0]<max_f3){
					$('#comment_pole').css('font-size',"54px");
				}
			}else if (temp_str>=4 && temp_str<11){
				$('#comment_pole').css('font-size',"36px");
			}else{
				$('#comment_pole').css('font-size',"18px");
			}
			$(".print_comment").text($("#comment_pole").val()).css('font-size',$('#comment_pole').css('font-size'));
		}
		t_font_size=$('#comment_pole').css('font-size').split('px');
		max_f3=54;
		$('#comment_pole').bind('input', function(){
		obr_comment($(this),t_font_size,max_f3);
		
		});
		//$('#comment_pole').input();
		obr_comment($('#comment_pole'),t_font_size,max_f3);
</script>
{/literal}
{else}
<form action="" method="post">
<table border="0" cellspacing="5" cellpadding="5">
<tr>
<td class="tab_two_col">Дата доставки: с <input type="text" name="date_from" value="{$search.date_from}" id="date_from" /><a href="#" id="button3" onClick="return false;"><img src="../i/calendar.gif" alt="Календарь" border="0" /></a> по <input type="text" name="date_to" value="{$search.date_to}" id="date_to" /><a href="#" id="button4" onClick="return false;"><img src="../i/calendar.gif" alt="Календарь" border="0" /></a>
<br>Исполнитель: <select type="text" name="courier_id"><option>все исполнители</option>{foreach from=$couriers item=item}<option value="{$item.id}"{if $search.courier_id eq $item.id} selected{/if}>{$item.name}</option>{/foreach}</select>
<br>Инициатор: <select type="text" name="user_id"><option>все инициаторы</option>{foreach from=$users item=item}<option value="{$item.id}"{if $search.user_id eq $item.id} selected{/if}>{$item.name}</option>{/foreach}</select>
Статус: <select type="text" name="done"><option value="">все</option><option value="0"{if $search.done eq "0"} selected{/if}>актуальные</option><option value="1" {if $search.done eq "1"} selected{/if}>выполненные</option></select></td>
</tr>
<tr><td align=center><input type="submit" value="Поиск" style="width: 100px; height: 30px; font-size: 17px;"/></td></tr>
</table>
</form>
{if $courier_tasks}
<table>
<tr class="tab_query_tit">
<td class="tab_query_tit">Задание</td>
<td class="tab_query_tit">Дата поездки</td>

<td class="tab_query_tit">Инициатор</td>
<td class="tab_query_tit">Исполнитель</td>
<td class="tab_query_tit">Архив</td>
<td class="tab_query_tit" colspan="2">Операции</td>
</tr>
{foreach from=$courier_tasks item=item}
<tr>
<td class="tab_td_norm" onmouseover="Tip('<div class=stat_podr_alt>{$item.text}</div>')">{$item.text_small}</td>
<td class="tab_td_norm">{$item.date}</td>

<td class="tab_td_norm">{$item.user}</td>
<td class="tab_td_norm">{$item.courier}</td>
<td class="tab_td_norm">{if $item.done eq 1}+{else}-{/if}</td>
<td class="tab_td_norm"><a href="?id={$item.id}" onmouseover="Tip('Редактировать')"><img width="20" height="20" src="../i/edit2.gif" /></a></td>
<td class="tab_td_norm"><a href="?del={$item.id}" onmouseover="Tip('Удалить')" onClick="return confirm('Вы уверены, что хотите удалить?');"><img widt="20" height="20" src="../i/del.gif" /></a></td>
</tr>
{/foreach}
</table>
{/if}
{literal}
<script type="text/javascript"> /*
        Calendar.setup({
                inputField     :    "date_from",      // id of the input field
                button         :    "button3"   // trigger for the calendar (button ID)
        });
        Calendar.setup({
                inputField     :    "date_to",      // id of the input field
                button         :    "button4"   // trigger for the calendar (button ID)
        }); */
		
		
		
</script>
{/literal}
{/if}
<script type="text/javascript">
init_point();
</script>