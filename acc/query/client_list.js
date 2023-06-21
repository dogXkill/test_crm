// JavaScript Document
<!--

// ���������� ����
var xpos=0;
var ypos=0;



/*   **************    ����������� ����� ������   ******** <<<<<<<<<<    */

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

/*   >>>>> *********    ����������� ����� ������   ***********    */





<!-- <<<<<<<<<<<< ******** ����������� ��������� ����  *****************  //-->

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
// ������� ��������
// � ������� document.write ������� ���������� ����� � ���� ��������
// ��� ����� ��������� ��� �������� ����
document.onmousemove = function(event) {
     var event = event || window.event;
      xpos = defPosition(event).x;
			ypos = defPosition(event).y;
}

<!-- >>>>>>>>>>>> ******** ����������� ��������� ����  *****************  //-->


// �������������� ������, ������� � ������ ��������� � �������� �������
function replace_str(v) {
	var reg_sp = /^\s*|\s*$/g;
	v = v.replace(reg_sp, "");
	return v;
}

// �������� ������������ �����
function check() {
	// �������� ��������
	sh = replace_str(document.getElementById('short').value);
	if(sh == '') {
	  document.getElementById('short').focus();
		alert('�� ��������� ���� "�������� ��������"!');
		return false;
	}
	// ������ ������������
	nm = replace_str(document.getElementById('name').value);
	if(nm == '') {
	  document.getElementById('name').focus();
		alert('�� ��������� ���� "������ ��. ������������"!');
		return false;
	}
	// ����������� ��������
	gndir = replace_str(document.getElementById('gen_dir').value);
	if(gndir == '') {
	  document.getElementById('gen_dir').focus();
		alert('�� ��������� ���� "����������� ��������"!');
		return false;
	}
}

// �������� ���� ������������
function show_specif(id) {
	document.getElementById('div_spec').style.top = (ypos-20)+'px';
	document.getElementById('div_spec').style.left = (xpos-280)+'px';
	document.getElementById('div_spec').style.display = 'block';
	document.getElementById('id_dog').value = id;
}




// ������������� �� �������� ������� ��� ��������� � �����
function del_cl(id,sel_us,tp) {
	if(tp == 0) {
		if(confirm("� ����� ������� �������?"))
			document.location = '?del='+id+'&sel_us='+sel_us+'';
	} else {
		if(confirm("������ �������� ������� �� ����, \n ��� ���� ����� ����� ������� ��� ��������� �������! \n ���������� ��������?"))
			document.location = '?del='+id+'&sel_us='+sel_us+'';
	}
	return false;
}




// �������� ��������� �������� ��� � �����
function del_vyb(val) {
	if(val == 0) {	// � �����
		if(confirm('����������� � ����� ��������� ��������?'))
			return true;
		else
			return false;
	}
	if(val == 1) {	// �������� �� ������
		if(confirm("������ �������� �������� �� ����, \n ��� ���� ����� ����� ������� ��� ��������� �������! \n ���������� ��������?"))
			return true;
		else
			return false;
	}
}



// �����������, ����������� ������� ������������
function run_type_act(val) {
	if(val == 1)
		str = '���������� ������������ ��������� ��������?';
	if(val == 2)
		str = '����������� ������������ ��������� ��������?';

	if(confirm(str))	// �������������
		document.f_act.submit();
}


// �������������� ������ �������
function rest_one(id,sel_us) {
	if(confirm('������������?'))
		document.location = "?rest="+id+"&sel_us="+sel_us;
	else
		return false;
}


// �������������� ��������� ��������
function rest_vyb() {
	if(confirm('������������ ��������� ��������?'))
		return true;
	else
		return false;
}

// ������������� ��������������� ������ ��������
function conf_auto_nm(id) {

if($("#cmd_"+id).prop("checked")){urlico = "cmd";}else{urlico = "kpf";}

	if(confirm('������������ ����� �������� �������������?'))
		document.location="form_dog.php?dog="+id+"&fl=1&urlico="+urlico;	// ����
	else

		document.location="form_dog.php?dog="+id+"&urlico="+urlico;					// ����� �� ����

}

function set_urlico(id, urlico){
var link = $("#link_"+id).attr("href");
alert(link+urlico)

}