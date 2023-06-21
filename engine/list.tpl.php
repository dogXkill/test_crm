<?php if ($customers) { ?>
	<?php foreach ($customers as $customer) { ?>
	<tr id="customer_<?=$customer['uid'];?>" data-customer-id="<?=$customer['uid'];?>" data-user-id="<?=$customer['user_id'];?>" data-order-type="<?=$customer['order_type'];?>">
		<td class="col-clientShort"><a href="/acc/query/?search=<?=$customer['short'];?>" target="_blank"><?=$customer['short'];?>


        </a></td>
		<td class="col-clientFull"><?=$customer['cont_pers'];?> <?=$customer['cont_tel'];?></td>
		<td class="col-manager" align="center"><?php echo isset($managers[$customer['user_id']]) ? $managers[$customer['user_id']]['surname'] : '—'; ?></td>
		<td class="col-orders" align="center"><?php CustomersCallsHelper::printOrderCountCell($customer); ?></td>
		<td class="col-lastOrder" align="center"><?php CustomersCallsHelper::printLastOrderCell($customer); ?></td>
		<td class="col-orderType"><?php CustomersCallsHelper::printOrderTypeCell($customer); ?></td>
		<td class="col-lastCall" align="center"><?php CustomersCallsHelper::printLastCallCell($customer); ?></td>
		<td class="col-actions"><span class="actionViewCalls"><i class="fas fa-phone-alt"></i> <span><?=$customer['calls_count'];?></span></span></td>
	</tr>
	<?php } ?>
<?php } else { ?>
	<tr class="emptyList">
		<td colspan="8">Нет данных</td>
	</tr>
<?php } ?>