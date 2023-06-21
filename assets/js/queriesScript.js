jQuery(document).ready(function($) {

	/** РАБОТА С ПЛАТЕЖАМИ **/
	var editPaymentsDialog = $('#editPaymentsDialog');

	// Создание окна редактирования платежей
	editPaymentsDialog.dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		maxWidth: 600,
		draggable: false,
		resizable: false,
		position: { my: 'center center-100px', at: 'center', of: window },
		open: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			$.post('ajax.php', {action: 'getPaymentsEditForm', queryId: currentQueryId}, function(data) {
				if (data.status && data.status == 200) {
					// Подгрузили контент
					dialogContent.html(data.template_part);

					dialogLoader.hide();
					dialogContent.show();

					editPaymentsDialog.dialog('option', 'position', { my: 'center center-100px', at: 'center', of: window });

					// В контенте вешаем датапикер на нужные поля
					dialogContent.find('.inputDate').datepicker();
				} else {
					console.log(data);
				}

				$('.ui-widget-overlay').on('click', function() {
					editPaymentsDialog.dialog('close');
				});
			}, 'json');
		},
		close: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			// После закрытия удалим поинтер со строки
			$('tr#query_' + currentQueryId).removeClass('pointed');

			dialogLoader.show();
			dialogContent.hide().html('');
		}
	});

	editPaymentsDialog.on('click', 'span.setDate', function() {
		var days = $(this).data('days'),
			input = $(this).closest('tfoot').find('input.inputDate');

		input.datepicker('setDate', '-' + days);
	});

	// Удаление платежа
	editPaymentsDialog.on('click', 'i.deletePayment', function() {
		var thisDialog = editPaymentsDialog,
			dialogContent = thisDialog.find('.dialogContent'),
			dialogLoader = thisDialog.find('.dialogLoader'),
			button = $(this),
			currentRow = button.closest('tr');

		if (!confirm('Вы действительно хотите удалить платеж на сумму ' + currentRow.data('valueSumm') + ' руб. по платежке номер ' + (currentRow.data('valueNumber') != '' ? currentRow.data('valueNumber') : 'НЕ УКАЗАН') + ' от ' + currentRow.data('valueDate') + '?')) {
			return false;
		}

		var queryId = thisDialog.data('currentQueryId');

		var sendData = {};
		sendData.action = 'deletePayment';
		sendData.queryId = queryId,
		sendData.paymentId = currentRow.data('paymentId');

		dialogLoader.show();

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				var tfoot = currentRow.closest('table').find('tfoot'),
					paymentsBox = currentRow.closest('tbody'),
					addBox = tfoot.find('.add-row');

				// Удалить ошибки перед строкой, если есть
				currentRow.prev('.errors-row').remove();

				// Удалить текущую строку
				currentRow.remove();

				// Если строк нет - выводим сообщение, что нет платежей
				if (paymentsBox.find('> tr').length == 0) {
					var noPaymentsRow = paymentsBox.closest('table').find('tfoot .no-payments-row').clone();
					paymentsBox.append(noPaymentsRow);
				}

				// Обновляем форму добавления, указываем новый долг
				addBox.find('input[name=summ]').val(data.dolg);

				// Обновляем колонку "оплачено" и "долг" запроса в списке запросов
				var queryRow = $('#query_' + queryId);
				queryRow.find('.col-payments > span').text(data.oplaceno);
				queryRow.find('.col-debt > span').text(data.dolg);

				dialogLoader.hide();
			} else {
				console.log(data);
			}
		}, 'json');
	});

	// Добавление платежа
	editPaymentsDialog.on('click', 'i.addPayment', function() {
		var thisDialog = editPaymentsDialog,
			dialogLoader = thisDialog.find('.dialogLoader'),
			button = $(this),
			currentRow = button.closest('tr');

		dialogLoader.show();
		currentRow.prev('tr.errors-row').remove();
		currentRow.find('input.error').removeClass('error');

		var sumInput = currentRow.find('input[name=summ]'),
			dateInput = currentRow.find('input[name=date]'),
			numberInput = currentRow.find('input[name=number]');

		var queryId = thisDialog.data('currentQueryId');

		var sendData = {};
		sendData.action = 'addPayment';
		sendData.queryId = queryId;
		sendData.payment = {
			summ: sumInput.val(),
			date: dateInput.val(),
			number: numberInput.val()
		};

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				var paymentsBox = currentRow.closest('table').find('tbody'),
					noRows = paymentsBox.find('.no-payments-row');

				if (noRows.length > 0) {
					noRows.remove();
				}

				paymentsBox.append(data.template_part);

				var payment = paymentsBox.find('#payment_' + data.paymentId);
				// Вешаем datepicker на новый платеж
				payment.find('.inputDate').datepicker();

				// Обнуляем форму добавления (выставляем текущий долг в сумму и выставляем текущую дату)
				sumInput.val(data.dolg);
				dateInput.datepicker('setDate', new Date());
				numberInput.val('');

				// Меняем значение в таблице запросов
				var queryRow = $('#query_' + queryId);
				queryRow.find('.col-payments > span').text(data.oplaceno);
				queryRow.find('.col-debt > span').text(data.dolg);

				dialogLoader.hide();
			} else if (data.status && data.status == 600) {
				var errorBox = $('<tr class="errors-row"><td colspan="4"></td></tr>');

				$.each(data.errors, function(key, message) {
					errorBox.find('td').append('<div>- ' + message + '</div>');
					currentRow.find('input[name=' + key + ']').addClass('error');
				});

				currentRow.before(errorBox);

				dialogLoader.hide();
			} else {
				console.log(data);
			}
		}, 'json');
	});

	// Редактирование платежа
	editPaymentsDialog.on('click', 'i.editPayment', function() {
		var thisDialog = editPaymentsDialog,
			dialogContent = thisDialog.find('.dialogContent'),
			dialogLoader = thisDialog.find('.dialogLoader'),
			button = $(this),
			currentRow = button.closest('tr');

		if (button.hasClass('disabled')) {
			return false;
		}

		dialogLoader.show();
		currentRow.prev('tr.errors-row').remove();
		currentRow.prev('tr.success-row').remove();
		currentRow.find('input.error').removeClass('error');

		var queryId = thisDialog.data('currentQueryId'),
			paymentId = currentRow.data('paymentId');

		var sumInput = currentRow.find('input[name=summ]'),
			dateInput = currentRow.find('input[name=date]'),
			numberInput = currentRow.find('input[name=number]');

		var sendData = {};
		sendData.action = 'editPayment';
		sendData.queryId = queryId;
		sendData.paymentId = paymentId;
		sendData.payment = {
			summ: sumInput.val(),
			date: dateInput.val(),
			number: numberInput.val()
		};

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				var successBox = currentRow.closest('table').find('tfoot tr.success-row').clone();
				successBox.removeClass('temp');

				currentRow.before(successBox);
				button.addClass('disabled');

				// Меняем значение в таблице запросов
				var queryRow = $('#query_' + queryId);
				queryRow.find('.col-payments > span').text(data.oplaceno);
				queryRow.find('.col-debt > span').text(data.dolg);

				dialogLoader.hide();
			} else if (data.status && data.status == 600) {
				var errorBox = $('<tr class="errors-row"><td colspan="4"></td></tr>');

				$.each(data.errors, function(key, message) {
					errorBox.find('td').append('<div>- ' + message + '</div>');
					currentRow.find('input[name=' + key + ']').addClass('error');
				});

				currentRow.before(errorBox);

				dialogLoader.hide();
			} else {
				console.log(data);
			}
		}, 'json');
	});

	editPaymentsDialog.on('change', 'table tbody input[name=summ], table tbody input[name=date], table tbody input[name=number]', function() {
		var input = $(this),
			currentRow = input.closest('tr'),
			button = currentRow.find('i.editPayment');

		button.removeClass('disabled');

		currentRow.prev('.success-row').remove();
	});

	editPaymentsDialog.on('keypress', 'table tbody input[name=summ], table tbody input[name=number]', function() {
		var input = $(this),
			currentRow = input.closest('tr'),
			button = currentRow.find('i.editPayment');

		button.removeClass('disabled');

		currentRow.prev('.success-row').remove();
	});

	// При клике на кнопку, открываем окно с редактированием платеже
	$('#queryList tbody').on('click', '.editPaymentsOpenButton', function() {
		var button = $(this),
			tr = button.closest('tr');

		tr.addClass('pointed');
		editPaymentsDialog.data('currentQueryId', tr.data('queryId'));

		editPaymentsDialog.dialog('open');

	});



	/** РАБОТА С КОММЕНТАРИЕМ К ЗАКАЗУ **/
	var editCommentDialog = $('#editCommentDialog');

	// Создание окна редактирования комментария к заказу
	editCommentDialog.dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		maxWidth: 600,
		draggable: false,
		resizable: false,
		position: { my: 'center center-100px', at: 'center', of: window },
		open: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			$.post('ajax.php', {action: 'getCommentEditForm', queryId: currentQueryId}, function(data) {
				if (data.status && data.status == 200) {
					// Подгрузили контент
					dialogContent.html(data.template_part);

					dialogLoader.hide();
					dialogContent.show();

					editCommentDialog.dialog('option', 'position', { my: 'center center-100px', at: 'center', of: window });
				} else {
					console.log(data);
				}

				$('.ui-widget-overlay').on('click', function() {
					editCommentDialog.dialog('close');
				});
			}, 'json');
		},
		close: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			// После закрытия удалим поинтер со строки
			$('tr#query_' + currentQueryId).removeClass('pointed');

			dialogLoader.show();
			dialogContent.hide().html('');
		}
	});

	// Сохранение комментария
	editCommentDialog.on('click', 'span.saveCommentAction', function() {
		var thisDialog = editCommentDialog,
			dialogLoader = thisDialog.find('.dialogLoader'),
			button = $(this),
			result = editCommentDialog.find('.commentEditForm_result'),
			input = editCommentDialog.find('.commentEditForm_input textarea');

		dialogLoader.show();
		result.removeClass('error-result success-result').hide().html('');

		var queryId = thisDialog.data('currentQueryId');

		var sendData = {};
		sendData.action = 'editComment';
		sendData.queryId = queryId;
        sendData.user_name_full = user_name_full;

        //sendData.user_name_full =  $_COOKIE['name'] . " " . $_COOKIE['surname'];
		sendData.comment = input.val();

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				result.addClass('success-result');
				result.html('Комментарий успешно сохранен!');
				result.show();
                editCommentDialog.dialog('close');
               // console.log(data)

				var queryRow = $('#query_' + queryId),
					commentButton = queryRow.find('.actionComment');

				if (data.commentIsEmpty) {
					commentButton.removeClass('active');
					commentButton.find('i').removeClass('fa-comment').addClass('fa-comment-o');
					commentButton.attr('title', 'Добавить комментарий');
				} else {
					commentButton.addClass('active');
					commentButton.find('i').removeClass('fa-comment-o').addClass('fa-comment');
					commentButton.attr('title', input.val());
				}

				dialogLoader.hide();
			} else if (data.status && data.status == 600) {
				result.addClass('error-result');
				result.append('<div>- ' + data.message + '</div>');

				dialogLoader.hide();
				result.show();
			} else {
				console.log(data);
			}
		}, 'json');
	});

	// При клике на кнопку, открываем окно с редактированием комментария
	$('#queryList tbody').on('click', '.actionComment', function() {
		var button = $(this),
			tr = button.closest('tr');

		tr.addClass('pointed');
		editCommentDialog.data('currentQueryId', tr.data('queryId'));

		editCommentDialog.dialog('open');
	});


	var popup_delete = $('#popup_delete');
	/** СОЗДАНИЕ ОКНА УДАЛЕНИЯ **/
	popup_delete.dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		maxWidth: 600,
		draggable: false,
		resizable: false,
		position: { my: 'center center-100px', at: 'center', of: window },
		open: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');
			$.post('ajax.php', {action: 'getDeleteForm', queryId: currentQueryId}, function(data) {
				if (data.status && data.status == 200) {
					// Подгрузили контент
					//
					dialogContent.html(data.template_part);
					console.log(data.tip_delete.tip_reason);
					console.log(data.tip_delete.comment);
					//
					$(".btn_tip_delete").find('span').attr('value',data.tip_delete.tip_reason);
					let text_btn=$("#select_delete").find('div');
					$(text_btn).each(function( index ) {
						if ($(this).attr('value')==data.tip_delete.tip_reason){
							$(".btn_tip_delete").find('span').text($(this).text());
						}
					});
					$("#comment_delete").val(data.tip_delete.comment);
					
					
					dialogLoader.hide();
					dialogContent.show();
					popup_delete.dialog('option', 'position', { my: 'center center-100px', at: 'center', of: window });
					//кнопка открыти причин удаления
	$(".btn_tip_delete").click(function(e) {
		//$(".body_tip_delete").toggleClass("show_tip_delete");
		if ($(".body_tip_delete").hasClass("show_tip_delete")==true){
			$(".body_tip_delete").removeClass("show_tip_delete");
		}else{
			$(".body_tip_delete").addClass("show_tip_delete");
		}
	});
	$("#select_delete div").click(function(e){
		
		
		//console.log($(this).attr('value'));
		$(".btn_tip_delete").find('span').text($(this).text());
		$(".btn_tip_delete").find('span').attr("value",$(this).attr('value'));
		$("#select_delete").removeClass("show_tip_delete");
	});
				} else {
					console.log(data);
				}

				$('.ui-widget-overlay').on('click', function() {
					popup_delete.dialog('close');
				});
			}, 'json');
		},
		close: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			// После закрытия удалим поинтер со строки
			$('tr#query_' + currentQueryId).removeClass('pointed');

			dialogLoader.show();
			dialogContent.hide().html('');
		}
	});
	//кнопка "удалить" окно удаления
	popup_delete.on('click', 'span.saveDeleteAction', function() {
		var thisDialog = popup_delete,
			dialogLoader = thisDialog.find('.dialogLoader'),
			button = $(this),
			result = popup_delete.find('.commentDeleteForm_result'),
			input = popup_delete.find('.commentDeleteForm_input textarea');
		
		dialogLoader.show();
		result.removeClass('error-result success-result').hide().html('');


		var sendData = {};
		let select_val=$(".btn_tip_delete").find('span').attr('value');
		var queryId = thisDialog.data('currentQueryId');
		sendData.action = 'deleteQuery';
		sendData.queryId = queryId;
        sendData.user_name_full = user_name_full;
		sendData.comment=$("#comment_delete").val();
		sendData.select_val=select_val;
		if (select_val==8 && $("#comment_delete").val()==""){
			result.addClass('error-result');
				result.append('<div>- заполните поле комментарий</div>');
				dialogLoader.hide();
				result.show();
		}else{
        //sendData.user_name_full =  $_COOKIE['name'] . " " . $_COOKIE['surname'];
		

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200 && data.message==null) {
				result.addClass('success-result');
				result.html('Заказ удалён!');
				result.show();
                popup_delete.dialog('close');
               // console.log(data)
			   var queryRow = $('#query_' + queryId);
				queryRow.fadeOut(500, function() {
					$(this).remove();
				});
				//var queryRow = $('#query_' + queryId),
				//	commentButton = queryRow.find('.actionComment');

				//if (data.commentIsEmpty) {
				//	commentButton.removeClass('active');
				//	commentButton.find('i').removeClass('fa-comment').addClass('fa-comment-o');
				//	commentButton.attr('title', 'Удаление');
				//} else {
				//	commentButton.addClass('active');
				//	commentButton.find('i').removeClass('fa-comment-o').addClass('fa-comment');
				//	commentButton.attr('title', input.val());
				//}
				dialogLoader.hide();
			} else if (data.status && data.status == 600) {
				result.addClass('error-result');
				result.append('<div>- ' + data.message + '</div>');

				dialogLoader.hide();
				result.show();
			} else if(data.message!=""){
				result.addClass('error-result');
				result.append('<div>- ' + data.message + '</div>');

				dialogLoader.hide();
				result.show();
			}else {
				console.log(data);
			}
		}, 'json');
		}
	});
	/** КНОПКА УДАЛЕНИЕ ЗАКАЗА **/
	$('#queryList tbody').on('click', '.actionDelete', function() {
		var button = $(this),
			row = button.closest('tr');
		
        //защита от второго клика
		if (button.hasClass('process')) return false;




		button.addClass('process');
		row.addClass('pointed');

		var sendData = {};
		//sendData.action = 'deleteQuery';
		//sendData.queryId = row.data('queryId');
		
        if (button.hasClass('restore')){sendData.act = 'restore'; txt = 'восстановить';} else{sendData.act = 'delete'; txt = 'удалить';}
		
        if (!confirm('Вы действительно хотите ' + txt + ' этот заказ и все его данные?')) {
			return false;
		}
		if (txt=='удалить'){
			var button = $(this),
			tr = button.closest('tr');
			popup_delete.data('currentQueryId', tr.data('queryId'));
		
			
			popup_delete.dialog('open');
			
		}
		if (txt=='восстановить'){
			sendData.action='deleteQuery';
			sendData.act = 'restore';
			sendData.queryId = row.data('queryId');;
			sendData.user_name_full = user_name_full;
			$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				// Успешное удаление
				row.removeClass('pointed');
				//row.addClass(data.order_access_edit ? 'deleted' : 'markedForDeletion');
                 if(data.order_access_edit == 2 && data.order_access_edit == 'delete')
                    {row.addClass('deleted');}
                            else
                    {row.addClass('markedForDeletion');}

				row.fadeOut(500, function() {
					$(this).remove();
				});
			}


		}, 'json');
		}

		/*$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				// Успешное удаление
				row.removeClass('pointed');
				//row.addClass(data.order_access_edit ? 'deleted' : 'markedForDeletion');
                 if(data.order_access_edit == 2 && data.order_access_edit == 'delete')
                    {row.addClass('deleted');}
                            else
                    {row.addClass('markedForDeletion');}

				row.fadeOut(500, function() {
					$(this).remove();
				});
			}


		}, 'json');
		*/
	});
	//изменение комментария или причины удаления
	//izm_tip_delete
	var popup_izm_delete = $('#popup_izm_delete');
	//создание окна изменения удалённых заказов
	popup_izm_delete.dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		maxWidth: 600,
		draggable: false,
		resizable: false,
		position: { my: 'center center-100px', at: 'center', of: window },
		open: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');
			$.post('ajax.php', {action: 'getIzmDeleteForm', queryId: currentQueryId}, function(data) {
				if (data.status && data.status == 200) {
					// Подгрузили контент
					dialogContent.html(data.template_part);
					console.log(data.tip_delete.tip_reason);
					$(".btn_tip_delete").find('span').attr('value',data.tip_delete.tip_reason);
					let text_btn=$("#select_delete").find('div');
					$(text_btn).each(function( index ) {
						if ($(this).attr('value')==data.tip_delete.tip_reason){
							$(".btn_tip_delete").find('span').text($(this).text());
						}
					});
					$("#comment_delete").val(data.tip_delete.comment);
					dialogLoader.hide();
					dialogContent.show();
					popup_izm_delete.dialog('option', 'position', { my: 'center center-100px', at: 'center', of: window });
					//кнопка открыти причин удаления
	$(".btn_tip_delete").click(function(e) {
		//$(".body_tip_delete").toggleClass("show_tip_delete");
		if ($(".body_tip_delete").hasClass("show_tip_delete")==true){
			$(".body_tip_delete").removeClass("show_tip_delete");
		}else{
			$(".body_tip_delete").addClass("show_tip_delete");
		}
	});
	$("#select_delete div").click(function(e){
		
		
		//console.log($(this).attr('value'));
		$(".btn_tip_delete").find('span').text($(this).text());
		$(".btn_tip_delete").find('span').attr("value",$(this).attr('value'));
		$("#select_delete").removeClass("show_tip_delete");
	});
				} else {
					console.log(data);
				}

				$('.ui-widget-overlay').on('click', function() {
					popup_izm_delete.dialog('close');
				});
			}, 'json');
		},
		close: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			// После закрытия удалим поинтер со строки
			$('tr#query_' + currentQueryId).removeClass('pointed');

			dialogLoader.show();
			dialogContent.hide().html('');
		}
	});
	//
	//кнопка "сохранить" окно удаления
	popup_izm_delete.on('click', 'span.saveIzmDeleteAction', function() {
		var thisDialog = popup_izm_delete,
			dialogLoader = thisDialog.find('.dialogLoader'),
			button = $(this),
			result = popup_izm_delete.find('.commentDeleteForm_result'),
			input = popup_izm_delete.find('.commentDeleteForm_input textarea');
		
		dialogLoader.show();
		result.removeClass('error-result success-result').hide().html('');


		var sendData = {};
		let select_val=$(".btn_tip_delete").find('span').attr('value');
		var queryId = thisDialog.data('currentQueryId');
		sendData.action = 'deleteQuery';
		sendData.queryId = queryId;
        sendData.user_name_full = user_name_full;
		sendData.comment=$("#comment_delete").val();
		sendData.select_val=select_val;
		if (select_val==8 && $("#comment_delete").val()==""){
			result.addClass('error-result');
				result.append('<div>- заполните поле комментарий</div>');
				dialogLoader.hide();
				result.show();
		}else{
        //sendData.user_name_full =  $_COOKIE['name'] . " " . $_COOKIE['surname'];
		

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				result.addClass('success-result');
				result.html('Заказ удалён!');
				result.show();
                popup_izm_delete.dialog('close');
               // console.log(data)

				var queryRow = $('#query_' + queryId),
					commentButton = queryRow.find('.actionComment');

				if (data.commentIsEmpty) {
					commentButton.removeClass('active');
					commentButton.find('i').removeClass('fa-comment').addClass('fa-comment-o');
					commentButton.attr('title', 'Удаление');
				} else {
					commentButton.addClass('active');
					commentButton.find('i').removeClass('fa-comment-o').addClass('fa-comment');
					commentButton.attr('title', input.val());
				}
				dialogLoader.hide();
			} else if (data.status && data.status == 600) {
				result.addClass('error-result');
				result.append('<div>- ' + data.message + '</div>');

				dialogLoader.hide();
				result.show();
			} else {
				console.log(data);
			}
		}, 'json');
		}
	});
	
	//
	$('#queryList tbody').on('click', '.izm_tip_delete', function() {
		//console.log('ok');
		var button = $(this),
			tr = button.closest('tr');
			//popup_izm_delete
			//
			var thisDialog = popup_izm_delete,
			dialogLoader = thisDialog.find('.dialogLoader'),
			button = $(this),
			result = popup_izm_delete.find('.commentDeleteForm_result'),
			input = popup_izm_delete.find('.commentDeleteForm_input textarea');
			popup_izm_delete.data('currentQueryId', tr.data('queryId'));
			dialogLoader.show();
			result.removeClass('error-result success-result').hide().html('');
			popup_izm_delete.dialog('open');
			
		
	});
	//форма загрузки окна с pdf/world/exl
	var popup_files = $('#file_popup');
	/** СОЗДАНИЕ ОКНА обработки файлов **/
	popup_files.dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		maxWidth: 600,
		draggable: false,
		resizable: false,
		position: { my: 'center center-100px', at: 'center', of: window },
		open: function(event, ui) {
			var thisDialog = $(this), 
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent'),
				tip_files=thisDialog.data('tip');
			$.post('ajax.php', {action: 'getDialogFiles', queryId: currentQueryId,tip:tip_files}, function(data) {
				if (data.status && data.status == 200) {
					// Подгрузили контент
					dialogContent.html(data.template_part);
					
					dialogLoader.hide();
					dialogContent.show();
					popup_files.dialog('option', 'position', { my: 'center center-100px', at: 'center', of: window });
					
				} else {
					console.log(data);
				}

				$('.ui-widget-overlay').on('click', function() {
					popup_files.dialog('close');
				});
			}, 'json');
			$(".otp_email_client").attr('disabled',false);
		},
		close: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			// После закрытия удалим поинтер со строки
			$('tr#query_' + currentQueryId).removeClass('pointed');

			dialogLoader.show();
			dialogContent.hide().html('');
		}
	});
	
	$('#queryList tbody').on('click', '.pdf_popup', function() {
		var button = $(this),
			tr = button.closest('tr');
			popup_files.data('currentQueryId', tr.data('queryId'));
			popup_files.data('tip','pdf');
			popup_files.dialog('open');
	});
	$('#queryList tbody').on('click', '.pdf1_popup', function() {
		var button = $(this),
			tr = button.closest('tr');
			popup_files.data('currentQueryId', tr.data('queryId'));
			popup_files.data('tip','pdf1');
			popup_files.dialog('open');
	});
	$('#queryList tbody').on('click', '.word_popup', function() {
		var button = $(this),
			tr = button.closest('tr');
			popup_files.data('currentQueryId', tr.data('queryId'));
			popup_files.data('tip','word');
			popup_files.dialog('open');
	});
	var text_true="<span style='color:#090;font-weight:900;'>Отправлено</span>";
	var text_error="<span style='color:#CC0000;'>Ошибка отправки</span>";
	var text_error1="<span style='color:#CC0000;'>Некоректная почта</span>";
	const EMAIL_REGEXP = /^(([^<>()[\].,;:\s@"]+(\.[^<>()[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/iu;
	function isEmailValid(value) {
		return EMAIL_REGEXP.test(value);
	}
	//отправка клиенту .otp_email_client
	$(document).on('click', '.otp_email_client', function(e) {
		$(".otp_email_client").attr('disabled',true);
		//$(".otp_email_client").click(function(e){
		//
		if (isEmailValid($("#to_email").val())) {
			$("#to_email").css('borderColor','black');
			flag_mails=1;
		  } else {
			$("#to_email").css('borderColor','red');
			flag_mails=0;
		  }
		  if (flag_mails==1){
			var uids=$("#uid_popup").val();
			var tip_popup=$("#tip_popup").val();
			var to=$("#to_email").val();
			var name_client=$("#name_client").val();
			$.post('/engine/mails/email.php', {uid:uids,tip:tip_popup,to:to,qid:uids,name_client:name_client}, function(data) {
					if (data.status && data.status == 200) {
						// Подгрузили контент
						console.log(typeof data);
						$("#result_docs").html(text_true);
					} else {
						console.log(typeof data);
						$("#result_docs").html(text_error);
					}
					if (data=="1" || data==1){
							$("#result_docs").html(text_true);
							
						}else{
							$("#result_docs").html(text_error);
							$(".otp_email_client").attr('disabled',false);
						}

				}, 'json');
		}else{
			$("#result_docs").html(text_error1);
		}
			//
		});
		//
		
		/** РАБОТА СО СДЕЛКОЙ AMO CRM **/
	var editAmoCrmIdDialog = $('#editAmoCrmIdDialog');

	// Создание окна редактирования сделки AMO
	editAmoCrmIdDialog.dialog({
		autoOpen: false,
		modal: true,
		width: 'auto',
		maxWidth: 600,
		draggable: false,
		resizable: false,
		position: { my: 'center center-100px', at: 'center', of: window },
		open: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			$.post('ajax.php', {action: 'getAmoCrmIdEditForm', queryId: currentQueryId}, function(data) {
				if (data.status && data.status == 200) {
					// Подгрузили контент
					dialogContent.html(data.template_part);

					dialogLoader.hide();
					dialogContent.show();

					editCommentDialog.dialog('option', 'position', { my: 'center center-100px', at: 'center', of: window });
				} else {
					console.log(data);
				}

				$('.ui-widget-overlay').on('click', function() {
					editAmoCrmIdDialog.dialog('close');
				});
			}, 'json');
		},
		close: function(event, ui) {
			var thisDialog = $(this),
				currentQueryId = thisDialog.data('currentQueryId'),
				dialogLoader = thisDialog.find('.dialogLoader'),
				dialogContent = thisDialog.find('.dialogContent');

			// После закрытия удалим поинтер со строки
			$('tr#query_' + currentQueryId).removeClass('pointed');

			dialogLoader.show();
			dialogContent.hide().html('');
		}
	});

	// При клике на кнопку, открываем окно с редактированием комментария
	$('#queryList tbody').on('click', '.actionEditAmoCrmId', function() {
		var button = $(this),
			tr = button.closest('tr');

		tr.addClass('pointed');
		editAmoCrmIdDialog.data('currentQueryId', tr.data('queryId'));

		editAmoCrmIdDialog.dialog('open');
	});

	// Сохранение сделки AMO
	editAmoCrmIdDialog.on('click', 'span.saveAmoCrmIdAction', function() {
		var thisDialog = editAmoCrmIdDialog,
			dialogLoader = thisDialog.find('.dialogLoader'),
			button = $(this),
			result = thisDialog.find('.amoCrmIdEditForm_result'),
			input = thisDialog.find('#amoCrmId');

		dialogLoader.show();
		result.removeClass('error-result success-result').hide().html('');

		var queryId = thisDialog.data('currentQueryId');

		var sendData = {};
		sendData.action = 'editAmoCrmId';
		sendData.queryId = queryId;
		sendData.amoCrmId = input.val();

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				result.addClass('success-result');
				result.html('Успешно сохранено!');
				result.show();

				var queryRow = $('#query_' + queryId),
					amoButton = queryRow.find('.actionEditAmoCrmId');

				if (data.empty) {
					amoButton.removeClass('active');
					amoButton.attr('title', 'Добавить сделку AMO');
				} else {
					amoButton.addClass('active');
					amoButton.attr('title', 'Сделка в AMO: ' + input.val());
				}

				dialogLoader.hide();
			} else if (data.status && data.status == 600) {
				result.addClass('error-result');
				result.append('<div>- ' + data.message + '</div>');

				dialogLoader.hide();
				result.show();
			} else {
				console.log(data);
			}
		}, 'json');
	});

	// Переход к сделке в AMO CRM
	editAmoCrmIdDialog.on('click', 'span.goToAmo', function() {
		var thisDialog = editAmoCrmIdDialog,
			input = thisDialog.find('#amoCrmId');

		if (input.val().length > 0) {
			var url = 'https://upakme.amocrm.ru/leads/detail/' + input.val();
			window.open(url, '_blank');
		}

	});


	/** КНОПКА отменить начисление % **/
	$('#queryList tbody').on('click', '.actionCancelPercentage', function() {
		var button = $(this),
			isActive = button.hasClass('active'),
			i = button.find('i'),
			tr = button.closest('tr'),
			queryId = tr.data('queryId');

		var sendData = {};
		sendData.action = 'CancelPercentage';
		sendData.queryId = queryId;
		sendData.value = isActive ? 0 : 1;
        console.log(sendData)
		$.post('ajax.php', sendData, function(data) {

			if (data.status && data.status == 200) {
				var newClass = data.value == 1 ? button.addClass('active') : button.removeClass('active');
			} else {
				console.log(data);
			}
		}, 'json');


	});


	/** КНОПКА print_manager **/
	$('#queryList tbody').on('click', '.actionPrintManager', function() {
		var button = $(this),
			isActive = button.hasClass('active'),
			i = button.find('i'),
			tr = button.closest('tr'),
			queryId = tr.data('queryId');

		if (i.hasClass('fa-cog')) {
			return false;
		}

		i.removeClass('fa-user fa-user-o').addClass('fa-cog fa-spin');

		var sendData = {};
		sendData.action = 'setPrintManager';
		sendData.queryId = queryId;
		sendData.value = isActive ? 0 : 1;

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {

				i.removeClass('fa-cog fa-spin');

				var newClass = data.value == 1 ? 'fa-user' : 'fa-user-o';
				i.addClass(newClass);

				if (data.value == 1) {
					button.addClass('active');
					button.attr('title', button.data('offLabel'));
				} else {
					button.removeClass('active');
					button.attr('title', button.data('onLabel'));
				}
			} else {
				console.log(data);
			}
		}, 'json');


	});



    	/** КНОПКА отгружено **/
	$('#queryList tbody').on('click', '.actionShipped', function() {
		var button = $(this),
			isActive = button.hasClass('actionShippedGreen'),
			i = button.find('i'),
			tr = button.closest('tr'),
			queryId = tr.data('queryId');

		var sendData = {};
		sendData.action = 'ActionShipped';
		sendData.queryId = queryId;
		sendData.value = isActive ? 0 : 1;
         console.log(sendData)
		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				var newClass = data.value == 1 ? button.addClass('actionShippedGreen').removeClass('actionShippedRed') : button.removeClass('actionShippedGreen');
			} else {
				console.log(data);
			}
		}, 'json');


	});





	/** КНОПКА summaryButton - ИТОГ ВЫБОРКИ **/
	$('#queryList tfoot').on('click', '.summaryButton', function() {
		var button = $(this),
			i = button.find('i'),
			tr = button.closest('tr');

		if (i.hasClass('fa-cog')) {
			return false;
		}

		if ($.running()) {
			return false;
		}

		i.removeClass('fa-calculator').addClass('fa-cog fa-spin');

		$.setRunning(true);

		var sendData = {};
		sendData.action = 'getQueriesSummary';

		var currentState = $.getCurrentState();
		if (currentState.filter != undefined && !$.isEmptyObject(currentState.filter)) {
			sendData.filter = currentState.filter;
		}

		$.ajax({
			url: 'ajax.php',
			data: sendData,
			dataType: 'json',
			type: 'POST',
			async: false,
			beforeSend: function() {},
			success: function (data) {
				console.log(data);

				if (data.status && data.status == 200) {
					tr.find('.col-count').html('<span>' + data.summary.count + ' шт.</span>');
					tr.find('.col-amount').html('<span>' + data.summary.amount + '</span>');
					tr.find('.col-payments').html('<span>' + data.summary.paid + '</span>');
					tr.find('.col-debt').html('<span>' + data.summary.debt + '</span>');

					button.remove();
				}

				$.setRunning(false);

			}
		});
	});



	/** КОЛОНКА "Номер накладной" **/
	$('#queryList tbody').on('change', '.col-accNumber input[type=text]', function(e) {
		var input = $(this),
			td = input.closest('td'),
			queryRow = td.closest('tr');

		if (!td.hasClass('load')) {
			var queryId = queryRow.data('queryId');

			td.addClass('load');
			queryRow.addClass('pointed');

			var sendData = {};
			sendData.action = 'saveAccNumber';
			sendData.queryId = queryId;
			sendData.number = input.val();
			$.post('ajax.php', sendData, function(data) {
				if (data.status && data.status == 200) {
					input.val(data.number);

					var notification = $('<div class="notificationItem success saveAccNumberSuccess">Номер счета для заказа <b>' + queryId + '</b> успешно сохранен!</div>');
					$('#notificationBox').append(notification);
					notification.fadeIn(300, function() {
						setTimeout(function() {
							notification.fadeOut(300, function() {
								notification.remove();
							})
						}, 2500);
					});
				} else {
					console.log(data);
				}

				td.removeClass('load');
				queryRow.removeClass('pointed');
			});
		}
	});

	$('#queryList tbody').on('click', '.col-accNumber .saveAccNumberButton i', function() {
		var button = $(this),
			td = button.closest('td'),
			queryRow = td.closest('tr'),
			loader = td.find('.saveAccNumberLoader'),
			input = td.find('input');

		var queryId = queryRow.data('queryId');

		button.hide();
		loader.show();
		queryRow.addClass('pointed');

		var sendData = {};
		sendData.action = 'saveAccNumber';
		sendData.queryId = queryId;
		sendData.number = input.val();

		$.post('ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				input.addClass('success');
				input.val(data.number);
				setTimeout(function() {
					input.removeClass('success');
				}, 2000);
			} else {
				console.log(data);
			}

			button.show();
			loader.hide();
			queryRow.removeClass('pointed');
		});
	});



	/** ВСПЛЫВАЮЩИЕ ПОДСКАЗКИ **/
	$(document).tooltip({
		items: '#queryList .showOrderItems, #queryList [title], body [data-tooltip]',
		content: function() {
			// console.log($(this));
			if ($(this).hasClass('showOrderItems')) {
				var elem = $(this).next('.itemsTooltip').clone();
				return elem.get(0);
			} else {
				if ($(this).is('[data-tooltip]')) {
					return '<div class="titleTooltip">' + $(this).data('tooltip') + '</div>';
				} else {
					return '<div class="titleTooltip">' + $(this).attr('title') + '</div>';
				}

			}
		},
		track: true,
		close: function () {
			$(".ui-helper-hidden-accessible > *:not(:last)").remove();
		},
		tooltipClass: 'paketoffTooltip',
		show: 200,
		position: {
			my: 'left+20 top+15',
			at: 'left bottom',
			collision: 'flipfit'
		}
	});


	/** FILTERS **/
	var filterBox = $('#filters');
		filters = filterBox.find('.filter');

	// При клике на фильтр-селект - открыть\закрыть всплывающие опшены
	filterBox.on('click', '.filterType_select .filter__button', function() {
		var button = $(this),
			filter = button.closest('.filter'),
			filters = filter.siblings('.filterType_select'),
			optionsBox = filterBox.find('.filter__options');

		if (!optionsBox.hasClass('opened')) {
			filters.removeClass('opened');
		}

		filter.toggleClass('opened');
	});

	// При клике на любое место страницы - закрываем открытые всплывающие окна
	$(document).on('click', function(e) {
		if ($(e.target).closest('.filterType_select').length == 0) {
			filterBox.find('.filterType_select').filter('.opened').removeClass('opened');
		}
		if ($(e.target).closest('#perPage_selector').length == 0) {
			filterBox.find('#perPage_selector').removeClass('opened');
		}
	});

	// При клике на значение фильтра-селекта - выставляем активный фильтр и значение или наоборот
	filterBox.on('click', '.filterType_select .filter__option', function() {
		if ($.running()) return false;

		var option = $(this),
			options = option.siblings('.filter__option'),
			filter = option.closest('.filter'),
			filterLabel = filter.find('.filter__button_label'),
			filterId = filter.data('filterId'),
			optionsBox = filterBox.find('.filter__options');

		if (!option.hasClass('active')) {
			// спец фильтры могут сочетаться с фильтрами период, менеджер и долг
			// в остальных случаях спец фильтры убираем
			if (filterId !== 'manager' && filterId !== 'debt') {
				$.resetSpecialFilters();
			}

			if (options.filter('.active').length == 0) {
				filter.addClass('active');
			} else {
				options.filter('.active').removeClass('active');
			}

			option.addClass('active');

			filter.data('value', option.data('value'));
			filterLabel.text(option.text());

			filter.removeClass('opened');

			$.setFilter({
				filterId: filterId,
				filterValue: option.data('value')
			});

			$.runAjax('filter');
		}
	});

	// Сброс фильтра-селекта
	filterBox.on('click', '.filterType_select .filter__button_reset', function(e) {
		e.stopPropagation();

		if ($.running()) return false;

		var resetButton = $(this),
			arrow = resetButton.prev(),
			filter = resetButton.closest('.filter'),
			filterLabel = filter.find('.filter__button_label'),
			options = filter.find('.filter__option'),
			options1 = filter.find('.filter__option_multi');

		filter.removeClass('active');
		options.filter('.active').removeClass('active');
		options1.filter('.active').removeClass('active');
		filterLabel.text(filterLabel.data('placeholder'));
		filter.removeClass('opened');

		$.removeFilter(filter.data('filterId'));
		$.runAjax('filter');
	});

	// Фильтр ПЕРИОДА
	// При клике на как "с" или "по" - открываем календарь
	var filter_period = $('#filter_period'),
		filter_periodInputs = filter_period.find('input[type=text]'),
		filter_periodFrom = filter_period.find('.periodRange_from input'),
		filter_periodTo = filter_period.find('.periodRange_to input');

	filter_periodInputs.datepicker({
		beforeShow: function() {
			if ($.running()) return false;
		},
		onSelect: function(dateText, instance) {
			var input = $(this),
				rangeBox = input.parent(),
				anotherBox = rangeBox.siblings('.periodRange'),
				anotherInput = anotherBox.find('input'),
				filter = rangeBox.closest('.filter'),
				placeholder = input.data('placeholder');

			rangeBox.addClass('active');

			if (anotherBox.hasClass('active')) {
				if (!filter.hasClass('active')) {
					filter.addClass('active');
				}
			}

			if (rangeBox.is('#periodRange_from')) {
				anotherInput.datepicker('option', 'minDate', dateText);
				if (!anotherBox.hasClass('active')) {
					anotherInput.val(placeholder);
				}

				$.setFilter({filterId: 'from', filterValue: dateText});
			} else {
				anotherInput.datepicker('option', 'maxDate', dateText);
				if (!anotherBox.hasClass('active')) {
					anotherInput.val(placeholder);
				}

				$.setFilter({filterId: 'to', filterValue: dateText});
			}

			$.runAjax('filter');
		}
	});

	// Фильтр Названия
	var filter_search = $('#filter_search'),
		filter_searchInput = filter_search.find('input[type=text]');

	filterBox.on('keydown', '.filterType_input input[type=text]', function() {
		// if ($.running()) return false;
	});

	filterBox.on('keyup change', '.filterType_input input[type=text]', function(e) {
		var input = $(this),
			filter = input.closest('.filter');

		if ((e.type == 'keyup' && e.keyCode == 13) || e.type == 'change') {
			// убрать специальные фильтры
			$.resetSpecialFilters();

			if (input.val().length >= 2) {
				filter.addClass('active');
				$.cancelPrevAjaxRequest();
				$.setFilter({filterId: 'search', filterValue: input.val()});
				$.runAjax('filter');
			} else if (input.val() == '') {
				filter.removeClass('active');
				$.cancelPrevAjaxRequest();
				$.removeFilter('search');
				$.runAjax('filter');
			}
		}
		//setTimeout(()=> window.location.href = window.location.href, 500);
	});

	// Фильтр-чекбокс
	filterBox.on('click', '.filterType_checkbox .filter__button', function() {
		if ($.running()) return false;

		var filter = $(this).closest('.filter');

		if (filter.hasClass('active')) {
			filter.removeClass('active');

			$.removeFilter(filter.data('filterId'));
		} else {
			filter.addClass('active');

			$.setFilter({
				filterId: filter.data('filterId'),
				filterValue: 1
			});
		}

		$.runAjax('filter');
	});
	//multi фильтр
	filterBox.on('click', '.filterType_select .filter__option_multi', function() {
		if ($.running()) return false;

		var option = $(this),
			options = option.siblings('.filter__option'),
			filter = option.closest('.filter'),
			filterLabel = filter.find('.filter__button_label'),
			filterId = filter.data('filterId'),
			optionsBox = filterBox.find('.filter__options');

		if (!option.hasClass('active')) {
			// спец фильтры могут сочетаться с фильтрами период, менеджер и долг
			// в остальных случаях спец фильтры убираем
			if (filterId !== 'manager' && filterId !== 'debt') {
				$.resetSpecialFilters();
			}

			if (options.filter('.active').length == 0) {
				filter.addClass('active');
			} else {
				options.filter('.active').removeClass('active');
			}

			option.addClass('active');

			filter.data('value', option.data('value'));
			$.setFilter({
				filterId: 'multi',
				filterValue: '1'
			});
			tek_zn=$.getFilterName(filterId);
			

			//filter.removeClass('opened');
			
			console.log("TEK"+tek_zn);
			if ( tek_zn!=undefined ){
				//добавляем
				tek_zn=tek_zn+","+option.data('value');
			}else{
				tek_zn=option.data('value');
			}
			$.setFilter({
				filterId: filterId,
				filterValue: tek_zn
			});
			if (String(tek_zn).indexOf(",")!==-1){
				var kol_zn=tek_zn.split(",");
			}else{
				var kol_zn=1;
			}
			console.log(kol_zn);
			if (kol_zn.length>1){
				filterLabel.text("Выбрано("+kol_zn.length+")");
			}else{
				filterLabel.text(option.text());
			}
			$.runAjax('filter');
		}else{
			option.removeClass('active');
			//filter.removeClass('opened');
			tek_zn=$.getFilterName(filterId).split(",");
			var new_zn="";
			for (let i=0;i<tek_zn.length;i=i+1){
				if (tek_zn[i]!=option.data('value')){
				new_zn=new_zn+""+tek_zn[i]+",";
				}
			}
			new_zn=new_zn.replace(/,\s*$/, "");
			$.setFilter({
				filterId: filterId,
				filterValue: new_zn
			});
			//
			var kol_zn=new_zn.split(",");
			console.log(kol_zn);
			if (kol_zn.length>1){
				filterLabel.text("Выбрано("+kol_zn.length+")");
			}else{
				//filterLabel.text(option.text());
				text_ost=$("#filter_"+filterId).find(".active").text();
				if (text_ost!=''){
					filterLabel.text(text_ost);
				}else{
					//filterLabel.text($("#filter_"+filterId).find(".filter__button_label").attr("data-placeholder"));
					$("#filter_"+filterId).find(".filter__button_reset").click();
				}
			}
			//
			$.runAjax('filter');
			console.log(new_zn);
		}
	});
	/** Специальные фильтры **/
	filterBox.on('click', '.specialFilter .filter__button', function() {
		if ($.running()) return false;

		var filter = $(this).closest('.specialFilter'),
			otherFilter = filter.siblings('.specialFilter'),
			filterValue = filter.data('filterId');

		// Если другой фильтр активен, деактивируем его
		if (otherFilter.hasClass('active')) {
			otherFilter.removeClass('active');
		}

		filter.addClass('active');
		$.setFilter({filterId: 'specialFilter', filterValue: filterValue});

		// Деактивируем простые фильтры, которые не сочетаются со специальными
		// это все, кроме фильтров менеджер, долг и период
		var currentState = $.getCurrentState();
		if (currentState.filter !== undefined) {
			$.each(currentState.filter, function(filter_id) {
				if (filter_id !== 'from' && filter_id !== 'to' && filter_id !== 'manager' && filter_id !== 'debt') {
					$.resetFilter(filter_id);
				}
			});
		}

		$.runAjax('filter');
	});

	/** Сортировка **/
	$('#queryList thead .sortFilter').on('click', 'span', function() {
		if ($.running()) return false;

		var span = $(this),
			filter = span.parent(),
			filterId = filter.data('filterId');

		if (filter.hasClass('active')) {
			// Если этот фильтр активен
			if (filter.hasClass('desc')) {
				// Если активен текущий фильтр с desc суфиксом - ставим asc суфикс (2 шаг)
				filter.removeClass('desc');
				$.setSort({field: filterId, value: 'asc'});

				$.runAjax('sort');
			} else {
				// Если активен текущий фильтр с asc суфиксом - удаляем сортировку (3 шаг)
				filter.removeClass('active');
				$.resetSort();

				$.runAjax('sort');
			}
		} else {
			// Если этот фильтр НЕ активен

			// Если есть активные сортировочные фильтры - удаляем их
			var currentSort = $.getSort();
			if (currentSort) {
				$('#sortFilter-' + currentSort).removeClass('desc active');
				$.resetSort();
			}

			// Выставляем текущую сортировку в DESC (шаг 1)
			filter.addClass('active desc');
			$.setSort({field: filterId, value: 'desc'});

			$.runAjax('sort');
		}
	});

	// perPage Селектор
	var perPageSelector = $('#perPage_selector');
	perPageSelector.on('click', '.perPage_selector__button', function() {
		var selector = $(this).parent(),
			optionsBox = selector.find('.perPage_selector__options');

		selector.toggleClass('opened');
	});

	// При выборе количества на страницу
	perPageSelector.on('click', '.perPage_selector__option', function() {
		if ($.running()) return false;

		var option = $(this),
			label = perPageSelector.find('.perPage_selector__button_label');

		if (option.hasClass('active')) {
			return false;
		}

		label.text(option.text());
		option.addClass('active');
		option.siblings('.active').removeClass('active');
		perPageSelector.removeClass('opened');

		$.runAjax('perPage', option.data('value'));
	});

	// Пагинации
	var paginationContainers = $('.paginationLinks');
	paginationContainers.on('click', 'a', function() {
		if ($.running()) return false;

		var link = $(this);

		if (link.hasClass('first')) {
			$.setPagination(1);
		} else {
			$.setPagination(link.data('page'));
		}

		$.runAjax('page');

		return false;
	});




	$.initState = function() {
		// Активную строку поиска
		if (filter_search.is('.active')) {
			$.setFilter({filterId: 'search', filterValue: filter_searchInput.val()});
		}

		// Активный период
		filter_periodInputs.closest('.periodRange').filter('.active').each(function(index, elem) {
			if ($(elem).is('#periodRange_from')) {
				$.setFilter({filterId: 'from', filterValue: $(elem).find('input[type=text]').val()});
			} else {
				$.setFilter({filterId: 'to', filterValue: $(elem).find('input[type=text]').val()});
			}
		});

		// Активные селекты
		filters.filter('.filterType_select.active').each(function(index, elem) {
			$.setFilter({filterId: $(elem).data('filterId'), filterValue: $(elem).data('filterValue')});
		});

		// Специальные фильтры
		filterBox.find('.specialFilter.active').each(function(index, elem) {
			$.setFilter({filterId: 'specialFilter', filterValue: $(elem).data('filterId')});
		});

		// Сортировка
		$('#queryList thead .sortFilter.active').each(function(index, elem) {
			$.setSort({field: $(elem).data('filterId'), value: $(elem).hasClass('desc') ? 'desc' : 'asc'});
		});
	}

	$.resetFilter = function(filterId) {
		if (filterId == 'from' || filterId == 'to') {
			var filter = $('#periodRange_' + filterId),
				input = filter.find('input[type=text]'),
				otherFilter = filter.siblings('.periodRange'),
				otherInput = otherFilter.find('input[type=text]'),
				filterBox = filter.closest('.filter');

			if (filter.hasClass('active') && otherFilter.hasClass('active')) {
				filterBox.removeClass('active');
				filter.removeClass('active');
				input.val(input.data('placeholder'));
				otherFilter.removeClass('active');
				otherInput.val(otherInput.data('placeholder'));
			} else if (filter.hasClass('active')) {
				filter.removeClass('active');
				input.val(input.data('placeholder'));
			} else if (otherFilter.hasClass('active')) {
				otherFilter.removeClass('active');
				otherInput.val(otherInput.data('placeholder'));
			}
		} else {
			var filter = $('#filter_' + filterId);

			if (filter.is('.filterType_select')) {
				var options = filter.find('.filter__option'),
					filterLabel = filter.find('.filter__button_label');

				options.filter('.active').removeClass('active');
				filterLabel.text(filterLabel.data('placeholder'));
				filter.removeClass('active');
				filter.removeClass('opened');
			} else if (filter.is('.filterType_input')) {
				var input = filter.find('input[type=text]');

				filter.removeClass('active');
				input.val('');
			}
		}
	}

	$.resetSpecialFilters = function() {
		var filters = $('.specialFilter.active');

		filters.removeClass('active');
		$.removeFilter('specialFilter');
	}
	
});
jQuery(function($){
	$(document).mouseup( function(e){ // событие клика по веб-документу
		var div = $( ".show_tip_delete" ); // тут указываем ID элемента
		if ( !div.is(e.target) // если клик был не по нашему блоку
		    && div.has(e.target).length === 0 && !$(".btn_tip_delete").is(e.target)) { // и не по его дочерним элементам
			div.removeClass("show_tip_delete"); // скрываем его
		}
	});
	});