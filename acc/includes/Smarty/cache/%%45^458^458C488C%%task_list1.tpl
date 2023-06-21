136
a:4:{s:8:"template";a:1:{s:14:"task_list1.tpl";b:1;}s:9:"timestamp";i:1678744598;s:7:"expires";i:1678748198;s:13:"cache_serials";a:0:{}}<html>
<head>

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

<title>Путевой лист на 13-03-2023 для Самосеев Илья Анатольевич </title>
</head>
<body>

<h3>Путевой лист 13-03-2023 водитель: Самосеев Илья Анатольевич <img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()"> сформировано 14.03.23 00:56 </h3>

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
<table width=800 border=0><tr><td>
<!--<div style="position:relative;"><img src="../i/moscow_map.png" border="0" />
</div>-->
<!--new_maps-->

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

<div id='list_maps'></div>
<div id="maps1"></div>

</td></tr></table>
Всего точек: <strong>1</strong><br>
Всего наличные: 45р<br>
Ориентировочное вознаграждение водителю: 1500р<br>
Сдача: <strong>45р</strong>

<div style="page-break-after:always"></div>




сформировано 14.03.23 00:56

<div style="page-break-after:always"></div>

<table width=800>




<tr>

<td valign="top" style="border: 2px  solid  black;">
<a name="1"></a>
<strong>#</strong> <b>1</b><br/>
<strong>Получатель:</strong> <b><a href="http://crm.upak.me/acc/logistic/courier_tasks.php?id=34135">тест</a></b>





Номер заказа: <b><a href="https://crm.upak.me/acc/query/query_send.php?show=49086" target="_blank">49086</a></b>


<input type="checkbox" id="49086_shipped"   onchange="change_shipped_status('49086')"  />

<br/>
<br>
Форма оплаты: <span style="font-weight: bold; color: #00D600">
наличные cумма: <b>45 р.</b></span>
 <br>
<br>

<br>Сумма к получению: <b>45 р.</b><br>Вознаграждение водителю: <b>0 р. (ориентировочно)</b>
<br>
<br>


<strong>Адрес:</strong> 3<br/>
<strong>Контактное лицо:</strong> Павел<br/>
<strong>Контактные телефоны:</strong> +75555555564<br/>

&nbsp;


<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5" width="500">
<thead><tr><th>Арт.</th><th>Название</th><th>Кол-во</th><th>Склад</th></tr></thead><tbody>
	<tr>
				<td>147</td>
				<td>147 Коробочка для выпечки / суши 17x5x10, белый</td>
				<td>1</td>
				<td>130/Т5</td>
			</tr>
		

		

		

		

		

			</tbody></table>
<br/><br/>
<strong>Инициатор:</strong> Москвин Павел Дмитриевич (+79032479926)<br/><br/>
Груз получен: ___________________ / _______________________  <br/><br/><br/>
</td>
</tr>

</table>

<input type="hidden" id=task_date value="Путевой лист на 13-03-2023 на Самосеев Илья Анатольевич ">
<textarea id=tasks style="width:1px;height:1px;">

Путевой лист на 13-03-2023<br/>
Исполнитель: Самосеев Илья Анатольевич<br>
сформировано 14.03.23 00:56

<br><br>Всего точек: 1<br>
Всего наличные: 45р<br>
Ориентировочное вознаграждение водителю: 1500р<br>
Сдача: 45р
<br><br>
Цель: тест<br/>
<br>
Форма оплаты:
наличные cумма: 45 р. <br>
<br>
<br>Сумма к получению: 45 р.<br>Вознаграждение водителю: 0 р. (ориентировочно)
<br>
<br>

Адрес: 3<br/>
Контактное лицо: Павел<br/>
Контактные телефоны: +75555555564<br/>

Инициатор: Москвин Павел Дмитриевич (+79032479926)<br/><br/><br/><br/><br/>


</textarea>
<img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()">


</body>

<style>
#map{width: 794px; height: 784px; padding: 0; margin: 0;}
</style>
<script>

</script>



</html>