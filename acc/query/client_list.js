// JavaScript Document
<!--

// координаты мыши
var xpos=0;
var ypos=0;



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


// форматирование строки, удаляет в строке начальные и конечные пробелы
function replace_str(v) {
	var reg_sp = /^\s*|\s*$/g;
	v = v.replace(reg_sp, "");
	return v;
}

// проверка обязательных полей
function check() {
	// короткое название
	sh = replace_str(document.getElementById('short').value);
	if(sh == '') {
	  document.getElementById('short').focus();
		alert('Не заполнено поле "Короткое название"!');
		return false;
	}
	// полное наименование
	nm = replace_str(document.getElementById('name').value);
	if(nm == '') {
	  document.getElementById('name').focus();
		alert('Не заполнено поле "Полное юр. наименование"!');
		return false;
	}
	// Генеральный директор
	gndir = replace_str(document.getElementById('gen_dir').value);
	if(gndir == '') {
	  document.getElementById('gen_dir').focus();
		alert('Не заполнено поле "Генеральный директор"!');
		return false;
	}
}

// показать окно спецификации
function show_specif(id) {
	document.getElementById('div_spec').style.top = (ypos-20)+'px';
	document.getElementById('div_spec').style.left = (xpos-280)+'px';
	document.getElementById('div_spec').style.display = 'block';
	document.getElementById('id_dog').value = id;
}




// подтверждение на удаление клиента или помещение в архив
function del_cl(id,sel_us,tp) {
	if(tp == 0) {
		if(confirm("В архив данного клиента?"))
			document.location = '?del='+id+'&sel_us='+sel_us+'';
	} else {
		if(confirm("Полное удаление клиента из базы, \n при этом также будут удалены все связанные запросы! \n Продолжить операцию?"))
			document.location = '?del='+id+'&sel_us='+sel_us+'';
	}
	return false;
}




// удаление выбранных клиентов или в архив
function del_vyb(val) {
	if(val == 0) {	// в архив
		if(confirm('Переместить в архив выбранных клиентов?'))
			return true;
		else
			return false;
	}
	if(val == 1) {	// удаление из архива
		if(confirm("Полное удаление клиентов из базы, \n при этом также будут удалены все связанные запросы! \n Продолжить операцию?"))
			return true;
		else
			return false;
	}
}



// копирование, перемещение клиента пользователю
function run_type_act(val) {
	if(val == 1)
		str = 'Копировать пользователю выбранных клиентов?';
	if(val == 2)
		str = 'Переместить пользователю выбранных клиентов?';

	if(confirm(str))	// подтверждение
		document.f_act.submit();
}


// восстановление одного клиента
function rest_one(id,sel_us) {
	if(confirm('Восстановить?'))
		document.location = "?rest="+id+"&sel_us="+sel_us;
	else
		return false;
}


// восстановление выбранных клиентов
function rest_vyb() {
	if(confirm('Восстановить выбранных клиентов?'))
		return true;
	else
		return false;
}

// Подтверждение автоматического номера договора
function conf_auto_nm(id) {

if($("#cmd_"+id).prop("checked")){urlico = "cmd";}else{urlico = "kpf";}

	if(confirm('Сформировать номер договора автоматически?'))
		document.location="form_dog.php?dog="+id+"&fl=1&urlico="+urlico;	// авто
	else

		document.location="form_dog.php?dog="+id+"&urlico="+urlico;					// взять из базы

}

function set_urlico(id, urlico){
var link = $("#link_"+id).attr("href");
alert(link+urlico)

}