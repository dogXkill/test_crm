<?
if(!isset($name_curr_page))
$name_curr_page = 'main';	// ������� �������� �� ���������
$tpus = $user_type;		// ��� ������������
// ������ ���������� ������������� ��������

if(@$auth) {
?>
<table align="center" width=1100 border=0 cellpadding="0" cellspacing="0" id="main_menu">
<tr>
<td height="30">
| <a href="/" class="menu_link" id="main">�����</a> |
<?php
if ($_COOKIE['order_access'] == '1' || $user_access['order_access'] == '1') {
  ?><a href="/acc/query/" class="menu_link" id="query_list">������</a> |<?
}
?>
<?
if ($_COOKIE['shop_access'] == '1' || $user_access['shop_access'] == '1') {
  ?> <a href="/acc/shop/list.php?notemplate=0" class="menu_link" id="shop">�������</a> |<?
}
?>
<?
if ($_COOKIE['tasks_access'] == '1' || $user_access['tasks_access'] == '1') {
  ?> <a href="/acc/tasks/" class="menu_link" id="tasks">������</a> |<?
}
?>
<?
if ($_COOKIE['plans_access'] == '1' || $user_access['plans_access'] == '1') {
  ?> <a href="/acc/plan/" class="menu_link" id="plan">�����������</a> |<?
}
?>
<?
if ($_COOKIE['sprav_access'] == '1' || $user_access['sprav_access'] == '1') {
  ?> <a href="/acc/sprav/" class="menu_link" id="sprav">�����������</a>  | <?
}
?>
<?
if ($_COOKIE['proizv_access'] == '1' || $user_access['proizv_access'] == '1') {
  ?> <a href="/acc/applications/" class="menu_link" id="apl_list">������������</a> |<?
}
?>
<?
if ($_COOKIE['logistics_access'] == '1' || $user_access['logistics_access'] == '1') {
  ?> <a href="/acc/logistic/index.php" class="menu_link" id="logistic">���������</a> |<?
}
?>
<?
if ($_COOKIE['list_access'] == '1' || $user_access['list_access'] == '1') {
  ?> <a href="/acc/applications/timetable/report.php" class="menu_link" id="vedomost" target="_blank">���������</a> |<?
}
?>
<?
if (($_COOKIE['show_departments'] !== '0' || $_COOKIE['job_id'] == '10002')
    || ($user_access['show_departments'] !== '0' || $user_access['job_id'] == '10002')
) {
  ?> <a href="/acc/users/users.php" class="menu_link" id="sotr">����������</a> |<?
}
?>

</td>
</tr>
</table>
<script>
function mark_up(){
 markup_id = '<?=$name_curr_page;?>';
 if(markup_id !== "")
 $("#"+markup_id).css({'font-weight':'bold'});
}
mark_up()
</script>
<?}?>
