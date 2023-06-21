<?php /* Smarty version 2.6.26, created on 2022-10-28 16:11:12
         compiled from task_list.tpl */ ?>
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
	    alert(\'�������� ������: \' + xhr.responseCode);
          }
        });}
window.print();

}
</script>
'; ?>

<title>������� ���� �� <?php echo $this->_tpl_vars['date']; ?>
 ��� <?php echo $this->_tpl_vars['courier']; ?>
 </title>
</head>
<body>
<h3>������� ���� <?php echo $this->_tpl_vars['date']; ?>
 ��������: <?php echo $this->_tpl_vars['courier']; ?>
 <img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()"> <?php echo $this->_tpl_vars['vrem']; ?>
 </h3>

<div style="font-size:9px;border: 1px solid; color: black; padding: 5px; width:782px;">

1) ���� ����� � �������� ������� �������������� <u>������ �� ���������</u>. ���������� �������� � �������� ��������� ����� - ��������������� ��������.
�������� ����������� ����� �� ���������, ��������� ������� ������������ �� ���� ����.<br>
2) ��� ����������� �������� � ������ ����� ����� �� - ��������� �������� � ������� ������� ������������ ������ ������������ ����������� ������ ���������<br>
3) �������� ������ �� 1 ��� �� �������� ��������� ������� � ������������ � �������. ��� ������������� ��������� ���� � ������� ����, �������� ������ �������������� ����������� �� ������� � ������������ �� ������ ����, � ��� ����� ��������� ��������� - ����������<br>
4) ��� �������������, �������� ��������� ���� "�� ������" ����������<br>
5) ���� �������������� �������� ����� ����� ��� ���� ��������� � ����� ��, �� ������ ����������� ���� ������������ � ���� �� ���� ������������ �������� �������������, �� ������� ������<br>
6) �������� ������ ����� ��� ��������� � �������, � ��������������� � ������������ � ������� ������. ����� ��������� � ������� ����� �������� ������ �������� �������<br>
7) ��� ���������� � ������� ������������ ���� ������ ��� ������������, ��������, <b>� �������� ����������</b>, ����� �������� ���� �� �������������� ����������� ������ ���������, ��� ��������� ����������� ����������, ��� ���� �������� ���������� ������� � ����<br>
<b>8) ��� �������� ����� ����� ��, ����������� ����� ������ ���� ������� ����������� ������������, ��������� � ����������� ����������, ����, ���� �� ������� � �������<br>
9) ����������� ����� ����� � �� � ����������� ���� � ������ ���������. ���������� ���� ����������� ��, ������ ��������������� ���������� ��������</b>
</div>
<br>
<?php if ($this->_tpl_vars['courier_tasks']): ?>
<table width=800 border=0><tr><td><div style="position:relative;"><img src="../i/moscow_map.png" border="0" /><?php $_from = $this->_tpl_vars['courier_tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?><?php if ($this->_tpl_vars['item']['map_x']): ?><div style="position:absolute;right:<?php echo $this->_tpl_vars['item']['map_x']; ?>
px;bottom:<?php echo $this->_tpl_vars['item']['map_y']; ?>
px;z-index:<?php echo $this->_tpl_vars['key']; ?>
;" class=round><a href="#<?php echo $this->_tpl_vars['item']['num']; ?>
" class=href><?php echo $this->_tpl_vars['item']['num']; ?>
</a>
</div><?php endif; ?><?php endforeach; endif; unset($_from); ?></div></td></tr></table>
����� �����: <strong><?php echo $this->_tpl_vars['tochek']; ?>
</strong><br>
����� ��������: <?php echo $this->_tpl_vars['cash']; ?>
�<br>
��������������� �������������� ��������: <?php if (( $this->_tpl_vars['opl_voditel'] < 1500 )): ?>1500�<?php else: ?><?php echo $this->_tpl_vars['opl_voditel']; ?>
<?php endif; ?><br>
�����: <strong><?php echo $this->_tpl_vars['sdacha']; ?>
�</strong>

<div style="page-break-after:always"></div>

<?php 
include('show_how_much.php');
 ?>


<?php echo $this->_tpl_vars['vrem']; ?>


<div style="page-break-after:always"></div>

<?php $this->assign('key1', ($this->_tpl_vars['key'])); ?>
<table width=800>

<?php $_from = $this->_tpl_vars['courier_tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>

<tr>

<td valign="top" style="border:<?php if ($this->_tpl_vars['item']['first_point']): ?> 4px  dashed <?php else: ?> 2px  solid <?php endif; ?> black;">
<a name="<?php echo $this->_tpl_vars['item']['num']; ?>
"></a>
<strong>#</strong> <b><?php echo $this->_tpl_vars['item']['num']; ?>
</b><br/>
<strong>����������:</strong> <b><a href="http://crm.upak.me/acc/logistic/courier_tasks.php?id=<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['text']; ?>
</a></b> ����� ������: <b><?php echo $this->_tpl_vars['item']['query_id']; ?>
</b><br/>

<?php if ($this->_tpl_vars['item']['query_id']): ?>
<br>
����� ������: <span style="font-weight: bold; color: #00D600">
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == "" )): ?>��� ������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '0' )): ?>��� ������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '1' )): ?>�������� c����: <?php if (( $this->_tpl_vars['item']['cash_payment'] > '0' )): ?><b><?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 �.</b><?php endif; ?>
<?php if (( $this->_tpl_vars['item']['cash_payment'] == '0' )): ?><b>��������</b><?php endif; ?><?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '2' )): ?>������ �� �����<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '3' )): ?>������ �� ���������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '4' )): ?>�� �����. ����� ������:  <?php echo $this->_tpl_vars['item']['prdm_sum_acc']; ?>
 �.<?php endif; ?></span>
 <br>
<?php else: ?>
<b>��� �������� � ������</b>
<?php endif; ?>
<br>
<?php if (( $this->_tpl_vars['item']['cash_payment'] > '0' )): ?><br>����� � ���������: <b><?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 �.</b><br><?php endif; ?>
�������������� ��������: <b><?php echo $this->_tpl_vars['item']['opl_voditel']; ?>
 �. (��������������)</b>
<br>
<br>
<?php if ($this->_tpl_vars['item']['first_point']): ?><h2>������ �����, ������ �� <?php echo $this->_tpl_vars['item']['first_point']; ?>
</h2><?php endif; ?>


<strong>�����:</strong> <?php echo $this->_tpl_vars['item']['address']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['metro'] != ""): ?><strong>�����:</strong> <?php echo $this->_tpl_vars['item']['metro']; ?>
<br/><?php endif; ?>
<?php if ($this->_tpl_vars['item']['address_real']): ?><strong>����� ��������:</strong> <?php echo $this->_tpl_vars['item']['address_real']; ?>
<br/><?php endif; ?>
<strong>���������� ����:</strong> <?php echo $this->_tpl_vars['item']['contact_name']; ?>
<br/>
<strong>���������� ��������:</strong> <?php echo $this->_tpl_vars['item']['contact_phone']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['comment'] != ""): ?><strong>����������:</strong> <?php echo $this->_tpl_vars['item']['comment']; ?>
<br/><?php endif; ?>
<br/><br/>

<strong>���������:</strong> <?php echo $this->_tpl_vars['item']['user']; ?>
<br/><br/>
���� �������: ___________________ / _______________________  <br/><br/><br/><?php echo $this->_tpl_vars['sum_of_cash']; ?>




</td>
</tr>

<?php endforeach; endif; unset($_from); ?>
</table>

<input type="hidden" id=task_date value="������� ���� �� <?php echo $this->_tpl_vars['date']; ?>
 �� <?php echo $this->_tpl_vars['courier']; ?>
 ">
<textarea id=tasks style="width:1px;height:1px;">

������� ���� �� <?php echo $this->_tpl_vars['date']; ?>
<br/>
�����������: <?php echo $this->_tpl_vars['courier']; ?>
<br>
<?php echo $this->_tpl_vars['vrem']; ?>


<br><br>����� �����: <?php echo $this->_tpl_vars['tochek']; ?>
<br>
����� ��������: <?php echo $this->_tpl_vars['cash']; ?>
�<br>
��������������� �������������� ��������: <?php if (( $this->_tpl_vars['opl_voditel'] < 1500 )): ?>1500�<?php else: ?><?php echo $this->_tpl_vars['opl_voditel']; ?>
<?php endif; ?><br>
�����: <?php echo $this->_tpl_vars['sdacha']; ?>
�
<br><br>
<?php $this->assign('key1', ($this->_tpl_vars['key'])); ?>
<?php $_from = $this->_tpl_vars['courier_tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
����: <?php echo $this->_tpl_vars['item']['text']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['query_id']): ?>
<br>
����� ������:
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == "" )): ?>��� ������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '0' )): ?>��� ������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '1' )): ?>�������� c����: <?php if (( $this->_tpl_vars['item']['cash_payment'] > '0' )): ?><?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 �.<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['cash_payment'] == '0' )): ?>��������<?php endif; ?><?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '2' )): ?>������ �� �����<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '3' )): ?>������ �� ���������<?php endif; ?>
<?php if (( $this->_tpl_vars['item']['form_of_payment'] == '4' )): ?>������<?php endif; ?>
 <br>
<?php else: ?>
��� �������� � ������
<?php endif; ?>
<br>
<?php if (( $this->_tpl_vars['item']['cash_payment'] > '0' )): ?><br>����� � ���������: <?php echo $this->_tpl_vars['item']['cash_payment']; ?>
 �.<br><?php endif; ?>
�������������� ��������: <?php echo $this->_tpl_vars['item']['opl_voditel']; ?>
 �. (��������������)
<br>
<br>
<?php if ($this->_tpl_vars['item']['first_point']): ?><h2>������ �����, ������ �� <?php echo $this->_tpl_vars['item']['first_point']; ?>
</h2><?php endif; ?>

�����: <?php echo $this->_tpl_vars['item']['address']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['metro'] != ""): ?>�����: <?php echo $this->_tpl_vars['item']['metro']; ?>
<br/><?php endif; ?>
<?php if ($this->_tpl_vars['item']['address_real']): ?>����� ��������: <?php echo $this->_tpl_vars['item']['address_real']; ?>
<br/><?php endif; ?>
���������� ����: <?php echo $this->_tpl_vars['item']['contact_name']; ?>
<br/>
���������� ��������: <?php echo $this->_tpl_vars['item']['contact_phone']; ?>
<br/>
<?php if ($this->_tpl_vars['item']['comment'] != ""): ?>����������: <?php echo $this->_tpl_vars['item']['comment']; ?>
<br/><?php endif; ?>
���������: <?php echo $this->_tpl_vars['item']['user']; ?>
<br/><br/><br/><br/><br/><?php echo $this->_tpl_vars['sum_of_cash']; ?>


<?php endforeach; endif; unset($_from); ?>

</textarea>
<img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()">


<?php else: ?>
<p>������� ���</p>
<?php endif; ?>
</body>
</html>