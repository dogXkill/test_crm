<?=$menu;?>
<div id="customersCallsPage">
	<div id="filters">
		<div class="clear">
			<?php if ($user->isAdmin()) { ?>
			<div id="filter_manager" data-filter-id="manager"<?php if (isset($filters['manager'])) echo 'data-filter-value="' . $filters['manager'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['manager'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Мендеджер"><?php echo isset($filters['manager']) ? $managers[$filters['manager']]['surname'] : 'Мендеджер'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<?php foreach ($managers as $manager_id => $manager) { ?>
						<?php if ($manager['archive']) continue; ?>
						<div class="filter__option<?=(isset($filters['manager']) && $filters['manager'] == $manager_id ? ' active' : '');?>" data-value="<?php echo $manager_id; ?>"><?php echo $manager['surname']; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
		
			<?php
				
				$periodFrom = false;
				$periodTo = false;
				
				if (isset($filters['periodFrom'])) {
					$periodFrom = date('d.m.Y', strtotime($filters['periodFrom']));
				}
				
				if (isset($filters['periodTo'])) {
					$periodTo = date('d.m.Y', strtotime($filters['periodTo']));
				}
				
			?>
			
			<div id="filter_period" data-filter-id="period" class="filter filterType_period<?php if ($periodFrom && $periodTo) echo ' active'; ?>">
				<div class="filter_input">
					Период с <span class="periodRange periodRange_from<?php if ($periodFrom) echo ' active'; ?>"><input type="text" value="<?php echo $periodFrom ? $periodFrom : '—'; ?>" data-placeholder="—" readonly /><span class="periodRange_arrow"><i class="fa fa-arrow-down"></i></span></span> по <span class="periodRange periodRange_to<?php if ($periodTo) echo ' active'; ?>"><input type="text" value="<?php echo $periodTo ? $periodTo : '—'; ?>" data-placeholder="—" readonly /><span class="periodRange_arrow"><i class="fa fa-arrow-down"></i></span></span>
				</div>
			</div>
			
			<div id="filter_result" data-filter-id="result" data-filter-value="<?php if (isset($filters['result'])) echo implode(',', $filters['result']); ?>" class="filter filterType_select filterType_multiselect<?php if (isset($filters['result'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Результат" data-placeholder-active="Выбрано: "><?php echo isset($filters['result']) ? 'Выбрано: ' . count($filters['result']) : 'Результат'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<?php foreach (CustomersCallsConfig::$callsResults as $result_id => $label) { ?>
						<div class="filter__option<?=(isset($filters['result']) && in_array($result_id, $filters['result']) ? ' active' : '');?>" data-value="<?php echo $result_id; ?>"><?php echo $label; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="clear">
			<div class="topPagination clear">
				<div id="countingBox">Найдено звонков: <span><?=$callsCount;?></span></div>
				<div id="resetFiltersButton">Сбросить фильтры <i class="fas fa-redo"></i></div>
				<div class="paginationLinks">
					<?php echo $pagination; ?>
				</div>
			</div>
		</div>
	</div>
		
	<div id="listBox">
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$.datepicker.regional['ru'] = {
					closeText: 'Закрыть',
					prevText: '&#x3c;Пред',
					nextText: 'След&#x3e;',
					currentText: 'Сегодня',
					monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
					'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
					monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
					'Июл','Авг','Сен','Окт','Ноя','Дек'],
					dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
					dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
					dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
					weekHeader: 'Нед',
					dateFormat: 'dd.mm.yy',
					firstDay: 1,
					isRTL: false,
					showMonthAfterYear: false,
					yearSuffix: ''
				};
				
				$.datepicker.setDefaults($.datepicker.regional['ru']);
			});
		</script>
		
		<table id="callsList" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
			<thead>
				<tr>
					<td class="col-manager">Менеджер</td>
					<td class="col-date">Дата звонка</td>
					<td class="col-customer">Клиент</td>
					<td class="col-result">Результат</td>
					<td class="col-comment">Комментарий</td>
				</tr>
			</thead>
			<tbody>
				<?php echo $items; ?>
			</tbody>
		</table>
	</div>
	
	<div class="bottomPagination clear">
		<div class="paginationLinks">
			<?php echo $pagination; ?>
		</div>
	</div>
</div>