<?require_once("../../acc/includes/db.inc.php");

$act = $_GET["act"];

function general_settings(){
  $g_set = mysql_fetch_assoc(mysql_query("SELECT * FROM crm_general_settings"));
  $appeals_auto = $g_set['appeals_auto'];
  return array($appeals_auto);
}
list($appeals_auto) = general_settings();

?>

<table cellpadding=15>
<tr>
    <td>����������������� ��������:</td>
    <td>
      <input type="radio" name="appeals_auto_0" id="appeals_auto_0" value="0" <?if($appeals_auto == "0"){echo "checked";}?>/> <label for="appeals_auto_0">���������</label>
      <input type="radio" name="appeals_auto_1" id="appeals_auto_1" value="1" disabled/> <label for="appeals_auto_1">����������</label>
      <input type="radio" name="appeals_auto_2" id="appeals_auto_2" value="2" disabled/> <label for="appeals_auto_2">����������, ��������� � ���� ����� ���������</label>
    </td>
</tr>
<tr>
    <td>����:</td>
    <td><?echo get_tags();?></td>
</tr>
<tr>
    <td>������� �������� ������:</td>
    <td><?echo crm_end_deal_reasons();?></td>
</tr>

<tr>
    <td>���������� ���� ������ (������ ��������������� �������)</td>
    <td></td>
</tr>
<tr>
    <td>������� ��������� �����</td>
    <td></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
</table>




<?




function get_tags(){
  $tags = mysql_query("SELECT * FROM crm_deal_tags WHERE deleted <> '1'");
                echo mysql_error();
            while($r = mysql_fetch_array($tags)){
            $id = $r["id"];
            $tag = $r["tag"];

             $tags_txt .= "<span class=\"crm_set_list\" id=\"setting_tag_$id\">$tag <input type=\"button\" class=\"del_but_class\" value=\"-\" onclick=\"edit_tags('delete', '$id')\"/></span>";
            }

           $tags = "<span id=just_tags>".$tags_txt."</span> <input type=\"text\" class=\"new_set_class\" id=\"new_tag\"> <input type=\"button\" class=\"new_but_class\" value=\"+\"  onclick=\"edit_tags('add','')\"/>";

           return $tags;
}


function crm_end_deal_reasons(){
  $end_deal_reasons = mysql_query("SELECT * FROM crm_end_deal_reasons WHERE deleted <> '1'");
                echo mysql_error();
            while($r = mysql_fetch_array($end_deal_reasons)){
            $id = $r["id"];
            $name = $r["name"];

            $end_deal_reasons_txt .= "<span class=\"crm_set_list\" id=\"setting_end_deal_reasons_$id\">$name <input type=\"button\" class=\"del_but_class\" value=\"-\" onclick=\"save_end_deal_reasons('delete', '$id')\"/></span>";
            }

           $end_deal_reasons = "<span id=just_end_deal_reasons>".$end_deal_reasons_txt."</span> <input type=\"text\" class=\"new_set_class\" id=\"new_end_deal_reason\"> <input type=\"button\" class=\"new_but_class\" value=\"+\"  onclick=\"save_end_deal_reasons('add','')\"/>";

           return $end_deal_reasons;
}



?>