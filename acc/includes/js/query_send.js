// JavaScript Document
var arr_podr = new Array({'name':'��������...'});
//arr_podr[0]['name'] = '��������...';

// ----------------------------- ���������� ------------------------------------------
var num_prdm = 1;								// ����� ������� � ������ �������� �����
var num_podr = 1;								// ����� ������� � ������ �����������

var curr_num_podr = 	false;		// ����� ������� ��� �������� ����������� ����� ���������
//var podr_num = 0;
var podr_id = 0;
//var podr_name = '';
var podr_fl_ready = 0;					// ���� ��������� �� ���������� �� ���� ( 0-���, 1-���������, 2-��� ��������� )
var podr_opl_curr = 0;					// ������� ����� ���������� � ������ ��� �������������� �����

arr_all_data = new Array();					// ������ ���� ����� ��� ���������� � ����

var arr_podr_opl = new Array();  		// ������ ����� ��� �����������
var arr_predm_opl = new Array(); 	 	// ������ ����� ��� �������� �����

var num_opl;				// ����� ������ ����� ������
var opl_curr_tmp;		// ������� ����� ���������� ��� �����
var name_edit_opl = 'predm';	// ����� ������ ������������� - 'predm' - ������� ����� ��� 'podr' - ����������

var arr_all_load = new Array();		// ������ ���� ����� ��� �������� �� ����

// ���������� ����
var xpos=0;
var ypos=0;

    // ���������� ����� �������� ��������, ���������� �������������� ����� � ��.
    function init_page() {

        document.f_send.client_short.focus();		// ������ � ���� ��������� �������� �������
        doLoadPodr(); 	// ��������� ������ �����������

        if (op == 'new') {
        //	doLoad(0,0,1,'odin');	// �������������� ���������� � ���������, ������� ����������
        timerPodr = setTimeout("auto_podr(0)", 100);
        } else{
        timerPodr = setTimeout("auto_podr(1)", 100);
        }

    }




        // ������������� �������� ����� � �����������, ����������� ����� ��� �������� ������ �����������
        // op=0 - ���� ������ �����, ����� �������������� �������
        function auto_podr(opa) {
                if (podr_fl_ready == 0) {	// ���� ������ ����������� �� �������� - �����
                        timerPodr = setTimeout("auto_podr(" + opa + ")", 100);
                        return;
                }
                if (podr_fl_ready == 1) {
                        clearTimeout(timerPodr);
                        podr_fl_ready == 2;		// 2 - ������ ��� ��������� � ���������

                        if (opa == 0) {		// ����� ����� - �������� ������ ����
                                if (shop_id == 0) {
                                        write_feld_predm(0, 0, 0, 0, 0,0,0,0,0,0);	// �������� 1 ���� �������� �����
                                        write_feld_podr2(0, 0, 0, 0, 0, "");

                                } else {  // ����� �� ��������

                                        load_shop_order(shop_id);

                                }
                        }

                        if(op == 'edit'){
                    	// �������������� - �������� ��� ��������� ����
                                doLoad("get_req", client_id, "", ""); // �������������� ���������� � ���������
                                doLoadAllData(edit);
                                return;
                        }

                }
        }




    function ajrun(query, arr, func) {
        op = (arguments.length > 3) ? arguments[3] : '';
        file = (arguments.length > 4) ? arguments[4] : [];
        JsHttpRequest.query(
            query,    		// backend
            {op: op, arr: arr, file: file}, // ���������
            func,					// ��������� �����������
            true					// true - �� ���������� ������
        );
    }


// ���������� ��������
function ShowDivManager() {
  $('#div_manag').css('display', 'block');
}


    // �������� ������������ ����� �� ������ ��������
    //---------------------------------------------------------------------
    function check() {
$("#butt_send").prop('disabled', true)
        var obj = document.f_send;

         // �������� ��������, ������ ��. �������� �������
        obj.client_short.value = replace_str_cl_sh(obj.client_short.value);
        obj.client.value = replace_str(obj.client.value);
		
        // �� ��������� �������� ��������
        if (obj.client_short.value == "") {
          alert("������� �������� �������� �������");
          obj.client_short.focus();
		  $(obj.client_short).css("border-color","red");
					 setTimeout(function(){
						$(obj.client_short).css("border-color","rgb(118, 118, 118)");
					 }, 5000);
		  $("#butt_send").prop('disabled', false);
          return false;
        }

        if (obj.client_list.value == 0) {
            if (obj.client.value == "") {
                alert("������� ������ ����������� ������������");
                obj.client.focus();
				$("#butt_send").prop('disabled', false);
				   $(obj.client).css("border-color","red");
					 setTimeout(function(){
						$(obj.client).css("border-color","rgb(118, 118, 118)");
					 }, 5000);
                return false;
            }
        }

        // ��� �������
        if (!obj.client_type.value) {
          //alert("�������� ��� �������");
          var coords = $(obj.client_type).position();
          $(window).scrollTop(coords.top-50);
		  $(".client_type_me").focus();
		  $("#message_error").remove();
		  $(".client_type_me").append("<p id='message_error' style='color:red;font-weight:600;'>�������� ��� �������</p>");
		  $("#message_error").delay(2500).slideUp(300);
		  $("#butt_send").prop('disabled', false);
          return false;
        }

        // �������������� ����� ����������

        obj.post_add.value = replace_str(obj.post_add.value);
        obj.inn.value = replace_str(obj.inn.value);
        obj.kpp.value = replace_str(obj.kpp.value);
        // ���������� ����, ���������� �������
        obj.cont_pers.value = replace_str(obj.cont_pers.value);
        client_list_sel = $('#client_list').val();
		obj.status_check_email=$("#status_check_email").prop('checked');
		obj.sfera_dei=$("#sfera_dei").val();
		if ($("#sfera_dei").val()==0){
			 alert("�������� ����� ������������");
          var coords = $(obj.sfera_dei).position();
          $(window).scrollTop(coords.top);
		  $("#butt_send").prop('disabled', false);
		   $(obj.sfera_dei).css("border-color","red");
			 setTimeout(function(){
				$(obj.sfera_dei).css("border-color","rgb(118, 118, 118)");
			 }, 5000);
          return false;
			
		}
        console.log(client_list_sel+" - "+client_id);
       // alert("test")

        if($("#inn").val() !== ''){

            test_inn = validateInn('inn');
            if(test_inn == false){
            alert('������� �������������� ��� (��� ����� - 10 ��������, �� - 12 ��������)')
            $("#inn").focus();
							   $("#inn").css("border-color","red");
					 setTimeout(function(){
						$("#inn").css("border-color","rgb(118, 118, 118)");
					 }, 5000);
					 $("#butt_send").prop('disabled', false)
            return false
            }else{
                res = uniqFld('inn');
                if(res > 0){$("#butt_send").prop('disabled', false);return false}
            }
        }

        if ($('#firm_tel').val() == "") {
            alert("������� ������� ��������");
            $('#firm_tel').focus();
			$("#firm_tel").css("border-color","red");
					 setTimeout(function(){
						$("#firm_tel").css("border-color","rgb(118, 118, 118)");
					 }, 5000);
			$("#butt_send").prop('disabled', false);
            return false;
        }else{
                res = uniqFld('firm_tel');
                if(res > 0){$("#butt_send").prop('disabled', false);return false}
            }
            //  console.log('check')

        if ($('#email').val() !== "") {

        test_email = validateEmail('email');

        if(test_email == false){
            alert('������� �������������� �����')
            $("#email").focus();
			$("#email").css("border-color","red");
					 setTimeout(function(){
						$("#email").css("border-color","rgb(118, 118, 118)");
					 }, 5000);
			$("#butt_send").prop('disabled', false);
            return false
        }else{
                res = uniqFld('email');
                if(res > 0){$("#butt_send").prop('disabled', false);return false}
        }
        }

        typ_ord = $("#typ_ord").val();

        if (typ_ord == "") {
            alert('�������� ��� ������!');
            $('#typ_ord').focus();
			$("#typ_ord").css("border-color","red");
					 setTimeout(function(){
						$("#typ_ord").css("border-color","rgb(118, 118, 118)");
					 }, 5000);
			$("#butt_send").prop('disabled', false);
            return false;
        }
        //����� ������
        if ($('#form_of_payment').val() == 0) {
            alert('�������� ������ ������!');
            $('#form_of_payment').focus();
			$("#form_of_payment").css("border-color","red");
					 setTimeout(function(){
						$("#form_of_payment").css("border-color","rgb(118, 118, 118)");
					 }, 5000);
			$("#butt_send").prop('disabled', false);
            return false;
        }
        //��������
        if ($('#deliv_id').val() == "") {
            alert('�������� ������ ��������!');
            $('#deliv_id').focus();
			$("#deliv_id").css("border-color","red");
					 setTimeout(function(){
						$("#deliv_id").css("border-color","rgb(118, 118, 118)");
					 }, 5000);
			$("#butt_send").prop('disabled', false)
            return false;
        }
        if ($('#cont_pers').val() == "") {
            alert("������� ���������� ����");
            $('#cont_pers').focus();
			$("#cont_pers").css("border-color","red");
					 setTimeout(function(){
						$("#cont_pers").css("border-color","rgb(118, 118, 118)");
					 }, 5000);
			$("#butt_send").prop('disabled', false)
            return false;
        }
        if ($('#cont_tel').val() == "") {
            alert("������� ���������� �������");
            $('#cont_tel').focus();
			$("#cont_tel").css("border-color","red");
					 setTimeout(function(){
						$("#cont_tel").css("border-color","rgb(118, 118, 118)");
					 }, 5000);
			$("#butt_send").prop('disabled', false)
            return false;
        }else{
                res = uniqFld('cont_tel');
                if(res > 0){$("#butt_send").prop('disabled', false);return false}
        }




       var num_prdm = $("#tddivs").children('div').length;
	   //new 
	   
	   $('#tddivs div').each(function(){
		   console.log("t:"+$(this).find(".frm_art_num").val()+"|"+$(this).find(".frm_art_num").attr("id"));
	   });
	   //back 
        for (var i = 0; i < num_prdm; i++) {
            //$('#art_num_'+i).val() = a
			if ($('#art_num_' + i).val()!=undefined){
            a = replace_str($('#art_num_' + i).val());
            $('#art_num_' + i).val(a)
            if (a == '' && typ_ord == "2") {
                $('#art_num_' + i).focus();
                alert('������ ��� ������ �������, ������, �� ������ �������!');
				
				$("#butt_send").prop('disabled', false)
                return false;
            }
			}
        }

        for (var i = 0; i < num_prdm; i++) {
            //$('#art_num_'+i).val() = a
			if ($('#art_num_' + i).val()!=undefined){
            a = replace_str($('#art_num_' + i).val());
            $('#art_num_' + i).val(a)
            if (a == 'n' && typ_ord == "2") {
                $('#art_num_' + i).focus();
                alert('������ ��� ������ �������, ������, ��� ������� � ����!');
				$("#butt_send").prop('disabled', false)
                return false;
            }
			}
        }


        for (var i = 0; i < num_prdm; i++) {
            //$('#art_num_'+i).val() = a
			if ($('#prdm_nm_' + i).val()!=undefined){
            a = replace_str($('#prdm_nm_' + i).val());
            $('#prdm_nm_' + i).val(a)
            if (a == '0' || a == '') {
                $('#prdm_nm_' + i).focus();
                alert('�������� �� ����� ���� ����� 0');
                $('#prdm_nm_' + i).select();
				$("#butt_send").prop('disabled', false)
                return false;
            }
			}
        }

    //��������� ��������� ������ ���� ��� �� ����� ��� ��������. �� ���� �������� ������ ��� � ���������� ���� � �������
    if(!$.isNumeric(shop_id)){
        for (var i = 0; i < num_prdm; i++) {
			if ($('#art_num_' + i).val()!=undefined){
				art_num = $('#art_num_' + i).val();
				col_in_pack = $('#col_in_pack_' + i).val();
				prdm_nm = $('#prdm_nm_' + i).val();
				if (col_in_pack) {
					if (prdm_nm % col_in_pack) {
						if (confirm("������� " + art_num + " ������ � ���������� ���������. ���������� ������� ��������� ������ � ������� ����� � �������� � ���������. ���������� ��� ��������?")) {
						} else {
							$('#prdm_nm_' + i).focus();
							$("#butt_send").prop('disabled', false)
							return false;
						}

					}
				}
			}
        }
      }


        for (var i = 0; i < num_prdm; i++) {
			if ($('#art_num_' + i).val()!=undefined){
            art_num = $('#art_num_' + i).val();
            booked = $('#booked_' + i).val()*1;
            sklad = $('#sklad_' + i).val()*1;
            prdm_nm = $('#prdm_nm_' + i).val()*1;
            free = sklad - booked;
            if (free < prdm_nm) {
                    if (confirm("�� ��������� ������� " + prdm_nm + "��. ������� " + art_num + " , �� ��� �� ������� � ������ �����. ������ �� ������ "+ sklad + "��. �� ������� �������� " + free + "��. ���������� ��� ��������?")) {
                    } else {
                        $('#prdm_nm_' + i).focus();
						$("#butt_send").prop('disabled', false)
                        return false;
                    }
                 }
			}
        }


        //	������� �����
        for (var i = 0; i < num_prdm; i++) {
			if ($('#art_num_' + i).val()!=undefined){
            a = replace_str(document.getElementById('prdm_name_' + i).value);
            document.getElementById('prdm_name_' + i).value = a;
            if (a == '') {
                document.getElementById('prdm_name_' + i).focus();
                alert('���� \'������������\' ��� �������� ����� �� ���������!');
				$("#butt_send").prop('disabled', false)
                return false;
            }
			}
        }
        // ������������ �����������
        var cnt_podr = $("#tddivs2").children('div').length;
        for (var i = 0; i < cnt_podr; i++) {
			console.log('podr');
			console.log(i);
			if ($('#podr_name_' + i).val()!==undefined){
            a = replace_str(document.getElementById('podr_name_' + i).value);
            document.getElementById('podr_name_' + i).value = a;
            if (a == '') {
                document.getElementById('podr_name_' + i).focus();
                alert('���� \'������������\' ��� ����������� �� ���������!');
				$("#butt_send").prop('disabled', false)
                return false;
            }
			}
        }
		//�������� select izd � sdelka
		if (typ_ord==1 || typ_ord==3){
			//if ($(".select_izd").val()==0){$("#open_izd").css('color','red');alert('���� \'��� �������\' �� ���������!'); return false;}
			//if ($(".select_sdelka").val()==0){$("#open_sdelka").css('color','red');alert('���� \'��� ������\' �� ���������!');return false;}
		
			for (var i = 0; i < num_prdm; i++) {
				//$('#art_num_'+i).val() = a
				if ($('#art_num_' + i).val()!=undefined){
				a = replace_str($('#prdm_sel_sdelka_izd_' + i).attr('data-val1'));
				b = replace_str($('#prdm_sel_sdelka_izd_' + i).attr('data-val2'));
				if (a == "0" || a==0) {
					if (b!=3){
						console.log(a == "0" || a==0);
						$('#prdm_sel_sdelka_izd_' + i).css('color','red');
						$('#prdm_sel_sdelka_izd_' + i).click();
						//alert('���� \'��� �������\' �� ���������!')
						$("#butt_send").prop('disabled', false)
						return false;
					}
				}else if (b == "0" || b==0) {
					$('#prdm_sel_sdelka_izd_' + i).css('color','red');
					//alert('���� \'��� ������\' �� ���������!')
					$('#prdm_sel_sdelka_izd_' + i).click();
					$("#butt_send").prop('disabled', false)
					return false;
				}
				}
			}
			
		}
		//�������� �� ���������� ����� ������ � �������
		if (typ_ord==1 || typ_ord==3){
			var flag_prov_sovm=0;
			var mas_zn=new Array();
			for (var i = 0; i < num_prdm; i++) {
				if ($('#art_num_' + i).val()!=undefined){
					b = replace_str($('#prdm_sel_sdelka_izd_' + i).attr('data-val2'));
					if (b!=3){
					mas_zn.push(b);
					}
				}
			}
			console.log(mas_zn);
			var zn=0;
			mas_zn.forEach((element)=>{
				if (zn==0){zn=element;}
				else if (zn!=element){
					alert("������ ������ ����������� ������������ ��������� � ���� ������������");
					$("#butt_send").prop('disabled', false);
					flag_prov_sovm=1;
				}
				console.log(element);
			});
			if (flag_prov_sovm==1){return false;}
		}
		
        // ����������
        kalk_summ_predm();				// �������� ���� ������� ��� ��������
        kalk_summ_podr();					// ��� �����������
        create_arr_all_data();		// �������� ������� ���� ����� � ���������� � ����
       //alert("����� ������")
      // $('#butt_send').prop('disabled', true)
	  //$("#butt_send").prop('disabled', false)
    }


        function validateEmail(inp)
            {
                email = $("#"+inp).val();
                var re = /\S+@\S+\.\S+/;
                return re.test(email);
            }

        function validateInn(inp){
                inn = $("#"+inp).val();
                inn = inn.replace(/[^\d]/g, '');
                $("#"+inp).val(inn);
            	if(inn.length !== 10 && inn.length !== 12){return false;}else{return true}
        }

       function uniqFld(inp){
        client_list_sel = $('#client_list').val();



        //���������, ���� ��� ���� ������������ ������, ���� �� ������ �������, �� �������� �� ������������
        if(client_id == '' && (!client_list_sel || client_list_sel == '0')){

        var sendData = {};
        fld_val = $("#"+inp).val();
		sendData.inp = inp;
		sendData.fld_val = fld_val;

        client_list_sel = $('#client_list').val();

        var fld_titles  = {'email': '�������', 'firm_tel': '��������� �������', 'cont_tel': '���������� ���������', 'inn': '���'}
                                console.log('uniqFld: '+client_list_sel+' - '+client_id)
        if(client_id == '' && (!client_list_sel || client_list_sel == '0')){
                      title = fld_titles[inp];





                var data;
                    $.ajax({
                        type: 'POST',
                        url: '../backend/uniqFld.php',
                        data: sendData,
                        dataType: 'html',
                        async: false,
                        success: function(data){idata = data;}
                    });


    if(idata > 0){
                alert('��� ������� ������ ('+ idata +'��.) � ����� '+title+'. �������� ������� �� ������!')
                $('#client_list').focus();

                doLoad('search','', inp, fld_val)


       }

       return idata

   }  } }


        function give_discount() {
        skidka = $('#skidka').val();
        if (skidka > 5) {
            if (confirm("�������, ��� ����� ���� ����� ������?")) {
            } else {
                return false
            }
        }

        for (var i = 0; i < 200; i++) {
            a = $('#art_num_' + i).val();
            if ($.isNumeric(a)) {
                old_price = $('#prdm_pr_' + i).val()
                new_price = old_price - old_price * skidka / 100
                new_price = new_price.toFixed(2)
                $('#prdm_pr_' + i).val(new_price)

            }
        }
        kalk_summ_predm()
    }







// <<<<<<<<<<< ************ ���������� �������  *****************  //

// ������� ������ ���� ����� ��� ���������
//------------------------------------------------------------------------
function create_arr_all_data() {

    console.log('create_arr_all_data')
	form = document.f_send;
	console.log(form);
	arr_all_data['ed_us_id']		= 	ed_us_id;					        // �� ������������, ���������� ������
	arr_all_data['usid']			= 	user_id;					            // �� ������������, ���������� �������
	arr_all_data['edit'] 			= 	edit;						              // �� ���� ���. ����� 'new'
	arr_all_data['client_sh'] 		= 	form.client_short.value;	// �������� �������� �������
	arr_all_data['client_type'] 		= 	form.client_type.value;	// ��� �������


    client_list_sel = $('#client_list').val();


    //���� ������ ������ ��������, �� ��������� ���, ���� ������ �� ������, �� ������� ���� �� ������_�� � �����, � �������� ��� � ������. ���� ������ ����� �� ����� ��������
    //������ ������ � ��� ������� ������ �������
    if($.isNumeric(client_list_sel) && client_list_sel > 0){arr_all_data['client_lst'] = client_list_sel;}
    else if($.isNumeric(client_id) && !client_list_sel){arr_all_data['client_lst'] = client_id;}
   else{arr_all_data['client_lst'] = '';}

    //arr_all_data['client_lst'] 		= 	form.client_list.value;		// �� ������� � ������
//console.log(client_list_sel+" - "+client_id)

	arr_all_data['client_full'] 	= 	form.client.value;			   // ������ �������� �������
	arr_all_data['post_add'] 		= 	form.post_add.value;		     // ����������� �����
	arr_all_data['deliv_add'] 		= 	form.deliv_add.value;		     // ����� ��������
	arr_all_data['inn'] 			= 	form.inn.value;				         // ���
	arr_all_data['kpp'] 			= 	form.kpp.value;				         // ���
	arr_all_data['okpo'] 			= 	form.okpo.value;				     // ����
	arr_all_data['comment'] 		= 	form.comment.value;
	arr_all_data['rs'] 				= 	form.rs.value;				         // �/�
	arr_all_data['bank'] 			= 	form.bank.value;			         // � ����� ����� ������
	arr_all_data['bik'] 			= 	form.bik.value;				         // ���
	arr_all_data['firm_tel'] 		= 	form.firm_tel.value;		     // �������
	arr_all_data['email'] 			= 	$("#email").val();		       // email
    arr_all_data['cont_pers'] 		= 	form.cont_pers.value;		    // ���������� ����
	arr_all_data['cont_tel'] 		= 	form.cont_tel.value;		      // ���������� �������
	arr_all_data['user_id']			=	user_id;					              // �� ������������
	arr_all_data['user_full_name']	=	user_full_name;				      // ������ ��� ������������
	arr_all_data['tpacc']			=	tpacc;						                // ��� ������������
	arr_all_data['typ_ord']			=  $("#typ_ord").val();					// ��� ������
	arr_all_data['form_of_payment']	=	$('#form_of_payment').val();				// ����� �����
	arr_all_data['deliv_id']	    =	$('#deliv_id').val();				// ����� �����
	arr_all_data['corsina_order_uid']	=	$('#corsina_order_uid').val();						// id ������ � �� ��������
	arr_all_data['corsina_order_num']	=	$('#corsina_order_num').val();						// ����� ������ � �� ��������
	arr_all_data['uniq_id']	        =	    $('#uniq_id').val();
	arr_all_data['status_check_email']=$("#status_check_email").prop('checked');
	arr_all_data['sfera_dei']=$("#sfera_dei").val();
	// ------------------------------ ������� ����� ----------------------------------------
	kalk_summ_predm();
   //  console.log(arr_all_data['predmet']['summ'])
	arr_all_data['predmet']['opl']					=		arr_predm_opl;						// ���� ������

    arr_all_data['predmet']['skidka']	=		$('#skidka').val();
    arr_all_data['booking_till']	=	$('#booking_till').val();
	

	//  ------------------------------ ���������� -------------------------------------------
		kalk_summ_podr();

		arr_all_data['podr']['opl'] 						=		arr_podr_opl;							// ���� ������

	//  ------------------------------ ���������� -------------------------------------------

    if(edit == "new"){
	arr_all_data['note'] 										=		form.adition.value;					// ����������
    }
     //  console.log(arr_all_data['predmet']['summ_acc'])
       //console.log(arr_all_data)
   	doSaveQuery(arr_all_data);

}

function doSaveQuery(arr) {
	document.body.classList.add('loaded_hiding');
    window.setTimeout(function () {
      document.body.classList.add('loaded');
      document.body.classList.remove('loaded_hiding');
    }, 500);
	$(".preloader").show();
$("#butt_send").prop('disabled', true);
     console.log("pre:"+arr)
	var req_sq = new JsHttpRequest();
	req_sq.onreadystatechange = function() {
		if (req_sq.readyState == 4) {
		//id = req_sq.responseJS.id;
		id = req_sq.responseText;
		amo_id=req_sq.responseJS.amo_id;
		id_quer=req_sq.responseJS.id_qu;//���������� 
		//id_quer1=req_sq.responseJS.id;//��������
		//console.log();
	 	if (id == "edit"){
		  query_id = edit
		  act = "edit"
		 //id_quer=id_quer;
		}  else {
		  query_id = id
		  act = "insert"
		  //id_quer=id_quer1;
		  //
		  
		  //
		  }

			if(shop_id>0) {
	 set_shop_order_on(shop_id);     // �������� ����� � �������� ��� ����������� � ���������
           }
           console.log(req_sq.responseText)
		   //������� ���������
		   console.log("tip:"+req_sq.responseJS.id);
		   if (req_sq.responseJS.id!='edit'){
			   if (amo_id!=null && amo_id!=0){
			   $(".preloader").hide();
				/*$("body").overhang({
				  type: "success",
				  message: "����� ������ � AmoCrm"
				});*/
				
				setTimeout(function(){ window.location="index.php"; },2000);
			}else{
				
				/*$("body").overhang({
				  type: "error",
				  message: "����� �� ������ � AmoCrm"
				});*/
				//������ ������ �� ���������� ��� �������� ������ � 0
				//������ id ������ � ������� ���� (��� ����� ������)
				$(".preloader").hide();
				console.log("--");
				$("#izd_sdelka_red_id_amo").val(id_quer);
				$(".modal_amo_crm").parents(".modal").show();
				
				
			}
		   }else{
			    $(".preloader").hide();
			   setTimeout(function(){ window.location="index.php"; },2000);
		   }
		   
         //document.location = "index.php";
		 //setTimeout(function(){ window.location="index.php"; },5000);
	}	
	
       }

    req_sq.open(null, '../backend/back_SaveAllQuery.php', true);
	req_sq.send( { arr: arr } );

 }

// >>>>>>>>>>>>  ************ ���������� �������  *****************  //

// <<<<<<<<<<< ************ �������� ���� ����� �������  *****************  //

// ������� ������������ �������� ���� ����� �������
function doLoadAllData(id) {

	var req_la = new JsHttpRequest();
	req_la.onreadystatechange = function() {
		if (req_la.readyState == 4) {
						arr_all_load = req_la.responseJS.res;				// ������ ������������ ��������
						init_all_feld();
					}
	}
	req_la.open(null, '../backend/back_LoadAllQuery.php', true);
	req_la.send( { id:id } );
}

// ������������� ����� �������� ����� � ����������� ����� �������� �� ����
function init_all_feld() {
	// console.log(arr_all_load['predm']['list']);
	// ������ �������� �����
	for(var i=0;i<arr_all_load['predm']['list'].length;i++) {
		var rpriceour = arr_all_load['predm']['list'][i]['r_price_our'] * 1;

		if (rpriceour == 0) {
			rpriceour = (arr_all_load['predm']['list'][i]['price'] * 1) * 0.3;
		}

		if (arr_all_load['predm']['list'][i]['art_num'] == "d" || arr_all_load['predm']['list'][i]['art_num'] == "n") {
			rpriceour = 0;
		}
		tip_izd=arr_all_load['predm']['list'][i]['tip_izd'];
		tip_sdelki=arr_all_load['predm']['list'][i]['tip_sdelki'];
		write_feld_predm(i,arr_all_load['predm']['list'][i]['art_num'],arr_all_load['predm']['list'][i]['name'],arr_all_load['predm']['list'][i]['num'],arr_all_load['predm']['list'][i]['price'], 0, rpriceour,0,tip_izd,tip_sdelki);
	}
	// ������ ����� ��� �������� �����
	arr_predm_opl = new Array();
	arr_predm_opl['list'] = arr_all_load['predm']['opl'];
//	init_feld_predm();	// ������������� ����� ����� � ������� ����
	kalk_summ_predm();	// ������� ���� ����

	// ������ �����������

	arr_podr_opl = new Array();

	for(var i = 0;i < arr_all_load['podr']['list'].length;i++) {
		write_feld_podr2(i, arr_all_load['podr']['list'][i]['podr'], arr_all_load['podr']['list'][i]['name'], arr_all_load['podr']['list'][i]['num'], arr_all_load['podr']['list'][i]['price'], 0);
	//alert(arr_all_load['podr']['list'][i]['price'])
	arr_podr_opl[i] = new Array();
		arr_podr_opl[i]['list'] = new Array();
		arr_podr_opl[i]['list'] = arr_all_load['podr']['list'][i]['opl'];
	}
	kalk_summ_podr();
    if(edit !== "new")
    $("#date_query_span").html("���� ������: "+arr_all_load['date_query']);
}


// >>>>>>>>>>> ************ �������� ���� ����� �������  *****************  //

// <<<<<<<<<<<< ********* ���������  *****************  //

function clear_str(str){
	return str.replace(/\|\\/g, "");
}
function doLoad(act, uid, inp, fld_val){
    //console.log(inp + " " + fld_val)
	short = $('#client_short').val()
	short_length = short.length
	if (short_length >= 2 || act == "get_req" || inp !== '') {
		var geturl;
		geturl = $.ajax({
			type: "GET",
			url: '../backend/back_LoadReqClient1.php',
			data : 'act='+act+'&short='+short+'&uid='+uid+'&inp='+inp+'&fld_val='+fld_val,
			success: function () {
				var res = geturl.responseText
				var select = $('#client_list');

				if (act == "search") {
					select.find('option').not(':first-child').remove();

					if (res) {

						select.append(res);
					}
				} else if (act == "get_req") {
				   // console.log(res)
					res = res.split("[,]")
					$('#client_short').val(clear_str(res[1]))
					$('#client').val(clear_str(res[2]))
					$('#post_add').val(clear_str(res[3]))
					$('#deliv_add').val(clear_str(res[4]))
					$('#inn').val(clear_str(res[5]))
					$('#kpp').val(clear_str(res[6]))
					$('#okpo').val(clear_str(res[7]))
					$('#comment').val(clear_str(res[8]))
					$('#rs').val(clear_str(res[9]))
					bank_str=clear_str(res[10]);
					console.log("str:"+bank_str+"(type:"+typeof(res[10])+") clear:"+bank_str);
					$('#bank').val(clear_str(res[10]))
					$('#bik').val(clear_str(res[11]))
					$('#firm_tel').val(res[12])
					$('#email').val(res[13])
					$('#cont_pers').val(res[14])
					$('#cont_tel').val(res[15])
					console.log(res[16]);
					/*if (res[16]==1){
						$('#status_check_email').prop('checked', true);
					}else if (res[16]==2 || res[16]==0){
						$('#status_check_email').prop('checked', true);
						*/
						if (res[16]==2 || res[16]==1){
							$('#status_check_email').prop('checked', true);
							
					}else{
						$('#status_check_email').prop('checked', false);
					}
					if (res[17]==0){
							//$('#sfera_dei').val(1);
							$('#sfera_dei option[value=0]').prop('selected', true);
							
					}else{
						//$('#sfera_dei').val(res[17]);
						$('#sfera_dei option[value='+res[17]+']').prop('selected', true);
					}


				}
			}
		});
	}
}






// <<<<<<<<<<<<************ �������������� ����� � ������, �������, ���������� *****************  //

// �������������� ��������� - ��� ������� �������
function replace_price(v) {
	v = ''+v;
	for(i=0;i<3;i++) {
		var reg_sp = /[^\d,\.\-]*/g;		// ��������� ���� �������� ����� ����, ������� � �����
		v = v.replace(reg_sp, '');
		var reg_sp = /\.|,{2,}|\.{2,}|,\.|\.,|,/g; 	// ��������� ������ ������ ������� � �����
		v = v.replace(reg_sp, '.');
		var reg_sp = /^,|^\./g;				// ���� ������ ������ ����� ��� �������, �������� �� '0.'
		v = v.replace(reg_sp, '0.');
	}

	var reg_sp = /\.(\s)/g;					// ������ ������� ����� �������
	v = v.replace(reg_sp, '.');
	return v;
}


// �������������� ����� ��� ����������� �������� � �����������
function fix_number(v) {
	v = replace_zap((''+v))*1;		// �������������� ������� � �����
	v = ''+(v).toFixed(8);			// ���������� �� 2� ���� ����� �������
//	var reg_sp = /^(\d*\.0*[1-9]*)0*/g;			// ��������� ����� ����� �������
//	v = v.replace(reg_sp, '$1');
	var reg_sp = /0*$/g;			// ��������� ����� ����� �������
	v = v.replace(reg_sp, '');
	var reg_sp = /^(\w*)\.$/g;			// ������ ����� ���� ����� ��� ��� �����
	v = v.replace(reg_sp, '$1');
	return v;
}


// �������������� ��������� - ���������, ��� ������������ ������

function replace_price2(v) {
		var reg_sp = /^0*$/g;						// ���� � ������ ������ ���� ����, �������
		v = v.replace(reg_sp, '0');
		var reg_sp = /^0,$/g;						// ������� ��� ���� � ��������� ������ '0,'
		v = v.replace(reg_sp, "");
		var reg_sp = /^\s|,$/g;						// ������ ����� ������ ������
		v = v.replace(reg_sp, "");
//		v = fix_number(v);
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

// �������������� ������, ������� � ������ ��������� � �������� �������
function replace_str(v) { 
	if (v!=undefined){
	var reg_sp = /^\s*|\s*$/g;
	v = v.replace(reg_sp, "");
	return v;
	}else{return v;}
}

// �������� �������� �������, ������� � ������ ��������� � �������� �������, ������� � "���"
function replace_str_cl_sh(v) {
	var reg_sp = /(\")|(\')|(�)|(�)|(���)|(ooo)/ig;
	v = v.replace(reg_sp, "");
	v = replace_str(v);
	return v;
}

// �������������� ������ �����, �������� �������� - '���'
function replace_acc(val) {
	if((val == '���') || (val == 'no') || (val == '-'))
		return '���';
	else
		return replace_num_acc(val);
}

// �������������� ������ �����
function replace_num_acc(v) {
	var reg_sp = /[^\d]*/g;		// ��������� ���� �������� ����� ����
	v = v.replace(reg_sp, '');
//	var reg_sp = /^0*$/g;						// ���� � ������ ������ ���� ����, �������
//	v = v.replace(reg_sp, '');

	return v;
}



// >>>>>>>>>>>> ************ �������������� ����� � ������, �������, ���������� *****************  //

// ������ ����� ��� ����������� � �������� �����
function obj_podr_opl(summ,dat,num_pp) {
	this.summ = summ;				// 	�����
	this.date = dat;				// 	����
	this.num_pp = num_pp;		//  ����� ���������� ���������
}

// ��� ������� ������ "��" - ���������� ������ �������� � �������
function check_opl(){
	var obj = document.ff_new_opl;
	if(name_edit_opl == 'podr') {		// ���� ���� ������ � �����������
			if(num_opl < 1) {
				delete arr_podr_opl[opl_curr_tmp]['list'];
			}
			else {
				arr_podr_opl[opl_curr_tmp] = new Array();
				arr_podr_opl[opl_curr_tmp]['list'] = new Array();
				for(i=0;i<(num_opl);i++) {
					arr_podr_opl[opl_curr_tmp]['list'][i] = new obj_podr_opl(document.getElementById('opl_summ_'+(i)).value, document.getElementById('opl_dat_'+(i)).value,document.getElementById('opl_numpp_'+(i)).value);
				}
			}
			hide_div_opl();
			kalk_summ_podr();
	}
	if(name_edit_opl == 'predm') {		// ���� ���� ������ � �������� �����
			if(num_opl < 1) {
				arr_predm_opl = new Array();
			}
			else {
				arr_predm_opl = new Array();
				arr_predm_opl['list'] = new Array();
				for(i=0;i<(num_opl);i++) {
					arr_predm_opl['list'][i] = new obj_podr_opl(document.getElementById('opl_summ_'+(i)).value, document.getElementById('opl_dat_'+(i)).value,document.getElementById('opl_numpp_'+(i)).value);
				}
			}
			hide_div_opl();
			kalk_summ_predm();
	}
}

// <<<<<<<<< **********************************************************************  //
// <<<<<<<<< **************************  ������� �����  ***************************  //
// <<<<<<<<< **********************************************************************  //

//	������� ������ num �������� �����
function clear_feld_predm(num, art) {
	var cnt = $("#tddivs").children('div').length;

	if (cnt == 1) {
		return false;
	}
	$("#dv" + num).remove();


	var last_i = 0;
	$("#tddivs").children('div').each(function(i, elem) {
		last_i = i;
		$(elem).attr("id", "dv" + i);
		$(elem).find(".butt_plus:first").attr("onclick", "clear_feld_predm(" + i + ")");
		$(elem).find("td:first").find("span:first").text(i + 1);
		$(elem).find("td:last div:first").attr("id", "min" + i);

		if ($(elem).find(".plb").length == 1) {
			$(elem).find("td:last div:last").attr("id", "pl" + i);
			$(elem).find("td:last div:last input").attr("onclick", "write_feld_predm(" + (i) + ",0,0,0,0,1,0,0,0,0);  return false;");
		}
	});

	if ($("#tddivs").find(".plb").length == 0) {
		$("#dv" + last_i + " td:last").append('<div id="pl' + (last_i) + '" style="display:inline; width=15px;"><input class="butt_plus plb" onclick="write_feld_predm(' + (last_i) + ',0,0,0,0,1,0,0,0,0);  return false;" name="a" type="button" value="+" /></div>');
	}


	num_prdm--;
	kalk_summ_predm();		// ������� ���� ���� ����� ��� ��������
}


function get_art_title(id, jump, counts, art_id_fld_change){


	var qty = $("#prdm_nm_"+id).val();
	var art_num = $("#art_num_"+id).val();


	if ($.isNumeric(art_num)) {
		var geturl;
		geturl = $.ajax({
			type: "POST",
			url: '../backend/get_art_title.php?art_num='+art_num,
			error: function() {
				alert("�������� ��� ��������� �� � ����");
			},
			success: function () {
				var resp = geturl.responseText
				if (resp =="") {
					alert ("���� �� ��������. ��������� ����������� � ��������� ��� ������� ��������� �����")
				}

				if (resp !== "") {
					//alert(resp)
					resp=resp.split('*;*');
					if (resp == "no"){
						alert("������ �������� �� �������")
					} else {

                        uid=resp[0];
						title=resp[1];
						sklad=resp[2];
						booked=resp[3]
						col_in_pack=resp[4];
						price=resp[5]*1;
                        price_txt = price + '�.';
						price_our=resp[6]*1;
						r_price_our=resp[7]*1;
                        retail=resp[8];
                        retail_price=resp[9]*1;
                        if(retail_price > 0){retail_price_txt = retail_price + '�.';}else{retail_price_txt = '�� �����������';}


                        if(booked == ""){booked = 0;}

						$('#prdm_name_'+id).val(art_num+' '+title);
						$('#podr_name_'+id).val(title);



            //���� ������� ������ �������� - ������� ����
            var priceField = $('#prdm_pr_'+id).val();
            priceField = $.trim(priceField);
                                 console.log(priceField+" "+qty+" "+col_in_pack+" "+price)
            if (priceField == '' || priceField == '0' || art_id_fld_change == '1') {
              if (qty % col_in_pack == 0) {
                $('#prdm_pr_' + id).val(price);
              } else {
                  alert("��������! ������� "+art_num+" ������ � ���������� ��������� ��������, �������, �� ���� �� ��������� ����������� ��������� ����. ��� ���������� ��������, �������� ��������� ������� �� ���������� ������� �������� � �������� ������� ���������� �� ��������� ����")
                $('#prdm_pr_' + id).val(retail_price);
              }
            }
            //���� ������� �� ������ �������� - ���� ����

                        //���� �� � ���� �/� �� �����������, �� ���������� �/� ��� 0,8 �� ����
						if (price_our == 0) {
							price_our = price * 0.8;
							price_our = price_our.toFixed(2);
						}
						$('#podr_pr_'+id).val(price_our);

						if (r_price_our == 0) {
							r_price_our = price * 0.5;
							r_price_our = r_price_our.toFixed(2);
						}
						$('#prdm_pr_our_'+id).val(r_price_our);
                        $('#booked_'+id).val(booked);

						add_buttons_history = " <a href=\"/acc/stat/stat_shop.php?tip=by_art_num&art_num="+art_num+"&date_from=&date_to=&type=shop_history&act=show_bookings\" target=\"_blank\"><img src=\"../../i/stat_sm.png\" align=\"absmiddle\" onmouseover=\"Tip('���������� ������� ������');\"></a>";
						add_buttons_booked = " �����: <b>"+booked+"��.</b> <input type=\"hidden\" value=\""+sklad+"\" id=\"sklad_"+id+"\"/> <input type=\"hidden\" value=\""+booked+"\" id=\"booked_"+id+"\"/>";
						add_buttons_apps = " <a href=\"https://crm.upak.me/acc/applications/?art_num="+art_num+"\" target=\"_blank\"><i class=\"fa fa-cogs\" style=\"cursor: pointer; font-size:14px\" onmouseover=\"Tip('����������� ������ �� ������������')\"></i></a> ";
                        add_buttons_inf = " <i class=\"fa fa-info-circle\" aria-hidden=\"true\" style=\"cursor: pointer; font-size:14px\" onmouseover=\"Tip('��������� �� <b>" + col_in_pack + "��.</b> <br>���. ����: <b>" + price_txt + "</b> <br>����. ����: <b>" + retail_price_txt + "</b>')\"></i>";
                        //  add_buttons_inf = '';
					   	if (col_in_pack !== "") {
						  	col_in_pack_text = "<input type=\"hidden\" value=\""+col_in_pack+"\" id=\"col_in_pack_"+id+"\"/>";
						} else {
							col_in_pack_text = "";
						}

						$('#art_span_'+id).html("&nbsp;&nbsp;�����: <a href=\"https://www.paketoff.ru/shop/view/?id="+uid+"\" target=_blank  style=\"color: green; font-weight: bold\"\">"+sklad+"</a> "+ add_buttons_booked +" "+col_in_pack_text+" "+add_buttons_history+" "+add_buttons_apps+" "+add_buttons_inf);

						sid = id-1

						$("#podr_sel_"+sid+" [value='125']").attr("selected", "selected");

						if (jump=="1") {
							$('#prdm_nm_'+id).select();
						}

						art_num=""
					}
				}
			}
		});
	}

	if (art_num == "d" || art_num == "D" || art_num == "�"  || art_num == "�"){
		$('#art_num_'+id).val("d");
		$('#prdm_name_'+id).val("��������");
		$('#prdm_nm_'+id).val("1")
		$('#prdm_pr_'+id).val("800");

		$('#podr_name_'+id).val("��������");
		sid = id-1
		$("#podr_sel_"+sid+" [value='186']").attr("selected", "selected");
		$('#podr_nm_'+id).val("1");
		$('#podr_pr_'+id).val("800");
		kalk_summ_predm()
	}

	if (art_num == "�" || art_num == "�" || art_num == "n"  || art_num == "N"){
		$('#art_num_'+id).val("n");
		$('#prdm_name_'+id).val("��������� �����������");
		$('#podr_name_'+id).val("��������� �����������");
		sid = id-1
		kalk_summ_predm()
	}
}




function dubl_num(id){
  var opt_val = $("#typ_ord").val();

  if (opt_val == "2") {
    $('#podr_nm_'+id).val($('#prdm_nm_'+id).val());
    kalk_summ_podr();
  }
}

function change_ord_type(nbm){

  if (nbm == "1"){
  $('.frm_art_num').fadeTo(1, 0.5);} else {$('.frm_art_num').fadeTo(0.5, 1);
  }

}

// ����������� ������ ���� ��� ����� �������� �����, ���� name=0 �� ���� ������, ����� ����������� name,count,price
function write_feld_predm(num, art_id, name, count, price, focuss, r_price_our, plus,tip1,tip2) {//10
	console.log("tip1:"+tip1+"/tip2:"+tip2);
  var orderType = $("#typ_ord").val();

  if (orderType == "" && plus == '1') {
    alert("���������� ������� ������� ��� ������!");
    $("#typ_ord").focus();
    return false;
  }
	//frm_art_num
	var i_n=0;
	$( ".vendor_info" ).each(function( index ) {
		console.log($(this).find("span:eq(1)").attr('id'));
		$(this).find("span:eq(1)").attr('id','art_span_'+i_n);
		$(this).find(".frm_art_num").attr('id','art_num_'+i_n);
		$(this).find(".frm_art_num").attr('name','art_num_'+i_n);
		$(this).find("input").attr("onchange","get_art_title("+i_n+",1,0,1)");
		i_n=i_n+1;
	});
	//frm_predm_name
	i_n=0
	$( ".frm_predm_name" ).each(function( index ) {
		
		$(this).attr('id','prdm_name_'+i_n);
		$(this).find("button").attr('id','prdm_sel_sdelka_izd_'+i_n);
		$(this).find("button").attr("onkeyup","kalk_summ_predm();dubl_num("+i_n+")");
		$(this).find("button").attr("onchange","kalk_summ_predm();get_art_title("+i_n+",1,0,1)");
		$(this).find("button").attr("id","prdm_nm_"+i_n);
		i_n=i_n+1;
	});
	//frm_predm_num
	i_n=0
	$( ".frm_predm_num" ).each(function( index ) {
		console.log($(this).attr("onkeyup"));
		$(this).attr("onkeyup","kalk_summ_predm();dubl_num("+i_n+")");
		$(this).attr("onchange","kalk_summ_predm();get_art_title("+i_n+",1,0,1)");
		$(this).attr("id","prdm_nm_"+i_n);
		i_n=i_n+1;
	});
	//frm_predm_price
	//i_n=0
	$( ".frm_predm_price" ).each(function( index ) {
		console.log($(this).prev("input").attr("id"));
		//$(this).prev("input").attr("id","start_prdm_pr_"+i_n);
		//$(this).attr("id","prdm_pr_"+i_n);
		//$(this).closest('td').find("input:eq(2)").attr("id","prdm_pr_our_"+i_n);
		//$(this).closest('td').find("input:eq(3)").attr("id","retail_"+i_n);
		//$(this).closest('td').find("input:eq(4)").attr("id","retail_price"+i_n);
		//$(this).closest('td').find("input:eq(5)").attr("id","col_in_pack_"+i_n);
		//$(this).closest('td').find("input:eq(6)").attr("id","prdm_pr_our_"+i_n);
		//$(this).closest('tr').find('td:eq(4)').find('input').attr("id","prdm_pr_"+i_n);
		//$(this).closest('tr').find('td:eq(5)').find('input').attr("onkeyup","clear_feld_predm("+i_n+")");
		//i_n=i_n+1;
	});
	//tab_podr_main
	i_n=0
	$( "#tddivs .tab_podr_main" ).each(function( index ) {
		//3//4
		$(this).find('td:eq(3)').find("input:eq(0)").attr("id","start_prdm_pr_"+i_n);
		$(this).find('td:eq(3)').find("input:eq(1)").attr("id","prdm_pr_"+i_n);
		$(this).find('td:eq(3)').find("input:eq(2)").attr("id","retail_"+i_n);
		$(this).find('td:eq(3)').find("input:eq(3)").attr("id","retail_price"+i_n);
		$(this).find('td:eq(3)').find("input:eq(4)").attr("id","col_in_pack_"+i_n);
		$(this).find('td:eq(3)').find("input:eq(5)").attr("id","prdm_pr_our_"+i_n);
		i_n=i_n+1;
	});
	//var cnt = $("#tddivs").children('div').length;
	var cnt=i_n;
	// price = price.toFixed(2)
	if ((orderType == "2" || orderType == "3") && name == "0") {
		write_feld_podr2(num,0,0,0,0,0,1);
	}

	if(cnt > 0) {
		//document.getElementById('pl' + cnt).style.visibility = 'hidden';
		$("#pl" + (cnt - 1)).remove();
	}

	if (art_id=="0") {
		art_id=""
	}

	//document.getElementById('pr_feld'+num).style.display = 'block';
	str = '<table border="0" cellspacing="0" cellpadding="3" width=100% class="tab_podr_main"><tr>';

	if (!art_id) {
		fader = "style=\"opacity:0.5\"";
	} else {
		fader = ""
	}

  // ������� � ����������
	str += '<td align="left" width=300 class="vendor_info"'+ (orderType === '1' ? "style=\"display: none\"" : "") +'><span>' + (cnt + 1) + '</span>.&nbsp;&nbsp;<input type=text size=5 '+fader+' name=art_num_'+cnt+' id=art_num_'+cnt+' value=\"'+art_id+'\" class=frm_art_num onchange=get_art_title('+cnt+',1,'+count+',1)> <span id=art_span_'+cnt+'></span>';
	if (art_id && art_id != "d" && art_id != "D") {
		str += '</td>';
	}
	pod1="Tip('�������� ��� ������ � �������')";
	//pod2="Tip('��� ������')";
	
	if ((orderType == "1" || orderType == "3")) {
		dop="display:inline-block;";
	}else{
		dop="display:none;";
	}
	if ((tip1!="" && tip2!="") && (tip1!=0 && (tip2!=0 || tip2==3)) && (tip1!=undefined && tip2!=undefined)){
		console.log(tip1+"|"+tip2);
		dop=dop+"color:green;";
	}else{
		dop=dop+"color:red;";
	}
	//dop='<button  id="prdm_sel_idz_'+cnt+'" data-val="'+((name!=0) ? tip1 : '0')+'" style="'+dop+'margin-left: 1px;height: 20px;width: 20px;text-align: center;padding: 2px;" class="open_izd" onmouseover="'+pod1+'"><i class="fa-sharp fa-regular fa-droplet"></i></button><button id="prdm_sel_sdelka_'+cnt+'" data-val="'+((name!=0) ? tip2 : '0')+'" class="open_sdelka" style="'+dop+'margin-left: 1px;height: 20px;width: 20px;text-align: center;padding: 2px;" onmouseover="'+pod2+'"><i class="fa-regular fa-briefcase"></i></button>';
	dop='<button id="prdm_sel_sdelka_izd_'+cnt+'" data-val2="'+((name!=0) ? tip2 : '0')+'"  data-val1="'+((name!=0) ? tip1 : '0')+'" class="open_sdelka_izd" style="'+dop+'margin-left: 1px;height: 20px;width: 20px;text-align: center;padding: 2px;" onmouseover="'+pod1+'"><i class="fa-regular fa-briefcase"></i></button>';
	str += '<td align="left" width=400><input onmouseover="Tip(\'������������\')" style="width: 340px" id="prdm_name_'+cnt+'" onchange="this.value=replace_str(this.value)" name="predm_nam[]" type=text class="frm_predm_name" value="'+((name!=0) ? name : '')+'" />'+dop+'</td>' +
	'<td align="left" width=100><input onmouseover="Tip(\'����������\')" onkeyup="kalk_summ_predm();dubl_num('+cnt+')" onchange="kalk_summ_predm();get_art_title('+cnt+',1,'+count+',1)" name="predm_num[]"  id=\'prdm_nm_'+cnt+'\' type=text class="frm_predm_num" value="'+((name!=0) ? count : '0')+'" maxlength="10"/></td>' +
	'<td align="left" width=150><input type="hidden"  id=\'start_prdm_pr_'+cnt+'\'  value="'+((name!=0) ? price : '0')+'"/><input onmouseover="Tip(\'���� �� ��.(���)\')" onchange="kalk_summ_predm();" name="predm_price[]" id=\'prdm_pr_'+cnt+'\' type=text class="frm_predm_price" value="'+((name!=0) ? price : '0')+'" maxlength="10" />' +
    '<input id=\'prdm_pr_our_'+cnt+'\' type="hidden" value="' + r_price_our + '"/>'+
    '<input id=\'retail_'+cnt+'\' type="hidden"/><input id=\'retail_price'+cnt+'\' type="hidden"/><input id=\'col_in_pack_'+cnt+'\' type="hidden"/>'+
    '<input id=\'prdm_pr_our_'+cnt+'\' type="hidden" value="' + r_price_our + '"/>'+
	'<td align="left" width=120><input disabled="disabled" onmouseover="Tip(\'�����\')" id=\'prdm_crsm_'+cnt+'\' type=text class="frm_predm_price" value="0" maxlength="50" /></td><td align=center>';


		str += '<div id=\'min'+(num+1)+'\' style="visibility:visible; display:inline; width=15px;"><input class="butt_plus" onclick="clear_feld_predm(' + cnt + '); return false;" name="a" type="button" value="-" /></div>';

		str += '<div id=\'pl'+(cnt)+'\' style="display:inline; width=15px;"><input class="butt_plus plb" onclick="write_feld_predm('+(cnt)+',0,0,0,0,1,0,1,0,0);  return false;" name="a" type="button" value="+" /></div>';

        //num,art_id,name,count,price,focuss,r_price_our,plus

	str += '</td></tr></table>';

	$("#tddivs").append('<div id="dv' + cnt + '">' + str + '</div>');


    //���� ����� ��������, �� ���������� ���� � ����� � ��������� �� ��
     if($.isNumeric(art_id)){
        get_art_title(num,0,count,0)
     }

	num_prdm_next =  cnt - 1
	if (focuss=="1") {
		if (orderType == "2") {
			$('#art_num_' + cnt).focus()
		} else {
			$('#prdm_name_' + cnt).focus()
		}
	}
	$(".open_sdelka_izd").click(function(e){
		var zn_izd=$(this).attr('data-val1');
		var zn_sdelka=$(this).attr('data-val2');
		
		id_p=$(this).attr('id').split("_");
		$("#izd_sdelka_red_id").val(id_p[4]);
		//console.log($("#prdm_name_"+id_p[4]).val());
		$("#zn_pole").text($("#prdm_name_"+id_p[4]).val());
		strq1=$("#prdm_name_"+id_p[4]).val();
		
		if (strq1.indexOf("�����")!=-1 || strq1.indexOf("�����")!=-1 && zn_izd==0){
			$('#select_izd option:contains("������ ��������")').prop('selected', true);
			zn_izd=$('#select_izd').val();
			$(this).attr('data-val1',zn_izd);
		}
		else if (strq1.indexOf("����")!=-1 || strq1.indexOf("����")!=-1 && zn_izd==0){
			$('#select_izd option:contains("������������")').prop('selected', true);
			zn_izd=$('#select_izd').val();
			$(this).attr('data-val1',zn_izd);
		}
		else if (strq1.indexOf("�����")!=-1 || strq1.indexOf("�����")!=-1 && zn_izd==0){
			$('#select_izd option:contains("������������ �������")').prop('selected', true);
			zn_izd=$('#select_izd').val();
			$(this).attr('data-val1',zn_izd);
		}else if(strq1.indexOf("�������")!=-1 || strq1.indexOf("�������")!=-1 && zn_sdelka==0){
			$('#select_sdelka option:contains("��������")').prop('selected', true);
			zn_sdelka=$('#select_sdelka').val();
			$(this).attr('data-val2',zn_sdelka);
		}else if (zn_izd!=0){
			$("#select_izd").val(zn_izd); 
		}else{
			$("#select_izd").val(0); 
		}
		if (zn_sdelka!=0){
			console.log(zn_sdelka);
			$("#select_sdelka").val(zn_sdelka);
		}else{
			$("#select_sdelka").val(0);
		}
		if (zn_sdelka==3){$("#select_izd").prop('disabled', true);$("#prdm_sel_sdelka_izd_"+id_p[4]).css("color","green");$("#prdm_sel_sdelka_izd_"+id_p[4]).attr('data-val1','0');}else{$("#select_izd").prop('disabled', false);}
		$("#select_izd").parents(".wrap").show();
	});
	function check_izd_sdelka(t1,t2,id){
		if (t1!=0 && t2!=0){
			$("#prdm_sel_sdelka_izd_"+id).css("color","green");
			$("#select_izd").css("border","1px solid black");
			$("#select_sdelka").css("border","1px solid black");
		}else if (t1==0 && t2==3){
			$("#select_izd").css("border","1px solid black");
		}else{
			
			$("#prdm_sel_sdelka_izd_"+id).css("color","red");
			if (t1==0){
				$("#select_izd").css("border","2px solid red");
			}
			if (t2==0){
				$("#select_sdelka").css("border","2px solid red");
			}
		}
	}
	$("#select_izd").change(function(e){
		id_zn=$("#izd_sdelka_red_id").val();
		if ($("#select_izd").val()==0){
			$("#select_izd").css("border","2px solid red");
		}
		$("#prdm_sel_sdelka_izd_"+id_zn).attr('data-val1',$("#select_izd").val());
		check_izd_sdelka($("#select_izd").val(),$("#select_sdelka").val(),id_zn);
		//
		
	});
	$("#select_sdelka").change(function(e){
		id_zn=$("#izd_sdelka_red_id").val();
	if ($("#select_sdelka").val()==0){
			$("#select_sdelka").css("border","2px solid red");
		}
		$("#prdm_sel_sdelka_izd_"+id_zn).attr('data-val2',$("#select_sdelka").val());
		if ($("#select_sdelka").val()==3){
			$("#select_izd").prop('disabled', true);
			$("#prdm_sel_sdelka_izd_"+id_zn).attr('data-val1','0');
		}else{
			$("#select_izd").prop('disabled', false);
			$("#select_izd").change();
		}
		check_izd_sdelka($("#select_izd").val(),$("#select_sdelka").val(),id_zn);
	});
		$('.modal_select').draggable({
		start: function() {
            //$('.day_popup').css("width","0%");
			$(".modal_select").css("transform","none");
			//$('.day_popup').css("height","0%");
        }
	});
	$('.modal_amo_crm').draggable({
		start: function() {
            //$('.day_popup').css("width","0%");
			$(".modal_amo_crm").css("transform","none");
			//$('.day_popup').css("height","0%");
        }
	});
	 //����� ���� ��� 
  $(document).mouseup( function(e){ // ������� ����� �� ���-���������
		var div = $( ".modal_select" ); // ��� ��������� ID ��������
		if ( !div.is(e.target) // ���� ���� ��� �� �� ������ �����
		    && div.has(e.target).length === 0 ) { // � �� �� ��� �������� ���������
			div.parents(".wrap").hide(); // �������� ���
		}
	});
	$(document).mouseup( function(e){ // ������� ����� �� ���-���������
		var div = $( ".modal_amo_crm" ); // ��� ��������� ID ��������
		if ( !div.is(e.target) // ���� ���� ��� �� �� ������ �����
		    && div.has(e.target).length === 0 ) { // � �� �� ��� �������� ���������
			div.parents(".modal").hide(); // �������� ���
		}
	});
	$("#typ_ord").change(function(e){
	if ($(this).val()==1 || $(this).val()==3){
		$(".open_sdelka_izd").show(); 
	}else if ($(this).val()==2){
		$(".open_sdelka_izd").hide(); 
		$(".open_sdelka_izd").attr('data-val1','0');
		$(".open_sdelka_izd").css('color','red');
		$(".open_sdelka_izd").attr('data-val2','0');
	}
	else{
		$(".open_sdelka_izd").hide(); 
		
	}
});
	
}






/*
var customOptions = {
    onKeyPress: function (val, e, field, options) {

        if (val.replace(/\D/g, '').length === 2) {

            val = val.replace('8', '');
            field.val(val);
        }
        field.mask("+79999999999", options);
    },
    placeholder: "+7__________"
};

if(edit == 'new'){
    $("#firm_tel").mask("+79999999999", customOptions);
    $("#cont_tel").mask("+79999999999", customOptions);
} */

function standartize_phone(inp){
  old_phone = $("#"+inp).val();
  phone = old_phone.replace(/[^\d]/g, '');

    if(phone.charAt(0) == '8' || phone.charAt(0) == '7'){phone = phone.slice(1);}
       console.log(phone.length)
            if(phone.length > 10){
                alert("��������� ������� ����� ������������� ������. �� ���� ������������� ������� ������ ����������. �� ������ �� ������ � ���� �����������!")
                $("#comment").focus();
            }


  phone = phone.substr(0, 10);

          if(phone.length < 10){
            alert("��������� ������� ������� ��������!");
            $("#"+inp).focus();
            return false
            }
         console.log()
  new_phone = "+7"+phone;
      // console.log(new_phone+" "+phone)
  $("#"+inp).val(new_phone);
}

// ******************** �������������� ����� �������� ����� <<<< *****************  //



// ������������� ����� ���� ����� ������, ����� ����� ����� � ���� ��� �������� �����
function kalk_summ_predm() {

	summ_tmp = 0;
	if(typeof(arr_predm_opl['list'])!="undefined" ) {
		if(typeof(arr_predm_opl['list'][0])!="undefined" ) {
			if(typeof(arr_predm_opl['list'][0].summ)!="undefined" ) {
				for(j=0;j<arr_predm_opl['list'].length;j++) {
					t = replace_zap(arr_predm_opl['list'][j].summ)*1;
					t = replace_price(''+t);
					arr_predm_opl['list'][j].summ = fix_number(t);
					summ_tmp += replace_zap(arr_predm_opl['list'][j].summ)*1;
				}
			}
		}
	}
	if(typeof(arr_predm_opl['list'])=="undefined")
		delete(arr_predm_opl['list']);

    //   console.log(summ_tmp)
	summ_tmp2 = replace_price(''+summ_tmp);
	summ_tmp2 = fix_number(summ_tmp2);

	arr_predm_opl['summ'] = summ_tmp2;

	document.getElementById('predm_opl').innerHTML = summ_tmp2;

	summ = 0;

	arr_all_data['predmet'] = new Array();	// ���������� ������ ���� �������� ��� ����������

	arr_all_data['predmet']['list'] = new Array();

  var orderType = $('#typ_ord').val();

	$("#tddivs .tab_podr_main").each(function(i, elem){
		$elem = $(elem);
		$tid = $elem.find(".frm_art_num").attr('id').split('_');
		var vid = $tid[2];

    arr_all_data['predmet']['list'][i] = new Array();

    if (orderType !== '1') {
      // �������
      arr_all_data['predmet']['list'][i]['art_num'] = document.getElementById('art_num_'+vid).value;
    }
	 if (orderType == '1' || orderType == '3') {
		 arr_all_data['predmet']['list'][i]['tip_izd'] = $('#prdm_sel_sdelka_izd_'+vid).attr('data-val1');
		 arr_all_data['predmet']['list'][i]['tip_sdelki'] = $('#prdm_sel_sdelka_izd_'+vid).attr('data-val2');
	 }

	arr_all_data['predmet']['list'][i]['name'] = document.getElementById('prdm_name_'+vid).value;	// ������������
		a1 = replace_num_acc(document.getElementById('prdm_nm_'+vid).value);
		document.getElementById('prdm_nm_'+vid).value = a1;
		arr_all_data['predmet']['list'][i]['num'] = a1;			// ����������



		a2 = fix_number(replace_price(document.getElementById('prdm_pr_'+vid).value));
		a2 = +a2;
		a2 = a2.toFixed(2);
		document.getElementById('prdm_pr_'+vid).value 	= a2;
		arr_all_data['predmet']['list'][i]['price'] 	= a2;		// ���������

		a33 = fix_number(replace_price(document.getElementById('prdm_pr_our_'+vid).value));
		a33 = +a33;
		a33 = a33.toFixed(2);
		document.getElementById('prdm_pr_our_'+vid).value 	= a33;
		arr_all_data['predmet']['list'][i]['price_our'] 	= a33;		// �������������

		sm_cr = replace_zap(a1)*replace_zap(a2);		// ��� ������ �������� - ���������� * ���������

		sm_cr2 = fix_number(sm_cr);
		document.getElementById('prdm_crsm_'+vid).value = sm_cr2;

		summ+=sm_cr;

	});

	summ = summ.toFixed(1);



	dolg = (summ - arr_predm_opl['summ']).toFixed(2);

	dolg = replace_price(''+dolg);

	dolg = fix_number(dolg);
	arr_all_data['predmet']['dolg'] = dolg;					// ����
	document.getElementById('predm_dolg').innerHTML = dolg;
	summ = replace_price(''+summ);
	summ = fix_number(summ);		// �������� ����� ����� �������, ���� ����� �����
	arr_all_data['predmet']['summ_acc'] = summ;		// ����� �����
    //alert(summ)
	document.getElementById('summ_val').innerHTML = summ;

}



// <<<<<<<<< **********************************************************************  //
// <<<<<<<<< ***************************  ����������  *****************************  //
// <<<<<<<<< **********************************************************************  //

//	������� ���������� ���� �����������
function clear_feld_podr(num) {
	var cnt = $("#tddivs2").children('div').length;
	// ���� �������� ����� �� �������, �����


	if (cnt == 1) {
		return false;
	}

	$("#pdr" + num).remove();


	var last_i = 0;
	$("#tddivs2").children('div').each(function(i, elem) {
		last_i = i;
		$(elem).attr('id', "pdr" + i);
		$(elem).find("select").attr("id", "podr_sel_" + i);
		$(elem).find("a:first").attr("onclick", "curr_num_podr=" + i + "; show_add_podr();return false");

		$(elem).find("div.podr_oseb").attr("id", "podr_oseb_" + i);

		$(elem).find("div.podr_dolg").attr("id", "podr_dolg_" + i);
		//$(elem).find(".butt_plus").attr("onclick", "clear_feld_predm(" + i + ")");
		$(elem).find("td:first").find("span:first").text(i + 1);

		$(elem).find(".frm_podr_cost1").attr("id", "podr_pr_" + i);
		$(elem).find(".frm_podr_num1").attr("id", "podr_nm_" + i);


	});


	if ($("#tddivs2").find(".plb").length == 0) {
		$("#pdr" + last_i + " td:last").append('<div class="plb" id="podr_pl' + last_i + '" style="visibility:visible; display:inline; width:15px;"><input onmouseover="Tip(\'�������� ������������\')" class="butt_plus" onclick="write_feld_podr2(' + last_i + ',0,0,0,0,0,1); return false;" name="a" type="button" value="+" /></div>');
	}

	num_podr--;
	kalk_summ_podr();		// ������� ���� ���� ����� ��� ��������
}

// ���������� ����� ������
function write_feld_podr2(num,podr_id,name,numm,price,numacc,focusss,hide){
	var cnt = $("#tddivs2").children('div').length;

	if (numm > 0) {
		podr_oseb =  numm *  price;
		podr_oseb = podr_oseb.toFixed(2)
		podr_dolg   =  numm *  price;
		podr_dolg = podr_dolg.toFixed(2)
	} else {
		podr_dolg = "0";
		podr_oseb = "0";
	}

	if(cnt > 0) {
		$('#podr_pl' + (cnt - 1)).remove();
	}

	if (numacc == 0) {

		numacc = '';

	}

	//document.getElementById('pd_feld' + num).style.display = 'block';

	dis = (tpacc) ? '' : ' disabled="disabled" '

	alt_note = 'Tip(\'<textarea onmouseover=\\\'this.focus();\\\' class=alt_podr_note1 name=textfield cols=50 wrap=physical></textarea><br><input class=alt_podr_note_butt type=button value=\\\'���������\\\' />\',DELAY,400,TITLE,\'<div class=stat_podr_alttit>����������</div>\',STICKY,1,CLOSEBTN,1,OFFSETY,-5, OFFSETX,-5,TEXTALIGN, \'right\')';

	if (hide == 1) {
		str = '<table style="display: none;" border="0" cellspacing="0" cellpadding="3" class="tab_podr_main" width=100%><tr>';
	} else {
		str = '<table border="0" cellspacing="0" cellpadding="3" class="tab_podr_main" width=100%><tr>';
	}
	str += '<td  width=20 align=center><span>' + (cnt + 1) + '</span>.</td>' +
	'<td align="left" valign="middle"  width=200>' +
	'<select id="podr_sel_' + cnt + '" class="frm_podr_podr1" name="podr_podr[]">' +
	'<option value="0">�� ���������</option>' +
	'</select> <a href="#" onclick="curr_num_podr=' + cnt + '; show_add_podr();return false"><img width="16" height="16" vspace="5" onmouseover="Tip(\'����� ���������\')" src="../i/plus.gif" valign="middle" style="vertical-align:middle;"/></a></td>' +

	'<td class="td_podr_podr1" width=350>' +
	'<input class="frm_podr_name1" onchange="this.value=replace_str(this.value)" name=\'podr_nam[]\' type=text  id="podr_name_' + cnt + '"  value="' + ((name != 0) ? name : '') + '" maxlength="255" /></td>' +

	'<td width=90 align=center><input onmouseover="Tip(\'����������\')" onkeyup="kalk_summ_podr()" class="frm_podr_num1" name="podr_num[]" id="podr_nm_'+cnt+'" onchange="kalk_summ_podr()" type=text value="'+((name!=0) ? numm : '0')+'" maxlength="65" /></td>' +

	'<td align="left" width=90 align=center><input onmouseover="Tip(\'���� (���)\')" id="podr_pr_'+cnt+'" onchange="kalk_summ_podr();" name="podr_price[]" type=text class="frm_podr_cost1" value="'+((name!=0) ? price : '0')+'" maxlength="50" /></td>';

	str += '<td align="center" width=120 align=center><div class="podr_oseb" id="podr_oseb_'+cnt+'">'+podr_oseb+'</div></td>';





	str += '<td align="center" width=120><div id=\'podr_min'+(cnt)+'\' style="visibility:visible; display:inline; width=15px;"><input class="butt_plus" onclick="clear_feld_podr('+cnt+'); return false;" name="a" type="button" value="-" /></div>';

	if (cnt < 100) {
		str += '<div id=\'podr_pl'+(cnt)+'\' style="display:inline; width:15px;"><input onmouseover="Tip(\'�������� ������������\')" class="butt_plus plb" onclick="write_feld_podr2('+(cnt+1)+',0,0,0,0,0,1); return false;" name="a" type="button" value="+" /></div>';
	}
	str += '</td></tr></table>';
	podr_oseb = "0";
	kalk_summ_podr();

	//$("#tddivs2").append('<div id="' + podr_id + num + '">' + str + '</div>');
	$("#tddivs2").append('<div id="pdr' + cnt + '">' + str + '</div>');
	//document.getElementById('pd_feld'+num).innerHTML = str;
	if((num_podr) > arr_podr_opl.length) {
	}

	if(num_podr>1)
		document.getElementById('podr_min1').disabled = false;

	num_podr++;
	podr_set_sel(cnt,podr_id);


var opt_val = $('#typ_ord').val()
num_podr_next =  num_podr - 1
if(opt_val == "1" && focusss == "1") {$('#podr_name_'+cnt).focus()}

}

// ---- �������� ������ ����������� ----
function doLoadPodr() {

	var req3 = new JsHttpRequest();
	req3.onreadystatechange = function() {
		if (req3.readyState == 4) {
						arr_podr = req3.responseJS.res;
						podr_set_sel_all();						// �������� �������� ��� ���� select-�� �����������
						podr_fl_ready = (podr_fl_ready == 0) ? 1 : 2;
				}
	}
	req3.open(null, '../backend/back_LoadContr.php', true);
	req3.send( { t: 1 } );
}

// >>>>>*************** ���������� � ���� ������� **********************  //


// �������� ���� ��� ���������� ������ ����������
function show_add_podr() {
	window1= open("/acc/query/win_new_podr.php", "displayWindow","width=370,height=140,status=no,toolbar=no,titlebar=no,menubar=no,screenX=400,screenY=350");
}


// ��� ������� �� ��� ���������� ������ ����������
function add_predm_name(name,pers,tel,mail){
	doLoadPredmSave(name,pers,tel,mail);
}

// ������� � ������� ��������� ����� � ������ - ����������� ���������
function set_new_podr() {
	document.getElementById('podr_sel_'+curr_num_podr).selectedIndex = (document.getElementById('podr_sel_'+curr_num_podr).length-1);
}

// ---- ������������ ���������� ������ ���������� ----
function doLoadPredmSave(name,pers,tel,mail) {
	var req2 = new JsHttpRequest();
	req2.onreadystatechange = function() {
		if (req2.readyState == 4) {
						new_id = req2.responseJS.id;					// �� ������ ����������
						new_name = req2.responseJS.name;			// �������� ������ ����������
						podr_add_sel_all(new_name,new_id);		// ���������� ������ ���������� �� ��� �������
						set_new_podr();		// ��� ������� � ������� ���������� ��������� ������� ��� ���������
				}
	}
	req2.open(null, '../backend/back_SaveContr.php', true);
	req2.send( { name: name, pers: pers, tel: tel, mail: mail } );
}

// �������� �������� ��� ���� select-�� �����������
function podr_set_sel_all() {
	for(var j=0;j<(num_podr-1);j++) {
		podr_set_sel(j,0);
	}
}

// �������� �������� � ���� select ����� ����� num �����������
function podr_set_sel(num,sel) {
	document.getElementById('podr_sel_'+num).options[0] = new Option('�� ���������', 0);
	for(var i=0;i<arr_podr.length;i++) {
		document.getElementById('podr_sel_'+num).options[i+1] = new Option(arr_podr[i]['name'], arr_podr[i]['id']);
		if(sel == arr_podr[i]['id'])	// ��������� ���������� ��������
			document.getElementById('podr_sel_'+num).options[i+1].selected = true;
	}
}

// ���������� ������ ���������� �� ��� select ����� � � ����� ������ ��������
function podr_add_sel_all(name,val) {
	arr_podr[arr_podr.length] = {'id' : val, 'name' : name}; // �������� � ����� ������� ������ ����������
	for(var j=0;j<(num_podr-1);j++) {		// �������� �� ��� �������
		document.getElementById('podr_sel_'+j).options[document.getElementById('podr_sel_'+j).length] = new Option(name, val);
	}
}


// >>>>>*************** ���������� � ���� ������� **********************  //

// <<<<<<<< ********** �������������� ����� ���������� *****************  //




// ������������� ����� ���� ����� ������, ����� ����� � ������������� ��� �����������
function kalk_summ_podr() {
	var cnt = $("#tddivs2").children('div').length;

	summ = 0;
	summ_opl_tot = 0;
	summ_dolg_tot = 0;

	arr_all_data['podr'] = new Array();			// ���������� ������ ���� �������� ��� ����������

//	alert('wadaw');
	arr_all_data['podr']['list'] = new Array();
	for(var i=0;i<(cnt);i++) {
	//for(var i=0;i<(num_podr-1);i++) {

		arr_all_data['podr']['list'][i] = new Array();
	   if($('#podr_sel_'+i).is(':visible')){
		arr_all_data['podr']['list'][i]['podr'] = document.getElementById('podr_sel_'+i).value;		// ��������� (id)
		}

		// ����������� ������������ ���������� � ������ ����������
		sel = document.getElementById('podr_sel_'+i);
		if (sel != null) {
			for(k=0;k<sel.length;k++) {
				if(sel.options[k].selected) {
					arr_all_data['podr']['list'][i]['podr_name'] = sel.options[k].text;												// ��������� (������������)
					break;
				}
			}
		}

		var podrInput = document.getElementById('podr_name_'+(i));
        if (podrInput) {
            arr_all_data['podr']['list'][i]['name'] = podrInput.value;	// ������������
        }

		summ_tmp = 0;

		if(typeof(arr_podr_opl[i])!="undefined" ) {
			if(typeof(arr_podr_opl[i]['list'])!="undefined" ) {
				if(typeof(arr_podr_opl[i]['list'][0])!="undefined" ) {
					for(j=0;j<arr_podr_opl[i]['list'].length;j++) {
						if(typeof(arr_podr_opl[i]['list'][j].summ)!="undefined" ) {
							t = replace_zap(arr_podr_opl[i]['list'][j].summ)*1;
							t = replace_price(''+t);
							t = fix_number(t);					// ���������� �� 2� ���� ����� �������
							arr_podr_opl[i]['list'][j].summ = t;

							summ_tmp += t*1;

						}
					}
				}
			}
			else
				delete(arr_podr_opl[i]['list']);
		}
		else
			arr_podr_opl[i] = new Array();


		summ_tmp = fix_number(replace_price(summ_tmp));

		summ_opl_tot += summ_tmp*1;

		arr_podr_opl[i]['summ'] = summ_tmp;

		summ_tmp2 = fix_number(replace_price(summ_tmp));


		a1 = fix_number(replace_price(document.getElementById('podr_pr_'+(i)).value));
		arr_all_data['podr']['list'][i]['price'] = a1;																	// ����
		document.getElementById('podr_pr_'+(i)).value = a1;

		a2 = replace_num_acc(document.getElementById('podr_nm_'+(i)).value);
		arr_all_data['podr']['list'][i]['num'] = a2;																		// ����������
		document.getElementById('podr_nm_'+(i)).value = a2;

		// ����� ������������� ��� ������� ����������, �� �����������, ������ ������������
		a3 = fix_number(replace_price(a1 * a2));
		if (document.getElementById('podr_oseb_'+(i)) != null) {
			document.getElementById('podr_oseb_'+(i)).innerHTML = a3;
		}

		summ_curr = fix_number(replace_zap(a1)*replace_zap(a2));

		dolg = fix_number(replace_price(summ_curr - arr_podr_opl[i]['summ']));

		arr_all_data['podr']['list'][i]['dolg'] = fix_number(dolg);																	// ����

		summ_dolg_tot+= 1*replace_zap(dolg);

		if (document.getElementById('podr_dolg_'+i) != null) {
			document.getElementById('podr_dolg_'+i).innerHTML = dolg;
		}

			//document.getElementById('podr_dolg_'+i).innerHTML = dolg;

		summ+=summ_curr*1;

	}

	summ = fix_number(replace_price(summ));
	summ_dolg_tot = fix_number(replace_price(summ_dolg_tot*1));
	arr_all_data['podr']['sebist'] = summ;																// ����� �������������
	document.getElementById('summ_val_predm').innerHTML = summ;


}

function change_shipped_status(query_id){

if($("#shipped").is(':checked')){shipped = '1';}else{shipped = '0';}

                    if($.isNumeric(query_id) && $.isNumeric(shipped)) {

                            $.ajax({
                                type: 'GET',
                                url: '../backend/change_shipped_status.php?shipped='+shipped+'&query_id='+query_id,
                                dataType: 'html',
                                async: false,
                                success: function(data){idata = data;}
                            });

                    }
}
function izm_sdelka_amo(){
	$(".modal_amo_crm button").prop('disabled', true);
	var id_quers=$("#izd_sdelka_red_id_amo").val();
	var id_sdelki_amo=$("#search_sdelka_amo").val();
	if (id_quers!=null && id_quers!="" && id_sdelki_amo!=null && id_sdelki_amo!=''){
		//���� �� id_sdelki_amo � AmoCrm
		 $.ajax({
                                type: 'GET',
                                url: '../backend/amo_search.php?id_amo='+id_sdelki_amo+'&query_id='+id_quers+'&tip=1',
                                dataType: 'json',
                                async: false,
                                success: function(data){
										if (data.result==1){
											$("body").overhang({
											  type: "success",
											  message: "����� ������ � AmoCrm"
											});
											setTimeout(function(){ window.location="index.php"; },2000);
										}else{
											$("body").overhang({
											  type: "error",
											  message: "������ ����� ������"
											});
											$(".modal_amo_crm button").prop('disabled', false);
										}
									}
                            });
	}else{
		$("body").overhang({
				  type: "error",
				  message: "������ ����� ������(��������� id ������)"
				});
	}
}
function new_sdelka_amo(){
	
	var summ_itog=$("#summ_val").text();
	//
	var name_cont=encodeURIComponent($("#client").val());
	//var name_cont=$("#client").val();
	var phone=$("#firm_tel").val();
	var phone2=$("#cont_tel").val();
	var email=$("#email").val();
	//��������� url �� get (����� ��� ������ �����)
	//���� �����
	var id_quers=$("#izd_sdelka_red_id_amo").val();
	$(".modal_amo_crm button").prop('disabled', true);
	//var mas_towar='towar='+arr_all_data;
	$.ajax({
                                type: 'GET',
                                url: '../backend/amo_search.php?query_id='+id_quers+'&tip=2&summ_itog='+summ_itog+'&name_cont='+name_cont+'&phone='+phone+'&phone2='+phone2+'&email='+email,
                                dataType: 'json',
                                async: false,
                                success: function(data){
									console.log(data);
										if (data.result==1){
											$("body").overhang({
											  type: "success",
											  message: "����� ������ � AmoCrm"
											});
											setTimeout(function(){ window.location="index.php"; },2000);
										}else{
											$("body").overhang({
											  type: "error",
											  message: "������ ����� ������"
											});
											$(".modal_amo_crm button").prop('disabled', false);
										}
									},error: function (jqXHR, exception) {
										$("body").overhang({
											  type: "error",
											  message: "������ ����� ������ T1 - �"+id_quers
											});
											$(".modal_amo_crm button").prop('disabled', false);
									}
                            });
	//
	
}


$("#client_short").focus();