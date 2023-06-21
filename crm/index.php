<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../acc/includes/db.inc.php");
require_once("../acc/includes/auth.php");
require_once("../acc/includes/lib.php");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../acc/style.css" rel="stylesheet" type="text/css" />
<link href="includes/css.css" rel="stylesheet" type="text/css" />

<script async src="//jsfiddle.net/uVqG6/9/embed/"></script>
<script type="text/javascript" src="../acc/includes/js/jquery-1.11.3.min.js"></script>
<script src="../acc/includes/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript" src="includes/js/crm.js"></script>
</head>
<body>
<script type="text/javascript" src="../acc/includes/js/wz_tooltip.js"></script>
<?require_once("../acc/templates/top.php");
$name_curr_page = 'crm';
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;
require_once("../acc/templates/main_menu.php");

list($stages_html,$stages_js,$load_stages) = generate_stages();

function generate_stages(){

    $get_stages = mysql_query("SELECT * FROM crm_stages ORDER BY stage_num ASC");
                  echo mysql_error();
    while($r = mysql_fetch_assoc($get_stages)){

     $stage_num = $r["stage_num"];
     $title = $r["title"];
     $div_class = $r["div_class"];
     $title_class = $r["title_class"];

    $stages_html .= "<div class=\"$div_class\"><div class=\"$title_class\">$title</div><div id=\"stage_$stage_num\" class=\"connectedSortable ui-sortable empty_stage\"></div></div>\n";

    $stages_js .= "$(\"#stage_$stage_num\").sortable( {connectWith: \".connectedSortable\",	tolerance: \"pointer\", receive: function( event, ui ) { save_stages(ui.item.attr(\"id\"),'$stage_num')}});\n";

    $load_stages .= "load_stage_deals('$stage_num');\n";


    }
    return array($stages_html,$stages_js,$load_stages);
}




?>
<span class="crm_link" onclick="insert_new_deal()"><b>новый запрос</b></span> |
<span class="crm_link" onclick="users_settings('get_users', '', '')">пользователи</span> |
<span class="crm_link" onclick="users_settings('show_common_emails', '', '0')">общие ящики</span> |
<span class="crm_link" onclick="general_settings()">настройки</span> |
<a href="admin/index.php" class="crm_link" target="_blank">администраторская панель</a>

<div class="container">
<?=$stages_html?>
</div>


<div class="end_deal" id="end_deal_div">
  <div id="not_target_lead_div" class="end_deal_button connectedSortable ui-sortable">Не целевой<br>запрос</div>
  <div id="end_of_deal_div" class="end_deal_button connectedSortable ui-sortable">Завершение<br>сделки</div>
</div>


<span class="end_deal_reasons" id="end_deal_reasons_div"></span>
<div class=shade id=shade_div></div>

<div id=settings_div class="settings_div_class"></div>
<div class=shade id=settings_shade_div></div>

<script>
$(function(){
<?=$stages_js?>
$("#not_target_lead_div").sortable( {connectWith: ".connectedSortable",	tolerance: "pointer", receive: function( event, ui ) {

    var start_id = ui.sender.attr("id");

    if(start_id == "stage_0" || start_id == "stage_1"){
    var not_target_lead_conf = confirm("Вы подтверждаете что данный запрос НЕ целевой?");
     //console.log(ui.sender.attr("id")+" test "+not_target_lead_conf);
    if(not_target_lead_conf == true){
      lead_id = ui.item.attr("id");
      save_stages(lead_id,'not_target_lead');
      $("#"+lead_id).hide();
        }
      else{$(ui.sender).sortable('cancel');}


    }
    else
    {alert("Сделать сделку не целевой уже нельзя")
    $(ui.sender).sortable('cancel');}
  }});




$("#end_of_deal_div").sortable( {connectWith: ".connectedSortable",	tolerance: "pointer", receive: function( event, ui ) {

    var start_id = ui.sender.attr("id");


    var end_of_deal_conf = confirm("Вы уверены что сделку можно считать закрытой?");
     //console.log(ui.sender.attr("id")+" test "+not_target_lead_conf);
    if(end_of_deal_conf == true){
      lead_id = ui.item.attr("id");
      save_stages(lead_id,'end_of_deal');
      $("#"+lead_id).hide();
        }
      else{$(ui.sender).sortable('cancel');}



  }});

});

<?=$load_stages;?>
</script>
</body>
</html>
<? ob_end_flush(); ?>