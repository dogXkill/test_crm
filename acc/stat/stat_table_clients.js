// ------------------------------------------------------------------------------------------------
// ---------------------------------------- ПОСТАВЩИКИ --------------------------------------------
// ------------------------------------------------------------------------------------------------


// координаты мыши
var xpos=0;
var ypos=0;
var txpos = 0;
var typos = 0;

var arr_cost_tmp = new Array();			// временный массив хранения оплат

var num_opl;												// номер строки полей оплаты

var curr_cost_id = 0;								// текущий ид запроса, при редактировании полей оплаты

var arr_change = new Array();				// массив измененных значений




<!-- <<<<<<<<<<<< ******** ОПРЕДЕЛЕНИЕ КООРДИНАТ МЫШИ  *****************  //-->

function defPosition(event) {
      var x = y = 0;
      if (document.attachEvent != null) { // Internet Explorer & Opera
            x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
      }
      if (!document.attachEvent && document.addEventListener) { // Gecko
            x = event.clientX + window.scrollX;
            y = event.clientY + window.scrollY;
      }
      return {x:x, y:y};
}
// Простая проверка
// С помощью document.write выведем координаты прямо в окно браузера
// Они будут обновлять при движении мыши
document.onmousemove = function(event) {
     var event = event || window.event;
      xpos = defPosition(event).x;
			ypos = defPosition(event).y;
}

<!-- >>>>>>>>>>>> ******** ОПРЕДЕЛЕНИЕ КООРДИНАТ МЫШИ  *****************  //-->




<!-- <<<<<<<<<<<< ******** КАЛЕНДАРЬ ДЛЯ ЗАПОЛНЕНИЯ ПОЛЕЙ С ДАТОЙ  *****************  //-->

var oldLink = null;
// code to change the active stylesheet
function setActiveStyleSheet(link, title) {
  var i, a, main;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
      a.disabled = true;
      if(a.getAttribute("title") == title) a.disabled = false;
    }
  }
  if (oldLink) oldLink.style.fontWeight = 'normal';
  oldLink = link;
  link.style.fontWeight = 'bold';
  return false;
}

// This function gets called when the end-user clicks on some date.
function selected(cal, date) {
  cal.sel.value = date; // just update the date in the input field.
  if (cal.dateClicked && (cal.sel.id == "sel1" || cal.sel.id == "sel3"))
    // if we add this call we close the calendar on single-click.
    // just to exemplify both cases, we are using this only for the 1st
    // and the 3rd field, while 2nd and 4th will still require double-click.
    cal.callCloseHandler();
}

// And this gets called when the end-user clicks on the _selected_ date,
// or clicks on the "Close" button.  It just hides the calendar without
// destroying it.
function closeHandler(cal) {
  cal.hide();                        // hide the calendar
//  cal.destroy();
  _dynarch_popupCalendar = null;
}

// This function shows the calendar under the element having the given id.
// It takes care of catching "mousedown" signals on document and hiding the
// calendar if the click was outside.
function showCalendar(id, format, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    // we already have some calendar created
    _dynarch_popupCalendar.hide();                 // so we hide it first.
  } else {
    // first-time call, create the calendar.
    var cal = new Calendar(1, null, selected, closeHandler);
    // uncomment the following line to hide the week numbers
    // cal.weekNumbers = false;
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  // remember it in the global var
    cal.setRange(1900, 2070);        // min/max year allowed.
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    // set the specified date format
  _dynarch_popupCalendar.parseDate(el.value);      // try to parse the text in field
  _dynarch_popupCalendar.sel = el;                 // inform it what input field we use

  // the reference element that we pass to showAtElement is the button that
  // triggers the calendar.  In this example we align the calendar bottom-right
  // to the button.
  _dynarch_popupCalendar.showAtElement(el.nextSibling, "Br");        // show the calendar

  return false;
}

var MINUTE = 60 * 1000;
var HOUR = 60 * MINUTE;
var DAY = 24 * HOUR;
var WEEK = 7 * DAY;

// If this handler returns true then the "date" given as
// parameter will be disabled.  In this example we enable
// only days within a range of 10 days from the current
// date.
// You can use the functions date.getFullYear() -- returns the year
// as 4 digit number, date.getMonth() -- returns the month as 0..11,
// and date.getDate() -- returns the date of the month as 1..31, to
// make heavy calculations here.  However, beware that this function
// should be very fast, as it is called for each day in a month when
// the calendar is (re)constructed.
function isDisabled(date) {
  var today = new Date();
  return (Math.abs(date.getTime() - today.getTime()) / DAY) > 10;
}

function flatSelected(cal, date) {
  var el = document.getElementById("preview");
  el.innerHTML = date;
}

function showFlatCalendar() {
  var parent = document.getElementById("display");

  // construct a calendar giving only the "selected" handler.
  var cal = new Calendar(0, null, flatSelected);

  // hide week numbers
  cal.weekNumbers = false;

  // We want some dates to be disabled; see function isDisabled above
  cal.setDisabledHandler(isDisabled);
  cal.setDateFormat("%A, %B %e");

  // this call must be the last as it might use data initialized above; if
  // we specify a parent, as opposite to the "showCalendar" function above,
  // then we create a flat calendar -- not popup.  Hidden, though, but...
  cal.create(parent);

  // ... we can show it here.
  cal.show();
}
<!-- >>>>>>>>>>>> ******** КАЛЕНДАРЬ ДЛЯ ЗАПОЛНЕНИЯ ПОЛЕЙ С ДАТОЙ  *****************  //-->




// форматирование стоимости - при нажатии клавиши
function replace_price(v) {
	for(i=0;i<3;i++) {
		var reg_sp = /[^\d,\.]*/g;		// вырезание всех символов кроме цифр, запятой и точки
		v = v.replace(reg_sp, '');
		var reg_sp = /\.|,{2,}|\.{2,}|,\.|\.,/g; 	// вырезание подряд идущих запятых и точек
		v = v.replace(reg_sp, ',');
		var reg_sp = /^,|^\./g;				// если первый символ точка или запятая, заменяет на '0,'
		v = v.replace(reg_sp, '0,');
	}
	var reg_sp = /,(\s)/g;					// убрать пробелы после запятой
	v = v.replace(reg_sp, ",");

	v = fix_number(v);
	return v;
}



// Заменяет запятые на точки, для корректного подсчета суммы счета
function replace_zap(v) {
	var reg_sp = /,/g; 				// замена запятых на точки
	v = v.replace(reg_sp, '.');
	var reg_sp = /\s/g; 			// удаление пробелов
	v = v.replace(reg_sp, '');
	return v;
}



// форматирование числа для корректного подсчета и отображения
function fix_number(v) {
	v = replace_zap((''+v))*1;		// преобразование запятой в точку
	v = ''+(v).toFixed(8);			// округление до 2х цифр после запятой
//	var reg_sp = /^(\d*\.[1-9]*)0*/g;			// вырезание нулей после запятой
//	v = v.replace(reg_sp, '$1');
	var reg_sp = /0*$/g;			// вырезание нулей после запятой
	v = v.replace(reg_sp, '');
	var reg_sp = /^(\w*)\.$/g;			// убрать точку если после нее нет чисел
	v = v.replace(reg_sp, '$1');
	return v;
}




// Показать окно фильтра по цифре
function ShowFiltrNum(fltr) {

	curr_filtr = fltr;

	switch(curr_filtr) {
		case 'nm_acc':
		title='номеру счета';
		break;

		case 'num_prdm':
		title='количеству';
		break;

		case 'summ_prdm':
		title='сумме';
		break;

		case 'opl':
		title='сумме оплаты';
		break;

		case 'debt':
		title='размеру долга';
		break;

		case 'num_acc_prdm':
		title='номеру счета';
		break;

		case 'client':
		title='названию клиента';
		break;

		case 'podr':
		title='названию подрядчика';
		break;

		case 'spis':
		title='статусу долга';
		break;

	}

	title = "Фильтр по " + title;
	txpos = xpos;
	typos = ypos;

	hideAllDiv();		// скрыть все слои фильтров
	flg = 1;

	if( curr_filtr == 'client' ) {
		document.getElementById('div_fltr_client').style.top = (typos+5)+'px';
		document.getElementById('div_fltr_client').style.left = (txpos)+'px';
		document.getElementById('div_fltr_client').style.display = 'block';
		flg = 0;
	}

	if( curr_filtr == 'podr' ) {
		document.getElementById('div_fltr_podr').style.top = (typos+5)+'px';
		document.getElementById('div_fltr_podr').style.left = (txpos)+'px';
		document.getElementById('div_fltr_podr').style.display = 'block';
		flg = 0;
	}

	if( curr_filtr == 'spis' ) {
		document.getElementById('div_fltr_spis').style.top = (typos+5)+'px';
		document.getElementById('div_fltr_spis').style.left = (txpos)+'px';
		document.getElementById('div_fltr_spis').style.display = 'block';
		flg = 0;
	}

	if(flg==1) {

		document.getElementById("div_fltr_num_tit").innerHTML = title;
		document.getElementById('div_fltr_num').style.top = (typos+5)+'px';
		document.getElementById('div_fltr_num').style.left = (txpos)+'px';
		document.getElementById('div_fltr_num').style.display = 'block';
		document.getElementById('inp_fltr_num_name').value = curr_filtr;
	}
	// выбор фильтра долга "не равно 0"
	if( curr_filtr == 'debt') {		document.getElementById("sel_fltr_num_case").options[2].selected = true;
		document.getElementById("inp_fltr_num_val").value = 0;

	}
}



// обновление страницы с выбранным фильтром
function SetFiltrNum() {
	var fltr_name = 	document.getElementById("inp_fltr_num_name").value;
	var fltr_case = 	document.getElementById("sel_fltr_num_case").value;
	var fltr_val 	= 	replace_price(document.getElementById("inp_fltr_num_val").value);

	document.location = ("stat_table_clients.php?filtr="+fltr_name+'&case='+fltr_case+'&val='+fltr_val);

}

// обновление страницы с фильтром клиента
function SetFiltrClient() {
	var fltr_val 	= 	document.getElementById("sel_filtr_client").value;

	document.location = ("stat_table_clients.php?filtr=client&case=client&val="+fltr_val);

}

// обновление страницы с фильтром поставщика
function SetFiltrPodr() {
	var fltr_val 	= 	document.getElementById("sel_filtr_podr").value;

	document.location = ("stat_table_clients.php?filtr=podr&case=podr&val="+fltr_val);

}

// обновление страницы с фильтром списания долга
function SetFiltrSpis(val) {
	document.location = ("stat_table_clients.php?filtr=spis&case=spis&val="+val);
}



// разблокирование кнопки "сохранить изменения"
function enableSaveButt() {
	document.getElementById('SaveButt').disabled=false;
}


// добавление в массив нового измененного поля таблицы
function setValTab(id,feld,val) {
	if(typeof(arr_change[id]) == 'undefined')
		arr_change[id] = new Array();

	arr_change[id][feld] = val;
}


// сохранение всех измененных полей таблицы в базе данных
function SaveTabAllData() {
    var req_chn = new JsHttpRequest();
    req_chn.onreadystatechange = function() {
        if (req_chn.readyState == 4) {
						str = req_chn.responseJS.str;				// массив возвращенных значений
//						document.getElementById('debug').innerHTML = req_chn.responseText;
						document.getElementById('SaveButt').disabled=true;
						document.location = "stat_table_clients.php";
				}
    }
    req_chn.open(null, '../backend/back_SaveTabStatClientAll.php', true);
    req_chn.send( { arr:arr_change } );
}

// скрывает все слои фильтров
function hideAllDiv() {
	document.getElementById('div_fltr_num').style.display = 'none';
	document.getElementById('div_fltr_client').style.display = 'none';
	document.getElementById('div_fltr_podr').style.display = 'none';
	document.getElementById('div_fltr_spis').style.display = 'none';
//	document.getElementById('div_fltr_date').style.display = 'none';
//	document.getElementById('div_fltr_man').style.display = 'none';
}



// загрузка полей оплаты из базы данных
function LoadCostList(id) {
		type_opl = 0;		// тип оплаты - предмет счета
		curr_cost_id = id;
		txpos = xpos;
		typos = ypos;
    var req_cl = new JsHttpRequest();
    req_cl.onreadystatechange = function() {
        if (req_cl.readyState == 4) {
						arr_cost_tmp = req_cl.responseJS.arr;				// массив возвращенных значений
						show_add_cost();
//						document.getElementById('debug').innerHTML = req_cl.responseText;
//					init_all_feld();
//						alert(arr_all_load);
					}
    }
    req_cl.open(null, '../backend/back_LoadListCostContr.php', true);
    req_cl.send( { id:id } );
}


// отображение окна полей оплаты предмета счета
function show_add_cost() {
	num_opl = 1;
	document.getElementById('div_podr_opl').style.top = (typos-50)+'px';
	document.getElementById('div_podr_opl').style.left = (txpos-400)+'px';
	document.getElementById('div_podr_opl').style.display = 'block';
	init_feld_predm();
}



// при нажатии на ссылку оплаты - инициализация списка оплат в слой
function init_feld_predm() {
	write_feld_opl_null();

	if(typeof(arr_cost_tmp)!='undefined') {
		if(typeof(arr_cost_tmp[0])!='undefined') {
			for(i=0;i<(arr_cost_tmp.length);i++) {
				write_feld_opl(i,arr_cost_tmp[i]['summ'],arr_cost_tmp[i]['date'],arr_cost_tmp[i]['num_pp']);
			}
		}
	}
}




// стирание всех строк оплаты в слое и записсь первой пустой строки
function write_feld_opl_null() {

	for(i=0;i<=14;i++)
		document.getElementById('opl_feld'+i).innerHTML = '';
	str = '<table align="right" border="0" cellspacing="0" cellpadding="0" class="tab_podr_main"><tr>' +
	'<td><img src="../i/pix.gif" width="290" height="1" /></td>' +
	'<td><img src="../i/pix.gif" width="35" height="1" /></td></tr><tr><td align="center">--- пусто ---</td>';
	if(tpacc)
		str += '<td><div style="visibility:visible; display:inline; width:15px;"><input onmouseover="Tip(\'Добавить\')" class="butt_plus" onclick="write_feld_opl(0,0,curr_date,0); return false;" name="a" type="button" value="+" /></div></td>';

	str += '</tr></table>';
	document.getElementById('opl_feld0').innerHTML = str;
	document.getElementById('opl_feld0').style.display = 'block';
	num_opl--;
//	alert('1');
}




// добавление новой строки в список оплат
function write_feld_opl(num,summ,dat,numpp) {
	if((dat=='00.00.0000') || (dat=='0'))
		dat = curr_date;

	if(tpacc) {
		if(num > 0) {
			document.getElementById('opl_min'+num).style.visibility = 'hidden';
		}
		if(num>0)
			document.getElementById('opl_pl'+num).style.visibility = 'hidden';
	}
	document.getElementById('opl_feld'+num).style.display = 'block';

	dis = '';

	if(!tpacc)
		dis = ' disabled="disabled" ';

	str = '<table align="right" border="0" cellspacing="0" cellpadding="0" class="tab_podr_main"><tr>' +
	'<td><img src="../i/pix.gif" width="110" height="1" /></td>' +
	'<td><img src="../i/pix.gif" width="70" height="1" /></td>' +
	'<td><img src="../i/pix.gif" width="110" height="1" /></td>';
	if(tpacc)
		str += '<td><img src="../i/pix.gif" width="35" height="1" /></td>';
	str += '</tr>';
	if(num==0)
		str+= '<tr><td align="center">Сумма</td>'+
		'<td align="center">Дата</td>'+
		'<td align="center">Номер ПП</td>'+
		'<td align="center"></td></tr><tr>';

	str+= '<td align="right"><input ' + dis + ' name="opl_summ[]" onmouseover="Tip(\'Сумма оплаты\')" id="opl_summ_'+num_opl+'" onchange="this.value=replace_price(this.value)" type="text" class="new_podr" value="'+summ+'" /></td>' +

	'<td><input ' + dis +' onmouseover="Tip(\'Дата оплаты\')" name="opl_dat[]" type="text" id="opl_dat_'+num_opl+'" onclick="return showCalendar(\'opl_dat_'+num_opl+'\', \'%d.%m.%Y\');" size="9" readonly="1" value="'+dat+'" /><a></a></td>' +

	'<td><input ' + dis + ' name="opl_numpp[]" onmouseover="Tip(\'Номер платежного поручения\')" id="opl_numpp_'+num_opl+'" type="text" class="new_podr" value="'+numpp+'" /></td>';

	if(tpacc) {
		str += '<td align="left">';
		if(num<14) {
			str += '<div id=\'opl_pl'+(num+1)+'\' style="visibility:visible; display:inline; width:15px;"><input onmouseover="Tip(\'Добавить\')" class="butt_plus" onclick="write_feld_opl('+(num+1)+',0,0,0); return false;" name="a" type="button" value="+" /></div>';
		}
		if(num==0) {
			str += '<div id=\'opl_min'+(num+1)+'\' style="visibility:visible; display:inline; width=15px;"><input onmouseover="Tip(\'Удалить\')" class="butt_plus" onclick="write_feld_opl_null(); return false;" name="a" type="button" value="-" /></div>';
		}
		if(num>0) {
			str += '<div id=\'opl_min'+(num+1)+'\' style="visibility:visible; display:inline; width=15px;"><input onmouseover="Tip(\'Удалить\')" class="butt_plus" onclick="clear_feld_opl('+num+'); return false;" name="a" type="button" value="-" /></div>';
		}
		str += '</td>';
	}
	str += '</tr></table>';

	document.getElementById('opl_feld'+num).innerHTML = str;
	num_opl++;
}



//	скрытие последнего поля оплаты
function clear_feld_opl(num) {
	document.getElementById('opl_feld'+num).innerHTML='';
	document.getElementById('opl_feld'+num).style.display = 'none';
	document.getElementById('opl_pl'+num).style.visibility = 'visible';
	if(num>0)
		document.getElementById('opl_min'+num).style.visibility = 'visible';

	num_opl--;
//	kalk_summ_podr();
}



// инициализация полей оплаты предмета счета и оплаты менеджеру в массив для сохранения
function check_opl() {
			if(num_opl < 1) {
				arr_cost_tmp = new Array();
				sm = 0;
			}
			else {
				arr_cost_tmp = new Array();
				sm = 0;
				for(i=0;i<(num_opl);i++) {
					arr_cost_tmp[i] = new Array();
					arr_cost_tmp[i]['summ'] 	= document.getElementById('opl_summ_'+(i)).value;
					arr_cost_tmp[i]['date'] 	= document.getElementById('opl_dat_'+(i)).value;
					arr_cost_tmp[i]['num_pp'] = document.getElementById('opl_numpp_'+(i)).value;
					sm += replace_zap(arr_cost_tmp[i]['summ'])*1;
				}
			}
			sm = fix_number(sm);
			hide_div_opl();
			if(sm > 0)
				document.getElementById("opl_div_"+curr_cost_id).innerHTML = '<a href="#" class=stat_yes_alt><strong>'+sm+'</strong></a>';
			else
				document.getElementById("opl_div_"+curr_cost_id).innerHTML = '<a href="#" class=stat_no><strong>---<strong></a>';

			SaveCostList(sm);
}




// сохранение полей оплаты в базе данных
function SaveCostList(sm) {
    var req_cs = new JsHttpRequest();
    req_cs.onreadystatechange = function() {
        if (req_cs.readyState == 4) {
						str = req_cs.responseJS.str;				// массив возвращенных значений
//						document.getElementById('debug').innerHTML = req_cl.responseText;
						document.location = "stat_table_clients.php";
				}
    }
    req_cs.open(null, '../backend/back_SaveListCostContr.php', true);
    req_cs.send( { id:curr_cost_id, arr:arr_cost_tmp, sm:sm } );
}





// скрытие слоя оплат
function hide_div_opl() {
	document.getElementById('div_podr_opl').style.display = 'none';
}






/*   **************    ПЕРЕМЕЩЕНИЕ СЛОЕВ МЫШКОЙ   ******** <<<<<<<<<<    */

var flag=false;
var shift_x;
var shift_y;

function start_drag(itemToMove,e){
if(!e) e = window.event;
flag=true;
shift_x = e.clientX-parseInt(itemToMove.style.left);
shift_y = e.clientY-parseInt(itemToMove.style.top);

if(e.stopPropagation) e.stopPropagation();
else e.cancelBubble = true;
if(e.preventDefault) e.preventDefault();
else e.returnValue = false;
}

function end_drag(){ flag=false; }

function dragIt(itemToMove,e){
if(!flag) return;
if(!e) e = window.event;
itemToMove.style.left = (e.clientX-shift_x) + "px";
itemToMove.style.top = (e.clientY-shift_y) + "px";

if(e.stopPropagation) e.stopPropagation();
else e.cancelBubble = true;
if(e.preventDefault) e.preventDefault();
else e.returnValue = false;
}

/*   >>>>> *********    ПЕРЕМЕЩЕНИЕ СЛОЕВ МЫШКОЙ   ***********    */
