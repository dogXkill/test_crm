<table width="1100" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#F6F6F6">
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="5" cellpadding="0">
				<tr>
					<td width="150">
						<a href="query_send.php" class="sublink"><img src="../../i/invoice_sm.png" width="24" height="24" alt="" align=absmiddle /></a>
						<a href="query_send.php" class="sublink">запросить счет</a>
					</td>
					
                    <td width="170">
						<a href="/acc/stat/stat_shop.php?type=shop_history" class="sublink"><img src="../../i/statistics.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="/acc/stat/stat_shop.php?type=shop_history" class="sublink">статистика магазин</a>
					</td>
		
					<td width="110">
						<a href="clients_list.php" class="sublink"><img src="../../i/clients.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="clients_list.php" class="sublink">клиенты</a>
					</td>
					
					<td width="130">
				 		<a href="contractors_list.php" class="sublink"><img src="../../i/vendor.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="contractors_list.php" class="sublink">поставщики</a>
					</td>
					
					<? if ($user->getType() == 'sup' || $user->getType() == 'acc') { ?>
					<td width="150">
						<a href="/acc/stat/stat_table_query.php" target="_blank" class="sublink"><img src="../../i/tables.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="/acc/stat/stat_table_query.php" target="_blank" class="sublink">работа с таблицами</a>
					</td>
					<? } ?>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script type="text/javascript">
var appConfig = {
	upgrades: {
		app: 'Нанесение',
		hands: 'Замена ручек',
		luvers: 'Установка люверсов',
	},
	delivery: {
		label: 'Доставка',
		items: {
			msk: 'По Москве',
			msk_pickup: 'Из шоурума',
			tk: 'До ТК',
			sdek: 'СДЭК'
		}
	},
	contractors: {
		'125': 'Пакетофф'
	},
	strings: {
		'article_not_found': 'Такого артикула не найдено'
	}
};
</script>
<div id="queryPage">
	<div class="clear">
		
		<div class="area" id="searchClientArea">
			<div class="area_header">Поиск клиента</div>
			<div class="area_content">
				// Если это импорт заказа с сайта, тут еще один открытый блок "Поиск клиента", с выводом пришедших данных, 3-х полей (название, email, инн) и списком найденных клиентов. Блок сворачивающийся\разворачивающийся.<br/>
				// Если это просмотр заказа - блок свернут по-умолчанию<br/>
				// Если это создание заказа с нуля - блок развернут<br/>
			</div>
		</div>
		
		
		<div class="area" id="clientArea">
			<div class="area_header">Клиент <span class="client_badge new">новый</span></div>
			<div class="area_toolbar">
				<span class="action action-new-client">Создать нового <i class="fas fa-user-plus"></i></span>
			</div>
			<div class="area_content">
			
				<div class="row clear">
					<div class="inputBox clear" id="inputBox_client_short">
						<div class="inputBox_label">Короткое название</div>
						<div class="inputBox_content">
							<div class="inputBox_input" id="input_client_short">
								<input type="text" value="" name="asdf" />
								<div class="loader"></div>
								<div id="clientsListBox">
									<div class="clientsList_items">
										<div class="clientList_item">
											<div class="clientList_item_name"><span class="short"><span>Стар</span>з Энд Фишез</span> <span class="full">(ООО "Стар мастер класс")</span></div>
											<div class="clientList_item_address">141700, РФ, Московская область, г.Химки, кв-л Клязьма, Набережный проезд, д.1, кв.84</div>
											<div class="clientList_item_phones"><span>8-499-769-50-73</span> <span>7 968 716-62-59</span> <span>8-499-769-50-73</span></div>
											<div class="clientList_item_orders"><span class="count">Кол-во заказов: <span>2</span></span> <span class="last">Последний: <span>11.10.2019</span> на сумму <span>10 250 руб.</span></span></div>
										</div>
										<div class="clientList_item">
											<div class="clientList_item_name"><span class="short"><span>Стар</span>з Энд Фишез</span> <span class="full">(ООО "Стар мастер класс")</span></div>
											<div class="clientList_item_address">141700, РФ, Московская область, г.Химки, кв-л Клязьма, Набережный проезд, д.1, кв.84</div>
											<div class="clientList_item_phones"><span>8-499-769-50-73</span> <span>7 968 716-62-59</span> <span>8-499-769-50-73</span></div>
											<div class="clientList_item_orders"><span class="count">Кол-во заказов: <span>2</span></span> <span class="last">Последний: <span>11.10.2019</span> на сумму <span>10 250 руб.</span></span></div>
										</div>
										<div class="clientList_item">
											<div class="clientList_item_name"><span class="short"><span>Стар</span>з Энд Фишез</span> <span class="full">(ООО "Стар мастер класс")</span></div>
											<div class="clientList_item_address">141700, РФ, Московская область, г.Химки, кв-л Клязьма, Набережный проезд, д.1, кв.84</div>
											<div class="clientList_item_phones"><span>8-499-769-50-73</span> <span>7 968 716-62-59</span> <span>8-499-769-50-73</span></div>
											<div class="clientList_item_orders"><span class="count">Кол-во заказов: <span>2</span></span> <span class="last">Последний: <span>11.10.2019</span> на сумму <span>10 250 руб.</span></span></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="inputBox clear" id="inputBox_client_full">
						<div class="inputBox_label">Полнное наименование</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" /></div>
							<div class="inputBox_toolbar">
								<span class="action action-double">есть дубли (2)</span>
								<span class="action action-yandex-search"><i class="fab fa-yandex"></i> поиск</span>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="inputBox clear" id="inputBox_address">
						<div class="inputBox_label">Адрес</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" /></div>
							<div class="inputBox_toolbar">
								<span class="action action-yandex-search"><i class="fab fa-yandex"></i> поиск</span>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row clear">
					<div class="inputBox clear" id="inputBox_inn">
						<div class="inputBox_label">Инн</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" placeholder="" /></div>
						</div>
					</div>
					<div class="inputBox clear" id="inputBox_kpp">
						<div class="inputBox_label">Кпп</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" /></div>
						</div>
					</div>
					<div class="inputBox clear" id="inputBox_rs">
						<div class="inputBox_label">Расчетный счет</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" placeholder="" /></div>
						</div>
					</div>
					<div class="inputBox clear" id="inputBox_bik">
						<div class="inputBox_label">Бик</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" /></div>
						</div>
					</div>
				</div>
				
				
				<div class="row clear">
					<div class="inputBox clear" id="inputBox_phone">
						<div class="inputBox_label">Телефоны</div>
						<div class="inputBox_content">
							<div class="inputBox_input inputBox_inputs">
								<div class="tag">
									<div class="tag__input">
										<input type="text" />
										<span class="tag__remove"><i class="fa fa-times"></i></span>
										<div class="loader"></div>
									</div>
									<div class="tag__tools"></div>
								</div>
								
								<span class="tag__add"><i class="fa fa-plus"></i></span>
							</div>
						</div>
					</div>
					
					<div class="inputBox clear" id="inputBox_email">
						<div class="inputBox_label">E-Mail</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" /></div>
							<div class="inputBox_toolbar">
								<span class="action action-yandex-search"><i class="fab fa-yandex"></i> поиск</span>
							</div>
						</div>
					</div>
				</div>
				
				
				<div class="inputBox clear" id="inputBox_client_comment">
					<div class="inputBox_label">Комментарий к клиенту</div>
					<div class="inputBox_content">
						<div class="inputBox_input"><textarea></textarea></div>
					</div>
				</div>

				
			</div>
		</div>
		
		<div class="area" id="orderInfoArea">
			<div class="area_header">Инфо о заказе</div>
			<div class="area_content">
			
				<div class="row clear">
					<div class="inputBox clear" id="inputBox_orderType">
						<div class="inputBox_label">Тип заказа</div>
						<div class="inputBox_content">
							<div class="inputBox_input">
								<select>
									<option value=""></option>
									<option value="1">Под заказ</option>
									<option value="2">Магазин</option>
									<option value="3">Магазин с лого</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="inputBox clear" id="inputBox_paymentType">
						<div class="inputBox_label">Способ оплаты</div>
						<div class="inputBox_content">
							<div class="inputBox_input">
								<select>
									<option value=""></option>
									<option value="1">Наличные</option>
									<option value="2">Безнал по счету</option>
									<option value="3">Безнал по квитанции</option>
									<option value="4">По карте</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="inputBox clear" id="inputBox_deliveryType">
						<div class="inputBox_label">Способ доставки</div>
						<div class="inputBox_content">
							<div class="inputBox_input">
								<select>
									<option value=""></option>
									<option value="1">Самовывоз</option>
									<option value="2">По Москве</option>
									<option value="8">До ТК</option>
									<option value="5">СДЭК</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class="inputBox clear" id="inputBox_contactPhone">
						<div class="inputBox_label">Контактный телефон</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" /></div>
							<div class="inputBox_toolbar">
							</div>
						</div>
					</div>
					
					<div class="inputBox clear" id="inputBox_contactName">
						<div class="inputBox_label">Контактное лицо</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" /></div>
							<div class="inputBox_toolbar">
							</div>
						</div>
					</div>
				</div>
				
				<div class="row clear">
					<div class="inputBox clear" id="inputBox_deliveryAddress">
						<div class="inputBox_label">Адрес доставки</div>
						<div class="inputBox_content">
							<div class="inputBox_input"><input type="text" /></div>
							<div class="inputBox_toolbar">
							</div>
						</div>
					</div>
				</div>
				
				<div class="inputBox clear" id="inputBox_order_comment">
					<div class="inputBox_label">Примечания к заказу</div>
					<div class="inputBox_content">
						<div class="inputBox_input"><textarea></textarea></div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="area" id="orderItemsArea">
			<div class="area_header">Предмет счета</div>
			<div class="area_content">
				<div class="toolbar">
					<span class="btn btn-add-item"><i class="fas fa-plus"></i> Добавить артикул</span>
					<span class="btn btn-add-delivery"><i class="fas fa-truck"></i> Добавить доставку</span>
				</div>
				
				<table id="orderItemsTable" class="listTable" cellpadding="0" cellspacing="0" align="center" width="100%">
					<thead>
						<tr>
							<td class="col-nr">#</td>
							<td class="col-article">Артикул</td>
							<td class="col-info">Инфо</td>
							<td class="col-sys">Наименование</td>
							<td class="col-count">Кол-во</td>
							<td class="col-price">Цена</td>
							<td class="col-sum">Сумма</td>
							<td class="col-actions"></td>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				
				<div class="clear">
					<div id="orderItemsSummary" class="summary">
						<div class="summary_discount">
							<div class="label">Скидка</div>
							<div class="value"><input type="text" /></div>
						</div>
						<div class="summary_sum">
							<div class="label">Итого</div>
							<div class="value">—</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="area" id="itemsCostArea">
			<div class="area_header">Себестоимость</div>
			<div class="area_content">
				<div class="toolbar">
					<span class="btn btn-add-item"><i class="fas fa-plus"></i> Добавить строку</span>
				</div>
				<table id="itemsCostTable" class="listTable" cellpadding="0" cellspacing="0" align="center" width="100%">
					<thead>
						<tr>
							<td class="col-nr">#</td>
							<td class="col-podr">Подрядчик</td>
							<td class="col-sys">Наименование</td>
							<td class="col-count">Кол-во</td>
							<td class="col-price">Цена</td>
							<td class="col-sum">Сумма</td>
							<td class="col-actions"></td>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<div class="clear">
					<div class="summary">
						<div class="summary_sum">
							<div class="label">Себестоимость</div>
							<div class="value">—</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<div class="pageFader"></div>