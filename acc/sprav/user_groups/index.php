<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
if ($user_access['sprav_access'] == '0' || empty($user_access['sprav_access'])) {
  header('Location: /');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html" charset="windows-1251" />
    <title>Группы</title>
    <link href="../../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="../../includes/js/jquery-ui.js"></script>
    <script src="../../includes/js/jquery.cookie.js"></script>
  </head>
  <body>
    <script type="text/javascript" src="../../includes/js/wz_tooltip.js"></script>

    <?
    require_once("../../templates/top.php");
    $name_curr_page = 'sprav';
    require_once("../../templates/main_menu.php");
    $part = $_GET["part"];
    require_once("../../templates/spravmenu.php");

    ?>
    <?
    $user_groups = array();
    $q = "SELECT * FROM user_groups ORDER BY sort ASC";
    $r = mysql_query("$q");

    while ($row = mysql_fetch_row($r))
    {
      $user = array();
      $user['ID'] = $row[0];
      $user['NAME'] = $row[1];
      $user['SORT'] = $row[2];
      array_push($user_groups, $user);
    }


    ?>
    <div class="title_razd" style="text-align: center;">
      Группы пользователей
    </div>
    <table align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <tr class="tab_query_tit"><td><a class="sublink" href="new.php">Создать новую группу</a></td></tr>
      </tbody>
    </table>
    <table align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>

      </tbody>
    </table>
    <table id="user-groups-table" align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <tr class="tab_query_tit">
        <!--  <td class="tab_query_tit" align="center">ID</td>-->
          <td class="tab_query_tit" align="center">Название группы</td>
          <td class="tab_query_tit" align="center">Сортировка</td>
          <td class="tab_query_tit" align="center">Действия</td>
        </tr>
        <style media="screen">
          .del-photo {
            cursor: default;
            opacity: 0.3;
          }
        </style>
        <?
        foreach ($user_groups as $key => $value) {
          $onclick = ($value['ID'] == 1 || $value['ID'] == 2 || $value['ID'] == 3) ? '' : 'onclick="delete_group(this.id);" style="opacity: 1; cursor: pointer;"';
          ?>
          <tr id="group-<?=$value['ID']?>">
            <!--<td class="tab_td_marg" style="text-align: center;"><a href="edit.php?id=<?=$value['ID']?>"><?=$value['ID']?></a></td>-->
            <td class="tab_td_marg" style="text-align: center;"><a href="edit.php?id=<?=$value['ID']?>"><?=$value['NAME']?></a></td>
            <td class="tab_td_marg" style="text-align: center;"><?=$value['SORT']?></td>
            <td class="tab_td_marg" style="text-align: center;"><img <?=$onclick?> class="del-photo" id="group-del-<?=$value['ID']?>" data-name="<?=$value['NAME']?>" data-id="<?=$value['ID']?>" src="/acc/i/del.gif"/></td>
          </tr>
          <?
        }
        ?>
      </tbody>
    </table>
    <script type="text/javascript">
      function delete_group(id) {
        group_id = document.getElementById(id).getAttribute('data-id');
        name = document.getElementById(id).getAttribute('data-name');
        $.ajax({
          url: 'delete_group.php',
          method: 'POST',
          data: {group_id: group_id},
          dataType: 'html',
          async: false,
          success: function(data){
            document.getElementById('group-' + group_id).remove();
            alert('Группа "' + name + '" удалена');
          }
        });
      }
    </script>

  </body>
</html>



<? ob_end_flush(); ?>
