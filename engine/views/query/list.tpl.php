<?if ($queries) { 

?>
	<?foreach ($queries as $query) { ?>
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
		$trCss = array();

		if ($query['deleted'] == 1) {
			$trCss[] = 'deleted';
		} elseif ($query['deleted'] == 2) {
			$trCss[] = 'forDeletion';
		}
		 $queries[$k]['tip_reason']=$row['tip_reason'];
						 $queries[$k]['comment']=$row['comment'];
	?>
	<tr id="query_<?echo $query['uid']; ?>" class="<?echo implode(' ', $trCss); ?>" data-query-id="<?echo $query['uid']; ?>">
		<td class="col-order"><?QueryListHelper::printItems($query['items']);?></td>
		<td class="col-client" align="center"><a href="query_send.php?show=<?echo $query['uid']; ?>"><?echo $query['short']; ?></a></td>
		<td class="col-manager" align="center"><?$full_name = $managers[$query['user_id']]['surname'] . " " . $managers[$query['user_id']]['name']; echo isset($managers[$query['user_id']]) ? $full_name : '—'; ?></td>
		<td class="col-orderType">
            <?=QueryListHelper::printPaymentLink($query['uniq_id']);?> <?=QueryListHelper::printPdfFiles($query['uid']);?> <?QueryListHelper::printOrderType($query); ?>
        </td>
		<td class="col-delivery" align="center"><?echo DeliveryConfig::$types[$query['deliv_id']]; ?></td>
		<td class="col-paymentType" align="center"><?QueryListHelper::printPaymentType($query);?></td>
		<td class="col-date" align="center"><?QueryListHelper::printDate($query['date_query']); ?></td>
		<td class="col-amount" align="center"><?QueryListHelper::printAmount($query); ?></td>
		<td class="col-payments" align="center"><?QueryListHelper::printPayments($query); ?></td>
		<td class="col-debt" align="center"><?QueryListHelper::printDebt($query);?></td>
		<td class="col-accNumber" align="center"><?QueryListHelper::printAccNumber($query);?></td>
		<td class="col-managerPoints" align="center"><?echo $query['percent']; ?></td>
		<td class='col-tip-deleted' align="center">
		<?php if ($_GET['deleted']==1 || $_GET['deleted']==2){?>
		<!--<span title="Причина:<?php echo $mas_tip_delete[$query['tip_reason']];?></br> Комментарий:<?php echo $query['comment'];?>" class='info_tip_delete'><i class="fa fa-info"></i></span>-->
		<?php }
		//echo QueryListHelper::printCookieAccess();
		$ac_us=QueryListHelper::printCookieAccess();
		if ($ac_us==2){$css_bl='none';}else{$css_bl='inline-block';}
		 if (($query['deleted']==1) || ($query['deleted']==2  )){
			 if ($query['tip_reason']!=99){$css_bl_color="background-color:#090;border-color:#090;color:white;";}else{$css_bl_color="background-color:#fcfcfd;border-color:#dfdbdb;color:#aaa";}
			?>
			&nbsp;<span title="Изменить причини или комментарий:" class='izm_tip_delete' style='display:<?php echo $css_bl;?>;<?php echo $css_bl_color;?>'><i class="fa fa-info"></i></span>
			<?php
		}
		
		?>
		</td>
		<td class="col-actions"><?QueryListHelper::printActionButtons($query); ?></td>
	</tr>
	<?}
	?>
<?} else { ?>
	<tr>
		<td colspan="13">Нет данных</td>
	</tr>
<?} ?>
