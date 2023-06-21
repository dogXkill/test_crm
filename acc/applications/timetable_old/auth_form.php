
<div id=pass_div class="pass_form" style="display:<?if($_COOKIE["auth"] == "null" or $_COOKIE["auth"] == "off" or !$_COOKIE["auth"]){echo "block";}else{echo "none";}?>">
<form action="#" autocomplete="off">¬ведите пароль: <input type="password" id=cod size=5  autocomplete="off" value="" class="pass_inp" onclick="emtpy()"/> <button onclick="unblock(); return false;" class="pass_inp">OK</button></form>
</div>

<script>
function focus_pass(){$("#cod").focus(); $("#cod").val("")}
focus_pass()
function emtpy(){$("#cod").val("")}
//emtpy()
</script>