<table class="paymentsEditForm" cellpadding="0" cellspacing="0" border="0" width="100%">
	<thead>
		<tr>
			<td class="col-summ">Сумма</td>
			<td class="col-date">Дата</td>
			<td class="col-number">Номер платежного поручения</td>
			<?
             if ($editAccess) { ?>
			<td class="col-action"></td>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php 
			if ($payments) {
				foreach ($payments as $payment) {
					$params = array(
						'paymentId' => $payment['uid'],
						'summ' => $payment['sum_accounts'],
						'date' => date('d.m.Y', strtotime($payment['date_ready'])),
						'number' => $payment['number_pp'],
						'editAccess' => $editAccess
					);
					$this->render('query/onePayment', $params);
				}
			} else {
				?>
				<tr class="no-payments-row">
					<td colspan="<?php echo $editAccess ? 4 : 3; ?>">Платежей пока нет</td>
				</tr>
				<?php
			}
		?>
	</tbody>
	<?php if ($editAccess) { ?>
	<tfoot>
		<tr>
			<td class="col-head" colspan="4">Добавить оплату <span class="setDate" data-days="0">сегодня</span> <span class="setDate" data-days="1">вчера</span> <span class="setDate" data-days="2">п/вчера</span></td>
		</tr>
		<tr class="add-row">
			<?php
				if ($query['prdm_dolg'] > 0) {
					$dolg = $query['prdm_dolg'];
				} elseif ($query['prdm_dolg'] == false && $query['prdm_opl'] == false) {
					$dolg = $query['prdm_sum_acc'];
				} else {
					$dolg = '';
				}

				if ($query['form_of_payment'] == 1) {
					$pko = 'ПКО ';
				} else {
					$pko = '';
				}
			?>
			<td class="col-summ"><input type="text" name="summ" value="<?php echo $dolg; ?>" /></td>
			<td class="col-date"><input type="text" class="inputDate" name="date" value="<?php echo date('d.m.Y'); ?>" /></td>
			<td class="col-number"><input type="text" name="number" value="<?php echo $pko; ?>" /></td>
			<td class="col-action" align="center">
				<span><i class="fa fa-plus addPayment"></i></span>
			</td>
		</tr>
		<tr class="no-payments-row temp">
			<td colspan="4">Платежей пока нет</td>
		</tr>
		<tr class="errors-row temp">
			<td colspan="4"></td>
		</tr>
		<tr class="success-row temp">
			<td colspan="4">Платеж обновлен</td>
		</tr>
	</tfoot>
	<? } ?>
</table>
<?php /*
<!--
<tr id="payment_<?php echo $payment['uid']; ?>" data-payment-id="<?php echo $payment['uid']; ?>">
	<td class="col-summ"><input type="text" value="<?php echo $payment['sum_accounts']; ?>" <?php echo $inputDisabled; ?>/></td>
	<td class="col-date"><input class="inputDate" type="text" value="<?php echo date('d.m.Y', strtotime($payment['date_ready'])); ?>" <?php echo $inputDisabled; ?>/></td>
	<td class="col-number"><input type="text" value="<?php echo $payment['number_pp']; ?>" <?php echo $inputDisabled; ?>/></td>
	<?php if ($editAccess) { ?>
	<td class="col-action" align="center">
		<span><i class="fa fa-floppy-o savePayment"></i></span>
		<span><i class="fa fa-trash-o deletePayment"></i></span>
	</td>
	<?php } ?>
</tr> --> */ ?>