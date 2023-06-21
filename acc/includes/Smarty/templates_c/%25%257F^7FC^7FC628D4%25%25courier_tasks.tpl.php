<?php /* Smarty version 2.6.26, created on 2021-09-21 11:15:32
         compiled from courier_tasks.tpl */ ?>
<script type="text/javascript" src="../includes/js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/lang/calendar-ru.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar-setup.js"></script>

<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script src="../includes/js/jquery.ui.datepicker-courier-ru.js"></script>
<link rel="stylesheet" href="../includes/js/jquery-ui.css">
<script type="text/javascript">
<?php echo '
jQuery(document).ready(function(){
$("#map").click(function(e){
    var offset = $(this).offset();
        var x = e.pageX - offset.left - 7;
        var y = e.pageY - offset.top - 7;
        x = x.toFixed(0);
        y = y.toFixed(0);
        document.getElementById(\'map_x\').value = x;
        document.getElementById(\'map_y\').value = y;
        document.getElementById(\'map_point\').style.left = "" + x + "px";
        document.getElementById(\'map_point\').style.top = "" + y + "px";
});
})

function init_point()
{
'; ?>

<?php if ($this->_tpl_vars['content']['map_x']): ?>

    document.getElementById('map_point').style.left = "" +  <?php echo $this->_tpl_vars['content']['map_x']; ?>
 + "px";
    document.getElementById('map_point').style.top = "" + <?php echo $this->_tpl_vars['content']['map_y']; ?>
 + "px";

<?php endif; ?>
<?php echo '
}

</script> '; ?>


<form action="<?php if ($this->_tpl_vars['content']): ?>courier_tasks.php?id=<?php echo $_GET['id']; ?>
&query_id=<?php echo $this->_tpl_vars['content']['query_id']; ?>
<?php endif; ?>" id=forma method="post">

<?php if ($_GET['id'] || $this->_tpl_vars['queries']): ?>

<?php if ($this->_tpl_vars['content']['query_id']): ?><input type="hidden" name="query_id" value="<?php echo $_GET['query_id']; ?>
" /><?php endif; ?>
<table border="0" cellspacing="5" cellpadding="5" width=588>
<tr><td class="tab_first_col">Инициатор *</td><td class="tab_two_col"><select type="text" name="user_id" class="cour_wdfull_sel"><?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"<?php if (( $this->_tpl_vars['content']['user_id'] && $this->_tpl_vars['content']['user_id'] == $this->_tpl_vars['item']['id'] ) || ( ! $this->_tpl_vars['content']['user_id'] && $this->_tpl_vars['item']['login'] == $_COOKIE['user_des'] )): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select>
</td></tr>
<tr><td class="tab_first_col">Цель *</td><td class="tab_two_col"><textarea name="text" id=text style="height:70px;" class="cour_wdfull"><?php if ($this->_tpl_vars['content']): ?><?php echo $this->_tpl_vars['content']['text']; ?>
<?php else: ?><?php endif; ?></textarea></td></tr>
<tr><td class="tab_first_col">
Адрес доставки *</td><td class="tab_two_col"><textarea name="address" id=adress  class="cour_wdfull_adress"><?php if ($this->_tpl_vars['content']): ?><?php echo $this->_tpl_vars['content']['address']; ?>
<?php endif; ?></textarea>
<a href="#" onclick="openmap();"><img src="../i/search.png" border="0" alt="" valign="absmiddle" style="cursor: pointer" onclick="search('leg_add')"></a>

<?php echo '
<script>


function check_form(){

    if ($(\'#text\').val() == "")
    {$(\'#text\').focus()
}
    else if ($(\'#adress\').val() == ""){
    $(\'#adress\').focus()
}
    else if ($(\'#courier_id_sel\').val() == "")
    {
    alert("Необходимо выбрать водителя")
        $(\'#courier_id_sel\').focus()
}
    else if ($(\'#opl_voditel\').val() == ""){
    alert("Необходимо назначить водителю оплату. Если ее нет, поставьте просто 0")
    $(\'#opl_voditel\').focus()
}
        else if ($(\'#contact_name\').val() == "")
    {$(\'#contact_name\').focus()
}
    else if ($(\'#contact_phone\').val() == "")
    {$(\'#contact_phone\').focus()
}


    else {
 $(\'#forma\').submit()
    }

}


function check_deliv_possibility(){
deliv_id = \''; ?>
<?php echo $this->_tpl_vars['content']['deliv_id']; ?>
<?php echo '\';
prdm_sum_acc = \''; ?>
<?php echo $this->_tpl_vars['content']['prdm_sum_acc']; ?>
<?php echo '\';
prdm_opl = \''; ?>
<?php echo $this->_tpl_vars['content']['prdm_opl']; ?>
<?php echo '\';
prdm_dolg = prdm_sum_acc - prdm_opl;


if(prdm_dolg > 0 && deliv_id == \'12\'){alert("Самовывоз из шоурума разрешен только после поступления полной оплаты от клиента!")}

}

check_deliv_possibility()
</script>

'; ?>

</td></tr>
<tr>
<td></td>
<td><?php if ($this->_tpl_vars['content']['query_id']): ?>
Привязка к заказу <b><a href="/acc/query/query_send.php?show=<?php echo $this->_tpl_vars['content']['query_id']; ?>
" target=_blank><?php echo $this->_tpl_vars['content']['query_id']; ?>
</a></b>
Сумма заказа: <b><?php echo $this->_tpl_vars['content']['prdm_sum_acc']; ?>
 р.</b> <?php if (( $this->_tpl_vars['content']['prdm_dolg'] > "-1" )): ?>долг: <b><?php echo $this->_tpl_vars['content']['prdm_dolg']; ?>
 р.</b>
<input type="hidden" name="prdm_sum_acc" id="prdm_sum_acc" value="<?php echo $this->_tpl_vars['content']['prdm_sum_acc']; ?>
"/>
<?php endif; ?> Форма оплаты:
<b><?php if (( $this->_tpl_vars['content']['form_of_payment'] == "" )): ?>нет данных<?php endif; ?>
<?php if (( $this->_tpl_vars['content']['form_of_payment'] == '0' )): ?>нет данных<?php endif; ?>
<?php if (( $this->_tpl_vars['content']['form_of_payment'] == '1' )): ?>наличные <img src="/i/cash.png" width="16" height="16" alt="" onmouseover="Tip('Наличные');" align="absmiddle"><?php endif; ?>
<?php if (( $this->_tpl_vars['content']['form_of_payment'] == '2' )): ?>безнал по счету <img src="/i/invoice16.png" width="16" height="16" alt="" onmouseover="Tip('Безнал по счету');" align="absmiddle"><?php endif; ?>
<?php if (( $this->_tpl_vars['content']['form_of_payment'] == '3' )): ?>безнал по квитанции <img src="/i/kvit16.png" width="16" height="16" alt="" onmouseover="Tip('По квитанции');" align="absmiddle"><?php endif; ?>
<?php if (( $this->_tpl_vars['content']['form_of_payment'] == '4' )): ?>по карте<?php endif; ?></b>

<br>
<?php if (( $this->_tpl_vars['content']['form_of_payment'] == '2' && ( $this->_tpl_vars['content']['prdm_dolg'] > 0 || $this->_tpl_vars['content']['prdm_dolg'] == "" ) )): ?>
<span style="font-size:15px;font-weight:bold; border: 1px solid; color: #FF0000; padding: 5px;">От клиента оплата поступила не полностью или не отмечена. Вы уверены что стоит отгружать?</span><?php endif; ?>
<br>
<br>

<?php else: ?>
<br>
<b>Нет привязки к заказу</b>
<?php endif; ?>

Наличные к получению водителем:
<input type=text name="cash_payment" id="cash_payment" value="
<?php if (( ! $this->_tpl_vars['content']['cash_payment'] && ( $this->_tpl_vars['content']['form_of_payment'] == '1' || $this->_tpl_vars['content']['form_of_payment'] == '4' ) && $this->_tpl_vars['content']['cash_payment'] !== '0' )): ?>
<?php if (( $this->_tpl_vars['content']['prdm_opl'] )): ?><?php echo $this->_tpl_vars['content']['prdm_dolg']; ?>

<?php else: ?>
<?php echo $this->_tpl_vars['content']['prdm_sum_acc']; ?>
<?php endif; ?>

<?php endif; ?>
<?php echo $this->_tpl_vars['content']['cash_payment']; ?>

" size=6 maxlength=6> оплата водителю: <input type=text name="opl_voditel" id="opl_voditel" value="<?php echo $this->_tpl_vars['content']['opl_voditel']; ?>
" size=6 maxlength=6>



</td>
</tr>
<tr><td class="tab_first_col">На карте</td><td class="tab_two_col">


<div id=map style="margin-top:5px;border: 1px solid;background-image: url(../i/moscow_map.png);background-repeat: no-repeat;width:792px;height:784px;"><img src="../i/top.png" border="0" id="map_point" style="position:relative;z-index:100;"/></div>
<div id=map_g style="margin-top:5px;"></div>

<input type="hidden" name="map_x" value="<?php echo $this->_tpl_vars['content']['map_x']; ?>
" id="map_x" /><input type="hidden" name="map_y" value="<?php echo $this->_tpl_vars['content']['map_y']; ?>
" id="map_y" /></td></tr>
<tr style="display:none;"><td class="tab_first_col">Ближайшее метро</td><td class="tab_two_col"><select type="text" name="metro_id" ><option></option><?php $_from = $this->_tpl_vars['metro']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"<?php if ($this->_tpl_vars['content'] && $this->_tpl_vars['content']['metro_id'] == $this->_tpl_vars['item']['id']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select></td></tr>
<tr><td class="tab_first_col">Контактное лицо *</td><td class="tab_two_col"><input type="text" id=contact_name class="cour_wdfull_cnt" name="contact_name"<?php if ($this->_tpl_vars['content']): ?> value="<?php echo $this->_tpl_vars['content']['contact_name']; ?>
"<?php endif; ?>  size=100/></td></tr>
<tr><td class="tab_first_col">Контактный телефон *</td><td class="tab_two_col"><input type="text" id=contact_phone class="cour_wdfull_cnt" name="contact_phone"<?php if ($this->_tpl_vars['content']): ?> value="<?php echo $this->_tpl_vars['content']['contact_phone']; ?>
"<?php endif; ?>  size=100 /></td></tr>
<tr><td class="tab_first_col">Адрес отправки (при отправке через транспортную компанию)</td><td class="tab_two_col"><textarea name="address_real" class="cour_wdfull"><?php if ($this->_tpl_vars['content']): ?><?php echo $this->_tpl_vars['content']['address_real']; ?>
<?php endif; ?></textarea></td></tr>
<tr><td class="tab_first_col">Предполагаемая дата поездки *</td><td class="tab_two_col">
<input type="text" name="date" class="frm_wdfull_date" value="<?php if ($this->_tpl_vars['content']['date']): ?><?php echo $this->_tpl_vars['content']['date']; ?>
<?php else: ?><?php echo $this->_tpl_vars['date']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['content']['st_date']): ?><?php echo $this->_tpl_vars['content']['st_date']; ?>
<?php else: ?><?php echo $this->_tpl_vars['st_date']; ?>
<?php endif; ?>" id="date" />
<span onclick="set_date('0')" style="text-decoration: underline; cursor: pointer">сегодня</span> &nbsp;&nbsp;&nbsp;
<span onclick="set_date('1')" style="text-decoration: underline; cursor: pointer">завтра</span>
&nbsp;&nbsp;&nbsp;
<span onclick="set_date('2')" style="text-decoration: underline; cursor: pointer">послезавтра</span>
&nbsp;&nbsp;&nbsp;
<span onclick="set_date('3')" style="text-decoration: underline; cursor: pointer">после-послезавтра</span>
<?php echo '
<script type="text/javascript">
$("#date").datepicker($.datepicker.regional[ "ru" ]);

function set_date(days){
$("#date").datepicker("setDate", \'+\'+days);
}

function change_id(){

cour_sev_val = $("#courier_id_sel").val();
cour_sev_val = cour_sev_val.split(\';\')

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
'; ?>

</td></tr>
<tr><td class="tab_first_col">Предполагаемый исполнитель *</td><td class="tab_two_col">
<select type="text" id=courier_id_sel name="courier_id_sel"  class="cour_wdfull_sel" onchange="change_id()">

<option value="">выберите водителя</option>
<?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><option value="<?php echo $this->_tpl_vars['item']['id']; ?>
;<?php echo $this->_tpl_vars['item']['base_tarif']; ?>
" <?php if ($this->_tpl_vars['content'] && $this->_tpl_vars['content']['courier_id'] == $this->_tpl_vars['item']['id']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select>

<input type="hidden" name="courier_id" id=courier_id value="<?php echo $this->_tpl_vars['content']['courier_id']; ?>
"/>
</td></tr>
<tr><td class="tab_first_col">Комментарий</td><td class="tab_two_col">

<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border=1 cellspacing=0 cellpadding=5 width=500>
<?php $_from = $this->_tpl_vars['queries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<tr>
<td><?php echo $this->_tpl_vars['item']['name']; ?>
</td>
<td><?php echo $this->_tpl_vars['item']['col']; ?>
 шт</td>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table>


<textarea name="comment" class="cour_wdfull_comm">
<?php if ($this->_tpl_vars['queries']): ?><?php $_from = $this->_tpl_vars['queries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><?php echo $this->_tpl_vars['item']['name']; ?>
 - <?php echo $this->_tpl_vars['item']['col']; ?>
 шт.
<?php endforeach; endif; unset($_from); ?>
<?php echo $this->_tpl_vars['content']['note']; ?>
<?php else: ?><?php echo $this->_tpl_vars['content']['comment']; ?>
<?php endif; ?>
</textarea></td></tr>

<tr><td class="tab_first_col"><label for="first_point_chk">Первая точка!</label></td><td class="tab_two_col" align="left">
<input type="checkbox" id="first_point_chk" onchange="set_first_point()" name="first_point_chk"<?php if ($this->_tpl_vars['content'] && $this->_tpl_vars['content']['first_point']): ?> checked<?php endif; ?> />
<span id="first_point_span" style="display:<?php if (! $this->_tpl_vars['content']['first_point']): ?>none<?php endif; ?>">успеть до:<input type="text" value="<?php echo $this->_tpl_vars['content']['first_point']; ?>
" name="first_point" id="first_point" style="width:200px;" maxlength="255"/></span>
</td>
</tr>
<tr><td colspan="2" align="center"><input type="button" onclick="check_form(); return false;" value="<?php if ($this->_tpl_vars['content'] && ! $this->_tpl_vars['queries']): ?>Изменить задание<?php else: ?>Добавить задание<?php endif; ?>" style="width:500px;font-size:30px;height:50px;" /></td></tr>
</table>
</form>
<?php echo '
<script type="text/javascript">  /*
        Calendar.setup({
                inputField     :    "date",      // id of the input field
        button         :    "date",   // trigger for the calendar (button ID)

        });   */
</script>
'; ?>

<?php else: ?>
<form action="" method="post">
<table border="0" cellspacing="5" cellpadding="5">
<tr>
<td class="tab_two_col">Дата доставки: с <input type="text" name="date_from" value="<?php echo $this->_tpl_vars['search']['date_from']; ?>
" id="date_from" /><a href="#" id="button3" onClick="return false;"><img src="../i/calendar.gif" alt="Календарь" border="0" /></a> по <input type="text" name="date_to" value="<?php echo $this->_tpl_vars['search']['date_to']; ?>
" id="date_to" /><a href="#" id="button4" onClick="return false;"><img src="../i/calendar.gif" alt="Календарь" border="0" /></a>
<br>Исполнитель: <select type="text" name="courier_id"><option>все исполнители</option><?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"<?php if ($this->_tpl_vars['search']['courier_id'] == $this->_tpl_vars['item']['id']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select>
<br>Инициатор: <select type="text" name="user_id"><option>все инициаторы</option><?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"<?php if ($this->_tpl_vars['search']['user_id'] == $this->_tpl_vars['item']['id']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select>
Статус: <select type="text" name="done"><option value="">все</option><option value="0"<?php if ($this->_tpl_vars['search']['done'] == '0'): ?> selected<?php endif; ?>>актуальные</option><option value="1" <?php if ($this->_tpl_vars['search']['done'] == '1'): ?> selected<?php endif; ?>>выполненные</option></select></td>
</tr>
<tr><td align=center><input type="submit" value="Поиск" style="width: 100px; height: 30px; font-size: 17px;"/></td></tr>
</table>
</form>
<?php if ($this->_tpl_vars['courier_tasks']): ?>
<table>
<tr class="tab_query_tit">
<td class="tab_query_tit">Задание</td>
<td class="tab_query_tit">Дата поездки</td>

<td class="tab_query_tit">Инициатор</td>
<td class="tab_query_tit">Исполнитель</td>
<td class="tab_query_tit">Архив</td>
<td class="tab_query_tit" colspan="2">Операции</td>
</tr>
<?php $_from = $this->_tpl_vars['courier_tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<tr>
<td class="tab_td_norm" onmouseover="Tip('<div class=stat_podr_alt><?php echo $this->_tpl_vars['item']['text']; ?>
</div>')"><?php echo $this->_tpl_vars['item']['text_small']; ?>
</td>
<td class="tab_td_norm"><?php echo $this->_tpl_vars['item']['date']; ?>
</td>

<td class="tab_td_norm"><?php echo $this->_tpl_vars['item']['user']; ?>
</td>
<td class="tab_td_norm"><?php echo $this->_tpl_vars['item']['courier']; ?>
</td>
<td class="tab_td_norm"><?php if ($this->_tpl_vars['item']['done'] == 1): ?>+<?php else: ?>-<?php endif; ?></td>
<td class="tab_td_norm"><a href="?id=<?php echo $this->_tpl_vars['item']['id']; ?>
" onmouseover="Tip('Редактировать')"><img width="20" height="20" src="../i/edit2.gif" /></a></td>
<td class="tab_td_norm"><a href="?del=<?php echo $this->_tpl_vars['item']['id']; ?>
" onmouseover="Tip('Удалить')" onClick="return confirm('Вы уверены, что хотите удалить?');"><img widt="20" height="20" src="../i/del.gif" /></a></td>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
<?php endif; ?>
<?php echo '
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
'; ?>

<?php endif; ?>
<script type="text/javascript">
init_point();
</script>