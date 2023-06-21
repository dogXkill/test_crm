//$("#everything").children().hide();
var params = window
    .location
    .search
    .replace('?','')
    .split('&')
    .reduce(
        function(p,e){
            var a = e.split('=');
            p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
            return p;
        },
        {}
    );





function show_hide_app_type_flds(app_type){
 // alert("test")

if(!app_type){app_type = $("#app_type").val();}
art_id_new_checked = $("#art_id_new").prop('checked');

$("#everything").children().hide();

//if(app_type == "start"){show_arr = ["user_id_span", "app_type_span"]}

//заказная продукция (колонка с с/с в заказе не отображается)
if(app_type == "1"){
$("#art_id").val("");
$("#art_uid").val("");
show_arr = ["email_mas_otp","comment","user_id_span", "app_type_span", "ClientName_span", "preview_span", "zakaz_id", "old_title", "izd_type_span", "shelko_span", "num_ord_span", "tiraz_span", "size_span", "izd_v_span", "izd_b_span", "izd_material_span", "izd_lami_span", "paper_col_ext_span", "tisnenie_span", "paper_num_list_span", "luve_span", "izd_ruchki_span", "ruchki_dop_polya", "gluing_material_span", "strengt_bot_span", "pack_span", "deadline_span", "resperson_material_span","resperson_pechat_span", "spec_req_span", "utv_pech_list_span", "utv_got_izd_span", "org_param_span", "tech_param_span", "job_rate_box", "sborka_type_span", "save_but", "print_view_but", "open_job_span"];
}
//серийная продукция
if(app_type == "2" || app_type == "start"){

$("#ClientName").val("");
$("#zakaz_id").val("");


show_arr = ["user_id_span", "app_type_span", "art_id_span", "old_title", "izd_type_span", "num_ord_span", "tiraz_span", "size_span", "izd_v_span", "izd_b_span", "izd_material_span", "izd_lami_span", "paper_col_ext_span", "paper_num_list_span", "luve_span", "izd_ruchki_span", "ruchki_dop_polya", "gluing_material_span", "strengt_bot_span", "pack_span", "deadline_span", "resperson_material_span","resperson_pechat_span", "spec_req_span", "org_param_span", "tech_param_span", "job_rate_box", "sborka_type_span", "ss_span", "save_but","print_view_but","plan_span", "open_job_span"];

if(art_id_new_checked == true){show_arr.push("add_art_add_flds");}

}
//сторонний заказ
if(app_type == "3"){
show_arr = ["email_mas_otp","comment","user_id_span", "app_type_span", "num_ord_span", "art_id_span", "save_but", "print_view_but"];
}
//шелкография
if(app_type == "4"){
show_arr = ["email_mas_otp","comment","dressing_span","user_id_span", "size_span", "izd_material_span", "izd_ruchki_span", "ruchki_dop_polya", "app_type_span", "num_ord_span", "ClientName_span", "preview_span", "shelko_span", "shelko_span_art", "izd_type_span", "tiraz_span", "paper_col_ext_span", "save_but", "deadline_span", "spec_req_span", "utv_got_izd_span", "job_rate_box", "pack_span", "print_view_but", "open_job_span"];
}
$("#everything").show();

jQuery.each(show_arr, function() {
span_id = this

$("#"+span_id).show("200");
});



}

function hide_izd_type_flds(){

//перед тем как прятать показываем все
show_hide_app_type_flds()

  //прячем ненужные поля в зависимости от типа изделия
izd_type = $("#izd_type").val();
var hide_arr = ''
//конверты
if(izd_type == "16"){
  hide_arr = ["izd_b_span", "luve_span", "izd_ruchki_span", "strengt_bot_span", "paper_num_list_span", "podvorot_klapan_span", "sborka_type_span", "ruchki_dop_polya"];
}
//коробки
if(izd_type == "5"){
  hide_arr = ["luve_span", "izd_ruchki_span", "paper_num_list_span", "podvorot_klapan_span", "strengt_bot_span", "sborka_type_span"];
}
//кашир коробки
if(izd_type == "23"){
  hide_arr = ["luve_span", "izd_ruchki_span", "paper_num_list_span", "podvorot_klapan_span", "strengt_bot_span", "sborka_type_span"];

  //костыль, но надо ведь как то ставить спец тариф на кашированные коробки
  if($("#rate_4").val() == "" || $("#rate_4").val() == "0"){$("#rate_4").val("20");}$("#rate_4").val("20");
}
//ручки с клипсами
if(izd_type == "8"){
  hide_arr = ["jump_span", "izd_v_span", "izd_b_span", "izd_gramm_span", "color_inn_span", "luve_span", "izd_ruchki_span", "paper_num_list_span", "podvorot_klapan_span", "izd_lami_span", "strengt_bot_span", "gluing_material_span", "org_param_span", "tech_param_span", "sborka_type_span", "utv_pech_list"];
}
//ящики
if(izd_type == "22"){
  hide_arr = ["luve_span", "strengt_bot_span", "gluing_material_span", "paper_num_list_span", "podvorot_klapan_span", "izd_gramm_span", "izd_lami_span", "org_param_span", "tech_param_span", "sborka_type_span", "utv_pech_list"];
}
//наклейки замочки
if(izd_type == "18"){
  hide_arr = ["izd_v_span", "luve_span", "strengt_bot_span", "gluing_material_span", "paper_num_list_span", "podvorot_klapan_span", "izd_gramm_span", "izd_lami_span", "org_param_span", "tech_param_span", "izd_ruchki_span", "ruchki_dop_polya", "sborka_type_span"];
}

//наклейки замочки
if(izd_type == "35"){
  hide_arr = ["luve_span", "izd_ruchki_span", "paper_num_list_span", "podvorot_klapan_span", "strengt_bot_span", "sborka_type_span"];
  }

if(hide_arr !== ""){
jQuery.each(hide_arr, function() {
span_id = this
$("#"+span_id).hide();
});
}


}

$("#app_type").on('change', function() {
  show_hide_result_files();
});

function show_hide_result_files() {
  var app_type = $("#app_type").val();
  console.log('tapp_type = ' + app_type);
  if (app_type == '1' || app_type == '4') {
    if ($("#result_files")) {
      $("#result_files").css('display', 'block');   
      $("#result_files_input").on('change', function() {
          check_result_files();
      });
    }  
  }  
  if ($("#uid").val()=='' || $("#uid").val()==0){$("#hand_txt_bl").hide();}
}
$(document).ready(function() {
  show_hide_result_files();
});

console.log(params);

//получаем данные из существующей заявки
function get_app_data(uid){
$.ajax({
  type: "GET",
  url: "backend/get_app_data.php",
  data: "uid="+uid,
  success: function(app_arr){
unserialize_str(app_arr, '1')
}
});

}

var tek_tir;
var tek_izd_w;
var tek_izd_v;
var tek_izd_b;
var tek_stamp_num;
var tek_deadline;
var tek_num_ord;
var tek_izd_material;
var tek_izd_gramm;
var tek_shelko_num_colors;
var tek_shelko_prokatok;
var tek_shelko_storon;
var tek_shelko_art;
var tek_material_comment;
var tek_vip;
var tek_tisnenie;
var tek_luve;
var tek_izd_ruchki;
var tek_hand_txt;
var tek_izd_color_inn;
var tek_color_pantone;
var tek_izd_type;
var tek_izd_color;
var tek_dressing;
var tek_on_izd;
var tek_hand_thick;
var tek_hand_length;
var tek_hand_type;
var tek_hands_krepl;
var tek_hand_color;
var tek_pack;
var tek_strengt_bot;
var tek_strengt_side;
var tek_col_in_pack;
var tek_paper_num_list;
var tek_paper_list_typ;
var tek_gluing_material;
var tek_sborka_type;
var tek_in_material=[];
var tek_general=[];
var tek_dops=[];
var tek_shelko=[];
var obj_str;
//mas_izm=["tiraz","app_type","num_ord","ClientName","text_on_izd","izd_type","shelko_num_colors","izd_w","izd_v","izd_b"];//поля для проверки
//mas_izm=["uid", "num_ord", "art_id", "art_uid",  "user_id", "tiraz", "limit_per", "dat_ord", "deadline", "deadline_stamp", "deadline_material", "izd_w", "izd_v", "izd_b", "klapan", "podvorot", "stamp_num", "izd_color", "color_pantone", "izd_color_inn", "izd_material", "material_suppl", "izd_gramm", "material_comment", "virub_isdely_per_list", "izd_ruchki", "list_h", "list_w", "isdely_per_list", "paper_suppl", "sborka_type", "no_sborka", "paper_num_list", "paper_list_typ", "luve", "izd_lami", "izd_lami_storon", "izd_virub_storon", "lami_isdely_per_list", "tisnenie", "izd_tisn_storon", "col_ottiskov_izd", "tisn_comment", "hands_krepl", "hand_thick", "hand_color", "hand_length", "hand_type", "hand_txt", "gluing_material", "strengt_bot", "strengt_side", "pack", "col_in_pack", "spec_req", "resperson_material", "rate_1", "rate_2", "rate_3", "rate_4", "rate_5", "rate_6", "rate_7", "rate_8", "rate_9", "rate_10", "rate_11", "rate_12", "rate_13", "rate_14", "rate_15", "rate_16", "rate_17", "rate_18", "rate_19", "rate_20", "rate_21", "rate_22", "rate_23", "rate_24", "rate_25", "rate_26", "rate_27", "rate_28", "rate_30", "rate_31", "app_type", "shelko_art", "shelko_num_colors", "shelko_prokatok", "shelko_storon", "izd_type", "ClientName", "text_on_izd", "preview_link", "zakaz_id", "vip","dressing", "plan_in", "stamp_order", "klishe_order", "shnur_order", "utv_pech_list", "utv_got_izd", "utv_ruchki"]
var mas_izm=new Array();
var mas_name=new Array();
mas_tek=[];
//эта функция заполняет форму массивом переданным через строку вида - &title=Пакет 30x40x13, белый матовый&izd_type=4&izd_w=30&izd_v=40
function unserialize_str(str, force_inp){
//obj_str=str;
var app_arr = str.split('&');

var len = app_arr.length-1
if(len > 1){

for (var i=0; i <= len; i++)
{
	//console.log(app_arr[i]);
	 
  var app_arr_n = app_arr[i].split('=');
  console.log(app_arr_n[0]+"-"+app_arr_n[1]);
  if (app_arr_n[0]!=""){
  mas_izm[app_arr_n[0]]=app_arr_n[1];
  mas_name[i]=app_arr_n[0];}
  /*$.each(app_arr_n,function(index,value){
	  //console.log("a:"+app_arr_n[index]);
	  //console.log("v:"+value);
	  //mas_izm[value[0]]=value[1];
	  console.log(index+"-"+value);
	  $.each(value,function(key,vals){
		  console.log("K:"+key+" v:"+vals);
	  });
  });*/
  if (app_arr_n[0]=='tiraz'){tek_tir=app_arr_n[1];}
  if (app_arr_n[0]=='izd_w'){tek_izd_w=app_arr_n[1];}
  if (app_arr_n[0]=='izd_v'){tek_izd_v=app_arr_n[1];}
  if (app_arr_n[0]=='izd_b'){tek_izd_b=app_arr_n[1];}
  if (app_arr_n[0]=='stamp_num'){tek_stamp_num=app_arr_n[1];}
  if (app_arr_n[0]=='deadline'){tek_deadline=app_arr_n[1];}
  if (app_arr_n[0]=='num_ord'){tek_num_ord=app_arr_n[1];}
  if (app_arr_n[0]=='izd_material'){tek_izd_material=app_arr_n[1];}
  if (app_arr_n[0]=='izd_gramm'){tek_izd_gramm=app_arr_n[1];}
  if (app_arr_n[0]=='shelko_num_colors'){tek_shelko_num_colors=app_arr_n[1];}
  if (app_arr_n[0]=='shelko_prokatok'){tek_shelko_prokatok=app_arr_n[1];}
  if (app_arr_n[0]=='shelko_storon'){tek_shelko_storon=app_arr_n[1];}
  if (app_arr_n[0]=='vip'){tek_vip=app_arr_n[1];}
  if (app_arr_n[0]=='izd_type'){tek_izd_type=app_arr_n[1];}
  if (app_arr_n[0]=='dressing'){tek_dressing=app_arr_n[1];}
  if (app_arr_n[0]=='text_on_izd'){tek_on_izd=app_arr_n[1];}
  if (app_arr_n[0]=='shelko_art'){tek_shelko_art=app_arr_n[1];}
  if (app_arr_n[0]=='material_comment'){tek_material_comment=app_arr_n[1];}
  if (app_arr_n[0]=='izd_color'){tek_izd_color=app_arr_n[1];}
  if (app_arr_n[0]=='color_pantone'){tek_color_pantone=app_arr_n[1];}
  if (app_arr_n[0]=='izd_color_inn'){tek_izd_color_inn=app_arr_n[1];}
  if (app_arr_n[0]=='izd_ruchki'){tek_izd_ruchki=app_arr_n[1];}
  if (app_arr_n[0]=='hand_txt'){tek_hand_txt=app_arr_n[1];}
  if (app_arr_n[0]=='hand_thick'){tek_hand_thick=app_arr_n[1];}
  if (app_arr_n[0]=='hand_length'){tek_hand_length=app_arr_n[1];}
  if (app_arr_n[0]=='hand_type'){tek_hand_type=app_arr_n[1];}
  if (app_arr_n[0]=='hands_krepl'){tek_hands_krepl=app_arr_n[1];}
  if (app_arr_n[0]=='hand_color'){tek_hand_color=app_arr_n[1];}
  if (app_arr_n[0]=='pack'){tek_pack=app_arr_n[1];}
  if (app_arr_n[0]=='col_in_pack'){tek_col_in_pack=app_arr_n[1];}
  if (app_arr_n[0]=='luve'){tek_luve=app_arr_n[1];}
  if (app_arr_n[0]=='strengt_bot'){tek_strengt_bot=app_arr_n[1];}
  if (app_arr_n[0]=='strengt_side'){tek_strengt_side=app_arr_n[1];}
  if (app_arr_n[0]=='strengt_tip'){tek_strengt_side=app_arr_n[1];}
  if (app_arr_n[0]=='tisnenie'){tek_tisnenie=app_arr_n[1];}
  if (app_arr_n[0]=='paper_num_list'){tek_paper_num_list=app_arr_n[1];}
  if (app_arr_n[0]=='paper_list_typ'){tek_paper_list_typ=app_arr_n[1];}
  if (app_arr_n[0]=='gluing_material'){tek_gluing_material=app_arr_n[1];}
  if (app_arr_n[0]=='sborka_type'){tek_sborka_type=app_arr_n[1];}
  //tek_in_material
  if (app_arr_n[0]=='list_h'){tek_in_material['list_h']=app_arr_n[1];}
  if (app_arr_n[0]=='list_w'){tek_in_material['list_w']=app_arr_n[1];}
  if (app_arr_n[0]=='isdely_per_list'){tek_in_material['isdely_per_list']=app_arr_n[1];}
  if (app_arr_n[0]=='izd_lami_storon'){tek_in_material['izd_lami_storon']=app_arr_n[1];}
  if (app_arr_n[0]=='izd_virub_storon'){tek_in_material['izd_virub_storon']=app_arr_n[1];}
  if (app_arr_n[0]=='izd_tisn_storon'){tek_in_material['izd_tisn_storon']=app_arr_n[1];}
  if (app_arr_n[0]=='stamp_order'){tek_in_material['stamp_order']=app_arr_n[1];}
  if (app_arr_n[0]=='deadline_stamp'){tek_in_material['deadline_stamp']=app_arr_n[1];}
  if (app_arr_n[0]=='resperson_material'){tek_in_material['resperson_material']=app_arr_n[1];}
  if (app_arr_n[0]=='deadline_material'){tek_in_material['deadline_material']=app_arr_n[1];}
  if (app_arr_n[0]=='shnur_order'){tek_in_material['shnur_order']=app_arr_n[1];}
  if (app_arr_n[0]=='klishe_order'){tek_in_material['klishe_order']=app_arr_n[1];}
  if (app_arr_n[0]=='lami_isdely_per_list'){tek_in_material['lami_isdely_per_list']=app_arr_n[1];}
  if (app_arr_n[0]=='virub_isdely_per_list'){tek_in_material['virub_isdely_per_list']=app_arr_n[1];}
  if (app_arr_n[0]=='no_sborka'){tek_in_material['no_sborka']=app_arr_n[1];}
  //general
  if (app_arr_n[0]=='rate_1'){tek_general['rate_1']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_7'){tek_general['rate_7']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_2'){tek_general['rate_2']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_8'){tek_general['rate_8']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_11'){tek_general['rate_11']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_14'){tek_general['rate_14']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_4'){tek_general['rate_4']=app_arr_n[1];}
  //dops
  if (app_arr_n[0]=='rate_15'){tek_dops['rate_15']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_17'){tek_dops['rate_17']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_18'){tek_dops['rate_18']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_19'){tek_dops['rate_19']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_23'){tek_dops['rate_23']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_24'){tek_dops['rate_24']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_22'){tek_dops['rate_22']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_21'){tek_dops['rate_21']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_20'){tek_dops['rate_20']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_16'){tek_dops['rate_16']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_28'){tek_dops['rate_28']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_29'){tek_dops['rate_29']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_30'){tek_dops['rate_30']=app_arr_n[1];}
  //shelko
  if (app_arr_n[0]=='rate_25'){tek_shelko['rate_25']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_26'){tek_shelko['rate_26']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_27'){tek_shelko['rate_27']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_31'){tek_shelko['rate_31']=app_arr_n[1];}
  if (app_arr_n[0]=='ClientName'){console.log("name:"+app_arr_n[1]);}
  
 if (app_arr_n[0]=='resperson_pechat'){
	 if (app_arr_n[1]==4){
		 //$("#deadline_pechat_span").hide();
		 $("#deadline_pechat").prop("disabled", true);
				//$("#deadline_pechat_span_1").hide();
	 }
 }
if (app_arr_n[0]=='dat_ord'){
	//заполняем дату
	let new_dat=app_arr_n[1].split(" ");
	let format_dat=new_dat[0].split("-");
	$("#data_ord").text(format_dat[2]+"."+format_dat[1]+"."+format_dat[0]);
	
}
if (app_arr_n[0]=='comment'){
	//заполняем комментарий
	let comment=app_arr_n[1];
	uid=$("#uid").val();
	btn_com="<div class='none_print'><input type='button' id='new_comment' onclick='comment_app_dialog("+uid+");' value='Добавить комментарий'></input></div>";
	$("#comment").html(comment+"</br>"+btn_com);
	
}

//проверяем тип элемента формы, если текст то заполянем, если чекбокс то ставим галочку
if($("#"+app_arr_n[0]).attr('type') == "checkbox"){
  if(app_arr_n[1] == "1"){$("#"+app_arr_n[0]).attr("checked","checked");}else{$("#"+app_arr_n[0]).removeAttr("checked");}
}
else if($("#"+app_arr_n[0]).is('select') == true){

//заполняем селекты таким образом. Иначе, если они пустые, то создается новый опшн валуе, который выделен автоматом
if(app_arr_n[1] == "" || app_arr_n[1] == "0" || !$.isNumeric(app_arr_n[1])){$("#"+app_arr_n[0]).children().first().attr('selected','selected');
//console.log(app_arr_n[0] + " значение " + app_arr_n[1]);
}else{
$("#"+app_arr_n[0]).val(app_arr_n[1]);
}
}else{
if(app_arr_n[0] == 'rate_4'){
//тк цена сборки содержится в поле rate_4 подгружаемом из базы, дублируем это значение в ознакомительное поле при загрузке страницы
$("#sborka_cost_oznak").val(app_arr_n[1]);
}
//заполняем только если поле пустое или нет принудительного заполнения уже заполненных полей в качестве аргумента функции, т.к. есть например тарифы, которые в старых заявках не проставлены...
if($("#"+app_arr_n[0]).val() == "" || force_inp == "1"){$("#"+app_arr_n[0]).val(String(app_arr_n[1]).replace("<||>","&"));

}
}
//показываем / прячем слови в зависимости от типа заявки
 if(app_arr_n[0] == "app_type"){
   app_type = app_arr_n[1];
   show_hide_app_type_flds(app_arr_n[1]);
   //если тип заказа - серийка, то нельзя поменять на заказ, т.к. не будет привязки к заказу
   if(app_arr_n[1] == '1' && user_type !== 'sup'){$("#app_type").prop("disabled", true);}
   }

//если заявка старая, то рядом пишем старый тайтл
 if(app_arr_n[0] == "title" && app_arr_n[1] !== "" && app_type == "1"){$("#old_title").html(app_arr_n[1]);}

//если присутствует артикул, то рядом даем ссылки его редактирование на сайте и показываем кнопку сверки с сайтом
 if(app_arr_n[0] == "art_uid" && $.isNumeric(app_arr_n[1])){
 $("#art_link").hide().html("<a href=\"https://www.paketoff.ru/shop/view/?id="+app_arr_n[1]+"\" style=\"font-weight:bold;\" target=\"_blank\"><img src=\"/i/pkf.gif\"></a> <a href=\"https://www.paketoff.ru/admin/shop/goods_list/edit/?id="+app_arr_n[1]+"\" target=\"_blank\"><img src=\"../../i/editbut.png\"></a>").show("500");
 $("#art_id").css("background-color", "#cecece").attr('readonly','readonly');
 }

//если пакет без ручек, то прячем дополнительные поля по ручкам
 if(app_arr_n[0] == "izd_ruchki" && app_arr_n[1] == "6"){$("#ruchki_dop_polya").hide();}

//если пакет из 1 листа, то прячем ненужный селект
 if(app_arr_n[0] == "paper_num_list" && app_arr_n[1] !== "2"){$("#paper_list_typ_span").hide();}
if(app_arr_n[0] == "paper_num_list" && app_arr_n[1] == "2"){$("#paper_list_typ_span").show();}
 //если пакет без тиснения, то прячем дополнительные поля по тиснению
if(app_arr_n[0] == "tisnenie" && app_arr_n[1] == ""){$("#tisnenie_dop_polya").hide(); $("#tisn_storon_span").hide();}

  //просто обнуляем поле r_price_our, забыл зачем
 if(app_arr_n[0] == "r_price_our"){$("#r_price_our").val("");}

  //просто клапан пустой, то проставляем знаение по умолчанию
 if(app_arr_n[0] == "podvorot" && app_arr_n[1] == ''){$("#podvorot").val("5");}

  //просто клапан пустой, то проставляем знаение по умолчанию
 if(app_arr_n[0] == "klapan" && app_arr_n[1] == ''){$("#klapan").val("20");}

if(app_arr_n[0] == 'user_id'){
//если пользователь не админ и заявка не твоя, то блокируем все поля формы

user_id = app_arr_n[1]

}
if (app_arr_n[0]=='hand_txt'){
	console.log("TTTT:"+uid+"|"+app_arr_n[1]);
	if (app_arr_n[1]=='' || uid==0 || uid==''){$("#hand_txt_bl").hide();}
}


}
for (var i=0;i<mas_izm.length;i=i+1){
	console.log(mas_izm[i]+"|"+$("#"+mas_izm[i]+"").val());
}
}else{alert("Ошибка! Не получены данные по заявке!"+app_arr)}


//block_save_button(user_id, uid)
hide_show_compare_buts()
hide_izd_type_flds()

obr_comment($('#spec_req'),t_font_size,max_f3);
$("#stamp_order").change();
		 $("#resperson_material").change();
obj_str=$("#forma").serializeArray();
}

function hide_show_compare_buts(){
if($.isNumeric($("#art_id").val())){
$("#compare_flds_but").show();
$("#compare_ss_but").show();
}else{
$("#compare_flds_but").hide();
$("#compare_ss_but").hide();}
}

function new_art_form(){
if($("#art_id_new").is(":not(:checked)")){
$("#art_id").css("background-color", "white");
$("#art_id").prop("disabled", false);
$("#add_art_add_flds").hide();
$("#art_id_span_al").html("");
$("#additional_flds").html("");
$("#primechanie").html("");
$("#price_our").html("");
$("#price").html("");
$("#save_but_exit").prop("disabled", false);
$("#save_but_print").prop("disabled", false);
}else{
$("#art_id").val("");
$("#art_check").html("");
$("#art_id").css("background-color", "#cecece");
$("#art_id_span_al").html("<br>ниже появится кнопка добавления нового артикула");
$("#add_art_add_flds").show();
$("#save_but_exit").prop("disabled", true);
$("#save_but_print").prop("disabled", true);

}}

function check_new(){
if($("#art_id_new").prop("checked") && $.isNumeric($("#art_id").val())){$("#art_id_new").removeAttr("checked");}

}


function add_art_site(){
//массив полей, которые необходимо проверить на заполненность, перед добавлением артикула
izd_type = $("#izd_type").val();
if(izd_type == "4"){
check_arr = "tiraz,izd_type,izd_w,izd_material,izd_lami,izd_color,izd_color_inn,paper_num_list,col_in_pack,price,price_our";}
else{
check_arr = "tiraz,izd_type,izd_w,izd_material,izd_lami,izd_color,col_in_pack,price,price_our";
}

err = check_some_fields(check_arr, '0');
console.log(err)
if(err == "0"){
var str = $("#forma").serialize();
$.post("backend/add_art_site.php?act=add&"+str, function( reply ) {
reply_arr = reply.split(";");
art_id = reply_arr[0];
art_uid = reply_arr[1];
if($.isNumeric(art_id) && $.isNumeric(art_uid)){
$("#art_id").val(art_id);
$("#art_id").css("background-color", "#cecece");
$("#new_art_span").html("<span style=\"font-weight:bold;color:#33CC00\">Добавлен новый артикул!</b> <a href=\"https://www.paketoff.ru/shop/view/?id="+art_uid+"\" style=\"font-weight:bold;\" target=\"_blank\">"+art_id+"</a> <a href=\"https://www.paketoff.ru/admin/shop/goods_list/edit/?id="+art_uid+"\" target=\"_blank\"><img src=\"../i/edit3.gif\"></a>");
$("#art_uid").val(art_uid);
$("#save_but_exit").prop("disabled", false);
$("#save_but_print").prop("disabled", false);
$("#add_art_site_but").prop("disabled", true);
$("#art_id_span_al").html("<br>Артикул уже добавлен на сайт!");
$("#art_id_span_al").css("color", "green");
}else{alert("ошибка! "+reply) }
//$("#res").html(reply);
});
}
hide_show_compare_buts()
}


//функция получает информацию об артикуле с сайта и заполняет ей форму при помощи функции  unserialize_str()
function get_art_info(act, fld, value){
art_id = $("#art_id").val();
new_ss = $("#r_price_our").val();

if($.isNumeric(art_id)){
if(act == "get_data"){$("#art_link").html("<img src=\"../../i/load.gif\">");}

$.post("backend/get_art_info.php?act="+act+"&new_ss="+new_ss+"&art_id="+art_id+"&fld="+fld+"&val="+value, function( str ) {
}).done(function(str) {

if(act == "check"){
  //alert(str)
 if(str !== ""){
    $("#art_check").html("<br><span class=\"warning\">Внимание! Есть незакрытые (выполненные менее чем на 95%) заявки на этот артикул: "+str+"</span>");
  }else{$("#art_check").html("");}
}

if(act == "get_data"){
  $("#art_link").html("");
  //console.log(str);
  unserialize_str(str, '1');
}

//сравниваем введенные пользователем данные в заявке с данными сайта. В идеале, чтобы они совпадали
if(act == "compare_flds"){
//console.log(str)
compare_flds(str)
}

if(act == "compare_ss"){
$("#ss_site").html("На сайте: "+str);
compare_ss(str)
}

if(act == "change_ss" && $.isNumeric(str)){
  alert("Себестоимость на сайте приведена в соответствие со сметой - "+r_price_our)
}

if(act == "change_fld"){
  if($.isNumeric(str)){return "ok";}else{return str;}
  //console.log(str+' test')
}

});}}


//сравниваем текущую с/с выданную программой с сайтом. Если есть существенное отличие, предлагаем изменить ее на сайте тут же
function compare_ss(site_ss){
r_price_our = $("#r_price_our").val();
if($.isNumeric(site_ss) && $.isNumeric(r_price_our)){
if(site_ss == r_price_our){$("#compare_ss_span").html("Стоимость на сайте верна");}else{
$("#compare_ss_span").html("");
if (confirm("Валовая с/с на сайте - "+site_ss+" а по смете - "+r_price_our+". Хотите обновить валовая с/с на сайте сейчас?")){
get_art_info('change_ss')
}}}}


//сравниваем данные заявки на серийку с данными с сайта, при необходимости меняем их
function compare_flds(str){
  console.log(str)

 //проверяем поступающую строку на наличия символа &, если его нет, значит либо артикул удален либо данные с сайта не поступают
if(str.indexOf('&') > 0 ){
//создаем ассоциативный массив из полей сайта для дальнейшей циклической проверки
str_arr = str.split('&');
//console.log($.isArray([str_arr]))

var len = str_arr.length-1
if(len > 1){
var fld_arr = new Array ();
for (var i=0; i <= len; i++)
{
//console.log(str_arr[i])
str = str_arr[i].split('=');
fld_arr[str[0]] = str[1];

}
}


err = 0;
compared_flds = 'izd_v,izd_w,izd_b,izd_type,izd_material,izd_gramm,izd_lami,izd_color,izd_color_inn,paper_num_list,luve,izd_ruchki,hand_length,hand_thick,hand_color,hands_krepl,gluing_material,pack,col_in_pack,list_h,list_w,isdely_per_list';
var compared_flds_arr = compared_flds.split(',');


var len = compared_flds_arr.length-1
if(len > 1){
 for (var i=0; i <= len; i++)
{
//проверяемое поле
var fld = compared_flds_arr[i];
//текущая щаявка
app_val = $("#"+fld).val();

//с сайта
site_val = fld_arr[fld];
 //console.log(fld+' '+site_val)
if(app_val !== "" && app_val !== site_val){
err = err + 1;
if (confirm('Поле '+fld+' в заявке - \"'+app_val+'\" а на сайте - \"'+site_val+'\". Изменить данные на сайте?')){
res = get_art_info('change_fld', fld, app_val);
if(res == "ok"){alert("Данные изменены!")}

}}
//console.log(fld+'- -'++'- -'+app_val )

}

if(err == 0){alert("Данные, которые ЗАДАНЫ в заявке соответствуют данным сайта!")}
}}else{alert("Данные с сайта не поступают. Либо такого артикула нет, либо ошибка в номере id.")}}


//этот скрипт призван помогать в заполнении формы
function hlp(act,ish_inp,final_inp){
if(act == "same"){
ish_val = $('#'+ish_inp).val();
$('#'+final_inp+' option[value="'+ish_val+'"]').prop('selected', true);
}
else{
$('#'+final_inp+' option[value="'+act+'"]').prop('selected', true);
}


}

function jump(jumpfrom, maxsize, jumpto){
if($("#jumpoff").is(":not(:checked)")){
maxsize = maxsize-1
if ($('#'+jumpfrom).val().length > maxsize){$('#'+jumpto).select();
$('#'+jumpto).focus();
}}}


function search_similar_art(prinudit){

never_show = $("#similar_arts_never_show").val();
if(never_show !== "1" || prinudit == "1"){
app_type = $("#app_type").val();
izd_type  = $("#izd_type").val();
izd_w = $("#izd_w").val();
izd_v = $("#izd_v").val();
izd_b = $("#izd_b").val();
izd_material = $("#izd_material").val();
izd_lami = $("#izd_lami").val();
izd_color = $("#izd_color").val();

if(app_type == "2" && izd_w !== "" && izd_v !== ""){
if($("#similar_arts_div").is(':hidden'))
{$("#similar_arts_div").show("500");}

var base_url = "https://www.paketoff.ru/modules/admin/shop/backend/search_similar_art.php?";
var url = base_url+"&izd_w="+izd_w+"&izd_v="+izd_v+"&izd_b="+izd_b+"&izd_material="+izd_material+"&izd_lami="+izd_lami+"&izd_color="+izd_color+"&izd_type="+izd_type;
$("#similar_arts_div").html('<iframe border=\"0\" height=\"250\" width=\"450\" id=new_art frameborder=0 scrollbar=\"auto\" style=\"resize: both; overflow: auto;\" src=\"'+url+'\"></iframe><br><span style=\"color:red;cursor:pointer\" onclick=\"close_similar(\'just_close\')\">закрыть!</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"color:red;cursor:pointer\" onclick=\"close_similar(\'close_forever\')\">закрыть и больше не показывать</span>');
}else {
$("#similar_arts_div").hide("500");
$("#similar_arts_div").html('');
}}

}

function close_similar(type){
$("#similar_arts_div").html("");
$("#similar_arts_div").hide("500");
if(type == "close_forever"){$("#similar_arts_never_show").val("1");}
}


//$("#deadline").datepicker($.datepicker.regional[ "ru" ]);


//запускаем проверку формы при любом изменении полей формы
$("input,select,checkbox,radio").change(function(e){

hide_show_compare_buts()

show_hide_rezvorot()


//у менеджера не доступен расчет с/с поэтому, у него эта функция глючит, поэтому делаем проверку типа пользователя
//if(user_type == 'sup' || user_type == 'acc')

calc_ss()
set_view()

});


function show_hide_rezvorot(){


izd_w = $("#izd_w").val()*1;
izd_v = $("#izd_v").val()*1;
izd_b = $("#izd_b").val()*1;
klapan = 2;
podvorot = $("#podvorot").val()*1;

if(izd_w && izd_v && izd_b){
//ширина и высота целого
shirina_celogo = klapan + izd_w*2 + izd_b*2;
vysota_celogo = podvorot + izd_v + (izd_b/2+2);


$("#razvorot_cely").html("Целый: <b>"+shirina_celogo+"</b> x <b>"+vysota_celogo+"</b>см");

//ширина и высота половинки
shirina_pol = klapan + izd_w + izd_b;
vysota_pol = podvorot + izd_v + (izd_b/2+2);
$("#razvorot_half").html("Половинка: <b>"+shirina_pol+"</b> x <b>"+vysota_pol+"</b>см");
$("#razvorot").fadeIn("500");

}else{
$("#razvorot_cely").html("");
$("#razvorot_half").html("");}


}


function count_shelko_colors(){
shelko_prokatok = eval($("#shelko_num_colors option:selected").text());
 if($.isNumeric(shelko_prokatok)){
   $("#shelko_prokatok").val(shelko_prokatok);
 }
}

function calc_ss(){
	console.log('zap');
//сериализируем форму и с помощью цикла, создаем переменные с соответствующими именами
var str = $("#forma").serialize();
var material_cost = $("#material_cost").val();
var price_per_print = $("#price_per_print").val();

var str = str.split('&');
var len = str.length-1
//превращаем массив в переменные со значениями
for (var i=0; i <= len; i++)
{
  var str_n = str[i].split('=');
   if($.isNumeric(str_n[1]) && str_n[1] > -1){
   window[str_n[0]] = str_n[1]*1;
  }else{window[str_n[0]] = str_n[1];}
  }


//лист явно не может стоить больше 1000р, поэтому сразу меняем на тонну
if(material_cost > 1000){$('#material_cost_type option[value="per_tonn"]').prop('selected', true); material_cost_type = "per_tonn";}
else{$('#material_cost_type option[value="per_list"]').prop('selected', true); material_cost_type = "per_list";}

price_per_lami_film = lami_arr[izd_lami];
gluing_material_cost = glue_arr[gluing_material];

//вес листа
ves_lista = (list_h*list_w/10000*izd_gramm).toFixed(0)
$("#one_list_weight").val(ves_lista);

//считаем сколько листов нужно
list_total = (tiraz / isdely_per_list).toFixed(0);
if($.isNumeric(list_total)){$("#list_total").val(list_total);}

//вес материала
list_weight = (list_total * ves_lista/1000).toFixed(2);
if($.isNumeric(list_weight)){$("#list_weight").val(list_weight);}

//ламинация прогонов
lamin_total = (tiraz / lami_isdely_per_list).toFixed(0);


if($.isNumeric(lamin_total)){$("#lamin_total").val(lamin_total);}

//вырубка ударов
virub_total = (tiraz / virub_isdely_per_list).toFixed(0);
if($.isNumeric(virub_total)){$("#virub_total").val(virub_total);}

//цена за лист
if(material_cost_type == "per_tonn"){
//сколько листов в тонне
lists_in_tonn = 1000000 / ves_lista;
price_per_list = (material_cost / lists_in_tonn).toFixed(2);
}
if(material_cost_type == "per_list"){
price_per_list = material_cost;
}

if($.isNumeric(price_per_list)){$("#price_per_list").val((price_per_list / isdely_per_list).toFixed(2));}

//стоимость ламинации
if(izd_lami == "" || izd_lami == "0" || lami_isdely_per_list == ""){
$("#price_per_lami").val("0");
$("#price_per_lami_film").val("0");
price_per_lami = "0";
}else{
ploshad_lami = ((list_total * list_h * list_w)/10000).toFixed(0);
price_per_lami = (price_per_lami_film * ploshad_lami/tiraz + rate_1/lami_isdely_per_list).toFixed(2);
if($.isNumeric(price_per_lami)){$("#price_per_lami").val(price_per_lami);}
if($.isNumeric(price_per_lami_film)){$("#price_per_lami_film").val(price_per_lami_film);}
}

//стоимость вырубки на изд включая приладку
price_per_virub = ((virub_total * rate_2 + rate_7)/tiraz).toFixed(2);
if($.isNumeric(price_per_virub)){$("#price_per_virub").val(price_per_virub);}

//клеевой материал на пакет (проклейка дна + проклейка боковины + проклейка трубы * кол-во листов)
podvorot = $("#podvorot").val()*1;
//с изм. данными 
izd_w_t=$("#izd_w").val();
izd_v_t=$("#izd_v").val();
izd_b_t=$("#izd_b").val();
gluing_material_per_izd = ((izd_w_t * 2) + (izd_w * 0.7 * 2) + (podvorot + izd_v + izd_b / 2) * paper_num_list).toFixed(0);

if($.isNumeric(gluing_material_per_izd)){$("#gluing_material_per_izd").val(gluing_material_per_izd);}
price_gluing_material_per_izd = (gluing_material_cost * gluing_material_per_izd / 100).toFixed(2);
if($.isNumeric(price_gluing_material_per_izd)){$("#price_gluing_material_per_izd").val(price_gluing_material_per_izd);}

//проставляем стоимость сборки в отдельной функции
sborka_cost = set_sborka_cost(gluing_material_per_izd);

//суммируем стоимость некоторых допработ
dopraboty_total = rate_11 + rate_19 + rate_21 + rate_23;

$("#dopraboty_cost").val(dopraboty_total);

//ставим норму прибыли, если не задано иное

price_our = (price * 0.8).toFixed(2);
if ($.isNumeric(price_our)){
$("#price_our").val(price_our);
}

//желательно потом такие вещи убрать в отдельные функции
//если пакет из двух листов - выбираем одинаоквые или разные листы
if(paper_num_list == "1" || paper_num_list == ""){
//$("#paper_list_typ_span").hide("500");
$("#paper_list_typ").val("1");
}
//else{$("#paper_list_typ_span").show("500");}

//если пакет без ручек, то прячем дополнительные поля по ручкам
 if(izd_ruchki == "6"){$("#ruchki_dop_polya").hide();}else{$("#ruchki_dop_polya").show();}

//если пакет без тиснения, то прячем дополнительные поля по тисненнию
// if(tisnenie == ""){$("#tisnenie_dop_polya").hide(); $("#tisn_storon_span").hide();  }else{$("#tisnenie_dop_polya").show(); $("#tisn_storon_span").show(); }

//с/с изделия
r_price_our = (price_per_list*1/isdely_per_list + price_per_print*1 + price_per_lami*1 + price_per_virub*1 + rate_4*1 + price_gluing_material_per_izd*1 + price_per_ruchki*1 + dopraboty_total*1 + orgrashodi_cost*1).toFixed(2);
if($.isNumeric(r_price_our)){$("#r_price_our").val(r_price_our);}

}
var izm_glag=0;
//изменение тарифы при изменение полей
//change = calc_ss()
$("#izd_w").on("change",function(e){
	izm_glag=1;
	calc_ss();
	
});
//izd_v
$("#izd_v").on("change",function(e){
	izm_glag=1;
	calc_ss();
	
});
//izd_b
$("#izd_b").on("change",function(e){
	izm_glag=1;
	calc_ss();
	
});
//sborka_type
$("#sborka_type").on("change",function(e){
	izm_glag=1;
	calc_ss();
	
});
//paper_num_list
$("#paper_num_list").on("change",function(e){
	izm_glag=1;
	calc_ss();
	
});
//izd_ruchki
$("#izd_ruchki").on("change",function(e){
	izm_glag=1;
	calc_ss();
	
});

function set_view(){
var str = $("#forma").serialize();

//желательно потом такие вещи убрать в отдельные функции
//если пакет из двух листов - выбираем одинаоквые или разные листы
if(paper_num_list == "1" || paper_num_list == ""){
$("#paper_list_typ_span").hide("500");
$("#paper_list_typ").val("1");
}else{$("#paper_list_typ_span").show("500");}

//если пакет без ручек, то прячем дополнительные поля по ручкам
 if(izd_ruchki == "6" || izd_ruchki == ""){$("#ruchki_dop_polya").hide(); }else{$("#ruchki_dop_polya").show(); }



//если пакет без тиснения, то прячем дополнительные поля по тисненнию
 if(tisnenie == ""){$("#tisnenie_dop_polya").hide(); $("#tisn_storon_span").hide();  $("#klishe_order_span").hide(); }else{$("#tisnenie_dop_polya").show(); $("#tisn_storon_span").show(); $("#klishe_order_span").show();}
}


function set_sborka_cost(gluing_material_per_izd){
console.log('zapSET:'+gluing_material_per_izd);
izd_type = $("#izd_type").val();
uid = $("#uid").val();
rate_4 = $("#rate_4").val()*1;
//автоматически ставим тариф на сборку только если тип изделия = пакет и это новая заявка. Если это делать со старыми заявками, то поплывут старые тарифы и соответственно расчеты.
if(izd_type == "4" && (uid == '' || $.isNumeric(rate_4) == false || rate_4 == 0)){//если новый
//новый
//автоматически определяем стоимость сборки
base_cost = 3*1;
cost_per_meter = '0.014'*1;

//тип сборки оцениваем
sborka_k = $("#sborka_type").val()*1;

//надбавка за ручки
hand_type = $("#hand_type").val();
hand_type_cost = hand_types_arr[hand_type]*1;



sborka_cost = (base_cost + gluing_material_per_izd * 1 * cost_per_meter) * sborka_k + hand_type_cost;
sborka_cost = sborka_cost.toFixed(2);
rate_4 = sborka_cost
console.log("sborka"+sborka_cost);

$("#rate_4").val(sborka_cost);

$("#sborka_cost_oznak").val(sborka_cost);

}else if (uid != '' && izd_type == "4" && izm_glag==1){
	//сохранённый 
	base_cost = 3*1;
cost_per_meter = '0.014'*1;

//тип сборки оцениваем
sborka_k = $("#sborka_type").val()*1;

//надбавка за ручки
hand_type = $("#hand_type").val();
hand_type_cost = hand_types_arr[hand_type]*1;



sborka_cost = (base_cost + gluing_material_per_izd * 1 * cost_per_meter) * sborka_k + hand_type_cost;
sborka_cost = sborka_cost.toFixed(2);
rate_4 = sborka_cost
console.log("sborka"+sborka_cost);

$("#rate_4").val(sborka_cost);

$("#sborka_cost_oznak").val(sborka_cost);
izm_glag=0;
}


//return sborka_cost;
}


function show_tarif(type){
  $('#'+type).toggle(250)
}

function replace_zap(v) {
  v = v.replace(',', '.');
  v = v.replace(' ', '');
  return v;
}

function replace_num(v) {
  var reg_sp = /[^\d^.]*/g;		// вырезание всех символов кроме цифр и точки
  v = v.replace(reg_sp, '');
  return v;
}

//первым параметром передаем список полей, которые обязательно проверить, а вторым - нажата ли кнопка Сохранить заявку, т.к. данная проверка запускается из разных мест
function check_some_fields(check_arr, save){
//console.log(check_arr)

if($("#art_id_new").is(":checked") && $("#art_id").val() == "" && save == '1'){alert("Вы поставили галочку что артикул новый, однако, сам артикул на сайте пока не создали. Создайте артикул кликнув на кнопку \"Добавить артикул на сайт\"")
err = '1';}
else{
var check_arr = check_arr.split(',');
//перебираем все поля и проверяем заполненность, если не заполнено, то делаем фокус, если все заполнено, то продолжаем выполнять функцию
jQuery.each(check_arr, function() {
input_id = this

console.log(input_id)

if(input_id == 'rate_4'){
//проверяем чтобы тариф на сборку был проставлен
rate_4 = $("#rate_4").val()*1;
//смотрим тип заявки, т.к. проверять тариф на сборку надо только если собирается пакет по заявке на заказную или серийную
app_type = $("#app_type").val();

if(($.isNumeric(rate_4) == false || rate_4 == 0) && (app_type == '1' || app_type == '2')){
$("#rate_4").focus().css('border-width','2px').css('border-color','red'); err = '1';
show_tarif('general')
alert("Поле стоимость сборки должно быть заполнено правильно. Для его корректного отображения, необходимо заполнить все технические параметры изделия, влияющие на стоимости сборки, либо проставить тариф вручную.")
return false}else{$("#"+input_id).css('border-width','1px').css('border-color','#cecece').css('border-color','red'); err = '0';}

}else{

val = $("#"+input_id).val();
if(val == ""){$("#"+input_id).focus().css('border-width','2px').css('border-color','red'); err = '1';
return false }else{$("#"+input_id).css('border-width','1px').css('border-color','#cecece'); err = '0';}


}



});



}
return err;
}

//функция подсвечивает заданные поля и убирает выделение при необходимости
function highlight_flds(fld, act){
if(act == "1")
$("#"+fld).focus().css('border-width','2px').css('border-color','red');
else
$("#"+fld).css('border-width','1px').css('border-color','green');
}

function go_to_link(){
    zakaz_id = $("#zakaz_id").val();
    window.open("/acc/query/query_send.php?show="+zakaz_id, '_blank');
}

function isFileChanged() {
  $("#preview_photo").attr("data-changed", 1);
}
document.getElementById("preview_photo").addEventListener("change", isFileChanged);
function load_commnet(){
	uid=$("#uid").val();
	btn_com="<div class='none_print'><input type='button' id='new_comment' onclick='comment_app_dialog("+uid+");' value='Добавить комментарий'></input></div>";
	
	$.ajax({
			 url: 'backend/comment.php',
			 data: '&num_ord='+$("#num_ord").val()+'&act=get_comment',
			 dataType: 'text',
			 type: 'GET',
			 success: function(data) {
				 $("#comment").html(data+"</br>"+btn_com);
			 }
		   });
}
function comment_save_izm(text_izm){
	//смотрим галочку на отправку email 
	email_check=$("#email_mas_otp_check1").is(':checked');//true/false
 num_ord = $("#uid").val();
 user_name_full = user_name+' '+user_surname;
 comment=text_izm;
 if ($.isNumeric(num_ord))
   $.ajax({
     url: 'backend/comment.php',
     data: '&num_ord='+num_ord+'&act=save_comment1&comment='+comment+'&user_name_full='+user_name_full+'&email_check='+email_check,
     dataType: 'text',
     type: 'GET',
     success: function(data) {
         // alert(data)
		 
     if(data == 1){
         //$("#comment_div_text").html("");
		 
		   
      //if(comment == ""){$("#comment_but_"+num_ord).attr("src","../i/comment.png");}else{$("#comment_but_"+num_ord).attr("src","../i/comment_is.png");}
      }else{console.log(data)}
	  //обновление комментариев
	  //load_commnet();
     }
   });
   load_commnet();
}
var name_dop=[];
name_dop['rate_25']='приладка 1 цвет';
name_dop['rate_26']='нанесение на готовый пакет (1 цвет)';
name_dop['rate_27']='нанесение на лист или трубу (1 цвет)';
name_dop['rate_31']='перевязка ручек';
//dops 
name_dop['rate_15']='ручки с клипсами (производство)';
name_dop['rate_17']='нарезка шнура на станке';
name_dop['rate_18']='нарезка ленты (шнура) вручную';
name_dop['rate_19']='нарезка дна и боковин';
name_dop['rate_23']='сверление';
name_dop['rate_24']='установка люверсов';
name_dop['rate_22']='вставка ручек с клипсами';
name_dop['rate_21']='привязка шнура на узелок';
name_dop['rate_20']='привязка ленты узелок/бант';
name_dop['rate_16']='переупаковка';
name_dop['rate_28']='подрезка облоя';
name_dop['rate_29']='плетение шнура';
name_dop['rate_30']='допобработка';
//
name_dop['rate_1']='ламинация';
name_dop['rate_7']='приладка вырубки';
name_dop['rate_2']='вырубка';
name_dop['rate_2']='приладка тиснение/конгрев';
name_dop['rate_3']='тиснение';
name_dop['rate_11']='упаковка';
name_dop['rate_14']='выдача надомнику';
name_dop['rate_4']='сборка';

/*
//dops
  if (app_arr_n[0]=='rate_15'){tek_dops['rate_15']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_17'){tek_dops['rate_17']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_18'){tek_dops['rate_18']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_19'){tek_dops['rate_19']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_23'){tek_dops['rate_23']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_24'){tek_dops['rate_24']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_22'){tek_dops['rate_22']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_21'){tek_dops['rate_21']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_20'){tek_dops['rate_20']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_16'){tek_dops['rate_16']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_28'){tek_dops['rate_28']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_29'){tek_dops['rate_29']=app_arr_n[1];}
  if (app_arr_n[0]=='rate_30'){tek_dops['rate_30']=app_arr_n[1];}
  */
  function check_visible(obj){
	  return $(obj).is(":visible");
  }
  //
  
  //
function check_izm_com(mas_zn_com){

var text_izm="";
console.log(mas_zn_com);
console.log('----test----');
 for (var key in mas_zn_com) {
	 
		//text_izm=text_izm+"Изменил данные в заявке - "+obj_str[key].name+"|;|";
		//obj_str[key].value=mas_new[key].value;
		if (mas_zn_com[key]!=""){
			//console.log(mas_zn_com[key]);
			text_izm=text_izm+"Изменил данные в заявке - "+mas_zn_com[key]+"|;|";
		}
 }


	if (text_izm!=""){
		comment_save_izm(text_izm);
		text_izm="";
	}
	
	
	
	
}
function check_izm(){
	//mas_izm
	//console.log(mas_izm);
	
	for (var key in mas_izm) {
	 
	  if ($("#"+key).val()!=mas_izm[key] && $("#"+key).val()!=undefined){
		   console.log("KEY:"+key+":"+$("#"+key).val()+"|"+mas_izm[key]);
	  }
	}
}
function save_app(){

//базовый массив полей, которые необходимо проверить на заполненность, перед сохранением заявки
app_type = $("#app_type").val();
izd_type = $("#izd_type").val();
tisnenie = $("#tisnenie").val();
col_ottiskov_izd = $("#col_ottiskov_izd").val();
izd_material = $("#izd_material").val();
izd_gramm = $("#izd_gramm").val();
rate_4 = $("#rate_4").val()*1;

preview_changed = 0;

if (app_type != 2) {
  var warned = $("#preview_photo").attr("data-warned");
  if (warned == 0) {
    $("#preview_photo").attr("data-warned", 1);
    if ( ($("#preview_photo").val() == null || $("#preview_photo").val() == '') && $("#preview_photo").attr("data-exist") == 0) {
    alert('Поле "Файл превью" не заполнено. Заявка будет сохранена без него!');
    }
  }
}


//базовые поля для проверки меняем в зависимости от типа изделия
if(izd_type == '4'){
check_arr = "tiraz,izd_type,izd_w,izd_material,izd_lami,sborka_type,paper_num_list,izd_ruchki,hand_length,hand_thick,hands_krepl,hand_color,gluing_material,col_in_pack,rate_4,stamp_order,shnur_order";
}
//коробка
else if(izd_type == '5' || izd_type == '32' || izd_type == '11'){
check_arr = "tiraz,izd_type,izd_w,izd_material,izd_lami,col_in_pack,stamp_order";
}
//конверт
else if(izd_type == '16'){
check_arr = "tiraz,izd_type,izd_w,izd_material,izd_lami,col_in_pack,stamp_order";
}
else{
check_arr = "tiraz,izd_type";
}
//у каждого типа заявки есть свои поля, обязательные для заполнения
//у заказов например нужно указать zakaz_id, client_name
if(app_type == "1"){
check_arr = "ClientName,text_on_izd,deadline,resperson_material,resperson_pechat,"+check_arr;
}
if(app_type == "2"){
check_arr = "art_id,,list_h,list_w,isdely_per_list,virub_isdely_per_list,deadline,resperson_material,resperson_pechat,"+check_arr;
}
if(app_type == "3"){
check_arr = "art_id";
}
if(app_type == "4"){
check_arr = "tiraz,ClientName,text_on_izd,shelko_num_colors,deadline,";
}

//если предусмотрено тиснение, то кол-во оттисков должно быть заполнено
if(tisnenie !== "" && (col_ottiskov_izd == "" || col_ottiskov_izd == "0")){highlight_flds("col_ottiskov_izd", "1"); return false}else{highlight_flds("col_ottiskov_izd", "0");}

if(tisnenie !== ""){
check_arr = "klishe_order,"+check_arr;
}

err = check_some_fields(check_arr, '1');


if(err == "0"){
//поле которое disabled не сериализуется, поэтому, разблокируем его пока делаем сериализацию
$("#app_type").prop("disabled", false);
var str = $("#forma").serialize();
if (obj_str){
//добавление полей checkbox//
var $data = {};
$('#forma').find ('input[type=checkbox]').each(function() {
  $data[this.name] = $(this).val();
  //console.log(this.id+"|"+$(this).val());
  if ($(this).is(':checked')){flag_ch=1;}else{flag_ch=0;}
  //console.log('name:'+this.name+'(id:'+this.id+') display:'+$(this).is(':hidden'));
  if ($(this).is(':hidden')==false){
	  //обьект видим (значит надо записать изменение)
	  //смотрим массив на значения ,если таких нету - добавляем 
	  flag_poisk=0;
	  name_p=this.name;
	  console.log("pole"+name_p);
	  id_p=this.id;
	   $.each(obj_str, function (index, value) {
		  //console.log(value['name']);
		  
		  if (value['name']==name_p){//ищем его в старвом массиве (если был ==1)
			  //console.log("pole1:"+name_p);
			  //console.log('name:'+name_p+'(id:'+id_p+') display:'+$(this).is(':hidden'));
			  
			  //obj_str[index]['value']=flag_ch;
			  //нашли и меняем в массиве на другое значение 1/0 0/1
			  flag_poisk=1;
			//str=str+"&"+id_p+"="+flag_ch;
		  }
		//console.log($(this).is(':checked')+"|"+value['name']);
		//return (value['name'] !== 'rate_31');
	  });
	  if (flag_poisk==0){
		  //в массиве не нашли 
		  new_mas={"name":name_p,"value":flag_ch};
		  obj_str.push(new_mas);
	  }
	  //
	  
	  //сверяем со стартовым массивом
	  //если не нашли такого,то значит в базе ==""(т.к 0 записывается и выводиться в массив)
	  //flag_poisk=0;//не найдено
	  $.each(obj_str, function (index, value) {
		  //console.log(value['name']);
		  
		  if (value['name']==name_p){//ищем его в старвом массиве (если был ==1)
			  //console.log("pole1:"+name_p);
			  //console.log('name:'+name_p+'(id:'+id_p+') display:'+$(this).is(':hidden'));
			  if (obj_str[index]['value']!=flag_ch){
				  obj_str[index]['value']=flag_ch;
				  //нашли и меняем в массиве на другое значение 1/0 0/1
				  //flag_poisk=1;
				  str=str+"&"+id_p+"="+flag_ch;
			  }
		  }
		//console.log($(this).is(':checked')+"|"+value['name']);
		//return (value['name'] !== 'rate_31');
	  });
	  
	  //
	  
  }
  
});
}
console.log(str);
//test - perenos

result_files = forma.result_files_input.files;
query_uid = $("#result_files_input").attr("data-uid");
if (query_uid == '' || query_uid == null) {
  query_uid = uid;
}
if (result_files.length > 0 && result_files.length <= 3) {
  for (let i = 0; i < result_files.length; i++) {

    formData = new FormData();
    formData.append("photo", result_files[i]);
    
    var handler_url = '../applications/result_files_handler.php?uid=' + query_uid;
    $.ajax({
      url: handler_url,
      data: formData,
      processData: false,
      contentType: false,
      type: 'POST',
      async: false, 
      success: function(data) {
        $("#result_files_input").attr("data-changed", 1);
        if (data !== null && data !== '') {
          alert(data);
        }   
      }
    });
  }
 
} 
/*
if (params['uid']) {
  var prev_deadline = $('#deadline').attr('data-deadline');
  if (prev_deadline != deadline) {
    user_id = my_user_id;
    num_ord = $("#num_ord").val();
    from = prev_deadline;
    to = $("#deadline").val();
    $.ajax({
      url: 'change_deadline.php',
      method: 'POST',
      data: {num_ord: num_ord, uid: user_id, from: from, to: to},
      dataType: 'html', 
      async: false,
      success: function(data){
      }
    });
  }
}*/
console.log('uid', uid);
//end test-perenos 
$.post("backend/add_app_local.php?act=add&"+str, function( reply ) {

reply = reply.split(";");
uid = reply[0];
num_ord = reply[1];
mas_izm=reply[2];
console.log(mas_izm);

tek_uid = $("#uid").val();
console.log('tek_uid', tek_uid, 'uid', uid);
console.log('str:'+reply);
if (params['uid'] && ($("#preview_photo").attr("data-changed") == 1 || $("#result_files_input").attr("data-changed") == 1)) {
  uid = $("#uid").val();
}

if (mas_izm!=0){
	//есть изменения
	mas_izm=mas_izm.split("|");
	check_izm_com(mas_izm);
}
if ($("#preview_photo").attr('data-changed') == '1')
{
  preview_photo = forma.preview_photo.files[0];
  query_uid = $("#preview_photo").attr("data-uid");
  if (query_uid == '' || query_uid == null) {
    query_uid = uid;
  }
  if (preview_photo) { 
    formData = new FormData();
    formData.append("photo", preview_photo);
  //  file = formData.get("photo");
   
    var handler_url = '../applications/preview_handler.php?uid=' + query_uid;
    $.ajax({
      url: handler_url,
      data: formData,
      processData: false,
      contentType: false,
      type: 'POST',
      async: false,
      success: function(data) {
        $("#preview_photo").attr("data-exist", 1);
        if (data !== null && data !== '') {
          alert(data);
        } 
      }
    });
  }
}
//check_izm(mas_izm);
//check_izm_com();
//отправка в поле коментариев и уведомлений
if (uid == '0' && $("#preview_photo").attr("data-changed") == 0 && $("#result_files_input").attr("data-changed") == 0 ) {
    alert("Вы ничего не изменили в заявке, поэтому, сохранять нечего!")
    //$(location).attr('href','/acc/applications/');
} else if(uid == tek_uid) {
    alert("Заявка изменена!");
	//$(location).attr('href','/acc/applications/');
} else if(uid !== tek_uid && $.isNumeric(uid) == true) {
    //alert("Заявка создана!")
    $("#uid").val(uid);
    $("#num_ord").val(num_ord);
    $("#save_but").prop("disabled", true);
    if (confirm("Ваша заявка создана. Вы желаете ее распечатать?")) {
      print_view()

    } else {
      $(location).attr('href','/acc/applications/');
    }
} else {
    alert("Возникла ошибка! "+reply)
}
});
//$("#app_type").prop("disabled", true);
}

}


function delete_preview() {
  file = $("#preview_photo_del").attr("data-file");
  var conf = confirm('Вы действительно хотите удалить файл првеью?');
  if (conf == true) {
    $.ajax({
      url: '../applications/delete_preview.php',
      method: 'POST',
      data: {file: file},
      dataType: 'html',
      async: false,
      success: function(data){
        $("#preview_photo_del").remove();
        $("#preview_photo_img").remove();
        $("#preview_photo").attr("data-exist", 0);
        $("#preview_photo").attr("data-changed", 1);
      }
    });
  }

}

function delete_result_file(file) {
  console.log(file);
  $.ajax({
    url: '../applications/delete_result_file.php',
    method: 'POST',
    data: {file: file},
    dataType: 'html',
    async: false,
    success: function(data){
      $("#result_files_input").attr('data-changed', 1);
    }
  });
}

$(".del_result_file").each(function() {
  $(this).on('click', function() {
    conf = confirm('Вы действительно хотите удалить данный файл результата работ?');
    if (conf == true) {
      var file = $(this).attr('data-file');
      delete_result_file(file);
      $("#del_result_file_cont_" + $(this).attr('data-key')).remove();
    }

  });
});

function print_view(){
  //alert("print_app")
$("#art_id_span_al").hide();
$("#top_tbl").hide();
$("#preview_span").hide();
$("#auth_tr").hide();
$("#top_menu").hide();
$("#main_menu").hide();
$("#jump_span").hide();
$("#podvorot_klapan_span").hide();
$("#ss_span").hide();
$("#job_rate_box").hide();
$("#save_but").hide();
//$("#print_but").hide();
$("#add_art_add_flds").hide();
$("#hlp_hand_color_span").hide();
$("#hlp_izd_color_span").hide();
$("#compare_flds_but").hide();
$("img").hide();
$("#plan_span").hide();
$("#close_print_span").show();
$("#print_view_but").hide();
$(".print_comment").hide();
obr_comment($('#spec_req'),t_font_size,max_f3);

//
$("#spec_req").hide();
var cssValues = {
    "display":"block!important",
    "white-space":"break-spaces",
	"border":"5px solid black",
	"padding":"5px",
	"width":"794px",
	"line-height":"1"
}
$(".print_comment1").css(cssValues);
if ($("#spec_req").val()==""){$(".print_comment1").hide();}else{
$(".print_comment1").show();
}



//показываем тариф только если речь идет о заказной или серийной
app_type = $("#app_type").val();
console.log(app_type)
if(app_type == '1' || app_type == '2'){
tarif = $("#rate_4").val();
$("#sborka_cost_span").html(tarif);
$("#tarif_span").show();}
if($("#stamp_num").val() !== ""){
$("#stamp_num_span").show();}else{$("#stamp_num_span").hide();}

//выделяем некоторые поля, чтобы акцентировать на них внимание
$("#tiraz").css({ "border": "2px solid black"});
$("#izd_w").css({ "border": "2px solid black"});
$("#izd_v").css({ "border": "2px solid black"});
$("#izd_b").css({ "border": "2px solid black"});
$("#izd_material").css({ "border": "2px solid black"});
$("#izd_gramm").css({ "border": "2px solid black"});
$("#list_w").css({ "border": "2px solid black"});
$("#list_h").css({ "border": "2px solid black"});


}

function printr(){
setTimeout("window.print()", 500);
}




function close_print(){

    $(location).attr('href','/acc/applications/');

}

function block_save_button(user_id, uid){
console.log("MY:"+my_user_id)

if(my_user_id==user_id){owner = '1';}else{owner='0';}

if((owner == '0' && user_type == 'mng')){
$("#save_but").prop("disabled", true);
}


}




//если пользователь не проставлен, то проставляем его автоматически
user_id = $("#user_id").val();
if(user_id == ''){
$("#user_id").val(my_user_id);
}

function open_job(){
    num_ord = $("#num_ord").val();

    window.open('/acc/applications/count/?num_ord='+num_ord, '_blank');
}

function check_result_files() {
  var error = false;
  var error_message = '';
  var input = $("#result_files_input");
  var files = input.prop('files');

  console.log(files);
 
  for (let i = 0; i < files.length; i++) {
    if (files[i]['size'] > 700000) {
      error = true;
      error_message = 'Вес одного из файлов превышает 700kb. Выберите другой файл.';
      input.val('');
      break;
    }
  }
  if (files.length > 3) {
    error = true;
    error_message = 'Для загрузки доступно максимум 3 фото с результатом работ';
    input.val('');
  }

  if (error == true) {
    alert(error_message);
  } else {
    $("#result_files_input").attr('data-changed', 1);
  }
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
//
function comment_save(){

 num_ord = $("#num_ord_comment").val();
 flag_check_email=$("#email_mas_otp_check1").is(':checked');
 comment = $("#comment_text").val();
 user_name_full = user_name+' '+user_surname;
 if ($.isNumeric(num_ord))
   $.ajax({
     url: 'backend/comment.php',
     data: '&num_ord='+num_ord+'&act=save_comment1&comment='+comment+'&user_name_full='+user_name_full+"&email_check="+flag_check_email,
     dataType: 'text',
     type: 'GET',
     success: function(html) {
         //alert(data)
		 
     if(html == 1){
         $("#comment_text").val("");
		 alert("Комментарий добавлен");
         //$("#comment_div_text").html("");
      $('#div_comment').fadeOut(300);
      //if(comment == ""){$("#comment_but_"+num_ord).attr("src","../i/comment.png");}else{$("#comment_but_"+num_ord).attr("src","../i/comment_is.png");}
      load_commnet();}else{console.log(html)}
     }
   });
   
}
function comment_close(){$('#div_comment').fadeOut(300); $("#comment_text").val("");  $("#num_ord_comment").val("");}
//

function comment_app_dialog(num_ord){


$('#div_comment').hide();
var pos = $("#new_comment").position();

$("#num_ord_comment").val(num_ord);
$("#num_ord_comment_span").html(num_ord);

$('#div_comment').css({
    position: 'absolute',
    left: pos.left,
    top: pos.top
});

$('#div_comment').fadeIn(100);
return false;
}
function obr_comment(obj,t_font_size,max_f3){
			var temp_str=$(obj).val().split('\n').length;
			//console.log(temp_str);
			font_s=$('.print_comment1').css('font-size').split('px');
			if (temp_str<2){
				//меньше 4 строк  ,то x3 font-size
				t_font_size=$('.print_comment1').css('font-size').split('px');
				//console.log(t_font_size[0]+"|"+max_f3);
				if (t_font_size[0]<max_f3){
					$('.print_comment1').css('font-size',"54px");
				}
			}else if (temp_str>=2 && temp_str<4){
				$('.print_comment1').css('font-size',"36px");
			}else{
				$('.print_comment1').css('font-size',"18px");
			}
			$(".print_comment1").html($("#spec_req").val());
		}
		t_font_size=$('.print_comment1').css('font-size').split('px');
		max_f3=54;
		$('#spec_req').bind('input', function(){
		obr_comment($(this),t_font_size,max_f3);
		
		});
		//$('#comment_pole').input();
		function load_stamp_list(){
			var tip_izd=$("#izd_type").val();
			var izd_1=$("#izd_w").val();
			var izd_2=$("#izd_v").val();
			var izd_3=$("#izd_b").val();
			
			$.ajax({
			 url: 'backend/show_list_stamp.php',
			 data: 'type='+tip_izd,
			 dataType: 'html',
			 type: 'GET',
			 success: function(html) {
				 $("#modal_shtamp").find(".content-modal").html(html);
				 if ($("#stamp_num").val()!="" && $("#stamp_num").val()!="0" && $("#stamp_num").val()!=undefined){
				
			}else{
				$("#list_stamps_in2").val(izd_1);
				$("#list_stamps_in3").val(izd_2);
				change_sthamp();
			}
				 //
				 show_hide_modal('#modal_shtamp','show');
				 //
				 $('#list_stamps_in1').on('keyup input', function() {
					 var $this = $(this);
					 var $delay = 500;
					 clearTimeout($this.data('timer'));
					 this.data('timer', setTimeout(function(){
					 $this.removeData('timer');
					 change_sthamp();
					 }, $delay));

					});
				 $('#list_stamps_in2').on('keyup input', function() {
					  var $this = $(this);
					 var $delay = 500;
					 clearTimeout($this.data('timer'));
					 this.data('timer', setTimeout(function(){
					 $this.removeData('timer');
					 change_sthamp();
					 }, $delay));
				 });
				 $('#list_stamps_in3').on('keyup input', function() {
					  var $this = $(this);
					 var $delay = 500;
					 clearTimeout($this.data('timer'));
					 this.data('timer', setTimeout(function(){
					 $this.removeData('timer');
					 change_sthamp();
					 }, $delay));
				 });
					 /*
					 $( ".list_stamps p" ).show();
					$( ".list_stamps p" ).each(function( index ) {
						strq=String($(this).data('num'));
						if (strq.indexOf($("#list_stamps_in1").val())==-1){
							$(this).hide();
						}
					});
					*/
					
				 //
			 }
			});
		}
		function show_hide_modal(obj,tipe){
			console.log($(obj).parent('.wrap'));
			if (tipe=='hide'){
				$(obj).parents('.wrap').hide();
			}else if(tipe=='show'){
				$(obj).parents('.wrap').show();
			}
		}
		//
		function change_sthamp(){
			$( ".list_stamps p" ).show();
			$( ".list_stamps p" ).each(function( index ) {
				strq=String($(this).data('num'));
				strq1=String($(this).data('vis'));
				strq2=String($(this).data('sh'));
				console.log("strq:"+strq+":::"+strq.indexOf($("#list_stamps_in1").val()));
				console.log("strq1:"+strq1+":::"+strq1.indexOf($("#list_stamps_in3").val()));
				console.log("strq2:"+strq2+":::"+strq2.indexOf($("#list_stamps_in2").val()));
				if ((strq.indexOf($("#list_stamps_in1").val())==-1) ){
							$(this).hide();
				}
				if (strq1.indexOf($("#list_stamps_in3").val())==-1){$(this).hide();}
				if (strq2.indexOf($("#list_stamps_in2").val())==-1){
									$(this).hide();
								}
			});
		}
		 $(document).on('click','.list_stamps p',function(){
			 $("#izd_w").val(String($(this).data('sh')));
				$("#izd_v").val(String($(this).data('vis')));
				$("#izd_b").val(String($(this).data('bok')));
				$("#stamp_num").val(String($(this).data('num')));
				$("#open_stamp").parents('a').attr('href',$(this).find('a').attr("href")).show()
				show_hide_modal('#modal_shtamp','hide');
				
		 });
		  $(document).on('click','.list_stamps p a',function(){
			  e.preventDefault();
		  });
		 $(document).on('mouseover','.list_stamps p',function(){
			 
			 var img_src=String($(this).data('img'));
			 if (img_src!=""){
			 console.log(img_src);
			 $(".img_list_stamp").find('img').attr('src',img_src);
			 var $this1 = $(this);
					 var $delay = 1000;
					 clearTimeout($this1.data('timer'));
					 $(this).data('timer', setTimeout(function(){
					 $this1.removeData('timer');
					 $(".img_list_stamp").show();
					 }, $delay));
			// $(".img_list_stamp").show();
			 //if ($(this).offset().top+40)
			 //$(".img_list_stamp").css("top",$(this).offset().top+40);
			 //$(".img_list_stamp").show();
			 }else{$(".img_list_stamp").hide();}
		 });
		  $(document).on('mouseout','.list_stamps p',function(){
			 
			 $(".img_list_stamp").find('img').attr('src',"");
			 $(".img_list_stamp").hide();
		 });
		 
		 $(document).on('change','#resperson_pechat',function(){
			if ($(this).val()==4){
				//$("#deadline_pechat_span").hide();
				//$("#deadline_pechat_span_1").hide();
				$("#deadline_pechat").prop("disabled", true);
				$("#deadline_pechat").val("");
			}else{
				//$("#deadline_pechat_span").show();
				//$("#deadline_pechat_span_1").show();
				$("#deadline_pechat").prop("disabled", false);
			}
		 });
		 //stamp_order
		 $(document).on('change','#stamp_order',function(){
			 if ($(this).val()==2){
				 $("#deadline_stamp").val("").prop("disabled", true);
			 }else{
				 $("#deadline_stamp").prop("disabled", false);
			 }
		 });
		 $(document).on('change','#resperson_material',function(){
			 if ($(this).val()==3){
				 $("#deadline_material").val("").prop("disabled", true);
			 }else{
				 $("#deadline_material").prop("disabled", false);
			 }
		 });
		 

		