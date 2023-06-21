<?php if ($calls) { ?>
	<?php foreach ($calls as $call) { ?>
	<tr id="call_<?=$call['id'];?>">	
		<td class="col-manager" align="center"><?php echo isset($managers[$call['user_id']]) ? $managers[$call['user_id']]['surname'] : '—'; ?></td>
		<td class="col-date" align="center"><?=date('d.m.Y H:i', strtotime($call['date']));?></td>
		<td class="col-customer"><a href="/crm/customersCalls/?search=<?=$call['client_short'];?>" target="_blank"><?=$call['client_short'];?></a></td>
		<td class="col-result" align="center"><?php CustomersCallsHelper::printCallResultCell($call);?></td>
		<td class="col-comment"><?=nl2br($call['comment']);?></td>
	</tr>
	<?php } ?>
<?php } else { ?>
	<tr class="emptyList">
		<td colspan="8">Нет данных</td>
	</tr>
<?php } ?>