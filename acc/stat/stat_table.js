// JavaScript Document


// ���������� ����
var xpos=0;
var ypos=0;
var txpos = 0;
var typos = 0;

var type_opl = 0;			// ��� �������������� ������� ������ 0 - ������ ������, 1 - ������ ���������

var arr_cost_tmp = new Array();			// ��������� ������ �������� �����

var num_opl;						// ����� ������ ����� ������

var curr_cost_id = 0;		// ������� �� �������, ��� �������������� ����� ������

var arr_change = new Array();		// ������ ���������� ��������

//var cr_id = 0; 	// �� ������ �������



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








// �������������� ��������� - ��� ������� �������
function replace_price(v) {
	for(i=0;i<3;i++) {
		var reg_sp = /[^\d,\.]*/g;		// ��������� ���� �������� ����� ����, ������� � �����
		v = v.replace(reg_sp, '');
		var reg_sp = /\.|,{2,}|\.{2,}|,\.|\.,/g; 	// ��������� ������ ������ ������� � �����
		v = v.replace(reg_sp, ',');
		var reg_sp = /^,|^\./g;				// ���� ������ ������ ����� ��� �������, �������� �� '0,'
		v = v.replace(reg_sp, '0,');
	}
	var reg_sp = /,(\s)/g;					// ������ ������� ����� �������
	v = v.replace(reg_sp, ",");

	v = fix_number(v);

	return v;
}




// �������� ������� �� �����, ��� ����������� �������� ����� �����
function replace_zap(v) {
	var reg_sp = /,/g; 				// ������ ������� �� �����
	v = v.replace(reg_sp, '.');
	var reg_sp = /\s/g; 			// �������� ��������
	v = v.replace(reg_sp, '');
	return v;
}




// �������������� ����� ��� ����������� �������� � �����������
function fix_number(v) {
	v = replace_zap((''+v))*1;		// �������������� ������� � �����
	v = ''+(v).toFixed(8);			// ���������� �� 2� ���� ����� �������
//	var reg_sp = /^(\d*\.[1-9]*)0*/g;			// ��������� ����� ����� �������
//	v = v.replace(reg_sp, '$1');
	var reg_sp = /0*$/g;			// ��������� ����� ����� �������
	v = v.replace(reg_sp, '');
	var reg_sp = /^(\w*)\.$/g;			// ������ ����� ���� ����� ��� ��� �����
	v = v.replace(reg_sp, '$1');
	return v;
}



// ���������� �������� � ��������� ��������
function SetFiltrNum() {
	var fltr_name = 	document.getElementById("inp_fltr_num_name").value;
	var fltr_case = 	document.getElementById("sel_fltr_num_case").value;
	var fltr_val 	= 	replace_price(document.getElementById("inp_fltr_num_val").value);

	document.location = ("stat_table_query.php?filtr="+fltr_name+'&case='+fltr_case+'&val='+fltr_val);

}



// ���������� �������� � �������� �������
function SetFiltrClient() {
//	var fltr_name = 	document.getElementById("inp_fltr_num_name").value;
//	var fltr_case = 	document.getElementById("sel_fltr_num_case").value;
	var fltr_val 	= 	document.getElementById("sel_filtr_client").value;

	document.location = ("stat_table_query.php?filtr=client&case=client&val="+fltr_val);

}


// ���������� �������� � �������� �������
function SetFiltrDate() {
//	var fltr_name = 	document.getElementById("inp_fltr_num_name").value;
//	var fltr_case = 	document.getElementById("sel_fltr_num_case").value;
	var fltr_val1 	=  document.getElementById("sel_filtr_dat1").value;
	var fltr_val2 	=  document.getElementById("sel_filtr_dat2").value;

	document.location = ("stat_table_query.php?filtr=dat&case="+fltr_val1+"&val="+fltr_val2);

}



// ���������� �������� � �������� �������
function SetFiltrManager() {
	var fltr_val 	= 	document.getElementById("sel_filtr_man").value;

	document.location = ("stat_table_query.php?filtr=manager&case=manager&val="+fltr_val);

}




// �������� ���� ������� �� �����
function ShowFiltrNum(fltr) {

	curr_filtr = fltr;
	switch(curr_filtr) {


		case 'dat':
		title='����';
		break;

		case 'manager':
		title='���������';
		break;

	}

	title = "������ �� " + title;
	txpos = xpos;
	typos = ypos;

	hideAllDiv();		// ������ ��� ���� ��������
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

// �������� ��� ���� ��������
function hideAllDiv() {
    $("#div_fltr_num").hide();
    $("#div_fltr_date").hide();
    $("#div_fltr_man").hide();

}






// >>>>>>>>>>>>>#################------ ���� ������ ������������ ��������� -----------##########################







// ################################   ���� �������    ###########################

// ���������� � ������ ������ ����������� ���� �������
function setValTab(id,feld,val) {
	if(typeof(arr_change[id]) == 'undefined')
	arr_change[id] = new Array();
    arr_change[id][feld] = val;
}

// ��������������� ������ "��������� ���������"
function enableSaveButt() {
	document.getElementById('SaveButt').disabled=false;
}

// ���������� ���� ���������� ����� ������� � ���� ������
function SaveTabAllData() {
    var req_chn = new JsHttpRequest();
    req_chn.onreadystatechange = function() {
        if (req_chn.readyState == 4) {
						str = req_chn.responseJS.str;				// ������ ������������ ��������
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

//��������� ������ ���� ����� �� �������
if($("#ignoreerror_"+tek_id).is(":not(:checked)")){
 // console.log(tek_id);



//��� ������
typ_ord = $("#typ_ord_"+tek_id).val()

//����� �����
prdm_sum_acc = $("#prdm_sum_acc_"+tek_id).val()*1;

//������� ����
dolg = $("#dolg_"+tek_id).val()*1;
//�����
marja = $("#marja_"+tek_id).val()*1;
//�������������
podr_sebist = $("#podr_sebist_"+tek_id).val();
//���� �����
marja_proc =  100 - (podr_sebist * 100 / prdm_sum_acc);
marja_proc = marja_proc.toFixed(0);
//����� ������ ��� �������� ����������
//������� �����



if(typ_ord == "1"){sred_marja = $("#sred_marja_order").val();

//alert(prdm_sum_acc/podr_sebist)
if(sred_marja < marja_proc){
    $("#td_marja_"+tek_id).css({'background-color':'#FF3333'});
    counter = counter + 1;
    $("#span_marja_"+tek_id).html(marja_proc);
    }}





//�� ������� ������� ���� �������
if(dolg > 0){$("#td_dolg_"+tek_id).css({'background-color':'#FF3333'}); counter = counter + 1;}
 }
});

alert("������� "+counter+" ������������� ������")
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
if (confirm("���������� �������� �������������?")){


//������
proc_1 = $("#"+type+"_1").val()
//�������
proc_2 = $("#"+type+"_2").val()
//������� � ����
proc_3 = $("#"+type+"_3").val()

if(proc_1 == "" || proc_2 == "" || proc_3 == ""){alert("���� �� ����� � % ������")} else{


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
