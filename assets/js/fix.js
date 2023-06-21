jQuery(document).ready(function($) {
	
	var button = $('#getNextClient'),
		currentUid = $('#currentUid'),
		result = $('#resultContainer'),
		props = ['uid', 'short', 'name', 'legal_address', 'postal_address', 'cont_tel', 'firm_tel'];
	
	var workingArea = $('#workingArea');
	
	button.on('click', function() {
		// var currentUid = result.data('currentUid');
		
		var sendData = {};
		sendData.action = 'testGetNextClientWoNumber';
		sendData.uid = currentUid.val();
		
		result.hide();
		
		
		
		$.each(props, function(index, value) {
			$('#client_' + value).text('');
		});
		
		workingArea.find('input.new_phone').val('');
		workingArea.find('div.error').remove();
		
		$.post('/acc/query/ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				$.each(props, function(index, value) {
					$('#client_' + value).text(data.data[value]);
				});
				
				result.show();
				
				currentUid.val(data.data['uid']);
			}
			
		}, 'json');
	});
	
	
	
	workingArea.on('click', '.put_cont_tel', function() {
		var input = $(this).siblings('input[type=text]');
		
		input.val($('#client_cont_tel').text());
	});
	
	workingArea.on('click', '.put_firm_tel', function() {
		var input = $(this).siblings('input[type=text]');
		
		input.val($('#client_firm_tel').text());
	});
	
	workingArea.on('click', '.clearq', function() {
		var input = $(this).siblings('input[type=text]');
		var what = $(this).data('what');
		var value = input.val();
		
		var new_value = value;
		
		if (what == 'reset') {
			new_value = '';
		} else if (what == 'space') {
			new_value = value.replace(/\s/g, '');
		} else if (what == 'defis') {
			new_value = value.replace(/\-/g, '');
		} else if (what == 'dot') {
			new_value = value.replace(/[\.\,]/g, '');
		} else if (what == 'bracket') {
			new_value = value.replace(/[\(\)\[\]]/g, '');
		} else if (what == 'char') {
			new_value = value.replace(/[\p{L}]/giu, '');
		} else if (what == 'all') {
			new_value = value.replace(/\D/gi, '');
		}
		
		input.val(new_value);
	});
	
	workingArea.on('click', '.clear_spaces', function() {
		var input = $(this).siblings('input[type=text]');
		var value = input.val();
		
		input.val(value.replace(' ', ''));
	});
	
	$('#savePhones').on('click', function() {
		var _this = $(this);
		_this.siblings('div.error').remove();
		
		var sendData = {};
		sendData.action = 'testSavePhones';
		sendData.uid = currentUid.val();
		sendData.phones = [];
		
		workingArea.find('.new_phone').each(function(index, elem) {
			if ($(elem).val() != '') {
				sendData.phones.push($(elem).val());
			}
		});
		
		$.post('/acc/query/ajax.php', sendData, function(data) {
			if (data.status && data.status == 200) {
				$('#count').text(data.count);
				button.click();
			} else if (data.status && data.status == 499) {
				_this.after('<div class="error">' + data.message + '</div>');
			}
		}, 'json');
	});
	
});