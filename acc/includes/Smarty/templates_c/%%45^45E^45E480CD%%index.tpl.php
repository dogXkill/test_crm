<?php /* Smarty version 2.6.26, created on 2023-02-08 18:12:02
         compiled from index.tpl */ ?>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=cc64a72b-8539-43fc-85b0-cdf3d05fc153" type="text/javascript"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/lang/calendar-ru.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar-setup.js"></script>



<?php if ($this->_tpl_vars['courier_tasks']): ?>

<form action="task_list.php" method="get" target="_blank">
<table>
<tr><td><strong>�����������:</strong>&nbsp;<select type="text" name="courier_id" id="courier_id" class="vod_select"><?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i']):
?><option value="<?php echo $this->_tpl_vars['i']['id']; ?>
"><?php echo $this->_tpl_vars['i']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?>
<option value="all">�� ����</option>
</select>
<strong>���� �������:</strong> <input type="text" name="date" value="<?php echo $this->_tpl_vars['date']; ?>
" size="10" class="date_select" id="date" />
<input type="submit" value="������� ����" class="put_list" />
<input type="button" value="������� �����" class="put_list" onclick="show_how_much()" />
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
var r1="<a href=\'#\' class=\'btn_clear_poisk\'><img src=\'/acc/i/x.gif\'></a>";
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
//���� �� ������ � ��������, ������������ save_but


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

}else{$(\'#search_result\').html("<br><span style=\\"color: red; font-weight:bold;\\">������ �� �������</span>");}
}});
}
}

function fcs(){$(\'#val\').focus()}


 $(document).ready(function() {
		var flag_clear_btn=0;//����
     //$(\'#val\').AddXbutton({ img: \'/acc/i/x.gif\' });
		
		$(\'#val\').keyup(function(){
			if (flag_clear_btn==0){
				$(\'#val\').closest(\'td\').append(r1);
				$("#search_result").show();
				$(".btn_clear_poisk").click(function(e){
					//
					$("#no_search").show();
					$("#search_result").hide();
					$(this).remove();
					flag_clear_btn=0;
					$(\'#val\').val("");
					//
				});
				flag_clear_btn=1;
			}
		});
		
 });


 /*]]>*/
</script>

<table border="0" cellspacing="5" cellpadding="0">
<tr>
	  	<td align="center">
	  	<input id="val" onload=fcs() name="val" autocomplete="off" onkeydown="show_result()"  value="" class=search type="text"/>
	  	</td>
		<td valign=bottom>
			<input name="submit" type="button" onclick="show_result()" class="search_submit" value="�����!"/></form>
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
<div style="display:none;" id="adress_yandex">
<?php echo $this->_tpl_vars['item']['address']; ?>


</div>
<table class=highlightd <?php if ($this->_tpl_vars['item']['first_point']): ?>style="border:2px dashed;"<?php endif; ?> height="100%"  width=100%>
<tr>
<td valign=top><div>
<?php if ($this->_tpl_vars['item']['coord_yandex']): ?>
<img src="https://static-maps.yandex.ru/1.x/?spn=0.3,0.3&size=300,300&pt=<?php echo $this->_tpl_vars['item']['coord_yandex']; ?>
,pm2rdm&l=map" border="0" width=198 height=196/>
<?php endif; ?>
</td>
<td valign="top">

<b>����:</b> <a href="/acc/logistic/courier_tasks.php?id=<?php echo $this->_tpl_vars['item']['id']; ?>
" target=_blank><strong><?php echo $this->_tpl_vars['item']['text']; ?>
</strong></a><br/><br>

<?php if ($this->_tpl_vars['item']['query_id']): ?>
����� #: <b><a href="/acc/query/query_send.php?show=<?php echo $this->_tpl_vars['item']['query_id']; ?>
" target_blank><?php echo $this->_tpl_vars['item']['query_id']; ?>
</a></b><br>

����� ������: <span style="font-weight: bold; color: #00D600">
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == "" )): ?>��� ������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '0' )): ?>��� ������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '1' )): ?>��������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '2' )): ?>������ �� �����<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '3' )): ?>������ �� ���������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '4' )): ?>������<?php endif; ?></span>
<br>
<?php else: ?>
<b>��� �������� � ������</b>
<?php endif; ?>

<br>����� � ���������: <b><?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 �.</b><br>
�������������� ��������: <b><?php echo $this->_tpl_vars['item']['opl_voditel']; ?>
 �.</b>
<br>
<br>
<?php if ($this->_tpl_vars['item']['first_point']): ?>������ �����, ������ �� <?php echo $this->_tpl_vars['item']['first_point']; ?>
<br><br><?php endif; ?>


<b>�����:</b> <a href="http://maps.yandex.ru/?text=<?php echo $this->_tpl_vars['item']['address']; ?>
" target=_blank><img src="../i/search.png"  border="0" alt="" valign="middle"></a> <?php echo $this->_tpl_vars['item']['address']; ?>
<br/>
<b>���������� ����:</b> <?php echo $this->_tpl_vars['item']['contact_name']; ?>
<br/>
<b>���������� ��������:</b> <?php echo $this->_tpl_vars['item']['contact_phone']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['comment'] != ""): ?><strong><b>����������</b>:</strong> <?php echo $this->_tpl_vars['item']['comment']; ?>
<br/><?php endif; ?>
<b>���������:</b> <?php echo $this->_tpl_vars['item']['user']; ?>
<br/>


</td>
</tr>
<tr><td width="35%"><a href="/acc/logistic/courier_tasks.php?id=<?php echo $this->_tpl_vars['item']['id']; ?>
"><img width="20" height="20" src="../i/lupa.gif" onmouseover="Tip('����������� / �������������')" /></a>&nbsp;<a href="/acc/logistic/courier_tasks.php?del=<?php echo $this->_tpl_vars['item']['id']; ?>
&r=1" onClick="return confirm('�� �������, ��� ������ ������� ������?');"><img width="20" height="20" src="../i/del.gif" onmouseover="Tip('�������')" /></a></td>
<td><select type="text" name="courier_id[<?php echo $this->_tpl_vars['item']['id']; ?>
]" disabled><?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i']):
?><option value="<?php echo $this->_tpl_vars['i']['id']; ?>
"<?php if ($this->_tpl_vars['item']['courier_id'] == $this->_tpl_vars['i']['id']): ?> selected<?php endif; ?>><?php echo $this->_tpl_vars['i']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select><br/>
<strong>���� �������:</strong> <input type="text"  value="<?php echo $this->_tpl_vars['item']['date']; ?>
" size="10" disabled/></td></tr></table>
</td>
<?php if ($this->_tpl_vars['item']['num'] == 2): ?>
</tr>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['item']['num'] != 2): ?>
<td></td></tr>
<?php endif; ?>
<tr align="center"><td colspan="2"><input type="submit" value="��������� ���������" class="button_big" /></td></tr>
</table>
</form>
<form action="task_list.php" method="post" target="_blank">
<table>
<tr><td><strong>�����������:</strong>&nbsp;<select type="text" name="courier_id"><?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i']):
?><option value="<?php echo $this->_tpl_vars['i']['id']; ?>
"><?php echo $this->_tpl_vars['i']['name']; ?>
</option><?php endforeach; endif; unset($_from); ?></select> <strong>���� �������:</strong> <input type="text" name="date" value="<?php echo $this->_tpl_vars['date']; ?>
" size="10" id="date" /><a href="#" id="button1" onClick="return false;"><img src="../i/calendar.gif" alt="���������" border="0" /></a> <input type="submit" value="������������ ������� ����" class="button_big" /></td></tr>
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
<p>������� ������� �����������</p>
<?php endif; ?></div>

<div id=search_result style="text-align: left; width: 500px"></div>