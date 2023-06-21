

$(document).ready(function() {

$("#cols_vis_form").change(function(){
cols_cookie()
});
});


function highlight_app_dialog(act, uid){
          //    console.log(act, uid)
if(act == 'open'){
     var pos = $("#svetofor_"+uid).position();


    $("#uid_highlight_inp").val(uid);


    $('#div_highlight').show().css({
    position: 'absolute',
    left: pos.left,
    top: pos.top
});
}

if(act == 'sbros'){$.cookie("highlighted_apps", "");}

}


function highlight_app(highlight_color, app_status){

   uid = $("#uid_highlight_inp").val();
   let app_statuses = ["не принята", "заявка принята", "в работе", "требует внимания", "выполнена"];
   app_status_txt = app_statuses[app_status];

    if($.isNumeric(uid)){
            //   console.log("uid="+uid+"&highlight_color="+highlight_color)
      $.post("backend/highlight_app.php?uid="+uid+"&highlight_color="+highlight_color+"&app_status="+app_status, function( reply ) {

      if(reply == "OK"){
          $('#div_highlight').hide();
          $("#uid_highlight_inp").val("");
          $('#td_art_id_'+uid).css('background-color', highlight_color);
          $('#span_app_status_'+uid).html(app_status_txt);

          if(app_status > 0){$('#app_status_upd_err_'+uid).hide();}else{$('#app_status_upd_err_'+uid).show();}

          //console.log('#col_art_id_'+uid+' '+reply)
          }else{console.log(reply)}

});

    }



}


function show_preview(path, uid, act) {

if(act == "show"){
    var pos = $("#svetofor_"+uid).position();

    $('#div_preview').html("<img src="+path+" width=400>");
    $('#div_preview').show().css({
    position: 'absolute',
    left: pos.left,
    top: pos.top
});
}else{
  $('#div_preview').html("");
  $('#div_preview').hide();
}

}
function show_result_files(uid, act) {

  if(act == "show") {

    var pos = $("#svetofor_"+uid).position();
    $('#div_result_files').show().css({
      position: 'absolute',
      left: pos.left,
      top: pos.top
    });
    var files = $('#result_files_list_' + uid).html();
    $('#div_result_files').html('<div>' + files + '</div>');

  } else {

    $('#div_result_files').html("");
    $('#div_result_files').hide();
    
  } 

}


function save_app_status(uid){
    if($.isNumeric(uid)){
      var str = $("#app_status_form_"+uid).serialize();
      console.log(uid+" "+str)

      $.post("backend/save_app_status.php?uid="+uid+"&"+str, function( reply ) {

});

    }
}


function get_app_list(order){
var str = $("#forma").serialize();
var planIn = $('#plan_in').val();
$("#app_list_div").html("<img src=\"../../i/load2.gif\" style=\"align:middle;padding-bottom:30px;\">");
$.post("backend/app_list.php?order="+order+"&"+str+"&plan_in="+planIn, function( app_list_txt ) {}).done(function(app_list_txt) {$("#app_list_div").html(app_list_txt);  });
 console.log(str);
//убираем дивы с итогами по материалам, если они активны
$("#material_qty_div").fadeOut(50).html("");
$("#tiraz_qty_div").fadeOut(50).html("");
$("#material_w_qty_div").fadeOut(50).html("");
}
function get_app_list_new(order){
var str = $("#forma").serialize();
var planIn = $('#plan_in').val();
$("#app_list_div").html("<img src=\"../../i/load2.gif\" style=\"align:middle;padding-bottom:30px;\">");
$.post("backend/app_list.php?order="+order+"&"+str+"&plan_in="+planIn, function( app_list_txt ) {}).done(function(app_list_txt) {$("#app_list_div").html(app_list_txt);  });
 console.log(str);
//убираем дивы с итогами по материалам, если они активны
$("#material_qty_div").fadeOut(50).html("");
$("#tiraz_qty_div").fadeOut(50).html("");
$("#material_w_qty_div").fadeOut(50).html("");
}

//функция создает куки в зависимости от выбранных чекбокосов и скрывает/показывает ненужные колонки
function cols_cookie(){
var cols_to_hide = [];
$('input[class="col_vis_chk"]:not(:checked)').each(function() {
  coloumn_name = $(this).val();

  cols_to_hide += coloumn_name+','

  if(coloumn_name.length > 0)
  {
  $('td[name^='+coloumn_name+']').hide();
  $('th[name^='+coloumn_name+']').hide();}
});

$.cookie("app_list_cols_hidden", cols_to_hide);
  console.log(cols_to_hide)


$('input[class="col_vis_chk"]:checked').each(function() {
  coloumn_name = $(this).val();
  if(coloumn_name.length > 0)
  {
  $('td[name^='+coloumn_name+']').show();
  $('th[name^='+coloumn_name+']').show();}
});


}

//alert($.cookie("app_list_cols_hidden"))

//функция читает куки, разбивает его на массив и прячет ненужные колонки при каждой загрузке
function col_vis(){
  if($.cookie("app_list_cols_hidden")){
var cols_to_hide = $.cookie("app_list_cols_hidden");

cols_to_hide = cols_to_hide.split(',');
jQuery.each(cols_to_hide, function() {
coloumn_name = this

if(coloumn_name.length > 0){
$('td[name^='+coloumn_name+']').hide();
$('th[name^='+coloumn_name+']').hide();}
});}
}

//функция показывает слой управления колонками, читает куки и снимает галочки на тех чекбоксах, которые отвечают за соответствующие колонки
function fill_cols_chk(){

$("#col_checker").toggle();
$(".col_vis_chk").attr("checked","checked");
var cols_to_hide = $.cookie("app_list_cols_hidden");
cols_to_hide = cols_to_hide.split(',');
jQuery.each(cols_to_hide, function() {
coloumn_name = this
if(coloumn_name.length > 0){
coloumn_name_chk = coloumn_name+'_chk'
$('#'+coloumn_name_chk).removeAttr('checked');

}
});
// 

 
}

$( function() {$("#col_checker").draggable(); if ($("#col_checker").length) {
        // клик внутри элемента
        return;
    }else{
		fill_cols_chk('close');
	}} );


function jump(jumpfrom, maxsize, jumpto){

maxsize = maxsize-1
if ($('#'+jumpfrom).val().length > maxsize){$('#'+jumpto).select();
$('#'+jumpto).focus();
}}

function replace_num(v) {
  var reg_sp = /[^\d^.]*/g;		// вырезание всех символов кроме цифр и точки
  v = v.replace(reg_sp, '');
  return v;
}

function del_app(num_ord){
if(confirm("Вы уверены что хотите удалить заявку и ВСЮ работу добавленную по ней?")) {
$.post("backend/del_app.php?num_ord="+num_ord, function( reply ) {
if(reply == "ok"){
  alert("Заявка удалена!")
  $("#tr_"+num_ord).animate({opacity: "0.3"}, 300);

  }else{alert("Возникла ошибка! "+reply)}
});
}
}

function show_app_info(uid){
  if (uid) {
    $('#app_info_'+uid).toggle(250);

    var show_info;
    show_info = $.ajax({
      type: "GET",
      url: 'backend/show_app_info.php',
      data : '&uid='+uid,
      success: function () {
        var resp = show_info.responseText
        if (resp !== "") {
          $('#app_info_'+uid).html(resp);
		  //$("#app_info_"+uid).css('height','auto');
        }
      }
    });
  }
}

function show_xtra_flds(){
if($('#xtra_flds').css('display') == 'none'){
$("#xtra_flds").show();
$("#show_xtra_flds_span").html("-");
}else{
$("#xtra_flds").hide();
$("#show_xtra_flds_span").html("+");
}


}

function check_workout(num_ord_arr){

num_ord_arr = num_ord_arr.split(",");

//массив номеров работ, для дальнейшей циклической проверки
workout_job_arr = "1,2,3,4,11";
workout_job_arr = workout_job_arr.split(",");
var workout_arr = new Array();
var prev_val = ""

jQuery.each(num_ord_arr, function() {

num_ord = this
if(num_ord.length > 0){
jQuery.each(workout_job_arr, function() {
job = this


if($("#workout"+num_ord+"-"+job).val() !== undefined) {

tek_val = $("#workout"+num_ord+"-"+job).val()
if(tek_val*1 > prev_val*1 && prev_val !== ""){
$("#workout_div_"+num_ord+"_"+job).css("background-color", "#FFFF66");
}
prev_val = $("#workout"+num_ord+"-"+job).val()

}

});

}
prev_val = ""

});

console.log(workout_arr)

}




function calc_sum(typ, num_ord, sum_text, fon){


if(fon == 1){
//красим ячейку в цвет в зависимости от того выбран чекбокс или нет
if($("#"+typ+"_"+num_ord).prop('checked') == false){
  $("#"+typ+"_"+num_ord).prop('checked', true);
  $("#col_"+typ+"_"+num_ord).css("background-color", "rgb(153, 153, 255)");
  }else{
  $("#"+typ+"_"+num_ord).prop('checked', false);
  $("#col_"+typ+"_"+num_ord).css("background-color", "rgb(251, 251, 251)");
  }
}
                var sum = 0;
                var arr = $('input.'+typ+'_class:checked');
    //перебираем все элементы, ищем валуе, считаем итог
                arr.each(function(index, el){
                    var vl = el.value;
                    if(vl>0){sum += parseFloat(vl);}
                })

                console.log(sum)
    //если сумма больше 0, то выводим слой с информацией
                if(sum > 0){
               $("#"+typ+"_div").fadeIn(200).html(sum+" "+sum_text+" <span onclick=\"calc_sum_off('"+typ+"')\" class=\"x\">x</span>");
                }else{
               $("#"+typ+"_div").html("").fadeOut(200);
                }


      }


function calc_sum_off(typ){
                var arr = $('input.'+typ+'_class:checked');
                arr.each(function(index, el){
                $(this).removeAttr("checked");
                $("#col_"+el.id).css("background-color", "rgb(251, 251, 251)");
               $("#"+typ+"_div").fadeOut(200);
                })
}



//функция, которая циклом проверяет все заявки, text hidden около каждого вида работ каждой заявки и прячет лишнее
function workout_filters(){

//вид работы
type = $("#compar_job").val();
//больше или меньше
compar = $("#compar").val();
//процент выполнения
compar_perc = $("#compar_perc").val()*1;


var oper = {
    'more': function (cur_workout_perc, compar_perc) { return cur_workout_perc > compar_perc;},
    'less': function (cur_workout_perc, compar_perc) { return cur_workout_perc < compar_perc;},
    'equal': function (cur_workout_perc, compar_perc) { return cur_workout_perc == compar_perc;}
};


if(compar_job !== "" && compar !== "" && compar_perc !== ""){

num_ord_arr1 = num_ord_arr.split(",");

jQuery.each(num_ord_arr1, function() {
num_ord = this
$("#tr_"+num_ord).show();
cur_workout_perc = $("#workout"+num_ord+"-"+type).val()*1;

if(oper[compar](cur_workout_perc, compar_perc) == false){

$("#tr_"+num_ord).hide();
$("#sbros_filters").show();
}
cur_workout_perc = "";

});
  }
}

function show_filtered_apps(){
jQuery.each(num_ord_arr1, function() {
num_ord = this
$("#tr_"+num_ord).show();
});
$("#sbros_filters").hide();
}

function sklad_art_filter(typ){
    //   $("#limit").val("1000").change();
    //   $("#app_type").val("2").change();
    //   $("#plan_in").val("0").change();
      // get_app_list('')

            // console.log(num_ord_arr)
            num_ord_arr2 = num_ord_arr.split(",");
            jQuery.each(num_ord_arr2, function() {
            num_ord = this
            //$("#tr_"+num_ord).show();
             if ($.isNumeric(num_ord)){
            sklad_ostatok = $("#sklad_ostatok_"+num_ord).val()*1;
            virubka = $("#workout"+num_ord+"-2").val()*1;
            virub_isdely_per_list = $("#virub_isdely_per_list_"+num_ord).val()*1;
            sborka = $("#workout"+num_ord+"-4").val()*1;

            if(virubka > 0 && virub_isdely_per_list > 0){
            isdely_virubleno = virubka / virub_isdely_per_list;
            isdely_ne_sobrano = isdely_virubleno - sborka;
            }else{isdely_virubleno = 0; isdely_ne_sobrano = 0;}

             console.log(num_ord, isdely_virubleno, isdely_ne_sobrano)


            if(sklad_ostatok !== 0 && typ == 0){
                    $("#tr_"+num_ord).hide();
            }
            if(sklad_ostatok !== 0 && typ == 1 && sborka >= isdely_virubleno && $.isNumeric(isdely_ne_sobrano)){
                    $("#tr_"+num_ord).hide();
                   //  console.log(num_ord, sklad_ostatok, virubka, virub_isdely_per_list, sborka)
                     console.log(num_ord, isdely_virubleno, isdely_ne_sobrano)
            }

            }


});


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


function comment_app_dialog(num_ord){


$('#div_comment').hide();
var pos = $("#comment_but_"+num_ord).position();

$("#num_ord_comment").val(num_ord);
$("#num_ord_comment_span").html(num_ord);

$('#div_comment').css({
    position: 'absolute',
    left: pos.left-550,
    top: pos.top
});


   $.ajax({
     url: 'backend/comment.php',
     data: '&num_ord='+num_ord+'&act=get_comment',
     dataType: 'text',
     type: 'GET',
     success: function(data) {
      $("#comment_div_text").html(data);
      $('#div_comment').fadeIn(100);
}
   });



}

function comment_close(){$('#div_comment').fadeOut(300); $("#comment").val("");  $("#num_ord_comment").val("");}

function comment_save(){
	email_check=$("#email_mas_otp_check").is(':checked');//true/false
 num_ord = $("#num_ord_comment").val();
 num_ord=$("#tr_"+num_ord).find('td[name=col_tiraz]').attr('id').split('_');
 num_ord=Number(num_ord[3]);
 comment = $("#comment").val();
 user_name_full = user_name+' '+user_surname;

 if ($.isNumeric(num_ord))
   $.ajax({
     url: 'backend/comment.php',
     data: '&num_ord1='+num_ord+'&act=save_comment1&comment='+comment+'&user_name_full='+user_name_full+'&email_check='+email_check,
     dataType: 'text',
     type: 'GET',
     success: function(data) {
         // alert(data)
     if(data.trim() == "1"){
         $("#comment").val("");
         $("#comment_div_text").html("");
      $('#div_comment').fadeOut(300);
      //if(comment == ""){$("#comment_but_"+num_ord).attr("src","../i/comment.png");}else{$("#comment_but_"+num_ord).attr("src","../i/comment_is.png");}
	  if(comment == ""){$("#comment_but_"+num_ord).removeClass("icon_message_plus").removeClass("fa-duotone").addClass("fa-light");}else{$("#comment_but_"+num_ord).removeClass("icon_message_plus").addClass("icon_message_plus").removeClass("fa-light").addClass("fa-duotone");}
      }else{console.log(data+"("+typeof(data)+")")}
     }
   });
}




function update_app(num_ord, type) {

  var error = false;
  var error_message = '';
  //обрабатываем поле план
  if(type == 'plan_in') {
    if($("#plan_in_"+num_ord).is(":not(:checked)")){val = '0';}else{val = '1';}
  }

  if(type == 'archive') {
    var uid = Number($(window).attr('uid'));
    var result_files = Number($('#tr_' + num_ord).attr('data-resultfiles'));
    var app_type = Number($('#tr_' + num_ord).attr('data-type'));
    console.log('type = ' + app_type);
    if (result_files > 0 || uid == 11) {
      if($("#archive_"+num_ord).is(":not(:checked)")){val = '0';}else{val = '1'; $("#tr_"+num_ord).animate({opacity: "0.2"}, 300);}
      console.log(result_files);
      console.log('true');
    } else {
      console.log(result_files);
      console.log('false');
      if($("#archive_"+num_ord).is(":not(:checked)")) {
        val = '0';
        //$("#archive_"+num_ord).prop('checked', true);
      } else {
        if (app_type == 1 || app_type == 4) {
          //val = '0';
          //$("#archive_"+num_ord).prop('checked', false);
          //error = true;
          error_message = 'Заявка перемещена без фото';
          val = '1';
          $("#tr_"+num_ord).animate({opacity: "0.2"}, 300);
          alert(error_message);
        } else {
          val = '1';
          $("#tr_"+num_ord).animate({opacity: "0.2"}, 300);
        }
      }


    }
  }

  if(type == 'tiraz') {
    val = $("#tiraz_"+num_ord).val();
  }

  if (error == false) {
    if(num_ord) {
      $.post("backend/update_app.php?num_ord="+num_ord+"&type="+type+"&val="+val, function(plan_response){}).done(function(plan_response) {console.log(plan_response)});
      }
  } else {
    alert(error_message);
  }

}

function set_nakl(num_ord){
var pos = $("#td_buttons_"+num_ord).position();

$('#div_nakl').css({
    position: 'absolute',
    left: pos.left-100,
    top: pos.top
});

   $('#div_nakl').fadeIn(300);
   $('#nakl_num_ord').val(num_ord);
}

function show_nakl(){
num_ord = $('#nakl_num_ord').val();
qty = $('#qty').val();
window.open("nakl/?num_ord="+num_ord+"&qty="+qty);
close_nakl()
}

function close_nakl(){
   $('#div_nakl').fadeOut(300);
}
$(".app_deadline_input").each(function(index) {
  console.log(index);
  $(this).on('change', function () {
    num_ord = $(this).attr('data-numord');
    deadline = $(this).val();
    console.log(num_ord, deadline);
  });
});
