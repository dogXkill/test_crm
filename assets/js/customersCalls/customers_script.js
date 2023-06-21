jQuery(document).ready(function ($) {
  /** DIALOGS **/
  var editCallsDialog = $('#editCallsDialog');
  // Создание окна звонков
  editCallsDialog.dialog({
    autoOpen: false,
    modal: true,
    width: 'auto',
    maxWidth: 600,
    draggable: false,
    resizable: false,
    position: { my: 'center center-100px', at: 'center', of: window },
    open: function (event, ui) {
      var thisDialog        = $(this),
          currentCustomerId = thisDialog.data('currentCustomerId'),
          dialogLoader      = thisDialog.find('.dialogLoader'),
          dialogContent     = thisDialog.find('.dialogContent');
      $.post('ajax.php', { action: 'customersCalls/getCallsDialog', customerId: currentCustomerId }, function (data) {
        if (data.status && data.status == 200) {
          // Подгрузили контент
          dialogContent.html(data.template_part);
          dialogLoader.hide();
          dialogContent.show();
          editCallsDialog.dialog('option', 'position', { my: 'center center-100px', at: 'center', of: window });
          // В контенте вешаем датапикер на нужные поля
          dialogContent.find('.inputDate').datetimepicker();
        } else {
          console.log(data);
        }
      }, 'json');
    },
    close: function (event, ui) {
      var thisDialog        = $(this),
          currentCustomerId = thisDialog.data('currentCustomerId'),
          dialogLoader      = thisDialog.find('.dialogLoader'),
          dialogContent     = thisDialog.find('.dialogContent');
      // После закрытия удалим поинтер со строки
      $('tr#customer_' + currentCustomerId).removeClass('pointed');
      dialogLoader.show();
      dialogContent.hide().html('');
    },
  });
  // Выбор предустановленной даты
  editCallsDialog.on('click', 'span.setDate', function () {
    var days  = $(this).data('days'),
        input = $(this).closest('.addCallBox').find('input.inputDate');
    var date = new Date();
    date.setDate(date.getDate() - days);
    input.datetimepicker('setDate', date);
  });
  // Добавление звонка
  editCallsDialog.on('click', '#addCallButton', function () {
    var thisDialog   = editCallsDialog,
        dialogLoader = thisDialog.find('.dialogLoader'),
        button       = $(this),
        form         = button.closest('.addCallBox').find('.addCallForm'),
        fields       = form.find('.addCallForm_field'),
        errorBox     = form.prev('.addCallForm_errors');
    dialogLoader.show();
    errorBox.hide().html('');
    form.find('.addCallForm_field').removeClass('hasError');
    var sendData = {};
    sendData.action = 'customersCalls/addCall';
    sendData.customerId = thisDialog.data('currentCustomerId');
    sendData.call = {};
    fields.each(function (index, elem) {
      var inputBox = $(elem),
          fieldId  = inputBox.data('fieldId');
      if (fieldId == 'date') {
        sendData.call['date'] = inputBox.find('input[type=text]').val();
      } else if (fieldId == 'result') {
        sendData.call['result'] = inputBox.find('select').val();
      } else if (fieldId == 'customResult') {
        sendData.call['customResult'] = inputBox.find('input[type=text]').val();
      } else if (fieldId == 'comment') {
        sendData.call['comment'] = inputBox.find('textarea').val();
      }
    });
    $.post('ajax.php', sendData, function (data) {
      if (data.status && data.status == 200) {
        var callsTable = thisDialog.find('table.callsList');
        callsTable.replaceWith(data.template_part);
        // Обнуляем форму добавления
        fields.each(function (index, elem) {
          var inputBox = $(elem),
              fieldId  = inputBox.data('fieldId');
          if (fieldId == 'date') {
            inputBox.find('input[type=text]').val('');
          } else if (fieldId == 'result') {
            inputBox.find('select').val('');
          } else if (fieldId == 'customResult') {
            inputBox.find('input[type=text]').val('');
          } else if (fieldId == 'comment') {
            inputBox.find('textarea').val('');
          }
        });
        // Меняем значение числа звонков в таблице клиентов
        var customerRow = $('#customer_' + thisDialog.data('currentCustomerId'));
        customerRow.find('.col-actions .actionViewCalls span').text(data.callsCount);
        customerRow.find('.col-lastCall > span').text(data.lastCall);
        dialogLoader.hide();
      } else if (data.status && data.status == 600) {
        $.each(data.errors, function (key, message) {
          errorBox.append('<div>- ' + message + '</div>');
          form.find('#addCallForm_field__' + key).addClass('hasError');
        });
        errorBox.show();
        dialogLoader.hide();
      } else {
        console.log(data);
      }
    }, 'json');
  });
  // Удаление звонка
  editCallsDialog.on('click', 'i.deleteCall', function () {
    var thisDialog   = editCallsDialog,
        dialogLoader = thisDialog.find('.dialogLoader'),
        button       = $(this),
        currentRow   = button.closest('tr');
    var customerId = thisDialog.data('currentCustomerId');
    var sendData = {};
    sendData.action = 'customersCalls/deleteCall';
    sendData.customerId = customerId,
      sendData.callId = currentRow.data('callId');
    dialogLoader.show();
    $.post('ajax.php', sendData, function (data) {
      if (data.status && data.status == 200) {
        var callsTable = thisDialog.find('table.callsList');
        callsTable.replaceWith(data.template_part);
        // Меняем значение числа звонков в таблице клиентов
        var customerRow = $('#customer_' + thisDialog.data('currentCustomerId'));
        customerRow.find('.col-actions .actionViewCalls span').text(data.callsCount);
        customerRow.find('.col-lastCall > span').text(data.lastCall);
        dialogLoader.hide();
      } else {
        console.log(data);
      }
    }, 'json');
  });
  // При выборе результата звонка Другой - открываем текстовое поле
  editCallsDialog.on('change', '#addCallForm_field__result select', function () {
    var select      = $(this),
        otherResult = select.closest('.addCallBox').find('#addCallForm_field__customResult'),
        value       = select.val();
    console.log(value);
    if (value == 1) {
      otherResult.addClass('showed');
    } else {
      otherResult.removeClass('showed');
    }
  });
  // При клике на кнопку в таблице, открываем окно со звонками
  $('#customersList tbody').on('click', '.actionViewCalls', function () {
    var button = $(this),
        tr     = button.closest('tr');
    tr.addClass('pointed');
    editCallsDialog.data('currentCustomerId', tr.data('customerId'));
    editCallsDialog.dialog('open');
  });
  /** TOOLTIPS **/
  $(document).tooltip({
    items: '#customersList .showItemsHandle',
    content: function () {
      if ($(this).hasClass('showItemsHandle')) {
        var elem = $(this).next('.itemsTooltip').clone();
        return elem.get(0);
      }
    },
    track: true,
    close: function () {
      $('.ui-helper-hidden-accessible > *:not(:last)').remove();
    },
    tooltipClass: 'paketoffTooltip',
    show: 200,
    position: {
      my: 'left+20 top+15',
      at: 'left bottom',
      collision: 'flipfit',
    },
  });
  /** FILTERS **/
  var filterBox = $('#filters');
  filters = filterBox.find('.filter');
  // При клике на фильтр-селект - открыть\закрыть всплывающие опшены
  filterBox.on('click', '.filterType_select .filter__button', function () {
    var button     = $(this),
        filter     = button.closest('.filter'),
        filters    = filter.siblings('.filterType_select'),
        optionsBox = filterBox.find('.filter__options');
    if (!optionsBox.hasClass('opened')) {
      filters.removeClass('opened');
    }
    filter.toggleClass('opened');
  });
  filterBox.on('click', '.filterType_select .filter__switchGroup', function () {
    if ($.running()) return false;
    var groupSwitcher = $(this),
        groups        = groupSwitcher.data('groups'),
        showGroup     = groupSwitcher.data('showGroup'),
        options       = groupSwitcher.siblings('.filter__option');
    options.removeClass('showed');
    options.filter('.group-' + showGroup).addClass('showed');
    var nextGroup = groups[showGroup].next;
    var buttonGroup = groups[nextGroup];
    groupSwitcher.data('showGroup', nextGroup);
    groupSwitcher.html(buttonGroup.label);
  });
  // При клике на любое место страницы - закрываем открытые всплывающие окна
  $(document).on('click', function (e) {
    if ($(e.target).closest('.filterType_select').length == 0) {
      filterBox.find('.filterType_select').filter('.opened').removeClass('opened');
    }
  });
  // При клике на значение фильтра-селекта - выставляем активный фильтр и значение или наоборот
  filterBox.on('click', '.filterType_select .filter__option', function () {
    if ($.running()) return false;
    var option      = $(this),
        options     = option.siblings('.filter__option'),
        filter      = option.closest('.filter'),
        filterLabel = filter.find('.filter__button_label'),
        optionsBox  = filterBox.find('.filter__options');
    if (!option.hasClass('active')) {
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
        filterId: filter.data('filterId'),
        filterValue: option.data('value'),
      });
      $.runAjax('filter');
    }
  });
  // Сброс фильтра-селекта
  filterBox.on('click', '.filterType_select .filter__button_reset', function (e) {
    e.stopPropagation();
    if ($.running()) return false;
    var resetButton = $(this),
        arrow       = resetButton.prev(),
        filter      = resetButton.closest('.filter'),
        filterLabel = filter.find('.filter__button_label'),
        options     = filter.find('.filter__option');
    filter.removeClass('active');
    options.filter('.active').removeClass('active');
    filterLabel.text(filterLabel.data('placeholder'));
    filter.removeClass('opened');
    $.removeFilter(filter.data('filterId'));
    $.runAjax('filter');
  });
  // ФильтрЫ ПЕРИОДА. Вешаем календарь на клики "с" и "по"
  filters.filter('.filterType_period').each(function (index, elem) {
    $(elem).find('input[type=text]').datepicker({
      beforeShow: function (_input, inst) {
        if ($.running()) return false;
        if (inst.dpDiv.is(':empty')) {
          var input        = $(_input),
              anotherInput = input.parent().siblings('.periodRange').find('input'),
              placeholder  = input.data('placeholder');
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
      onSelect: function (dateText, instance) {
        var input        = $(this),
            rangeBox     = input.parent(),
            anotherBox   = rangeBox.siblings('.periodRange'),
            anotherInput = anotherBox.find('input'),
            filter       = rangeBox.closest('.filter'),
            filterId     = filter.data('filterId'),
            placeholder  = input.data('placeholder');
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
          $.setFilter({ filterId: filterId + 'From', filterValue: dateText });
        } else {
          anotherInput.datepicker('option', 'maxDate', dateText);
          if (!anotherBox.hasClass('active')) {
            anotherInput.val(placeholder);
          }
          $.setFilter({ filterId: filterId + 'To', filterValue: dateText });
        }
        $.runAjax('filter');
      },
    });
  });
  // Фильтры-чекбоксы
  filters.filter('.filterType_checkbox').on('click', '.filter__button', function () {
    if ($.running()) return false;
    var filter   = $(this).closest('.filter'),
        filterId = filter.data('filterId');
    if (filter.hasClass('active')) {
      // Если текущий фильтр активен, деактивируем его
      filter.removeClass('active');
      $.removeFilter(filterId);
    } else {
      // Активируем текущий фильтр
      filter.addClass('active');
      $.setFilter({ filterId: filterId, filterValue: 1 });
    }
    $.runAjax('filter');
  });
  filters.filter('.filterType_num').on('change', 'input[type=text]', function (e) {
    if ($.running()) return false;
    var input    = $(this),
        filter   = input.closest('.filter'),
        filterId = filter.data('filterId'),
        value    = parseInt(input.val());
    if (isNaN(value)) {
      // Если неверное значение выставляем предыдущее без перезагрузки фильтра
      var filterValue = $.getFilter(filterId);
      if (filterValue != undefined) {
        input.val(filterValue);
      } else {
        input.val('');
      }
    } else {
      filter.addClass('active');
      $.setFilter({ filterId: filterId, filterValue: value });
      $.runAjax('filter');
    }
  });
  // Фильтр Названия
  var filter_search      = $('#filter_search'),
      filter_searchInput = filter_search.find('input[type=text]');
  filterBox.on('keyup change', '.filterType_input input[type=text]', function (e) {
    var input  = $(this),
        filter = input.closest('.filter');
    if ((e.type == 'keyup' && e.keyCode == 13) || e.type == 'change') {
      if (input.val().length >= 2) {
        filter.addClass('active');
        $.cancelPrevAjaxRequest();
        $.setFilter({
          filterId: 'search',
          filterValue: input.val(),
        });
        $.runAjax('filter');
      } else if (input.val() == '') {
        filter.removeClass('active');
        $.cancelPrevAjaxRequest();
        $.removeFilter('search');
        $.runAjax('filter');
      }
    }
  });
  $('#resetFiltersButton').on('click', function () {
    if ($.running()) return false;
    var currentState = $.getCurrentState();
    if (currentState.filter != undefined) {
      $.each(currentState.filter, function (filter_id, filter_value) {
        $.resetFilter(filter_id);
      });
      $.removeFilters();
    }
    $.runAjax('filter');
  });
  /** Сортировка **/
  $('#customersList thead .sortFilter').on('click', 'span', function () {
    if ($.running()) return false;
    var span     = $(this),
        filter   = span.parent(),
        filterId = filter.data('filterId');
    if (filter.hasClass('active')) {
      // Если этот фильтр активен
      if (filter.hasClass('desc')) {
        // Если активен текущий фильтр с desc суфиксом - ставим asc суфикс (2 шаг)
        filter.removeClass('desc');
        $.setSort({ field: filterId, value: 'asc' });
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
      $.setSort({ field: filterId, value: 'desc' });
      $.runAjax('sort');
    }
  });
  // Пагинации
  var paginationContainers = $('.paginationLinks');
  paginationContainers.on('click', 'a', function () {
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
  $.initState = function () {
    // Активную строку поиска
    if (filter_search.is('.active')) {
      $.setFilter({ filterId: 'search', filterValue: filter_searchInput.val() });
    }
    // Активные периоды
    filters.filter('.filterType_period').each(function (index, elem) {
      var filter   = $(elem),
          filterId = filter.data('filterId');
      filter.find('.periodRange.active').each(function (index, elem) {
        if ($(elem).is('.periodRange_from')) {
          $.setFilter({ filterId: filterId + 'From', filterValue: $(elem).find('input[type=text]').val() });
        } else {
          $.setFilter({ filterId: filterId + 'To', filterValue: $(elem).find('input[type=text]').val() });
        }
      });
    });
    // Активные селекты
    filters.filter('.filterType_select.active').each(function (index, elem) {
      $.setFilter({ filterId: $(elem).data('filterId'), filterValue: $(elem).data('filterValue') });
    });
    // Активные чекбоксы
    filters.filter('.filterType_checkbox.active').each(function (index, elem) {
      $.setFilter({ filterId: $(elem).data('filterId'), filterValue: 1 });
    });
    // Активные цифровые фильтры
    filters.filter('.filterType_num.active').each(function (index, elem) {
      $.setFilter({ filterId: $(elem).data('filterId'), filterValue: $(elem).find('input[type=text]').val() });
    });
    // Сортировка
    $('#queryList thead .sortFilter.active').each(function (index, elem) {
      $.setSort({ field: $(elem).data('filterId'), value: $(elem).hasClass('desc') ? 'desc' : 'asc' });
    });
    // console.log($.getCurrentState());
  };
  // Сброс (визуальный) определенного фильтра по ID
  $.resetFilter = function (filterId) {
    if (filterId.endsWith('From')) {
      // Обрезаем From
      filterId = filterId.slice(0, -4);
    } else if (filterId.endsWith('To')) {
      // Обрезаем To
      filterId = filterId.slice(0, -2);
    }
    var filter = $('#filter_' + filterId);
    if (filter.is('.filterType_select')) {
      var options     = filter.find('.filter__option'),
          filterLabel = filter.find('.filter__button_label');
      options.filter('.active').removeClass('active');
      filterLabel.text(filterLabel.data('placeholder'));
      filter.removeClass('active');
      filter.removeClass('opened');
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
      inputs.each(function (index, input) {
        $(input).datepicker('option', { minDate: null, maxDate: null });
        $(input).val($(input).data('placeholder'));
      });
    } else if (filter.is('.filterType_num')) {
      filter.removeClass('active');
      filter.find('input[type=text]').val('');
    }
  };
});
let customer = {
  telephoneChange: function (uid, object) {
    // проверка телефона
    if (!this.isValidPhone(object.value)) {
      $(object).css('border-color', 'red');
      return false;
    } else {
      $(object).css('border-color', '');
    }

    var data = {
      item: {
        uid: uid
      }
    }

    if ($(object).data('phone-type') === 'cont_tel') {
      data['item']['cont_tel'] = object.value;
    } else {
      data['item']['firm_tel'] = object.value;
    }

    this.action('update', data).then(d => {
      object.blur();
    }).catch(() => {
      alert('Произошла ошибка');
    });
    // console.log('result', result);
  },
  persEmail:function(uid,object){
	  var data = {
      item: {
        uid: uid,
		tip:'email'
      }
    }
	data['item']['email'] = object.value;
	  this.action('update', data).then(d => {
		  console.log(data);
      object.blur();
    }).catch(() => {
      alert('Произошла ошибка');
    });
  },
  persChange:function(uid,object){
	  
    var data = {
      item: {
        uid: uid,
		tip:'pers'
      }
    }
	data['item']['cont_pers'] = object.value;
	  this.action('update', data).then(d => {
      object.blur();
    }).catch(() => {
      alert('Произошла ошибка');
    });
  },
  action: function (action, data) {
    data = {
      module: 'customer',
      action: action,
      data: data,
    };
    return axios({
      method: 'post',
      url: '/crm/api/',
      data: data,
    }).then(d => {
      return d.data;
    });
  },
  isValidPhone: function (value) {
    return value.length >= 11;
  },
};
//jQuery('.cont_tel').mask('+79999999999');
//jQuery('.firm_tel').mask('+79999999999');
$('.firm_tel').inputmask({
        mask: "+79999999999",
        definitions: {
            'X': {
                validator: "9",
                placeholder: "9"
            }
        }
    });
	$('.cont_tel').inputmask({
        mask: "+79999999999",
        definitions: {
            'X': {
                validator: "9",
                placeholder: "9"
            }
        }
    });
$(".cont_email").inputmask("email", { onUnMask: function(maskedValue, unmaskedValue) {
    return unmaskedValue;
}});