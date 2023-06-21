jQuery(document).ready(function($) {
	var xhr;
	var run;

	var currentState = {};
	var filterBox = $('#filters');
	var listBox = $('#listBox');
	
	$.runAjax = function(type, callback) {
		$.ajaxBegin();
		
		var sendData = {};
		sendData.action = 'customersCalls/getCustomers';
		
		if (type == 'page' && currentState.page != 1) {
			sendData.page = currentState.page;
		}
		
		if (currentState.filter != undefined && !$.isEmptyObject(currentState.filter)) {
			sendData.filter = currentState.filter;
		}
		
		if (currentState.sort != undefined) {
			sendData.sort = currentState.sort.field + '-' + currentState.sort.value;
		}
		
		// console.log(sendData);
		
		xhr = $.ajax({
			url: 'ajax.php',
			data: sendData,
			dataType: 'json',
			type: 'POST',
			beforeSend: function() {},
			success: function (data) {
				// console.log(data);
				if (data.status && data.status == 200) {
					$('#countingBox span').text(data.customersCount);
					$('.paginationLinks').html(data.paginationHtml);
					$('#customersList tbody').html(data.itemsHtml);
					
					// history.pushState(currentState, document.title, decodeURIComponent($.param.querystring(url.substr(0, url.indexOf('?')), params)));
					var state = data.link ? '?' + data.link : window.location.pathname;
					history.pushState(currentState, null, state);
					
					if (callback != undefined) {
						callback.call(this);
					}
				}

				$.ajaxEnd();
			}
		});
	};
	
	$.ajaxInProcess = function() {
		return xhr != undefined && xhr.readyState == 1 ? true : false;
	}
	
	$.ajaxBegin = function() {
		var loader = listBox.children('.loader'),
			list = listBox.find('table tbody');
			
		run = true;

		if (loader.length == 0) {
			listBox.append('<div class="loader"><div class="loader-bar"></div><div class="loader-bar"></div><div class="loader-bar"></div><div class="loader-bar"></div><div class="loader-bar"></div></div>');
		}
		
		list.hide();
	}
	
	$.ajaxEnd = function() {
		var loader = listBox.children('.loader'),
			list = listBox.find('table tbody');

		loader.remove();
		list.show();
		
		run = false;
	}
	
	$.running = function() {
		return run;
	}
	
	$.cancelPrevAjaxRequest = function() {
		if (xhr != undefined && xhr.readyState == 1) {
			xhr.abort();
		}
	}
	
	$.setFilter = function(obj) {
		if (currentState.filter == undefined) {
			currentState.filter = {};
		}
		
		if (obj.filterId == 'specialFilter') {
			currentState.filter['specialFilter'] = obj.filterValue;
		} else {
			currentState.filter[obj.filterId] = obj.filterValue;
		}
	}
	
	$.getFilter = function(filterId) {
		return currentState.filter[filterId];
	}
	
	$.removeFilter = function(filterId) {
		if (currentState.filter != undefined && currentState.filter[filterId] != undefined) {
			delete currentState.filter[filterId];
		}
	}
	
	$.removeFilters = function() {
		delete currentState.filter;
	}
	
	$.setPagination = function(page) {
		if (currentState.page != undefined && page == 1) {
			delete currentState.page;
		} else {
			currentState.page = page;
		}
	}
	
	$.getSort = function() {
		if (currentState.sort == undefined) {
			return false;
		}
		
		return currentState.sort.field;
	}
	
	$.setSort = function(obj) {
		currentState.sort = obj;
	}
	
	$.resetSort = function() {
		if (currentState.sort != undefined) {
			delete currentState.sort;
		}
	}
	
	$.getCurrentState = function() {
		return currentState;
	}
	
	$.initState();
});
