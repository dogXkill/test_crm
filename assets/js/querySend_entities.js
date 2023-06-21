(function(){
	window.entities = {};
	
	var helpers = {
		generateId: function() {
			return Math.random().toString(36).substr(2, 9);
		}
	};
	
	// Строка продукта
	entities.orderItem = (function() {
		var _id, _bindedId;

		var template = '<tr class="entry entry-item">\
							<td class="col-nr" align="center"><span>-</span></td>\
							<td class="col-article"><input type="text" /></td>\
							<td class="col-info"></td>\
							<td class="col-sys"><input type="text" /></td>\
							<td class="col-count"><input type="text" /></td>\
							<td class="col-price"><input type="text" /></td>\
							<td class="col-sum" align="center"><span>-</span></td>\
							<td class="col-actions"><div></div></td>\
						</tr>';
		return {
			new: function() {
				_id = helpers.generateId();
				return this;
			},
			bind: function(id) {
				_bindedId = id;
			},
			getId: function() {
				return _id;
			},
			render: function() {
				var obj = $(template);
				
				obj.addClass('entry-' + _id);
				obj.attr('id', 'item-' + _id);
				obj.data('itemId', _id);
				obj.data('bindedId', _bindedId);
				
				var actionsBox = obj.find('.col-actions div');
				actionsBox.append(entities.addUpgradeButton.render());
				actionsBox.append(entities.removeButton.render());
				
				return obj;
			},
		}
	})();

	// Строка доставки
	entities.orderItemDelivery = (function() {
		var _id, _bindedId;

		var template = '<tr class="entry entry-delivery">\
							<td class="col-nr" align="center"><span>-</span></td>\
							<td class="col-article"></td>\
							<td class="col-info"></td>\
							<td class="col-sys"></td>\
							<td class="col-count"></td>\
							<td class="col-price"><input type="text" /></td>\
							<td class="col-sum" align="center"><span>-</span></td>\
							<td class="col-actions"><div></div></td>\
						</tr>';

		return {
			new: function() {
				_id = helpers.generateId();
				return this;
			},
			bind: function(id) {
				_bindedId = id;
			},
			getId: function() {
				return _id;
			},
			render: function() {
				var obj = $(template);
				
				obj.addClass('entry-' + _id);
				obj.attr('id', 'item-' + _id);
				obj.data('itemId', _id);
				obj.data('bindedId', _bindedId);
				obj.find('.col-sys').append(entities.deliverySelect.render());
				
				var actionsBox = obj.find('.col-actions div');
				actionsBox.append(entities.removeButton.render());
				
				return obj;
			},
		}
		
	})();
	
	// Строка апгрейда (подстрока продукта)
	entities.orderItemUpgrade = (function() {
		var _id, _parentId, _bindedId, _type;
		
		var template = '<tr class="sub-entry sub-entry-upgrade">\
							<td class="col-nr" align="center"><span><i class="fas fa-level-up-alt"></i></span></td>\
							<td class="col-article"></td>\
							<td class="col-info"></td>\
							<td class="col-sys"><div class="upgrade"><i class="fas fa-angle-double-up"></i><span></span></div></td>\
							<td class="col-count"><input type="text" /></td>\
							<td class="col-price"><input type="text" /></td>\
							<td class="col-sum" align="center"><span>-</span></td>\
							<td class="col-actions"><div></div></td>\
						</tr>';
		
		return {
			new: function() {
				_id = helpers.generateId();
				delete _parentId;
				return this;
			},
			setParentId: function(parentId) {
				_parentId = parentId;
				return this;
			},
			setType: function(type) {
				_type = type;
				return this;
			},
			bind: function(id) {
				_bindedId = id;
				return this;
			},
			getId: function() {
				return _id;
			},
			render: function() {
				var obj = $(template);
				
				obj.addClass('entry-' + _parentId);
				obj.attr('id', 'item-' + _id);
				obj.data('itemId', _id);
				obj.data('bindedId', _bindedId);
				obj.find('.col-sys span').text(appConfig.upgrades[_type]);
				
				var actionsBox = obj.find('.col-actions div');
				actionsBox.append(entities.removeButton.render());
				
				return obj;
			},
		}
	})();
	
	// Кнопка добавления апгрейда
	entities.addUpgradeButton = (function() {
		var cachedObj;
		var template = '<div class="btn-action actionAddUpgrade">\
							<div class="action-trigger"><i class="fas fa-angle-double-up"></i></div>\
							<div class="dropdown"></div>\
						</div>';
		
		return {
			render: function() {
				if (cachedObj == undefined) {
					cachedObj = $(template);

					var dropdown = cachedObj.find('.dropdown');
					
					$.each(appConfig.upgrades, function(index, upgrade) {
						dropdown.append('<div data-type="' + index + '">' + upgrade + '</div>');
					});
				}
				
				return cachedObj.prop('outerHTML');
			}
		}
	})();
	
	
	// Кнопка удаление строки
	entities.removeButton = (function() {
		var template = '<div class="btn-action actionRemoveItem"><i class="fas fa-times"></i></div>';
		
		return {
			render: function() {
				return template;
			}
		}
	})();
	
	// Селект доставки в строке доставки
	entities.deliverySelect = (function() {
		var cachedObj;
		var template = '<div>%label% <select></select></div>';
		
		return {
			render: function() {
				if (cachedObj == undefined) {
					cachedObj = $(template.replace('%label%', appConfig.delivery.label));

					var select = cachedObj.find('select');
					
					$.each(appConfig.delivery.items, function(index, text) {
						select.append('<option value="' + index + '">' + text + '</option>');
					});
				}
				
				return cachedObj.prop('outerHTML');
			}
		}
	})();
	
	// Селект подрядчиков
	
	
	/* СЕБЕСТОИМОСТЬ */
	
	// Строка себестоимости продукта
	entities.orderItemCost = (function() {
		var _id, _bindedId;

		var template = '<tr class="entry entry-item">\
							<td class="col-nr" align="center"><span>-</span></td>\
							<td class="col-podr"></td>\
							<td class="col-sys"><input type="text" /></td>\
							<td class="col-count"><input type="text" /></td>\
							<td class="col-price"><input type="text" /></td>\
							<td class="col-sum" align="center"><span>-</span></td>\
							<td class="col-actions"><div></div></span>\
							</td>\
						</tr>';
		return {
			new: function() {
				_id = helpers.generateId();
				return this;
			},
			bind: function(id) {
				_bindedId = id;
			},
			getId: function() {
				return _id;
			},
			render: function() {
				var obj = $(template);
				
				obj.addClass('entry-' + _id);
				obj.attr('id', 'item-' + _id);
				obj.data('itemId', _id);
				obj.data('bindedId', _bindedId);
				obj.find('.col-podr').append(entities.contractorSelect.render());
				
				var actionsBox = obj.find('.col-actions div');
				// actionsBox.append(entities.addUpgradeButton.render());
				actionsBox.append(entities.removeButton.render());
				
				return obj;
			},
		}
	})();
	
	// Строка доставки
	entities.orderItemCostDelivery = (function() {
		var _id, _bindedId;

		var template = '<tr class="entry entry-delivery">\
							<td class="col-nr" align="center"><span>-</span></td>\
							<td class="col-podr"></td>\
							<td class="col-sys"></td>\
							<td class="col-count"></td>\
							<td class="col-price"><input type="text" /></td>\
							<td class="col-sum" align="center"><span>-</span></td>\
							<td class="col-actions"><div></div></span>\
							</td>\
						</tr>';

		return {
			new: function() {
				_id = helpers.generateId();
				return this;
			},
			bind: function(id) {
				_bindedId = id;
			},
			getId: function() {
				return _id;
			},
			render: function() {
				var obj = $(template);
				
				obj.addClass('entry-' + _id);
				obj.attr('id', 'item-' + _id);
				obj.data('itemId', _id);
				obj.data('bindedId', _bindedId);
				obj.find('.col-podr').append(entities.contractorSelect.render());
				obj.find('.col-sys').append(entities.deliverySelect.render());
				
				var actionsBox = obj.find('.col-actions div');
				actionsBox.append(entities.removeButton.render());
				
				return obj;
			},
		}
		
	})();
	
	
	// Строка апгрейда (подстрока продукта)
	entities.orderItemCostUpgrade = (function() {
		var _id, _parentId, _type, _bindedId;
		
		var template = '<tr class="sub-entry sub-entry-upgrade">\
							<td class="col-nr" align="center"><span><i class="fas fa-level-up-alt"></i></span></td>\
							<td class="col-podr"></td>\
							<td class="col-sys"><div class="upgrade"><i class="fas fa-angle-double-up"></i><span></span></div></td>\
							<td class="col-count"><input type="text" /></td>\
							<td class="col-price"><input type="text" /></td>\
							<td class="col-sum" align="center"><span>-</span></td>\
							<td class="col-actions"><div></div></td>\
						</tr>';
		
		return {
			new: function() {
				_id = helpers.generateId();
				delete _parentId;
				return this;
			},
			setParentId: function(parentId) {
				_parentId = parentId;
				return this;
			},
			setType: function(type) {
				_type = type;
				return this;
			},
			bind: function(id) {
				_bindedId = id;
				return this;
			},
			getId: function() {
				return _id;
			},
			render: function() {
				var obj = $(template);
				
				obj.addClass('entry-' + _parentId);
				obj.attr('id', 'item-' + _id);
				obj.data('itemId', _id);
				
				if (_bindedId != undefined) {
					obj.data('bindedId', _bindedId);
				}
				
				obj.find('.col-sys span').text(appConfig.upgrades[_type]);
				obj.find('.col-podr').append(entities.contractorSelect.render());
				
				var actionsBox = obj.find('.col-actions div');
				actionsBox.append(entities.removeButton.render());
				
				return obj;
			},
		}
	})();
	
	
	
	
	// Селект подрядчиков
	entities.contractorSelect = (function() {
		var cachedObj;
		var template = '<select></select>';
		
		return {
			render: function() {
				if (cachedObj == undefined) {
					select = $(template);
					select.append('<option value="0"></option>');
					
					$.each(appConfig.contractors, function(index, text) {
						select.append('<option value="' + index + '">' + text + '</option>');
					});
					
					cachedObj = select;
				}
				
				return cachedObj.prop('outerHTML');
			}
		}
	})();
	
	
	
})(jQuery)