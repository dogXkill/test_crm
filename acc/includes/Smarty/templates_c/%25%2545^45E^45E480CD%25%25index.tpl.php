<?php /* Smarty version 2.6.26, created on 2015-12-23 21:06:50
         compiled from index.tpl */ ?>
<script type="text/javascript" src="../includes/js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/lang/calendar-ru.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar-setup.js"></script>



<?php if ($this->_tpl_vars['courier_tasks']): ?>

<form action="task_list.php" method="get" target="_blank">
<table>
<tr><td><strong>исполнитель:</strong>&nbsp;<select type="text" name="courier_id" id="courier_id" class="vod_select"><?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i']):
?><option value="<?php echo $this->_tpl_vars['i']['id']; ?>
"><?php echo $this->_tpl_vars['i']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?>
<option value="all">на всех</option>
</select>
<strong>дата поездки:</strong> <input type="text" name="date" value="<?php echo $this->_tpl_vars['date']; ?>
" size="10" class="date_select" id="date" />
<input type="submit" value="Путевой лист" class="put_list" />
<input type="button" value="Сколько нужно" class="put_list" onclick="show_how_much()" />
</td></tr>
<?php echo '
<script type="text/javascript">

function show_how_much(){
date = $("#date").val();
courier_id = $("#courier_id").val();
window.open("show_how_much.php?date="+date+"&courier_id="+courier_id);

}


    Calendar.setup({
        inputField     :    "date",      // id of the input field
		button         :    "date",   // trigger for the calendar (button ID)

    });
</script>
<style type="text/css">
<!--
 .search{
   width: 250px; height: 30px; font-size: 20px;
 }
 .search_submit{
   width: 100px; height: 35px; font-size:20px;
 }
 .vod_select{
  width: 350px; height: 30px; font-size: 20px;
 }
  .date_select{
  width: 150px; height: 30px; font-size: 20px;
 }
  .put_list{
  width: 150px; height: 35px; font-size: 20px;
 }

-->
</style>

'; ?>

</table>
</form>
<br />
<?php echo '
<script type="text/javascript">
/*<![CDATA[*/
function check_fltr_form(){
if ($("#val").val() == ""){
$("#val").focus()
}else{
$("#ff_fltr_num").submit()
}}

function show_result(){
var val = $(\'#val\').val();
if (val.length >1){
$("#no_search").hide()
//если не пустой и номерной, разблокируем save_but


//alert(val)
var geturl;
  geturl = $.ajax({
    type: "GET",
    url: \'/acc/logistic/search_logistic.php\',
	data : \'val=\'+val,
    success: function () {
var resp1 = geturl.responseText
//alert(resp1)
if (resp1 !== ""){
$(\'#search_result\').html(resp1);

}else{$(\'#search_result\').html("<br><span style=\\"color: red; font-weight:bold;\\">Ничего не найдено</span>");}
}});
}
}

function fcs(){$(\'#val\').focus()}


 $(document).ready(function() {

     $(\'#val\').AddXbutton({ img: \'/acc/i/x.gif\' });

 });


 /*]]>*/
</script>

<table border="0" cellspacing="5" cellpadding="0">
<tr>
	  	<td align="center">
	  	<input id="val" onload=fcs() name="val" autocomplete="off" onkeydown="show_result()"  value="" class=search type="text"/>
	  	</td>
		<td valign=bottom>
			<input name="submit" type="button" onclick="show_result()" class="search_submit" value="Найти!"/></form>
		</td></tr></table>
'; ?>

<div id=no_search>
<form action="update_tasks.php" method="post">
<table style="background-color: white">
<?php $_from = $this->_tpl_vars['courier_tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<?php if ($this->_tpl_vars['item']['num'] == 1): ?>
<tr align="left" valign="top">
<?php endif; ?>
<td width="50%" align="left" valign="top">
<table class=highlightd <?php if ($this->_tpl_vars['item']['first_point']): ?>style="border:2px dashed;"<?php endif; ?> height="100%"  width=100%>
<tr>
<td valign=top><div><a href="/acc/logistic/courier_tasks.php?id=<?php echo $this->_tpl_vars['item']['id']; ?>
"><img src="../i/moscow_map_small.png" border="0" width=198 height=196/></div><?php if ($this->_tpl_vars['item']['map_x']): ?><div style="position:relative;"></a><img src="../i/top.png" border="0" style="position:absolute;right:<?php echo $this->_tpl_vars['item']['map_x']; ?>
px;bottom:<?php echo $this->_tpl_vars['item']['map_y']; ?>
px;z-index:<?php echo $this->_tpl_vars['key']+1; ?>
;" /></div><?php endif; ?></td>
<td valign="top">

<b>Цель:</b> <a href="/acc/logistic/courier_tasks.php?id=<?php echo $this->_tpl_vars['item']['id']; ?>
" target=_blank><strong><?php echo $this->_tpl_vars['item']['text']; ?>
</strong></a><br/><br>

<?php if ($this->_tpl_vars['item']['query_id']): ?>
Заказ #: <b><a href="/acc/query/query_send.php?show=<?php echo $this->_tpl_vars['item']['query_id']; ?>
" target_blank><?php echo $this->_tpl_vars['item']['query_id']; ?>
</a></b><br>

Форма оплаты: <span style="font-weight: bold; color: #00D600">
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == "" )): ?>нет данных<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '0' )): ?>нет данных<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '1' )): ?>наличные<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '2' )): ?>безнал по счету<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '3' )): ?>безнал по квитанции<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '4' )): ?>прочая<?php endif; ?></span>
<br>
<?php else: ?>
<b>Нет привязки к заказу</b>
<?php endif; ?>

<br>Сумма к получению: <b><?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 р.</b><br>
Вознаграждение водителю: <b><?php echo $this->_tpl_vars['item']['opl_voditel']; ?>
 р.</b>
<br>
<br>
<?php if ($this->_tpl_vars['item']['first_point']): ?>Первая точка, успеть до <?php echo $this->_tpl_vars['item']['first_point']; ?>
<br><br><?php endif; ?>


<b>Адрес:</b> <a href="http://maps.yandex.ru/?text=<?php echo $this->_tpl_vars['item']['address']; ?>
" target=_blank><img src="../i/search.png"  border="0" alt="" valign="middle"></a> <?php echo $this->_tpl_vars['item']['address']; ?>
<br/>
<b>Контактное лицо:</b> <?php echo $this->_tpl_vars['item']['contact_name']; ?>
<br/>
<b>Контактные телефоны:</b> <?php echo $this->_tpl_vars['item']['contact_phone']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['comment'] != ""): ?><strong><b>Примечания</b>:</strong> <?php echo $this->_tpl_vars['item']['comment']; ?>
<br/><?php endif; ?>
<b>Инициатор:</b> <?php echo $this->_tpl_vars['item']['user']; ?>
<br/>


</td>
</tr>
<tr><td width="35%"><a href="/acc/logistic/courier_tasks.php?id=<?php echo $this->_tpl_vars['item']['id']; ?>
"><img width="20" height="20" src="../i/lupa.gif" onmouseover="Tip('Просмотреть / редактировать')" /></a>&nbsp;<a href="/acc/logistic/courier_tasks.php?del=<?php echo $this->_tpl_vars['item']['id']; ?>
&r=1" onClick="return confirm('Вы уверены, что хотите удалить заявку?');"><img width="20" height="20" src="../i/del.gif" onmouseover="Tip('Удалить')" /></a></td>
<td><select type="text" name="courier_id[<?php echo $this->_tpl_vars['item']['id']; ?>
]" disabled><?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i']):
?><option value="<?php echo $this->_tpl_vars['i']['id']; ?>
"<?php if ($this->_tpl_vars['item']['courier_id'] == $this->_tpl_vars['i']['id']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['i']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select><br/>
<strong>дата поездки:</strong> <input type="text"  value="<?php echo $this->_tpl_vars['item']['date']; ?>
" size="10" disabled/></td></tr></table>
</td>
<?php if ($this->_tpl_vars['item']['num'] == 2): ?>
</tr>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['item']['num'] != 2): ?>
<td></td></tr>
<?php endif; ?>
<tr align="center"><td colspan="2"><input type="submit" value="Сохранить изменения" class="button_big" /></td></tr>
</table>
</form>
<form action="task_list.php" method="post" target="_blank">
<table>
<tr><td><strong>исполнитель:</strong>&nbsp;<select type="text" name="courier_id"><?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i']):
?><option value="<?php echo $this->_tpl_vars['i']['id']; ?>
"><?php echo $this->_tpl_vars['i']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select> <strong>дата поездки:</strong> <input type="text" name="date" value="<?php echo $this->_tpl_vars['date']; ?>
" size="10" id="date" /><a href="#" id="button1" onClick="return false;"><img src="../i/calendar.gif" alt="Календарь" border="0" /></a> <input type="submit" value="Сформировать путевой лист" class="button_big" /></td></tr>
</table>
</form>
<?php echo '
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "date",      // id of the input field
        button         :    "button1"   // trigger for the calendar (button ID)
    });




'; ?>


<?php echo '
</script>
'; ?>

<?php else: ?>
<p>Текущие задания отсутствуют</p>
<?php endif; ?></div>

<div id=search_result style="text-align: left; width: 500px"></div>