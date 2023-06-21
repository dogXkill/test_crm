<?
if(!isset($name_curr_page))
$name_curr_page = 'main';	// главная страница по умолчанию
$tpus = $user_type;		// тип пользователя
// чтение количества невыполненных запросов

if(@$auth) {
?>
<table align="center" width=1100 border=0 cellpadding="0" cellspacing="0" id="main_menu">
<tr>
<td height="30">
| <a href="/" class="menu_link" id="main">Общие</a> |
<?php
if ($_COOKIE['order_access'] == '1' || $user_access['order_access'] == '1') {
  ?><a href="/acc/query/" class="menu_link" id="query_list">Заказы</a> |<?
}
?>
<?
if ($_COOKIE['shop_access'] == '1' || $user_access['shop_access'] == '1') {
  ?> <a href="/acc/shop/list.php?notemplate=0" class="menu_link" id="shop">Магазин</a> |<?
}
?>
<?
if ($_COOKIE['tasks_access'] == '1' || $user_access['tasks_access'] == '1') {
  ?> <a href="/acc/tasks/" class="menu_link" id="tasks">Задачи</a> |<?
}
?>
<?
if ($_COOKIE['plans_access'] == '1' || $user_access['plans_access'] == '1') {
  ?> <a href="/acc/plan/" class="menu_link" id="plan">Планировщик</a> |<?
}
?>
<?
if ($_COOKIE['sprav_access'] == '1' || $user_access['sprav_access'] == '1') {
  ?> <a href="/acc/sprav/" class="menu_link" id="sprav">Справочники</a>  | <?
}
?>
<?
if ($_COOKIE['proizv_access'] == '1' || $user_access['proizv_access'] == '1') {
  ?> <a href="/acc/applications/" class="menu_link" id="apl_list">Производство</a> |<?
}
?>
<?
if ($_COOKIE['logistics_access'] == '1' || $user_access['logistics_access'] == '1') {
  ?> <a href="/acc/logistic/index.php" class="menu_link" id="logistic">Логистика</a> |<?
}
?>
<?
if ($_COOKIE['list_access'] == '1' || $user_access['list_access'] == '1') {
  ?> <a href="/acc/applications/timetable/report.php" class="menu_link" id="vedomost" target="_blank">Ведомость</a> |<?
}
?>
<?
if (($_COOKIE['show_departments'] !== '0' || $_COOKIE['job_id'] == '10002')
    || ($user_access['show_departments'] !== '0' || $user_access['job_id'] == '10002')
) {
  ?> <a href="/acc/users/users.php" class="menu_link" id="sotr">Сотрудники</a> |<?
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
