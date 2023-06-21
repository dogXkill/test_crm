<table class="callsList" cellpadding="0" cellspacing="0" border="0" width="100%">
	<thead>
		<tr>
			<td class="col-date">Дата</td>
			<td class="col-manager">Менеджер</td>
			<td class="col-result">Результат</td>
			<td class="col-comment">Комментарий</td>
			<td class="col-actions"></td>
		</tr>
	</thead>
	<tbody>
		<?php if ($calls) { ?>
			<?php foreach ($calls as $call) { ?>
			<tr id="call_<?=$call['id'];?>" data-call-id="<?=$call['id'];?>">
				<td class="col-date"><?=date('d.m.Y H:i', strtotime($call['date']));?></td>
				<td class="col-manager"><?php echo isset($managers[$call['user_id']]) ? $managers[$call['user_id']]['surname'] : '—'; ?></td>
				<td class="col-result"><?php CustomersCallsHelper::printCallResultCell($call);?></td>
				<td class="col-comment"><?=nl2br($call['comment']);?></td>
				<td class="col-actions" align="center"><?php CustomersCallsHelper::printCallActionsCell($call);?></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr class="no-calls-row">
			<td colspan="5">Звонков нет</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
