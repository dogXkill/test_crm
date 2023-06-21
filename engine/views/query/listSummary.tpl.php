<?php if (Engine::app()->user->isAdmin() && $queries) { ?>
	<?php
		$totalAmount = 0;
		$totalPayments = 0;
		$totalDebt = 0;
		
		foreach ($queries as $query) {
			$amount = floatval($query['prdm_sum_acc']);
			// $amount = MoneyHelper::format($amount);
			
			if ($amount) {
				$totalAmount += $amount;
			}
			
			$payment = floatval($query['prdm_opl']);
			if ($payment) {
				$totalPayments += $payment;
			}
			
			
			$debt = round($amount - $payment, 2);
			if ($debt) {
				$totalDebt += $debt;
			}
		}
	
	?>
	<tr class="summaryOfPage">
		<td class="cell-label" colspan="6">Итог страницы</td>
		<td class="col-count"><span><?php echo count($queries); ?> шт.</span></td>
		<td class="col-amount"><span><?php echo MoneyHelper::format($totalAmount); ?></span></td>
		<td class="col-payments"><span><?php echo MoneyHelper::format($totalPayments); ?></span></td>
		<td class="col-debt"><span><?php echo MoneyHelper::format($totalDebt); ?></span></td>
		<td colspan="3"></td>
	</tr>
	<tr class="summaryOfSelected">
		<td class="cell-label" colspan="6">Итог выборки <span class="summaryButton"><i class="fa fa-calculator"></i></span></td>
		<td class="col-count"></td>
		<td class="col-amount"></td>
		<td class="col-payments"></td>
		<td class="col-debt"></td>
		<td colspan="3"></td>
	</tr>
<?php } ?>