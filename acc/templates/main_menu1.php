<?
if(!isset($name_curr_page))
$name_curr_page = 'main';	// главная страница по умолчанию
$tpus = $user_type;		// тип пользователя
// чтение количества невыполненных запросов
if($tpacc) {
	$query = "SELECT uid FROM queries WHERE ready='0'";
	$res = mysql_query($query);
	$num_query = mysql_num_rows($res);
}
if(@$auth) {
?>
<table align="center" width=1100 border=0 cellpadding="0" cellspacing="0">
<tr>
<td height="30">
| <a href="/" class="menu_link" id="main">Общие</a> |
<a href="/acc/query/query_list.php" class="menu_link" id="query_list">Заказы</a> |
<a href="/acc/shop/list.php?notemplate=0" class="menu_link" id="shop">Магазин</a> |
<a href="/acc/tasks/" class="menu_link" id="tasks">Задачи</a> |
<?if($tpus == 'sup' || $tpus == 'acc'|| $tpus == 'meg' || $user_id == '199') {?><a href="/acc/plan/" class="menu_link" id="plan">Планировщик</a> | <a href="/acc/sprav/" class="menu_link" id="sprav">Справочники</a>  | <?}?>
<a href="/acc/applications/list.php" class="menu_link" id="apl_list">Производство</a> |
<?if($tpus == 'sup' || $tpus == 'meg' || $tpus == 'adm' || $tpus == 'mng' || $tpus == 'acc' ) {?><a href="/acc/logistic/index.php" class="menu_link" id="logistic">Логистика</a> | <?}?>

<?if($tpus == 'sup') {?>
<a href="/acc/applications/timetable/report.php" class="menu_link" id="vedomost" target="_blank">Ведомость</a> |
<a href="/acc/users/users.php" class="menu_link" id="sotr">Сотрудники</a> |
<a href="/acc/stat/stat_table_query.php " class="menu_link" id="sotr" target="_blank">Табл</a> |


<?}?>

<!--
<?if($tpus == 'sup' || $tpus == 'meg' || $tpus == 'acc') {?><a href="/acc/users/users.php" class="menu_link" id="users">Пользователи</a> | <?}?>
<?if($tpus == 'sup' || $tpus == 'mng' || $tpus == 'acc' || $tpus == 'meg') {?><a href="/acc/stat/stat.php" class="menu_link" id="stat">Статистика</a> | <?}?> -->
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
