function block_all(){
$("#block_div").hide();
$("#pass_div").show();
$("#cod").val("");
$.cookie('auth', null);
focus_pass()
}


function unblock(){
pass = $("#cod").val();
if(pass=="495") {
$("#block_div").show();
$("#pass_div").hide();
$.cookie('auth', 'on', {
    expires: 1
});
 return false;
}else{
$("#cod").val("");
alert("Неверый пароль!")
$.cookie('auth', null);
focus_pass()

}
}


 function count_sec(){
   sec = $("#counter").val()*1+1
   $("#counter").val(sec)
   if(sec == 300){block_all()}
 }

 document.onmousemove = start_over

 function start_over(){$("#counter").val(0);}

  setInterval(count_sec, 1000);