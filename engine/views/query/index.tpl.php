
<table width="1100" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#F6F6F6">
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="5" cellpadding="0">
				<tr>
					<td width="150">
						<a href="query_send.php" class="sublink"><img src="../../i/invoice_sm.png" width="24" height="24" alt="" align=absmiddle /></a>
						<a href="query_send.php" class="sublink">запросить счет</a>
					</td>

					<td width="150" valign="middle" class="new-sublink">
						<i class="fa fa-phone"></i> <a href="/crm/customersCalls/" class="sublink">обзвон клиентов</a>
					</td>

                    <td width="170">
					<a href="/acc/stat/stat_shop.php?type=shop_history" class="sublink"><img src="../../i/statistics.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="/acc/stat/stat_shop.php?type=shop_history" class="sublink">статистика магазин</a>
					</td>
					<?

					if ($_COOKIE['sprav_access'] == '1') {
						?>
						<td width="110">
							<a href="clients_list.php" class="sublink"><img src="../../i/clients.png" width="24" height="24" alt="" align="absmiddle"></a>
							<a href="clients_list.php" class="sublink">клиенты</a>
						</td>

						<td width="130">
							<a href="contractors_list.php" class="sublink"><img src="../../i/vendor.png" width="24" height="24" alt="" align="absmiddle"></a>
							<a href="contractors_list.php" class="sublink">поставщики</a>
						</td>
						<?
					}
					if ($_COOKIE['tabl_access'] == '1') {
						?>
						<td width="230">
							<a href="contractors_list.php" class="sublink"><img src="/i/journal.png" width="24" height="24" alt="" align="absmiddle"></a>
							<a href="/acc/stat/stat_table_query.php" class="sublink">работа с таблицами</a>
						</td>
						<?
					}
						?>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php if (isset($_COOKIE['dev'])) { ?>
<div id="filtersBox"></div>
<?php } ?>
<div id="filters" class="clear">
	<div class="filtersList clear">
		<div class="clear">
			<div id="filter_search" data-filter-id="search" class="filter filterType_input<?php if (isset($filters['search'])) echo ' active'; ?>">
				<div class="filter_input">
					<input type="text" class="textInput"<?php if (isset($filters['search'])) echo 'value="' . $filters['search'] . '"'; ?> placeholder="Поиск по названию" />
				</div>
			</div>
			<?php
				//print_r($filters);
				$peridoFrom = false;
				$peridoTo = false;

				if (isset($filters['from'])) {
					$periodFrom = date('d.m.Y', strtotime($filters['from']));
				}

				if (isset($filters['to'])) {
					$periodTo = date('d.m.Y', strtotime($filters['to']));
				}

			?>
			<div id="filter_period" data-filter-id="period" class="filter filterType_period<?php if ($periodFrom && $periodTo) echo ' active'; ?>">
				<div class="filter_input">
					Период с <span id="periodRange_from" class="periodRange<?php if ($periodFrom) echo ' active'; ?>"><input type="text" value="<?php echo $periodFrom ? $periodFrom : '—'; ?>" data-placeholder="—" readonly /><span class="periodRange_arrow"><i class="fa fa-arrow-down"></i></span></span> по <span id="periodRange_to" class="periodRange<?php if ($periodTo) echo ' active'; ?>"><input type="text" value="<?php echo $periodTo ? $periodTo : '—'; ?>" data-placeholder="—" readonly /><span class="periodRange_arrow"><i class="fa fa-arrow-down"></i></span></span>
					<input type="hidden" id="periodRange_from_value"/>
					<input type="hidden" id="periodRange_to_value"/>
				</div>
			</div>
			<?php if ($user->isAdmin()) { ?>
			<div id="filter_manager" data-filter-id="manager"<?php if (isset($filters['manager'])) echo 'data-filter-value="' . $filters['manager'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['manager'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Менеджер"><?php echo isset($filters['manager']) ? $managers[$filters['manager']]['surname'] : 'Менеджер'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<?php //Engine::debug($managers); ?>
						<?php foreach ($managers as $manager_id => $manager) { ?>
						<?php if ($manager['archive']) continue; ?>
						<div class="filter__option<?php if (isset($filters['manager']) && $filters['manager'] == $manager_id) echo ' active'; ?>" data-value="<?php echo $manager_id; ?>"><?php echo $manager['surname'] . " " . $manager['name']; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
			<div id="filter_orderType" data-filter-id="orderType"<?php if (isset($filters['orderType'])) echo 'data-filter-value="' . $filters['orderType'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['orderType'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Тип заказа"><?php echo isset($filters['orderType']) ? QueriesConfig::$filter_orderType[$filters['orderType']] : 'Тип заказа'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<?php foreach (QueriesConfig::$filter_orderType as $key => $value) { ?>
						<div class="filter__option<?php if (isset($filters['orderType']) && $filters['orderType'] == $key) echo ' active'; ?>" data-value="<?php echo $key; ?>"><?php echo $value; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div id="filter_deliveryType" data-filter-id="deliveryType"<?php if (isset($filters['deliveryType'])) echo 'data-filter-value="' . $filters['deliveryType'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['deliveryType'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Тип доставки"><?php echo isset($filters['deliveryType']) ? QueriesConfig::$filter_deliveryType[$filters['deliveryType']] : 'Тип доставки'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<?php foreach (QueriesConfig::$filter_deliveryType as $key => $value) { ?>
						<div class="filter__option<?php if (isset($filters['deliveryType']) && $filters['deliveryType'] == $key) echo ' active'; ?>" data-value="<?php echo $key; ?>"><?php echo $value; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div id="filter_paymentType" data-filter-id="paymentType"<?php if (isset($filters['paymentType'])) echo 'data-filter-value="' . $filters['paymentType'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['paymentType'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Способ оплаты"><?php echo isset($filters['paymentType']) ? QueriesConfig::$filter_paymentType[$filters['paymentType']] : 'Способ оплаты'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<?php foreach (QueriesConfig::$filter_paymentType as $key => $value) { ?>
						<div class="filter__option<?php if (isset($filters['paymentType']) && $filters['paymentType'] == $key) echo ' active'; ?>" data-value="<?php echo $key; ?>"><?php echo $value; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div id="filter_debt" data-filter-id="debt"<?php if (isset($filters['debt'])) echo 'data-filter-value="' . $filters['debt'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['debt'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Долг"><?php echo isset($filters['debt']) ? ($filters['debt'] ? '> 0' : '= 0') : 'Долг'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<div class="filter__option<?php if (isset($filters['debt']) && $filters['debt']) echo ' active'; ?>" data-value="1">> 0</div>
						<div class="filter__option<?php if (isset($filters['debt']) && !$filters['debt']) echo ' active'; ?>" data-value="0">= 0</div>
                        <div class="filter__option<?php if (isset($filters['debt']) && !$filters['debt']) echo ' active'; ?>" data-value="2">на доплату</div>
					</div>
				</div>
			</div>
		</div>

		<div class="clear">
			<?php if ($user->isAdmin()) { ?>
			<div id="filter_printManager" data-filter-id="printManager"<?php if (isset($filters['printManager'])) echo 'data-filter-value="' . $filters['printManager'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['printManager'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Принт-менеджер"><?php echo isset($filters['printManager']) ? ($filters['printManager'] ? 'Да' : 'Нет') : 'Принт-менеджер'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<div class="filter__option<?php if (isset($filters['printManager']) && $filters['printManager']) echo ' active'; ?>" data-value="1">Да</div>
						<div class="filter__option<?php if (isset($filters['printManager']) && !$filters['printManager']) echo ' active'; ?>" data-value="0">Нет</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<div id="filter_amoCrm" data-filter-id="amoCrm"<?php if (isset($filters['amoCrm'])) echo 'data-filter-value="' . $filters['amoCrm'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['amoCrm'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Сделка AMO"><?php echo isset($filters['amoCrm']) ? ($filters['amoCrm'] ? 'Да' : 'Нет') : 'Сделка AMO'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<div class="filter__option<?php if (isset($filters['amoCrm']) && $filters['amoCrm']) echo ' active'; ?>" data-value="1">Да</div>
						<div class="filter__option<?php if (isset($filters['amoCrm']) && !$filters['amoCrm']) echo ' active'; ?>" data-value="0">Нет</div>
					</div>
				</div>
			</div>

			<div id="filter_deleted" data-filter-id="deleted"<?php if (isset($filters['deleted'])) echo 'data-filter-value="' . $filters['deleted'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['deleted'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Только удаленные"><?php echo isset($filters['deleted']) ? QueriesConfig::$filter_deleted[$filters['deleted']] : 'Только удаленные'; ?></span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<?php foreach (QueriesConfig::$filter_deleted as $key => $value) { ?>
						<div class="filter__option<?php if (isset($filters['deleted']) && $filters['deleted'] == $key) echo ' active'; ?>" data-value="<?php echo $key; ?>"><?php echo $value; ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
			<!--Тип изделия-->
			<div id="filter_izd" data-filter-id="izd"<?php if (isset($filters['izd'])) echo 'data-filter-value="' . $filters['izd'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['izd'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Тип изделия">
						<?php 
						$multi_select=stripos($filters['izd'], ",");
						$mas_zn_active=array();
						if ($multi_select!==false){
							//multi select
							$kol=0;
							$kol=mb_substr_count($filters['izd'],",");$kol++;$mas_zn_active=explode(",",$filters['izd']);
							echo "Выбрано({$kol})";
						}else if ($filters['izd']!=0 || !isset($filters['izd'])){
							echo isset($filters['izd']) ? $types_izd[$filters['izd']]['type'] : 'Тип изделия';
						}else if ($filters['izd']==0){
							echo "Иное";
						}else{echo 'Тип изделия';}
							?>
						</span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options" >
						<?php //Engine::debug($types_izd); ?>
						<?php //print_r($types_izd); ?>
						<?php //print_r($filters); ?>
						<?php foreach ($types_izd as $type_izd_id => $type_izd) { ?>
						<div class="filter__option_multi<?php if (isset($filters['izd']) && $filters['izd'] == $type_izd_id || in_array($type_izd_id,$mas_zn_active)) echo ' active'; ?> " data-value="<?php echo $type_izd_id; ?>"><?php echo $type_izd['type']; ?></div>
						<?php } ?>
						<div class="filter__option_multi<?php if (isset($filters['izd']) && $filters['izd'] == 0 || in_array(0,$mas_zn_active)) echo ' active'; ?> " data-value="0">Иное</div>
					</div>
				</div>
			</div>
			<!--Тип сделки-->
			<div id="filter_sdelka" data-filter-id="sdelka"<?php if (isset($filters['sdelka'])) echo 'data-filter-value="' . $filters['sdelka'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['sdelka'])) echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label" data-placeholder="Тип сделки">
						<?php 
						$multi_select=stripos($filters['sdelka'], ",");
						$mas_zn_active=array();
						if ($multi_select!==false){
							//multi select
							$kol=0;
							$kol=mb_substr_count($filters['sdelka'],",");$kol++;$mas_zn_active=explode(",",$filters['sdelka']);
							echo "Выбрано({$kol})";
						}elseif ($filters['sdelka']!=0|| !isset($filters['sdelka'])){
							echo isset($filters['sdelka']) ? QueriesConfig::$filter_tip_sdelki[$filters['sdelka']] : 'Тип сделки';
						}else if ($filters['sdelka']==0){
							echo "Иное";
						}else{
							echo  'Тип сделки';
						}?>
						
						</span>
						<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
						<span class="filter__button_reset"><i class="fa fa-times"></i></span>
					</div>
					<div class="filter__options">
						<?php foreach (QueriesConfig::$filter_tip_sdelki as $key => $value) { ?>
						<div class="filter__option_multi<?php if (isset($filters['sdelka']) && $filters['sdelka'] == $key || in_array($key,$mas_zn_active)) echo ' active'; ?>" data-value="<?php echo $key; ?>"><?php echo $value; ?></div>
						<?php } ?>
						<div class="filter__option_multi<?php if (isset($filters['sdelka']) && $filters['sdelka'] == 0 || in_array(0,$mas_zn_active)) echo ' active'; ?>" data-value="0">Иное</div>
					</div>
				</div>
			</div>
		</div>

		<div class="clear">
			<div id="specialFilter_k-dostavke" data-filter-id="k-dostavke" class="specialFilter<?php if (isset($filters['specialFilter']) && $filters['specialFilter'] == 'k-dostavke') echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label">К доставке</span>
						<span class="filter__button_ico"><i class="fa fa-truck"></i></span>
					</div>
				</div>
			</div>

			<div id="specialFilter_k-proizvodstvu" data-filter-id="k-proizvodstvu" class="specialFilter<?php if (isset($filters['specialFilter']) && $filters['specialFilter'] == 'k-proizvodstvu') echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label">К производству</span>
						<span class="filter__button_ico"><i class="fa fa-cogs"></i></span>
					</div>
				</div>
			</div>

            <div id="specialFilter_booking_ended" data-filter-id="booking_ended" class="specialFilter<?php if (isset($filters['specialFilter']) && $filters['specialFilter'] == 'booking_ended') echo ' active'; ?>">
				<div class="filter__input">
					<div class="filter__button clear">
						<span class="filter__button_label">Просрочена бронь</span>
						<span class="filter__button_ico"><i class="fa fa-lock"></i></span>
					</div>
				</div>
			</div>

			<?php if (isset($_COOKIE['dev'])) { ?>
			<div id="filtersSet"></div>
			<?php } ?>



			<div class="topPagination clear">
				<div id="perPage_selector"<?php if ($pagination == '') echo ' style="display: none"'; ?>>
					<div class="perPage_selector__button">
						<span class="perPage_selector__button_label"><?php echo $perPage; ?></span>
						<span class="perPage_selector__button_arrow"><i class="fa fa-arrow-down"></i></span>
					</div>
					<div class="perPage_selector__options">
						<?php foreach (QueriesConfig::$perPage as $option) { ?>
						<div class="perPage_selector__option<?php if ($option == $perPage) echo ' active'; ?>" data-value="<?php echo $option; ?>"><?php echo $option; ?></div>
						<?php } ?>
					</div>
				</div>
				<div class="paginationLinks">
					<?php echo $pagination; ?>
				</div>
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
	<?php
		$sorting = array(
			'date' => '',
			'summ' => '',
			'debt' => ''
		);

		if ($sort) {
			$sorting[$sort['field']] = ' active';
			if ($sort['order'] == 'desc') {
				$sorting[$sort['field']] .= ' desc';
			}
		}
	?>
	<table id="queryList" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
		<thead>
			<tr>
				<td class="col-order"></td>
				<td class="col-client">Название</td>
				<td class="col-manager">Менеджер</td>
				<td class="col-orderType">Тип заказа</td>
				<td class="col-delivery">Доставка</td>
				<td class="col-paymentType">Способ оплаты</td>
				<td class="col-date"><div id="sortFilter-date" data-filter-id="date" class="sortFilter<?php echo $sorting['date']; ?>"><span>Дата</span><i class="fa fa-sort-amount-asc"></i><i class="fa fa-sort-amount-desc"></i></div></td>
				<td class="col-amount"><div id="sortFilter-summ" data-filter-id="summ" class="sortFilter<?php echo $sorting['summ']; ?>"><span>Сумма</span><i class="fa fa-sort-amount-asc"></i><i class="fa fa-sort-amount-desc"></i></div></td>
				<td class="col-payments">Оплачено</td>
				<td class="col-debt"><div id="sortFilter-debt" data-filter-id="debt" class="sortFilter<?php echo $sorting['debt']; ?>"><span>Долг</span><i class="fa fa-sort-amount-asc"></i><i class="fa fa-sort-amount-desc"></i></div></td>
				<td class="col-accNumber">Номер накладной</td>
				<td class="col-managerPoints">Баллы</td>
				<td class='col-tip-deleted'>Причина</td>
				<td class="col-actions">Операции</td>
			</tr>
		</thead>
		<tbody>
			<?php echo $items; ?>
		</tbody>
		<tfoot>
			<?php echo $summary; ?>
		</tfoot>
	</table>
	<?php if (!isset($_GET['deleted'])){$tab_tip='none';}?>
	<table cellpadding="0" cellspacing="0" border="0" align="left" class='tip_delete_tables' style='display:<?php echo $tab_tip;?>'>
		<thead>
			<tr>
				<td class="col-title-queries tip_delete_table_td">
					<b>Причина удаления</b>
				</td>
				<td class='tip_delete_table_td'>
					<b>Кол-во</b>
				</td>
			</tr>
		</thead>
		<tbody >
	
			<? echo $table_reason;?>
		</tbody>
		</table>
	
</div>
<div class="bottomPagination clear">
	<div class="paginationLinks">
		<?php echo $pagination; ?>
	</div>
</div>

<div id="editPaymentsDialog" class="paketoffDialog" title="Список оплат">
	<div class="dialogContent"></div>
	<div class="dialogLoader"><div></div></div>
</div>

<div id="editCommentDialog" class="paketoffDialog" title="Примечание к заказу">
	<div class="dialogContent"></div>
	<div class="dialogLoader"><div></div></div>
</div>

<div id="editAmoCrmIdDialog" class="paketoffDialog" title="Сделака в AMO CRM">
	<div class="dialogContent"></div>
	<div class="dialogLoader"><div></div></div>
</div>
<div id="popup_delete" class="paketoffDialog" title="Удаление заказа">
	<div class="dialogContent"></div>
	<div class="dialogLoader"><div></div></div>
</div>
<div id="popup_izm_delete" class="paketoffDialog" title="Изменение удалённого заказа">
	<div class="dialogContent"></div>
	<div class="dialogLoader"><div></div></div>
</div>
<div id="file_popup" class="paketoffDialog" title="Скачать или отправить">
	<div class="dialogContent"></div>
	<div class="dialogLoader"><div></div></div>
</div>
<div id="notificationBox"></div>

