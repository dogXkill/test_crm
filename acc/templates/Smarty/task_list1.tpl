<html>
<head>
{literal}
<style>
body, p, td, input, select, textarea
{
  font: 16px Verdana, Geneva, Arial, Helvetica, sans-serif;
}

   .round {
    border-radius: 20px;
    box-shadow: 0 0 0 3px red, 0 0 13px #333;

	  background-color: white;
   }
   .href {
     font-size: 15px;
	 font-weight: bold;
	 color: black;
	 text-decoration: none;
   }

</style>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=cc64a72b-8539-43fc-85b0-cdf3d05fc153" type="text/javascript"></script>
<script>
function printer(){
task_date = $("#task_date").val();
mail_text = $("#tasks").val();
mail_text = mail_text.replace(/[&\/\\#,+()$~%'"*?{}]/g,'');
if(mail_text !== ""){
$.ajax({
          type: 'POST',
          url: 'add_tasks_mail_temp.php',
          data: '&task_date='+task_date+'&mail_text='+mail_text,
          success: function(data) {
            //alert(data);
          },
          error:  function(xhr, str){
	    alert('Возникла ошибка: ' + xhr.responseCode);
          }
        });}
window.print();

}



function change_shipped_status(query_id){

if($("#"+query_id+"_shipped").is(':checked')){shipped = '1';}else{shipped = '0';}

                    if($.isNumeric(query_id) && $.isNumeric(shipped)) {

                            $.ajax({
                                type: 'GET',
                                url: '../backend/change_shipped_status.php?shipped='+shipped+'&query_id='+query_id,
                                dataType: 'html',
                                async: false,
                                success: function(data){idata = data;}
                            });

                    }
}


</script>
{/literal}
<title>Путевой лист на {$date} для {$courier} </title>
</head>
<body>

<h3>Путевой лист {$date} водитель: {$courier} <img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()"> {$vrem} </h3>

<div style="font-size:9px;border: 1px solid; color: black; padding: 5px; width:782px;">

1) Сбор груза и отгрузка клиенту осуществляется <u>строго по накладным</u>. Правильная проверка и загрузка водителем груза - ответственность водителя.
Водитель отгрузивший товар не правильно, следующую поездку осуществляет за свой счет.<br>
2) При некоректной отправке в другой город через ТК - повторная отправка и возврат неверно отгруженного товара оплачивается допустившим ошибку водителем<br>
3) Водитель обязан за 1 час по прибытия позвонить клиенту и договориться о встрече. При невозможности доставить груз в текущий день, водитель должен самостоятельно дозвониться до клиента и договориться на другой день, о чем потом уведомить менеджера - инициатора<br>
4) При необходимости, водитель переносит груз "до дверей" получателя<br>
5) Если осуществляется отправка груза более чем двум адресатам в одной ТК, то каждый последующий груз отправленный в этот же день оплачивается водителю дополнительно, по базовой ставке<br>
6) Водитель должен сдать все документы в файлике, в отсортированном в соответствии с путевым листом. Любые изменения в путевом листе водитель должен отметить вручную<br>
7) При отсутствии у клиента юридического лица печати или доверенности, водитель, <b>с согласия имициатора</b>, может передать груз по самостоятельно оформленной мягкой накладной, все оригиналы отгрузочных документов, при этом водитель возвращает обратно в офис<br>
<b>8) При отправке груза через ТК, получателем груза должно быть указано юридическое наименование, указанное в отгрузочных документах, если, иное не указано в задании<br>
9) Запрещается сдача груза в ТК в скрепленных друг с другом упаковках. Количество мест принимаемое ТК, должно соответствовать количеству упаковок</b>
</div>
<br>
{if $courier_tasks}
<table width=800 border=0><tr><td>
<!--<div style="position:relative;"><img src="../i/moscow_map.png" border="0" />
{foreach from=$courier_tasks item=item key=key}{if $item.map_x}<div style="position:absolute;right:{$item.map_x}px;bottom:{$item.map_y}px;z-index:{$key};" class=round><a href="#{$item.num}" class=href>{$item.num}</a>
</div>{/if}{/foreach}</div>-->
<!--new_maps-->
{literal}
<script>
function load_map(){
let params = (new URL(document.location)).searchParams; 
  $.ajax({
            url: "/acc/logistic/check_ymaps.php",
            type: "GET",
            data: {courier_id:params.get("courier_id"),date:params.get("date")},
            cache: false,
            success: function(html){
				$("#maps1").html(html);
			}
			});
			}
load_map();
</script>
{/literal}
<div id='list_maps'></div>
<div id="maps1"></div>

</td></tr></table>
Всего точек: <strong>{$tochek}</strong><br>
Всего наличные: {$cash}р<br>
Ориентировочное вознаграждение водителю: {if ($opl_voditel < 1500)}1500р{else}{$opl_voditel}{/if}<br>
Сдача: <strong>{$sdacha}р</strong>

<div style="page-break-after:always"></div>




{$vrem}

<div style="page-break-after:always"></div>

{assign var=key1 value=`$key`}
<table width=800>

{foreach from=$courier_tasks item=item key=key}


{$item.test}
<tr>

<td valign="top" style="border:{if $item.first_point} 4px  dashed {else} 2px  solid {/if} black;">
<a name="{$item.num}"></a>
<strong>#</strong> <b>{$item.num}</b><br/>
<strong>Получатель:</strong> <b><a href="http://crm.upak.me/acc/logistic/courier_tasks.php?id={$item.id}">{$item.text}</a></b>




{if $item.query_id}

Номер заказа: <b><a href="https://crm.upak.me/acc/query/query_send.php?show={$item.query_id}" target="_blank">{$item.query_id}</a></b>


<input type="checkbox" id="{$item.query_id}_shipped" {php}if($shipped == '1'){echo "checked";}{/php}  onchange="change_shipped_status('{$item.query_id}')" {if $shipped_edit == '0'}disabled{/if} />

<br/>
<br>
Форма оплаты: <span style="font-weight: bold; color: #00D600">
{if ($item.form_of_payment == "") }нет данных{/if}
{if ($item.form_of_payment == "0") }нет данных{/if}
{if ($item.form_of_payment == "1") }наличные cумма: {if ($item.cash_payment > "0") }<b>{$item.cash_payment} р.</b>{/if}
{if ($item.cash_payment == "0") }<b>уточнить</b>{/if}{/if}
{if ($item.form_of_payment == "2") }безнал по счету{/if}
{if ($item.form_of_payment == "3") }безнал по квитанции{/if}
{if ($item.form_of_payment == "4") }по карте. Сумма заказа:  {$item.prdm_sum_acc} р.{/if}</span>
 <br>
{else}
<b>Нет привязки к заказу</b>
{/if}
<br>

{if ($item.cash_payment > "0")}<br>Сумма к получению: <b>{$item.cash_payment} р.</b><br>{/if}
Вознаграждение водителю: <b>{$item.opl_voditel} р. (ориентировочно)</b>
<br>
<br>
{if $item.first_point}<h2>Первая точка, успеть до {$item.first_point}</h2>{/if}


<strong>Адрес:</strong> {$item.address}<br/>
{if $item.metro ne ""}<strong>Метро:</strong> {$item.metro}<br/>{/if}
{if $item.address_real}<strong>Адрес отправки:</strong> {$item.address_real}<br/>{/if}
<strong>Контактное лицо:</strong> {$item.contact_name}<br/>
<strong>Контактные телефоны:</strong> {$item.contact_phone}<br/>


{if $item.comment eq ''}
&nbsp;
{if ($item.test==1)}
<div style='border:5px dashed red;'>

<strong>Примечания:</strong> {$item.comment}<br/>
<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5" width="500">
<thead><tr><th>Арт.</th><th>Название</th><th>Кол-во</th><th>Склад</th></tr></thead><tbody>
{foreach from=$queries item=item1 key=key1}
	{foreach from=$item1[$item.query_id] item=item2 key=key2}
<tr>
				<td>{$item2.art_num}</td>
				<td>{$item2.name}</td>
				<td>{$item2.col}</td>
				<td>{$item2.num_sklad}</td>
			</tr>
		{foreach from=$item2 item=item3 key=key3}


		{/foreach}
	{/foreach}
{/foreach}
</tbody></table>
</div>
{/if}
{else}
<div style='border:5px dashed red;'>
<strong>Примечания:</strong> {$item.comment}<br/>
{if ($item.test==1)}
<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5" width="500">
<thead><tr><th>Арт.</th><th>Название</th><th>Кол-во</th><th>Склад</th></tr></thead><tbody>
{foreach from=$queries item=item1 key=key1}
	{foreach from=$item1[$item.query_id] item=item2 key=key2}
<tr>
				<td>{$item2.art_num}</td>
				<td>{$item2.name}</td>
				<td>{$item2.col}</td>
				<td>{$item2.num_sklad}</td>
			</tr>
		{foreach from=$item2 item=item3 key=key3}


		{/foreach}
	{/foreach}
{/foreach}
</tbody></table>
{/if}
</div>
{/if}


{if $item.query_id}
{if ($item.test!=1)}
<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5" width="500">
<thead><tr><th>Арт.</th><th>Название</th><th>Кол-во</th><th>Склад</th></tr></thead><tbody>
{foreach from=$queries item=item1 key=key1}
	{foreach from=$item1[$item.query_id] item=item2 key=key2}
<tr>
				<td>{$item2.art_num}</td>
				<td>{$item2.name}</td>
				<td>{$item2.col}</td>
				<td>{$item2.num_sklad}</td>
			</tr>
		{foreach from=$item2 item=item3 key=key3}


		{/foreach}
	{/foreach}
{/foreach}
</tbody></table>
{/if}
{/if}
<br/><br/>
<strong>Инициатор:</strong> {$item.user}<br/><br/>
Груз получен: ___________________ / _______________________  <br/><br/><br/>{$sum_of_cash}
</td>
</tr>
{/foreach}

</table>

<input type="hidden" id=task_date value="Путевой лист на {$date} на {$courier} ">
<textarea id=tasks style="width:1px;height:1px;">

Путевой лист на {$date}<br/>
Исполнитель: {$courier}<br>
{$vrem}

<br><br>Всего точек: {$tochek}<br>
Всего наличные: {$cash}р<br>
Ориентировочное вознаграждение водителю: {if ($opl_voditel < 1500)}1500р{else}{$opl_voditel}{/if}<br>
Сдача: {$sdacha}р
<br><br>
{assign var=key1 value=`$key`}
{foreach from=$courier_tasks item=item key=key}
Цель: {$item.text}<br/>
{if $item.query_id}
<br>
Форма оплаты:
{if ($item.form_of_payment == "") }нет данных{/if}
{if ($item.form_of_payment == "0") }нет данных{/if}
{if ($item.form_of_payment == "1") }наличные cумма: {if ($item.cash_payment > "0") }{$item.cash_payment} р.{/if}
{if ($item.cash_payment == "0") }уточнить{/if}{/if}
{if ($item.form_of_payment == "2") }безнал по счету{/if}
{if ($item.form_of_payment == "3") }безнал по квитанции{/if}
{if ($item.form_of_payment == "4") }прочая{/if}
 <br>
{else}
Нет привязки к заказу
{/if}
<br>
{if ($item.cash_payment > "0")}<br>Сумма к получению: {$item.cash_payment} р.<br>{/if}
Вознаграждение водителю: {$item.opl_voditel} р. (ориентировочно)
<br>
<br>
{if $item.first_point}<h2>Первая точка, успеть до {$item.first_point}</h2>{/if}

Адрес: {$item.address}<br/>
{if $item.metro ne ""}Метро: {$item.metro}<br/>{/if}
{if $item.address_real}Адрес отправки: {$item.address_real}<br/>{/if}
Контактное лицо: {$item.contact_name}<br/>
Контактные телефоны: {$item.contact_phone}<br/>

{if $item.comment ne ""}Примечания: {$item.comment}<br/>{/if}
Инициатор: {$item.user}<br/><br/><br/><br/><br/>{$sum_of_cash}

{/foreach}

</textarea>
<img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()">


{else}
<p>Заданий нет</p>
{/if}
</body>
{literal}
<style>
#map{width: 794px; height: 784px; padding: 0; margin: 0;}
</style>
<script>

</script>
{/literal}


</html>
