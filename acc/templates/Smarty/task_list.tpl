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
	    alert('�������� ������: ' + xhr.responseCode);
          }
        });}
window.print();

}
</script>
{/literal}
<title>������� ���� �� {$date} ��� {$courier} </title>
</head>
<body>

<h3>������� ���� {$date} ��������: {$courier} <img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()"> {$vrem} </h3>

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
{if $courier_tasks}
<table width=800 border=0><tr><td><div style="position:relative;"><img src="../i/moscow_map.png" border="0" />{foreach from=$courier_tasks item=item key=key}{if $item.map_x}<div style="position:absolute;right:{$item.map_x}px;bottom:{$item.map_y}px;z-index:{$key};" class=round><a href="#{$item.num}" class=href>{$item.num}</a>
</div>{/if}{/foreach}</div></td></tr></table>
����� �����: <strong>{$tochek}</strong><br>
����� ��������: {$cash}�<br>
��������������� �������������� ��������: {if ($opl_voditel < 1500)}1500�{else}{$opl_voditel}{/if}<br>
�����: <strong>{$sdacha}�</strong>

<div style="page-break-after:always"></div>

{php}
include('show_how_much.php');
{/php}


{$vrem}

<div style="page-break-after:always"></div>

{assign var=key1 value=`$key`}
<table width=800>

{foreach from=$courier_tasks item=item key=key}

<tr>

<td valign="top" style="border:{if $item.first_point} 4px  dashed {else} 2px  solid {/if} black;">
<a name="{$item.num}"></a>
<strong>#</strong> <b>{$item.num}</b><br/>
<strong>����������:</strong> <b><a href="http://crm.upak.me/acc/logistic/courier_tasks.php?id={$item.id}">{$item.text}</a></b> ����� ������: <b>{$item.query_id}</b><br/>

{if $item.query_id}
<br>
����� ������: <span style="font-weight: bold; color: #00D600">
{if ($item.form_of_payment == "") }��� ������{/if}
{if ($item.form_of_payment == "0") }��� ������{/if}
{if ($item.form_of_payment == "1") }�������� c����: {if ($item.cash_payment > "0") }<b>{$item.cash_payment} �.</b>{/if}
{if ($item.cash_payment == "0") }<b>��������</b>{/if}{/if}
{if ($item.form_of_payment == "2") }������ �� �����{/if}
{if ($item.form_of_payment == "3") }������ �� ���������{/if}
{if ($item.form_of_payment == "4") }�� �����. ����� ������:  {$item.prdm_sum_acc} �.{/if}</span>
 <br>
{else}
<b>��� �������� � ������</b>
{/if}
<br>
{if ($item.cash_payment > "0")}<br>����� � ���������: <b>{$item.cash_payment} �.</b><br>{/if}
�������������� ��������: <b>{$item.opl_voditel} �. (��������������)</b>
<br>
<br>
{if $item.first_point}<h2>������ �����, ������ �� {$item.first_point}</h2>{/if}


<strong>�����:</strong> {$item.address}<br/>
{if $item.metro ne ""}<strong>�����:</strong> {$item.metro}<br/>{/if}
{if $item.address_real}<strong>����� ��������:</strong> {$item.address_real}<br/>{/if}
<strong>���������� ����:</strong> {$item.contact_name}<br/>
<strong>���������� ��������:</strong> {$item.contact_phone}<br/>
{if $item.comment ne ""}<strong>����������1:</strong> {$item.comment}<br/>{/if}
<br/><br/>

<strong>���������:</strong> {$item.user}<br/><br/>
���� �������: ___________________ / _______________________  <br/><br/><br/>{$sum_of_cash}
�����

</td>
</tr>

{/foreach}
</table>

<input type="hidden" id=task_date value="������� ���� �� {$date} �� {$courier} ">
<textarea id=tasks style="width:1px;height:1px;">

������� ���� �� {$date}<br/>
�����������: {$courier}<br>
{$vrem}

<br><br>����� �����: {$tochek}<br>
����� ��������: {$cash}�<br>
��������������� �������������� ��������: {if ($opl_voditel < 1500)}1500�{else}{$opl_voditel}{/if}<br>
�����: {$sdacha}�
<br><br>
{assign var=key1 value=`$key`}
{foreach from=$courier_tasks item=item key=key}
����: {$item.text}<br/>
{if $item.query_id}
<br>
����� ������:
{if ($item.form_of_payment == "") }��� ������{/if}
{if ($item.form_of_payment == "0") }��� ������{/if}
{if ($item.form_of_payment == "1") }�������� c����: {if ($item.cash_payment > "0") }{$item.cash_payment} �.{/if}
{if ($item.cash_payment == "0") }��������{/if}{/if}
{if ($item.form_of_payment == "2") }������ �� �����{/if}
{if ($item.form_of_payment == "3") }������ �� ���������{/if}
{if ($item.form_of_payment == "4") }������{/if}
 <br>
{else}
��� �������� � ������
{/if}
<br>
{if ($item.cash_payment > "0")}<br>����� � ���������: {$item.cash_payment} �.<br>{/if}
�������������� ��������: {$item.opl_voditel} �. (��������������)
<br>
<br>
{if $item.first_point}<h2>������ �����, ������ �� {$item.first_point}</h2>{/if}

�����: {$item.address}<br/>
{if $item.metro ne ""}�����: {$item.metro}<br/>{/if}
{if $item.address_real}����� ��������: {$item.address_real}<br/>{/if}
���������� ����: {$item.contact_name}<br/>
���������� ��������: {$item.contact_phone}<br/>

{if $item.comment ne ""}����������: {$item.comment}<br/>{/if}
���������: {$item.user}<br/><br/><br/><br/><br/>{$sum_of_cash}

{/foreach}

</textarea>
<img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()">


{else}
<p>������� ���</p>
{/if}
</body>
</html>