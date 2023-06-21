jQuery(document).ready(function($) {
	var xhr;
	var run;
	// var currentState = JSON.parse(currentSettings);
	var currentState = {};
	var filterBox = $('#filters');
	var listBox = $('#listBox');

	$.runAjax = function(type, arg) {
		$.ajaxBegin();

		var sendData = {};
		sendData.action = 'getQueries';

		if (type == 'page' && currentState.page != 1) {
			sendData.page = currentState.page;
		}

		if (type == 'perPage') {
			sendData.perPage = arg;
		}

		if (currentState.filter != undefined && !$.isEmptyObject(currentState.filter)) {
			sendData.filter = currentState.filter;
		}

		if (currentState.sort != undefined) {
			sendData.sort = currentState.sort.field + '-' + currentState.sort.value;
		}
	
		console.log(sendData);
		xhr = $.ajax({
			url: 'ajax.php',
			data: sendData,
			dataType: 'json',
			type: 'POST',
			beforeSend: function() {},
			success: function (data) {
				//console.log(data);
				if (data.status && data.status == 200) {
					console.log(data.ac_us);
					
					$('.paginationLinks').html(data.paginationHtml);
					$('.tip_delete_tables tbody').html(data.reasonHtml);
					if (data.paginationHtml == '') {
						$('#perPage_selector').hide();
					} else {
						$('#perPage_selector').show();
					}
					if (data.reasonHtml==null){
						$('.tip_delete_tables').hide();
					}else{
						$('.tip_delete_tables').show();
					}
					$('#queryList tbody').html(data.itemsHtml);
					$('#queryList tfoot').html(data.itemsSummaryHtml);
					if (data.ac_us==2){
						$(".izm_tip_delete").show();
					}else{
						$(".izm_tip_delete").hide();
					}
					// history.pushState(currentState, document.title, decodeURIComponent($.param.querystring(url.substr(0, url.indexOf('?')), params)));
					var state = data.link ? '?' + data.link : window.location.pathname;
					history.pushState(currentState, null, state);
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
			list = listBox.find('table tbody'),
			foot = listBox.find('table tfoot');

		run = true;

		if (loader.length == 0) {
			listBox.append('<div class="loader"><div class="loader-bar"></div><div class="loader-bar"></div><div class="loader-bar"></div><div class="loader-bar"></div><div class="loader-bar"></div></div>');
		}

		list.hide();
		foot.hide();
	}

	$.ajaxEnd = function() {
		var loader = listBox.children('.loader'),
			list = listBox.find('table tbody'),
			foot = listBox.find('table tfoot');

		loader.remove();
		list.show();
		foot.show();

		run = false;
	}

	$.running = function() {
		return run;
	}

	$.setRunning = function(state) {
		run = state ? true : false;
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
	$.getFilterName=function(name){
	if (currentState['filter'][name]!==undefined){return currentState['filter'][name];}else{$.setFilter(name); return currentState['filter'][name];}
	}
	$.initState();
});
