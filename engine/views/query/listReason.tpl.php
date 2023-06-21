<?if ($reason) {
	?>
	
		<?php
$mas_tip_delete=array(
		99=>'НЕ УКАЗАНО',
		0=>'слишком дорого (нашли дешевле)',
		1=>'не устроили условия (сроки, доставка, договор)',
		2=>'клиент разместил у нас другой заказ (это дубль)',
		3=>'мы не смогли выполнить/доставить заказ вовремя',
		4=>'у клиента пропала потребность',
		5=>'нужной продукции не было в наличии',
		6=>'технически невыполнимый заказ',
		7=>'до клиента невозможно дозвониться',
		8=>'иная причина'
		);
		$sum=0;
		
		foreach ($reason['tip'] as $k => $query) {
			if ($k!=99){
			$sum+=$query;
			}
		}
		arsort($reason['tip']);
		//if ($sum>0){
		?>
		
		<?foreach ($reason['tip'] as $k => $query) {			?>
		<tr >
			<td class='tip_delete_table_td' style='font-size:12px;'><?php echo $mas_tip_delete[$k];?></td>
			<td class='tip_delete_table_td' style='font-size:12px;'  align="center"><?php if ($k!=99 && $sum!=0){$pr=($query/$sum)*100; echo $query."шт. (".round($pr,2)."%)";}else{echo $query;}?></td>
		</tr>
		
		<? } ?>
		<tr >
			<td class='tip_delete_table_td' style='font-size:12px;'><b>Итог</b></td>
			<td class='tip_delete_table_td' style='font-size:12px;'  align="center"><?php echo $sum;?></td>
		</tr>
		
		<?php
		//}
}
	//print_r($reason);
?>
