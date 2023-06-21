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
    <title>Должности</title>
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
    $user_posts = array();
    $q = "SELECT * FROM doljnost ORDER BY name ASC";
    $r = mysql_query("$q");

    while ($row = mysql_fetch_row($r))
    {
      $user = array();
      $user['ID'] = $row[0];
      $user['NAME'] = $row[1];
    //  $user['SORT'] = $row[2];
      array_push($user_posts, $user);
    }


    ?>
    <div class="title_razd" style="text-align: center;">
      Должности
    </div>
    <table align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <tr class="tab_query_tit"><td><a class="sublink" href="new.php">Создать новую должность</a></td></tr>
      </tbody>
    </table>
    <table align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>

      </tbody>
    </table>
    <table id="user-posts-table" align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <tr class="tab_query_tit">
        <!--  <td class="tab_query_tit" align="center">ID</td>-->
          <td class="tab_query_tit" align="center">Название должности</td>
        <!--  <td class="tab_query_tit" align="center">Сортировка</td>-->
          <td class="tab_query_tit" align="center">Действия</td>
        </tr>
        <?
        foreach ($user_posts as $key => $value) {
          ?>
          <tr id="post-<?=$value['ID']?>">
            <!--<td class="tab_td_marg" style="text-align: center;"><a href="edit.php?id=<?=$value['ID']?>"><?=$value['ID']?></a></td>-->
            <td class="tab_td_marg" style="text-align: center;"><a href="edit.php?id=<?=$value['ID']?>"><?=$value['NAME']?></a></td>
          <!--  <td class="tab_td_marg" style="text-align: center;"><?=$value['SORT']?></td>-->
            <td class="tab_td_marg" style="text-align: center;"><img onclick="delete_post(this.id);" id="post-del-<?=$value['ID']?>" daAta-name="<?=$value['NAME']?>" data-id="<?=$value['ID']?>" style="cursor: pointer;" src="/acc/i/del.gif"/></td>
          </tr>
          <?
        }
        ?>
      </tbody>
    </table>
    <script type="text/javascript">
      function delete_post(id) {
        post_id = document.getElementById(id).getAttribute('data-id');
        name = document.getElementById(id).getAttribute('data-name');
        $.ajax({
          url: 'delete_post.php',
          method: 'POST',
          data: {post_id: post_id},
          dataType: 'html',
          async: false,
          success: function(data){
            document.getElementById('post-' + post_id).remove();
            alert('Должность "' + name + '" удалена');
          }
        });
      }
    </script>

  </body>
</html>



<? ob_end_flush(); ?>
