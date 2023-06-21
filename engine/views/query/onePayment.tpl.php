<tr id="payment_<?php echo $paymentId; ?>" data-payment-id="<?php echo $paymentId; ?>" data-value-summ="<?php echo $summ; ?>" data-value-date="<?php echo $date; ?>" data-value-number="<?php echo $number; ?>">
	<td class="col-summ"><input type="text" name="summ" data-original-value="<?php echo $summ; ?>" value="<?php echo $summ; ?>" <?php echo $editAccess ? '' : 'disabled '; ?>/></td>
	<td class="col-date"><input type="text" class="inputDate" name="date" data-original-value="<?php echo $date; ?>" value="<?php echo $date; ?>" <?php echo $editAccess ? '' : 'disabled '; ?>/></td>
	<td class="col-number"><input type="text" name="number" data-original-value="<?php echo $number; ?>" value="<?php echo $number; ?>" <?php echo $editAccess ? '' : 'disabled '; ?>/></td>
	<?php if ($editAccess) { ?>
	<td class="col-action" align="center">
		<span><!--<i class="fa fa-floppy-o editPayment disabled"></i>-->
		<i class="fa-solid fa-floppy-disk editPayment disabled" style='cursor:pointer;'></i>
		</span>
		<span>
		<!--<i class="fa fa-trash-o deletePayment"></i>-->
		<i class="fa-solid fa-trash deletePayment" style='cursor:pointer;'></i>
		</span>
	</td>
	<?php } ?>
</tr>