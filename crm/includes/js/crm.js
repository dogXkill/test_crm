

function load_stage_deals(stage_num){

$.post("backend/load_stage_deals.php?stage_num="+stage_num, function( reply ) {

$("#stage_"+stage_num).html(reply);
});
}






function save_stages(id,stage_num){

$.post("backend/save_stages.php?stage_num="+stage_num+"&id="+id, function( reply ) {

if(stage_num == "end_of_deal"){show_end_deal_reasons(id,reply)}

});

}

function end_deal_save_reason(reason_end_id){
end_deal_reason_deal_id = $("#end_deal_reason_deal_id").val();
$.post("backend/save_end_deal_reason.php?deal_id="+end_deal_reason_deal_id+"&reason_end_id="+reason_end_id, function( reply ) {

if(reply == "updated"){ hide_end_deal_reasons() } else {console.log(reply)}

});

}

function show_end_deal_reasons(deal_id,reply){
  $("#end_deal_reasons_div").html(reply).fadeIn('500');
  $("#shade_div").fadeIn('500');
}

function hide_end_deal_reasons(){
  $("#end_deal_reasons_div").html("").fadeOut('500');
  $("#shade_div").fadeOut('500');
}

function block_email(client_id, email){
  //проверка!
  var del= confirm("Вы уверены что хотите чтобы обращения от данного пользователя больше не поступали в CRM?");
  if(del == true){

        $.post("backend/block_email.php?client_id="+client_id+"&email="+email, function( reply ) {

        if(reply == "blocked"){ load_stage_deals('0'); } else {console.log(reply)}

});
}
}

function check_form(str){
    var err = '';
    var str = str.split('&');
    var len = str.length-1
//получаем данные сериалайз в переменные!

        for (var i=0; i <= len; i++)
            {
                var str_n = str[i].split('=');
                if($.isNumeric(str_n[1]) && str_n[1] > -1){
                window[str_n[0]] = str_n[1]*1;
                }else{window[str_n[0]] = str_n[1];}
            }

if(crm_mail_login == ""){alert ("Введите почтовый логин!"); err="1";}
if(crm_mail_pass == ""){alert ("Введите пароль от почты!"); err="1";}
if(crm_imap_host == ""){alert ("Введите imap host!"); err="1";}

return err;
}

function users_settings(act, email_id, user_id){


    var str = ''

   if(act == "save"){

       str = $("#email_form_save_"+email_id).serialize();
       if(check_form(str) !== ""){return false;}
   }

  if(act == "add"){

       str = $("#email_form_add_"+user_id).serialize();
       err = check_form(str);

       if(err == "1"){return false;}

    }

//НУЖНО! обработка ошибок - проверка полей + вырезание спецсимволов

  $.post("backend/email_settings.php?act="+act+"&email_id="+email_id+"&user_id="+user_id+"&"+str, function( reply ) {



    if((act == "save" || act == "add" || act == "del") && user_id !== '0'){
        $("#email_settings_div"+user_id).html(reply);
      }

    if(act == "get_users" || act == "show_common_emails" || ((act == "save" || act == "add" || act == "del") && user_id == '0')){

        $("#settings_div").html(reply).fadeIn('500');
        $("#settings_shade_div").fadeIn('500');
      }


});

}

function show_email_settings(user_id){

   $("#email_settings_div"+user_id).toggle();
   //$("#user_settings_new_td_"+id).toggle();

}




function general_settings(){
  $.post("backend/general_settings.php", function( reply ) {
  $("#settings_div").html(reply).fadeIn('500');
  $("#settings_shade_div").fadeIn('500');

});


}

function edit_tags(act,id){
  var error = '0';
  var new_tag
  new_tag = $("#new_tag").val();

  if(act == 'add' && new_tag == ""){error = '1';}

  if(act == 'delete'){
    var del_tag = confirm("Вы уверены что хотите удалить данный тег?");
    if(del_tag == false){error = '1';}
  }


 if(error !== '1'){

  $.post("backend/edit_tags.php?act="+act+"&id="+id+"&new_tag="+new_tag, function( reply ) {

  if(act == "add" && $.isNumeric(reply)){
  $('#just_tags').append("<span class=\"crm_set_list\" id=\"setting_tag_"+reply+"\">"+new_tag+" <input type=\"button\" class=\"del_but_class\" value=\"-\" onclick=\"edit_tags('delete', '"+reply+"')\"/></span>");
  $("#new_tag").val("");
  }
   if(act == 'delete' && reply == "ok"){$("#setting_tag_"+id).fadeOut("500");}

});
}

}

function save_end_deal_reasons(act,id){
  var error = '0';
  var new_end_deal_reason
  var new_end_deal_reason = $("#new_end_deal_reason").val();

  if(act == 'add' && new_end_deal_reason == ""){error = '1';}

  if(act == 'delete'){
  var del_reason = confirm("Вы уверены что хотите удалить данную запись?");
  if(del_reason == false){error = '1';}
  }


 if(error !== '1'){

  $.post("backend/edit_end_deal_reasons.php?act="+act+"&id="+id+"&new_end_deal_reason="+new_end_deal_reason, function( reply ) {

    if(act == "add" && $.isNumeric(reply)){
        $('#just_end_deal_reasons').append("<span class=\"crm_set_list\" id=\"setting_end_deal_reasons_"+reply+"\">"+new_end_deal_reason+" <input type=\"button\" class=\"del_but_class\" value=\"-\" onclick=\"save_end_deal_reasons('delete', '"+reply+"')\"/></span>");
        $("#new_end_deal_reason").val("");
    }

     if(act == 'delete' && reply == "ok"){
        $("#setting_end_deal_reasons_"+id).fadeOut("500");
      }

});
}

}



$( document ).ready(function(){
	  $( "#settings_shade_div" ).click(function(){ // задаем функцию при нажатиии на элемент <button>
	    $("#settings_shade_div").fadeOut('500');
        $("#settings_div").html("").fadeOut('500');
	  });});