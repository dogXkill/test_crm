<form action="/acc/query/" id="search_by_name_form" method="get" onsubmit="check_fltr_form();return false;">

<input id="val" name="search" style="width: 200px; height: 30px; font-size: 20px;" type="text" />
<input type="submit" style="width: 70px; height: 32px; font-size:18px;" value=">>>"/>

 </form>
 <script type="text/javascript">
/*<![CDATA[*/
function check_fltr_form(){
if ($("#val").val() !== ""){
    val_txt = $("#val").val();

location.href = 'https://crm.upak.me/acc/query/?search='+encodeURIComponent(val_txt);

} else{$("#val").focus()}
}
$("#val").select()




//}
/*]]>*/
</script>
