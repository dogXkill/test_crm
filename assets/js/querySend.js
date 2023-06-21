jQuery(document).ready(function($) {

	$.app = (function () {
		var init = false;
		
		var xhrs = {};

		var domObjs = {
			pageFader: $('.pageFader'),
			pageContainer: $('#queryPage'),
			clientsSearchResult: $('#clientsListBox')
		};
		
		// Хелперы (много мелких действий)
		var helpers = {
			renumberOrderItems: function() {
				domObjs.orderItemsList.find('.entry').each(function(index, item) {
					$(item).find('.col-nr span').text((index + 1));
				});
			},
			renumberItemsCost: function() {
				domObjs.itemsCostList.find('.entry').each(function(index, item) {
					$(item).find('.col-nr span').text((index + 1));
				});
			},
			isInt: function(value) {
				return (Math.floor(value) == value && $.isNumeric(value));
			},
			sanitizePrice: function(value) {
				// Переобразовать все запятые в точки
				value = value.replace(/,/g, '.');
				
				// Убрать все символы кроме цифр и точек
				value = value.replace(/[^\d\.]/g, '');
				
				// Удаление подряд идущих точек
				value = value.replace(/\.{2,}/g, '.');
				
				// Если первый символ точка, заменяет на '0.'
				value = value.replace(/^\./g, '0.');	
				
				// Удаляем точку, если она последний символ
				value = value.replace(/\.$/g, '');
				
				// Если в числе больше точек, удаляем все после второй точки вместе с точкой
				value = value.replace(/^([\d]*\.[\d]*)\.[\d\.]*$/g, '$1');
				
				value = (+value).toFixed(2);
				
				return value;
			},
			sanitizeInt: function(value) {
				value = parseInt(value);
				
				if (isNaN(value)) {
					value = 0;
				}
				
				if (value < 0) {
					value = value * -1;
				}
				
				return value;
			},
			sanitizePriceInput: function(input) {
				input.val(this.sanitizePrice(input.val()));
			},
			sanitizeIntInput: function(input) {
				input.val(this.sanitizeInt(input.val()));
			},
			sanitizeDiscountInput: function(input) {
				var value = this.sanitizeInt(input.val());
				value = value == 0 ? '' : value;
				value = value > 50 ? 50 : value;
				
				input.val(value);
			},
			formatPrice: function(value) {
				var newValue = value.toLocaleString('ru', {minimumFractionDigits: 2, maximumFractionDigits: 2}).replace(/,/g, '.');
				return newValue.replace(/\.00$/g, '');
			}
		};

		// Инициализация клиента (единожды)
		var initClient = function() {

			// initDomObjs();
			domObjs.clientArea = domObjs.pageContainer.find('#clientArea');
			
			domObjs.clientShort = domObjs.clientArea.find('#inputBox_client_short input[type=text]');
			domObjs.clientFull = domObjs.clientArea.find('#inputBox_client_full input[type=text]');
			domObjs.clientAddress = domObjs.clientArea.find('#inputBox_address input[type=text]');
			domObjs.clientInn = domObjs.clientArea.find('#inputBox_inn input[type=text]');
			domObjs.clientKpp = domObjs.clientArea.find('#inputBox_kpp input[type=text]');
			domObjs.clientRs = domObjs.clientArea.find('#inputBox_rs input[type=text]');
			domObjs.clientBik = domObjs.clientArea.find('#inputBox_bik input[type=text]');
			domObjs.clientEmail = domObjs.clientArea.find('#inputBox_email input[type=text]');
			domObjs.clientComment = domObjs.clientArea.find('#inputBox_client_comment textarea');
			
			domObjs.clientPhones = {};
			domObjs.clientPhones.box = domObjs.clientArea.find('#inputBox_phone');
			domObjs.clientPhones.tagsBox = domObjs.clientPhones.box.find('.inputBox_inputs');
			domObjs.clientPhones.addButton = domObjs.clientPhones.tagsBox.find('.tag__add');

			
			// Поиск клиента по короткому названию
			domObjs.clientShort.data('valueLength', domObjs.clientShort.val().length);
			domObjs.clientShort.on('keyup', function() {
				var input = $(this),
					value = input.val();
				
				if (value.length != parseInt(input.data('valueLength'))) {
					input.data('valueLength', value.length);
					
					if (value.length >= 3) {
						searchCustomers();
					} else {
						hideSearchCustomersResult();
					}
				}
			});
			
			// Делать поиск при фокусе
			domObjs.clientShort.on('focus', function() {
				var input = $(this),
					value = input.val();
				
				if (domObjs.clientsSearchResult.is(':visible')) {
					return false;
				}
				
				if (value.length >= 3) {
					searchCustomers();
				} else {
					hideSearchCustomersResult();
				}
			});
			
			// При клике на результат поиска - подставляем клиента в форму
			domObjs.clientsSearchResult.on('click', '.short', function(data) {
				var client = $(this).closest('.clientList_item').find('.clientList_item_data');
				
				// Подставляем значения
				domObjs.clientShort.val(client.data('short'));
				domObjs.clientFull.val(client.data('full'));
				domObjs.clientAddress.val(client.data('address'));
				domObjs.clientInn.val(client.data('inn'));
				domObjs.clientKpp.val(client.data('kpp'));
				domObjs.clientRs.val(client.data('rs'));
				domObjs.clientBik.val(client.data('bik'));
				domObjs.clientEmail.val(client.data('email'));
				domObjs.clientComment.val(client.data('comment'));
				
				// Подставляем телефоны
				domObjs.clientPhones.tagsBox.find('.tag').remove();
				
				var phones = client.data('phones');
				if (phones.length > 0) {
					$.each(phones, function(index, phone) {
						var element = generatePhoneTag(phone);
						domObjs.clientPhones.addButton.before(element);
					});
				}
				
				// Переключаем на существующего пользователя
				
				// Меняем заголовок на существующего пользователя
				var badge = domObjs.clientArea.find('.area_header .client_badge');
				badge.removeClass('new').text(client.data('short'));
				
				hideSearchCustomersResult();
			});
			
			// Добавление телефона (тега)
			domObjs.clientPhones.addButton.on('click', function() {
				var button = $(this);
				
				var insert = true;
				
				if (button.prev().hasClass('tag') && button.prev().find('input').val() == '') {
					insert = false;
				}
				
				if (insert) {
					button.before(generatePhoneTag(''));
				}
			});
			
			// Скрытие результатов по клику на фэйдер
			domObjs.pageFader.on('click', hideSearchCustomersResult);
		};
		
		// Инициализация заказа (единожды)
		var initOrder = function() {
			domObjs.orderInfoArea = domObjs.pageContainer.find('#orderInfoArea');
			domObjs.orderItemsArea = domObjs.pageContainer.find('#orderItemsArea');
			domObjs.itemsCostArea = domObjs.pageContainer.find('#itemsCostArea');
			
			domObjs.orderType = domObjs.orderInfoArea.find('#inputBox_orderType select');
			domObjs.paymentType = domObjs.orderInfoArea.find('#inputBox_paymentType select');
			domObjs.deliveryType = domObjs.orderInfoArea.find('#inputBox_deliveryType select');
			domObjs.deliveryAddressBox = domObjs.orderInfoArea.find('#inputBox_deliveryAddress');
			
			domObjs.orderItemsTable = domObjs.pageContainer.find('#orderItemsTable');
			domObjs.orderItemsToolbar = domObjs.orderItemsArea.find('.toolbar');
			domObjs.orderItemsList = domObjs.orderItemsTable.find('tbody');
			domObjs.orderItemsSummary = domObjs.orderItemsArea.find('.summary');
			domObjs.orderItemsDiscount = domObjs.orderItemsSummary.find('.summary_discount input[type=text]');
			domObjs.orderItemsTotal = domObjs.orderItemsSummary.find('.summary_sum .value');
			
			domObjs.itemsCostTable = domObjs.pageContainer.find('#itemsCostTable');
			domObjs.itemsCostToolbar = domObjs.itemsCostTable.find('.toolbar');
			domObjs.itemsCostList = domObjs.itemsCostTable.find('tbody');
			domObjs.itemsCostSummary = domObjs.itemsCostArea.find('.summary');
			domObjs.itemsCostTotal = domObjs.itemsCostSummary.find('.summary_sum .value');
			
			
			// Смена типа заказа
			domObjs.orderType.on('change', function() {
				var select = $(this);
				
				// Если выбран "магазин" или "магазин с лого"
				if ($.inArray(select.val(), ['2', '3']) == -1) {
					domObjs.orderItemsTable.addClass('itemsWithArticle');
				} else {
					domObjs.orderItemsTable.removeClass('itemsWithArticle');
				}
			});
			
			// Смена типа доставки
			domObjs.deliveryType.on('change', function() {
				var select = $(this);
				
				if (select.val() == 1 || select.val() == 0) {
					domObjs.deliveryAddressBox.parent().hide();
				} else {
					domObjs.deliveryAddressBox.parent().show();
				}
			});
			
			// Добавление строки по нажатию на кнопку в тулбаре
			domObjs.orderItemsToolbar.on('click', '.btn-add-item', function() {
				var orderItem = entities.orderItem.new();
				var orderItemCost = entities.orderItemCost.new();
				
				orderItem.bind(orderItemCost.getId());
				orderItemCost.bind(orderItem.getId());
				
				domObjs.orderItemsList.append(orderItem.render());
				domObjs.itemsCostList.append(orderItemCost.render());
				
				helpers.renumberOrderItems();
				helpers.renumberItemsCost();
			});
			
			// Добавление доставки по нажатию на кнопку в тулбаре
			domObjs.orderItemsToolbar.on('click', '.btn-add-delivery', function() {
				var orderItem = entities.orderItemDelivery.new();
				var orderItemCost = entities.orderItemCostDelivery.new();

				orderItem.bind(orderItemCost.getId());
				orderItemCost.bind(orderItem.getId());

				domObjs.orderItemsList.append(orderItem.render());
				domObjs.itemsCostList.append(orderItemCost.render());
				
				helpers.renumberOrderItems();
				helpers.renumberItemsCost();
			});
			
			// Открытие выпадающиего списка апгредов
			domObjs.orderItemsTable.on('click', '.actionAddUpgrade .action-trigger', function() {
				var trigger = $(this),
					button = trigger.parent();
				
				// Закрыть все открытые
				domObjs.orderItemsTable.find('.actionAddUpgrade').not(button).removeClass('opened');
				
				button.toggleClass('opened');
			});
			
			// При клике на любое место страницы - закрываем открытые всплывающие окна
			$(document).on('click', function(e) {
				if ($(e.target).closest('.actionAddUpgrade').length == 0) {
					domObjs.orderItemsTable.find('.actionAddUpgrade.opened').removeClass('opened');
				}
			});
			
			// Добавление апгрейда
			domObjs.orderItemsTable.on('click', '.actionAddUpgrade .dropdown > div', function() {
				var button = $(this).closest('.actionAddUpgrade'),
					upgradeType = $(this).data('type'),
					item = button.closest('tr'),
					itemId = item.data('itemId');

				// Генерируем подстроку привязанную к основной строке
				var orderItem = entities.orderItemUpgrade.new();
				orderItem.setParentId(itemId).setType(upgradeType);
				
				// Проверяем если есть привязка с себестоимостью
				var bindedCost = domObjs.itemsCostList.find('#item-' + item.data('bindedId'))
				if (bindedCost.length > 0) {
					// Генерируем подстроку строки себестоимости
					var orderItemCost = entities.orderItemCostUpgrade.new();
					orderItemCost.setParentId(bindedCost.data('itemId')).setType(upgradeType).bind(orderItem.getId());
					
					// Привяжем подстроку из с/c к текущей подстроке
					orderItem.bind(orderItemCost.getId());
					
					// Добавить подстроку после всех подстрок привязанной строки с/c
					domObjs.itemsCostList.find('tr.entry-' + bindedCost.data('itemId') + ':last').after(orderItemCost.render());
				}
				
				// Добавить подстроку после всех подстрок текущей строки
				domObjs.orderItemsList.find('tr.entry-' + itemId + ':last').after(orderItem.render());
				
				// Закрыть селект
				button.removeClass('opened');
			});
						
			// Удаление строки
			domObjs.orderItemsTable.on('click', '.actionRemoveItem', function() {
				var button = $(this),
					item = button.closest('tr'),
					itemId = item.data('itemId');

				if (item.hasClass('entry-delivery') || item.hasClass('sub-entry')) {
					var bindedCost = domObjs.itemsCostList.find('#item-' + item.data('bindedId'));
					if (bindedCost.length > 0) {
						bindedCost.remove();
					}
					
					item.remove();
				} else if (item.hasClass('entry-item')) {
					var bindedCost = domObjs.itemsCostList.find('#item-' + item.data('bindedId'));
					if (bindedCost.length > 0) {
						domObjs.itemsCostList.find('.entry-' + item.data('bindedId')).remove();
					}
					
					item.siblings('.entry-' + itemId).remove();
					item.remove();
				}
				
				helpers.renumberOrderItems();
				helpers.renumberItemsCost();
				
				recalculateItems();
				recalculateCosts();
			});
			
			// Удаление строки с/с
			domObjs.itemsCostList.on('click', '.actionRemoveItem', function() {
				var button = $(this),
					item = button.closest('tr'),
					itemId = item.data('itemId');
					
				if (item.hasClass('entry-delivery') || item.hasClass('sub-entry')) {
					var binded = domObjs.orderItemsTable.find('#item-' + item.data('bindedId'));
					if (binded.length > 0) {
						binded.removeData('bindedId')
					}
					
					item.remove();
				} else if (item.hasClass('entry-item')) {
					var binded = domObjs.orderItemsTable.find('#item-' + item.data('bindedId'));
					if (binded.length > 0) {
						domObjs.orderItemsTable.find('.entry-' + item.data('bindedId')).removeData('bindedId');
					}
					
					item.siblings('.entry-' + itemId).remove();
					item.remove();
				}
				
				helpers.renumberItemsCost();
				
				recalculateCosts();
			});
			
			// Подгрузка артикула при изменении соответствующего поля
			domObjs.orderItemsTable.on('change', '.col-article input[type=text]', function() {
				var input = $(this),
					value = input.val();
					
				if (!helpers.isInt(value)) {
					alert(appConfig.strings['article_not_found']);
					return false;
				}
				
				var sendData = {};
				sendData.action = 'querySend/getArticleInfo';
				sendData.article = value;
				
				ajaxRequest({
					xhr: 'getArticleInfoXhr',
					sendData: sendData,
					callback: function(data) {
						console.log(data);
					}
				});
			});
			
			// При изменении цены
			domObjs.orderItemsTable.on('change', '.col-price input[type=text]', function() {
				var input = $(this);
			
				helpers.sanitizePriceInput(input);
				
				recalculateItems();
			});

			// При изменении количества
			domObjs.orderItemsTable.on('change', '.col-count input[type=text]', function() {
				var input = $(this);
				
				helpers.sanitizeIntInput(input);
				value = input.val();
				
				// Изменить связаанную запись в таблице с/c
				var item = input.closest('tr'),
					binded = domObjs.itemsCostList.find('#item-' + item.data('bindedId'));
					
				if (binded.length > 0) {
					binded.find('.col-count input[type=text]').val(value);
				}
				
				recalculateItems();
				recalculateCosts();
			});
			
			// При изменении скидки
			domObjs.orderItemsDiscount.on('change', function() {
				var input = $(this);
			
				helpers.sanitizeDiscountInput(input);
				
				recalculateItems();
			});
			
			// При изменении цены с/c
			domObjs.itemsCostTable.on('change', '.col-price input[type=text]', function() {
				var input = $(this);
			
				helpers.sanitizePriceInput(input);
				
				recalculateCosts();
			});
			
			// При изменении количества с/c
			domObjs.itemsCostTable.on('change', '.col-count input[type=text]', function() {
				var input = $(this);
				
				helpers.sanitizeIntInput(input);
				value = input.val();
				
				// Изменить связаанную запись в таблице предмета счета
				var item = input.closest('tr'),
					binded = domObjs.orderItemsTable.find('#item-' + item.data('bindedId'));
					
				if (binded.length > 0) {
					binded.find('.col-count input[type=text]').val(value);
				}
				
				recalculateItems();
				recalculateCosts();
			});
			
			// Дергаем триггеры после инициализации
			afterOrderInit();
		}
		
		// Общий пересчет
		function recalculate() {
			
		}
		
		// Пересчет таблицы предмата счета
		function recalculateItems() {
			var total = 0;
			
			helpers.sanitizeDiscountInput(domObjs.orderItemsDiscount);
			var discount = +domObjs.orderItemsDiscount.val();
			
			domObjs.orderItemsList.find('tr').each(function(index, _item) {
				var item = $(_item),
					inputCount = item.find('.col-count input[type=text]'),
					inputPrice = item.find('.col-price input[type=text]'),
					itemSum = item.find('.col-sum > span');
				
				var count = 1;
				
				if (inputCount.length > 0) {
					helpers.sanitizeIntInput(inputCount);
					var count = +inputCount.val();
				}
				
				helpers.sanitizePriceInput(inputPrice);
				var price = +inputPrice.val();
				
				if (discount > 0) {
					price = price - (price / 100) * discount;
					
					var discountPriceBox = inputPrice.siblings('span.discountPrice');
					// console.log(discountPriceBox);
					if (discountPriceBox.length > 0) {
						discountPriceBox.text(price.toFixed(2));
					} else {
						inputPrice.after('<span class="discountPrice">' + price.toFixed(2) + '</span>');
					}
				}
				
				var sum = count * price;
				itemSum.text(helpers.formatPrice(sum));
				
				total += sum;
			});
			
			/* helpers.sanitizeDiscountInput(domObjs.orderItemsDiscount);
			var discount = +domObjs.orderItemsDiscount.val();
			if (discount > 0) {
				total = total - (total / 100) * discount;
			} */
			
			domObjs.orderItemsTotal.text(helpers.formatPrice(total));
		}
		
		// Пересчет таблицы с/c
		function recalculateCosts() {
			var total = 0;
			
			domObjs.itemsCostList.find('tr').each(function(index, _item) {
				var item = $(_item),
					inputCount = item.find('.col-count input[type=text]'),
					inputPrice = item.find('.col-price input[type=text]'),
					itemSum = item.find('.col-sum > span');
				
				var count = 1;
				
				if (inputCount.length > 0) {
					helpers.sanitizeIntInput(inputCount);
					var count = +inputCount.val();
				}
				
				helpers.sanitizePriceInput(inputPrice);
				var price = +inputPrice.val();
				
				var sum = count * price;
				itemSum.text(helpers.formatPrice(sum));
				
				total += sum;
			});
			
			domObjs.itemsCostTotal.text(helpers.formatPrice(total));
		}
		
		function afterOrderInit() {
			domObjs.deliveryType.trigger('change');
			domObjs.orderType.trigger('change');
		}
		
		// Поиск пользователей по короткому названию
		function searchCustomers() {
			var input = domObjs.clientShort,
				box = input.closest('.inputBox');
			
			box.addClass('loading');
			domObjs.pageFader.hide();
			domObjs.clientsSearchResult.hide();
			
			var sendData = {};
			// sendData.action = 'query_send_searchCustomers';
			sendData.action = 'querySend/searchCustomers';
			sendData.search = input.val();
			
			cancelPrevAjaxRequest('searchCustomersXhr');
			ajaxRequest({
				xhr: 'searchCustomersXhr',
				sendData: sendData,
				callback: function(data) {
					// console.log(data);
					box.removeClass('loading');
					
					if (data.count > 0) {
						domObjs.pageFader.show();
						domObjs.clientsSearchResult.html(data.html);
						domObjs.clientsSearchResult.show();
					}
				}
			});
		};
		
		function hideSearchCustomersResult() {
			domObjs.pageFader.hide();
			domObjs.clientsSearchResult.hide();
		}
		
		// Отмена прдыдущего ajax-запроса по идентификатору
		function cancelPrevAjaxRequest(xhrId) {
			var xhr = xhrs[xhrId];
			
			if (xhr != undefined && xhr.readyState == 1) {
				xhr.abort();
			}
		};
		
		// ajax-обертка
		function ajaxRequest(args = {}) {
			if (args.xhr == undefined) {
				args.xhr = 'globalXhr';
			}
			// console.log(xhrs[args.xhr]);
			xhrs[args.xhr] = $.ajax({
				url: 'ajax.php',
				data: args.sendData,
				dataType: 'json',
				type: 'POST',
				beforeSend: function() {},
				success: args.callback
			});
		}
		
		// Генерация телефонного тега
		function generatePhoneTag(phone) {
			var str = '<div class="tag">\
							<div class="tag__input">\
								<input type="text" value="' + phone + '">\
								<span class="tag__remove"><i class="fa fa-times"></i></span>\
								<div class="loader"></div>\
							</div>\
							<div class="tag__tools"></div>\
						</div>';
						
			return $(str);
		}
		
		return {
			init: function() {
				if (!init) {
					initClient();
					initOrder();
				}
				
				return true;
			}
		}
	})();
	
	$.app.init();
	
});