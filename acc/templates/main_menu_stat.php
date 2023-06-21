<?
if(!isset($name_curr_page))
	$name_curr_page = 'query';	// главная страница по умолчанию
?>

<table align="center" border=0 cellpadding="0" cellspacing="0">
	<tr>
		<?
		if(@$tpacc) {
		
		if($name_curr_page == 'query') {
			$bgr = 'menu_act.gif';
			$link_beg = $link_end = '';
		}	
		else {
			$link_beg = '<a href="stat_table_query.php" class="menu_act">';
			$link_end = '</a>';
			$bgr = 'menu_norm.gif'; 
		}	
		?>
		<td height="30" background="/acc/i/<?=$bgr?>" align=center class="menu_no_act" width="122">
			<?=$link_beg?>Заказы<?=$link_end?>
		</td>
		<?
		if($name_curr_page == 'clients') {
			$bgr = 'menu_act.gif';
			$link_beg = $link_end = '';
		}	
		else {
			$link_beg = '<a href="stat_table_clients.php" class="menu_act">';
			$link_end = '</a>';
			$bgr = 'menu_norm.gif'; 
		}	
		?>
		<td height="30" background="/acc/i/<?=$bgr?>" align=center class="menu_no_act" width="122">
			<?=$link_beg?>Поставщики<?=$link_end?>
		</td>
	</tr>
</table>
<? } ?>
