<?php /* Smarty version 2.6.26, created on 2023-05-23 15:21:12
         compiled from task_list1.tpl */ ?>
<html>
<head>
<?php echo '
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
mail_text = mail_text.replace(/[&\\/\\\\#,+()$~%\'"*?{}]/g,\'\');
if(mail_text !== ""){
$.ajax({
          type: \'POST\',
          url: \'add_tasks_mail_temp.php\',
          data: \'&task_date=\'+task_date+\'&mail_text=\'+mail_text,
          success: function(data) {
            //alert(data);
          },
          error:  function(xhr, str){
	    alert(\'Возникла ошибка: \' + xhr.responseCode);
          }
        });}
window.print();

}



function change_shipped_status(query_id){

if($("#"+query_id+"_shipped").is(\':checked\')){shipped = \'1\';}else{shipped = \'0\';}

                    if($.isNumeric(query_id) && $.isNumeric(shipped)) {

                            $.ajax({
                                type: \'GET\',
                                url: \'../backend/change_shipped_status.php?shipped=\'+shipped+\'&query_id=\'+query_id,
                                dataType: \'html\',
                                async: false,
                                success: function(data){idata = data;}
                            });

                    }
}


</script>
'; ?>

<title>Путевой лист на <?php echo $this->_tpl_vars['date']; ?>
 для <?php echo $this->_tpl_vars['courier']; ?>
 </title>
</head>
<body>

<h3>Путевой лист <?php echo $this->_tpl_vars['date']; ?>
 водитель: <?php echo $this->_tpl_vars['courier']; ?>
 <img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()"> <?php echo $this->_tpl_vars['vrem']; ?>
 </h3>

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
<?php if ($this->_tpl_vars['courier_tasks']): ?>
<table width=800 border=0><tr><td>
<!--<div style="position:relative;"><img src="../i/moscow_map.png" border="0" />
<?php $_from = $this->_tpl_vars['courier_tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?><?php if ($this->_tpl_vars['item']['map_x']): ?><div style="position:absolute;right:<?php echo $this->_tpl_vars['item']['map_x']; ?>
px;bottom:<?php echo $this->_tpl_vars['item']['map_y']; ?>
px;z-index:<?php echo $this->_tpl_vars['key']; ?>
;" class=round><a href="#<?php echo $this->_tpl_vars['item']['num']; ?>
" class=href><?php echo $this->_tpl_vars['item']['num']; ?>
</a>
</div><?php endif; ?><?php endforeach; endif; unset($_from); ?></div>-->
<!--new_maps-->
<?php echo '
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
'; ?>

<div id='list_maps'></div>
<div id="maps1"></div>

</td></tr></table>
Всего точек: <strong><?php echo $this->_tpl_vars['tochek']; ?>
</strong><br>
Всего наличные: <?php echo $this->_tpl_vars['cash']; ?>
р<br>
Ориентировочное вознаграждение водителю: <?php if (( $this->_tpl_vars['opl_voditel'] < 1500 )): ?>1500р<?php else: ?><?php echo $this->_tpl_vars['opl_voditel']; ?>
<?php endif; ?><br>
Сдача: <strong><?php echo $this->_tpl_vars['sdacha']; ?>
р</strong>

<div style="page-break-after:always"></div>




<?php echo $this->_tpl_vars['vrem']; ?>


<div style="page-break-after:always"></div>

<?php $this->assign('key1', ($this->_tpl_vars['key'])); ?>
<table width=800>

<?php $_from = $this->_tpl_vars['courier_tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>


<?php echo $this->_tpl_vars['item']['test']; ?>

<tr>

<td valign="top" style="border:<?php if ($this->_tpl_vars['item']['first_point']): ?> 4px  dashed <?php else: ?> 2px  solid <?php endif; ?> black;">
<a name="<?php echo $this->_tpl_vars['item']['num']; ?>
"></a>
<strong>#</strong> <b><?php echo $this->_tpl_vars['item']['num']; ?>
</b><br/>
<strong>Получатель:</strong> <b><a href="http://crm.upak.me/acc/logistic/courier_tasks.php?id=<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['text']; ?>
</a></b>




<?php if ($this->_tpl_vars['item']['query_id']): ?>

Номер заказа: <b><a href="https://crm.upak.me/acc/query/query_send.php?show=<?php echo $this->_tpl_vars['item']['query_id']; ?>
" target="_blank"><?php echo $this->_tpl_vars['item']['query_id']; ?>
</a></b>


<input type="checkbox" id="<?php echo $this->_tpl_vars['item']['query_id']; ?>
_shipped" <?php if($shipped == '1'){echo "checked";} ?>  onchange="change_shipped_status('<?php echo $this->_tpl_vars['item']['query_id']; ?>
')" <?php if ($this->_tpl_vars['shipped_edit'] == '0'): ?>disabled<?php endif; ?> />

<br/>
<br>
Форма оплаты: <span style="font-weight: bold; color: #00D600">
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == "" )): ?>нет данных<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '0' )): ?>нет данных<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '1' )): ?>наличные cумма: <?php if (( $this->_tpl_vars['item']['cash_payment'] > '0' )): ?><b><?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 р.</b><?php endif; ?>
<?php if (( $this->_tpl_vars['item']['cash_payment'] == '0' )): ?><b>уточнить</b><?php endif; ?><?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '2' )): ?>безнал по счету<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '3' )): ?>безнал по квитанции<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '4' )): ?>по карте. Сумма заказа:  <?php echo $this->_tpl_vars['item']['prdm_sum_acc']; ?>
 р.<?php endif; ?></span>
 <br>
<?php else: ?>
<b>Нет привязки к заказу</b>
<?php endif; ?>
<br>

<?php if (( $this->_tpl_vars['item']['cash_payment'] > '0' )): ?><br>Сумма к получению: <b><?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 р.</b><br><?php endif; ?>
Вознаграждение водителю: <b><?php echo $this->_tpl_vars['item']['opl_voditel']; ?>
 р. (ориентировочно)</b>
<br>
<br>
<?php if ($this->_tpl_vars['item']['first_point']): ?><h2>Первая точка, успеть до <?php echo $this->_tpl_vars['item']['first_point']; ?>
</h2><?php endif; ?>


<strong>Адрес:</strong> <?php echo $this->_tpl_vars['item']['address']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['metro'] != ""): ?><strong>Метро:</strong> <?php echo $this->_tpl_vars['item']['metro']; ?>
<br/><?php endif; ?>
<?php if ($this->_tpl_vars['item']['address_real']): ?><strong>Адрес отправки:</strong> <?php echo $this->_tpl_vars['item']['address_real']; ?>
<br/><?php endif; ?>
<strong>Контактное лицо:</strong> <?php echo $this->_tpl_vars['item']['contact_name']; ?>
<br/>
<strong>Контактные телефоны:</strong> <?php echo $this->_tpl_vars['item']['contact_phone']; ?>
<br/>


<?php if ($this->_tpl_vars['item']['comment'] == ''): ?>
&nbsp;
<?php if (( $this->_tpl_vars['item']['test'] == 1 )): ?>
<div style='border:5px dashed red;'>

<strong>Примечания:</strong> <?php echo $this->_tpl_vars['item']['comment']; ?>
<br/>
<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5" width="500">
<thead><tr><th>Арт.</th><th>Название</th><th>Кол-во</th><th>Склад</th></tr></thead><tbody>
<?php $_from = $this->_tpl_vars['queries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key1'] => $this->_tpl_vars['item1']):
?>
	<?php $_from = $this->_tpl_vars['item1'][$this->_tpl_vars['item']['query_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['item2']):
?>
<tr>
				<td><?php echo $this->_tpl_vars['item2']['art_num']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['name']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['col']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['num_sklad']; ?>
</td>
			</tr>
		<?php $_from = $this->_tpl_vars['item2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key3'] => $this->_tpl_vars['item3']):
?>


		<?php endforeach; endif; unset($_from); ?>
	<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>
</tbody></table>
</div>
<?php endif; ?>
<?php else: ?>
<div style='border:5px dashed red;'>
<strong>Примечания:</strong> <?php echo $this->_tpl_vars['item']['comment']; ?>
<br/>
<?php if (( $this->_tpl_vars['item']['test'] == 1 )): ?>
<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5" width="500">
<thead><tr><th>Арт.</th><th>Название</th><th>Кол-во</th><th>Склад</th></tr></thead><tbody>
<?php $_from = $this->_tpl_vars['queries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key1'] => $this->_tpl_vars['item1']):
?>
	<?php $_from = $this->_tpl_vars['item1'][$this->_tpl_vars['item']['query_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['item2']):
?>
<tr>
				<td><?php echo $this->_tpl_vars['item2']['art_num']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['name']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['col']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['num_sklad']; ?>
</td>
			</tr>
		<?php $_from = $this->_tpl_vars['item2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key3'] => $this->_tpl_vars['item3']):
?>


		<?php endforeach; endif; unset($_from); ?>
	<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>
</tbody></table>
<?php endif; ?>
</div>
<?php endif; ?>


<?php if ($this->_tpl_vars['item']['query_id']): ?>
<?php if (( $this->_tpl_vars['item']['test'] != 1 )): ?>
<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5" width="500">
<thead><tr><th>Арт.</th><th>Название</th><th>Кол-во</th><th>Склад</th></tr></thead><tbody>
<?php $_from = $this->_tpl_vars['queries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key1'] => $this->_tpl_vars['item1']):
?>
	<?php $_from = $this->_tpl_vars['item1'][$this->_tpl_vars['item']['query_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['item2']):
?>
<tr>
				<td><?php echo $this->_tpl_vars['item2']['art_num']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['name']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['col']; ?>
</td>
				<td><?php echo $this->_tpl_vars['item2']['num_sklad']; ?>
</td>
			</tr>
		<?php $_from = $this->_tpl_vars['item2']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key3'] => $this->_tpl_vars['item3']):
?>


		<?php endforeach; endif; unset($_from); ?>
	<?php endforeach; endif; unset($_from); ?>
<?php endforeach; endif; unset($_from); ?>
</tbody></table>
<?php endif; ?>
<?php endif; ?>
<br/><br/>
<strong>Инициатор:</strong> <?php echo $this->_tpl_vars['item']['user']; ?>
<br/><br/>
Груз получен: ___________________ / _______________________  <br/><br/><br/><?php echo $this->_tpl_vars['sum_of_cash']; ?>

</td>
</tr>
<?php endforeach; endif; unset($_from); ?>

</table>

<input type="hidden" id=task_date value="Путевой лист на <?php echo $this->_tpl_vars['date']; ?>
 на <?php echo $this->_tpl_vars['courier']; ?>
 ">
<textarea id=tasks style="width:1px;height:1px;">

Путевой лист на <?php echo $this->_tpl_vars['date']; ?>
<br/>
Исполнитель: <?php echo $this->_tpl_vars['courier']; ?>
<br>
<?php echo $this->_tpl_vars['vrem']; ?>


<br><br>Всего точек: <?php echo $this->_tpl_vars['tochek']; ?>
<br>
Всего наличные: <?php echo $this->_tpl_vars['cash']; ?>
р<br>
Ориентировочное вознаграждение водителю: <?php if (( $this->_tpl_vars['opl_voditel'] < 1500 )): ?>1500р<?php else: ?><?php echo $this->_tpl_vars['opl_voditel']; ?>
<?php endif; ?><br>
Сдача: <?php echo $this->_tpl_vars['sdacha']; ?>
р
<br><br>
<?php $this->assign('key1', ($this->_tpl_vars['key'])); ?>
<?php $_from = $this->_tpl_vars['courier_tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
Цель: <?php echo $this->_tpl_vars['item']['text']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['query_id']): ?>
<br>
Форма оплаты:
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == "" )): ?>нет данных<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '0' )): ?>нет данных<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '1' )): ?>наличные cумма: <?php if (( $this->_tpl_vars['item']['cash_payment'] > '0' )): ?><?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 р.<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['cash_payment'] == '0' )): ?>уточнить<?php endif; ?><?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '2' )): ?>безнал по счету<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '3' )): ?>безнал по квитанции<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '4' )): ?>прочая<?php endif; ?>
 <br>
<?php else: ?>
Нет привязки к заказу
<?php endif; ?>
<br>
<?php if (( $this->_tpl_vars['item']['cash_payment'] > '0' )): ?><br>Сумма к получению: <?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 р.<br><?php endif; ?>
Вознаграждение водителю: <?php echo $this->_tpl_vars['item']['opl_voditel']; ?>
 р. (ориентировочно)
<br>
<br>
<?php if ($this->_tpl_vars['item']['first_point']): ?><h2>Первая точка, успеть до <?php echo $this->_tpl_vars['item']['first_point']; ?>
</h2><?php endif; ?>

Адрес: <?php echo $this->_tpl_vars['item']['address']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['metro'] != ""): ?>Метро: <?php echo $this->_tpl_vars['item']['metro']; ?>
<br/><?php endif; ?>
<?php if ($this->_tpl_vars['item']['address_real']): ?>Адрес отправки: <?php echo $this->_tpl_vars['item']['address_real']; ?>
<br/><?php endif; ?>
Контактное лицо: <?php echo $this->_tpl_vars['item']['contact_name']; ?>
<br/>
Контактные телефоны: <?php echo $this->_tpl_vars['item']['contact_phone']; ?>
<br/>

<?php if ($this->_tpl_vars['item']['comment'] != ""): ?>Примечания: <?php echo $this->_tpl_vars['item']['comment']; ?>
<br/><?php endif; ?>
Инициатор: <?php echo $this->_tpl_vars['item']['user']; ?>
<br/><br/><br/><br/><br/><?php echo $this->_tpl_vars['sum_of_cash']; ?>


<?php endforeach; endif; unset($_from); ?>

</textarea>
<img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()">


<?php else: ?>
<p>Заданий нет</p>
<?php endif; ?>
</body>
<?php echo '
<style>
#map{width: 794px; height: 784px; padding: 0; margin: 0;}
</style>
<script>

</script>
'; ?>



</html>