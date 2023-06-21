// JavaScript Document


// координаты мыши
var xpos=0;
var ypos=0;
var txpos = 0;
var typos = 0;

var type_opl = 0;			// тип редактирования текущей оплаты 0 - оплата заявки, 1 - оплата менеджеру

var arr_cost_tmp = new Array();			// временный массив хранения оплат

var num_opl;						// номер строки полей оплаты

var curr_cost_id = 0;		// текущий ид запроса, при редактировании полей оплаты

var arr_change = new Array();		// массив измененных значений

//var cr_id = 0; 	// ид строки запроса



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



// обновление страницы с выбранным фильтром
function SetFiltrNum() {
	var fltr_name = 	document.getElementById("inp_fltr_num_name").value;
	var fltr_case = 	document.getElementById("sel_fltr_num_case").value;
	var fltr_val 	= 	replace_price(document.getElementById("inp_fltr_num_val").value);

	document.location = ("stat_table_query.php?filtr="+fltr_name+'&case='+fltr_case+'&val='+fltr_val);

}



// обновление страницы с фильтром клиента
function SetFiltrClient() {
//	var fltr_name = 	document.getElementById("inp_fltr_num_name").value;
//	var fltr_case = 	document.getElementById("sel_fltr_num_case").value;
	var fltr_val 	= 	document.getElementById("sel_filtr_client").value;

	document.location = ("stat_table_query.php?filtr=client&case=client&val="+fltr_val);

}


// обновление страницы с фильтром клиента
function SetFiltrDate() {
//	var fltr_name = 	document.getElementById("inp_fltr_num_name").value;
//	var fltr_case = 	document.getElementById("sel_fltr_num_case").value;
	var fltr_val1 	=  document.getElementById("sel_filtr_dat1").value;
	var fltr_val2 	=  document.getElementById("sel_filtr_dat2").value;

	document.location = ("stat_table_query.php?filtr=dat&case="+fltr_val1+"&val="+fltr_val2);

}



// обновление страницы с фильтром клиента
function SetFiltrManager() {
	var fltr_val 	= 	document.getElementById("sel_filtr_man").value;

	document.location = ("stat_table_query.php?filtr=manager&case=manager&val="+fltr_val);

}




// Показать окно фильтра по цифре
function ShowFiltrNum(fltr) {

	curr_filtr = fltr;
	switch(curr_filtr) {


		case 'dat':
		title='дате';
		break;

		case 'manager':
		title='менеджеру';
		break;

	}

	title = "Фильтр по " + title;
	txpos = xpos;
	typos = ypos;

	hideAllDiv();		// скрыть все слои фильтров
	flg = 1;


	if( curr_filtr == 'dat' ) {
		document.getElementById('div_fltr_date').style.top = (typos+5)+'px';
		document.getElementById('div_fltr_date').style.left = (txpos)+'px';
		document.getElementById('div_fltr_date').style.display = 'block';
		flg = 0;
	}

	if( curr_filtr == 'manager' ) {
		document.getElementById('div_fltr_man').style.top = (typos+5)+'px';
		document.getElementById('div_fltr_man').style.left = (txpos-180)+'px';
		document.getElementById('div_fltr_man').style.display = 'block';
		flg = 0;
	}

if(flg==1) {

		document.getElementById("div_fltr_num_tit").innerHTML = title;
		document.getElementById('div_fltr_num').style.top = (typos+5)+'px';
		document.getElementById('div_fltr_num').style.left = (txpos)+'px';
		document.getElementById('div_fltr_num').style.display = 'block';
		document.getElementById('inp_fltr_num_name').value = curr_filtr;


	}
}

// скрывает все слои фильтров
function hideAllDiv() {
    $("#div_fltr_num").hide();
    $("#div_fltr_date").hide();
    $("#div_fltr_man").hide();

}






// >>>>>>>>>>>>>#################------ ПОЛЯ ОПЛАТЫ КОМИССИОННЫХ МЕНЕДЖЕРУ -----------##########################







// ################################   ПОЛЯ ТАБЛИЦЫ    ###########################

// добавление в массив нового измененного поля таблицы
function setValTab(id,feld,val) {
	if(typeof(arr_change[id]) == 'undefined')
	arr_change[id] = new Array();
    arr_change[id][feld] = val;
}

// разблокирование кнопки "сохранить изменения"
function enableSaveButt() {
	document.getElementById('SaveButt').disabled=false;
}

// сохранение всех измененных полей таблицы в базе данных
function SaveTabAllData() {
    var req_chn = new JsHttpRequest();
    req_chn.onreadystatechange = function() {
        if (req_chn.readyState == 4) {
						str = req_chn.responseJS.str;				// массив возвращенных значений
						document.getElementById('SaveButt').disabled=true;
						document.location = "stat_table_query.php";
				}
    }
    req_chn.open(null, '../backend/back_SaveTabStatAll.php', true);
    req_chn.send( { arr:arr_change } );
}


function tech_analis(){
var counter = 0
$(".ids").each(function () {

tek_id = $(this).val();

//проверяем только если игнор не включен
if($("#ignoreerror_"+tek_id).is(":not(:checked)")){
 // console.log(tek_id);



//тип заказа
typ_ord = $("#typ_ord_"+tek_id).val()

//сумма счета
prdm_sum_acc = $("#prdm_sum_acc_"+tek_id).val()*1;

//текущий долг
dolg = $("#dolg_"+tek_id).val()*1;
//маржа
marja = $("#marja_"+tek_id).val()*1;
//себестоимость
podr_sebist = $("#podr_sebist_"+tek_id).val();
//коэф маржи
marja_proc =  100 - (podr_sebist * 100 / prdm_sum_acc);
marja_proc = marja_proc.toFixed(0);
//маржа больше чем заданный коэфициэнт
//средняя маржа



if(typ_ord == "1"){sred_marja = $("#sred_marja_order").val();

//alert(prdm_sum_acc/podr_sebist)
if(sred_marja < marja_proc){
    $("#td_marja_"+tek_id).css({'background-color':'#FF3333'});
    counter = counter + 1;
    $("#span_marja_"+tek_id).html(marja_proc);
    }}





//по проекту имеется долг клиента
if(dolg > 0){$("#td_dolg_"+tek_id).css({'background-color':'#FF3333'}); counter = counter + 1;}
 }
});

alert("Найдено "+counter+" потенциальных ошибок")
sred_marja = "";


}




function set_ignoreerror(uid){

if($("#ignoreerror_"+uid).is(":not(:checked)")){ignore = '0';}else{ignore = '1';}

if(ignore !== ''){
$.post( "../backend/set_ignoreerror.php?uid="+uid+"&ignore="+ignore, function( data ) {
  console.log("ok "+ data)
});

}
}

function highlt_zero(){

$(".ids").each(function () {

tek_id = $(this).val();
proc = $("#proc_"+tek_id).val();

if(proc == "0"){
$("#proc_td_"+tek_id).css({'background-color':'#FF3333'})
}


});


}


function show_hide_settings(type){

$("#"+type).toggle();

}



function insert_percent(type){
if (confirm("Проставить проценты автоматически?")){


//заказы
proc_1 = $("#"+type+"_1").val()
//магазин
proc_2 = $("#"+type+"_2").val()
//магазин с лого
proc_3 = $("#"+type+"_3").val()

if(proc_1 == "" || proc_2 == "" || proc_3 == ""){alert("Одно из полей с % пустое")} else{


var array = new Array();
$(".ids").each(function () {
    tek_id = $(this).val();
    typ_ord = $("#typ_ord_"+tek_id).val()

   if(typ_ord == "1"){$("#proc_"+tek_id).val(proc_1); tek_proc = proc_1;}
   if(typ_ord == "2"){$("#proc_"+tek_id).val(proc_2); tek_proc = proc_2;}
   if(typ_ord == "3"){$("#proc_"+tek_id).val(proc_3); tek_proc = proc_3;}

   setValTab(tek_id,"percent",tek_proc);

 });



}}}




 function checkElements(classname) {
  $("input[type='checkbox']").each(function() {
    if ($(this).hasClass(classname)) {

    if($(this).prop("checked") == false){
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
    }
    }
  });
}

function ms_query_toggle(){ $("#ms_query").toggle();}
