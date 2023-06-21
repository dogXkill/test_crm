jQuery(document).ready(function($) {	
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
	});
	
	// При клике на значение фильтра-селекта - выставляем активный фильтр и значение или наоборот
	filterBox.on('click', '.filterType_select .filter__option', function() {
		if ($.running()) return false;
		
		var option = $(this),
			options = option.siblings('.filter__option'),
			filter = option.closest('.filter'),
			filterLabel = filter.find('.filter__button_label'),
			optionsBox = filterBox.find('.filter__options'),
			isMultiSelect = filter.hasClass('filterType_multiselect');
		
		if (!option.hasClass('active')) {
			if (isMultiSelect) {
				filter.addClass('active');
			} else {
				if (options.filter('.active').length == 0) {
					filter.addClass('active');
				} else {
					options.filter('.active').removeClass('active');
				}
			}
			
			option.addClass('active');
			
			var newValue;
			
			if (isMultiSelect) {
				var value = filter.data('filterValue');
				newValue = value ? value + ',' + option.data('value') : '' + option.data('value');
				filter.data('filterValue', newValue);
				filterLabel.text(filterLabel.data('placeholderActive') + newValue.split(',').length);
			} else {
				filter.data('filterValue', option.data('value'));
				filterLabel.text(option.text());
				newValue = option.data('value');
				
				filter.removeClass('opened');
			}
			
			$.setFilter({
				filterId: filter.data('filterId'),
				filterValue: newValue
			});
			
			$.runAjax('filter');
		} else {
			if (isMultiSelect) {
				option.removeClass('active');
				
				var newValue = filter.data('filterValue').split(',');
				var pos = newValue.indexOf(option.data('value').toString());
				
				newValue.splice(pos, 1);
				filter.data('filterValue', newValue.join(','));
				
				// Если еще есть активные
				if (newValue.length) {
					filterLabel.text(filterLabel.data('placeholderActive') + newValue.length);
				} else {
					filter.removeClass('active');
					filterLabel.text(filterLabel.data('placeholder'));
					filter.removeClass('opened');
					
					$.removeFilter(filter.data('filterId'));
				}
				
				$.runAjax('filter');
			}
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
			options = filter.find('.filter__option');
		
		filter.data('filterValue', '');
		filter.removeClass('active');
		options.filter('.active').removeClass('active');
		filterLabel.text(filterLabel.data('placeholder'));
		filter.removeClass('opened');
		
		$.removeFilter(filter.data('filterId'));
		$.runAjax('filter');
	});
	
	// ФильтрЫ ПЕРИОДА. Вешаем календарь на клики "с" и "по"
	filters.filter('.filterType_period').each(function(index, elem) {		
		$(elem).find('input[type=text]').datepicker({
			beforeShow: function(_input, inst) {
				if ($.running()) return false;
				
				if (inst.dpDiv.is(':empty')) {
					var input = $(_input),
						anotherInput = input.parent().siblings('.periodRange').find('input'),
						placeholder = input.data('placeholder');

					if (input.val() != '' && input.val() != placeholder) {
						if (input.parent().is('.periodRange_from')) {
							anotherInput.datepicker('option', 'minDate', input.val());
						} else {
							anotherInput.datepicker('option', 'maxDate', input.val());
						}
					}
					
					if (anotherInput.val() != '' && anotherInput.val() != placeholder) {
						if (anotherInput.parent().is('.periodRange_from')) {
							input.datepicker('option', 'minDate', anotherInput.val());
						} else {
							input.datepicker('option', 'maxDate', anotherInput.val());
						}
					}
					
					if (input.val() == '') input.val(placeholder);
					if (anotherInput.val() == '') anotherInput.val(placeholder);
				}
			},
			onSelect: function(dateText, instance) {
				var input = $(this),
					rangeBox = input.parent(),
					anotherBox = rangeBox.siblings('.periodRange'),
					anotherInput = anotherBox.find('input'),
					filter = rangeBox.closest('.filter'),
					filterId = filter.data('filterId'),
					placeholder = input.data('placeholder');
				
				rangeBox.addClass('active');
				
				if (anotherBox.hasClass('active')) {
					if (!filter.hasClass('active')) {
						filter.addClass('active');
					}
				}
				
				if (rangeBox.is('.periodRange_from')) {
					anotherInput.datepicker('option', 'minDate', dateText);
					if (!anotherBox.hasClass('active')) {
						anotherInput.val(placeholder);
					}
					
					$.setFilter({filterId: filterId + 'From', filterValue: dateText});
				} else {
					anotherInput.datepicker('option', 'maxDate', dateText);
					if (!anotherBox.hasClass('active')) {
						anotherInput.val(placeholder);
					}
					
					$.setFilter({filterId: filterId + 'To', filterValue: dateText});
				}
				
				$.runAjax('filter');
			}
		});
	});

	$('#resetFiltersButton').on('click', function() {
		if ($.running()) return false;
		
		var currentState = $.getCurrentState();
		if (currentState.filter != undefined) {
			$.each(currentState.filter, function(filter_id, filter_value) {
				$.resetFilter(filter_id);
			});
			$.removeFilters();
		}
		
		$.runAjax('filter');
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
		// Активные периоды
		filters.filter('.filterType_period').each(function(index, elem) {
			var filter = $(elem),
				filterId = filter.data('filterId');
			
			filter.find('.periodRange.active').each(function(index, elem) {
				if ($(elem).is('.periodRange_from')) {
					$.setFilter({filterId: filterId + 'From', filterValue: $(elem).find('input[type=text]').val()});
				} else {
					$.setFilter({filterId: filterId + 'To', filterValue: $(elem).find('input[type=text]').val()});
				}
			});
		});
		
		// Активные селекты
		filters.filter('.filterType_select.active').each(function(index, elem) {
			$.setFilter({filterId: $(elem).data('filterId'), filterValue: $(elem).data('filterValue')});
		});
		
		// console.log($.getCurrentState());
	}
	
	// Сброс (визуальный) определенного фильтра по ID
	$.resetFilter = function(filterId) {
		if (filterId.endsWith('From')) {
			// Обрезаем From
			filterId = filterId.slice(0, -4);
		} else if (filterId.endsWith('To')) {
			// Обрезаем To
			filterId = filterId.slice(0, -2);
		}
		
		var filter = $('#filter_' + filterId);
	
		if (filter.is('.filterType_select')) {
			var options = filter.find('.filter__option'),
				filterLabel = filter.find('.filter__button_label');
				
			options.filter('.active').removeClass('active');
			filterLabel.text(filterLabel.data('placeholder'));
			filter.removeClass('active');
			filter.removeClass('opened');
			filter.data('filterValue', '');
		} else if (filter.is('.filterType_input')) {
			var input = filter.find('input[type=text]');
			
			filter.removeClass('active');
			input.val('');
		} else if (filter.is('.filterType_checkbox')) {
			filter.removeClass('active');
		} else if (filter.is('.filterType_period')) {
			var inputs = filter.find('input[type=text]');
			
			filter.removeClass('active');
			filter.find('.periodRange').removeClass('active');
			
			inputs.each(function(index, input) {
				$(input).datepicker('option', {minDate: null, maxDate: null});
				$(input).val($(input).data('placeholder'));
			});
		} else if (filter.is('.filterType_num')) {
			filter.removeClass('active');
			filter.find('input[type=text]').val('');
		}
	}
	
});