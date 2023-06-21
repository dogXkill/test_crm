<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

if (!isset($_GET['id']) || !is_numeric($_GET['id']) )
{
  header('Location: /acc/sprav/user_posts');
}

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
if ($user_access['sprav_access'] == '0' || empty($user_access['sprav_access'])) {
  header('Location: /');
}

$post_id = $_GET['id'];

$q = "SELECT * FROM doljnost WHERE id = $post_id";
$r = mysql_query("$q");
$arr = mysql_fetch_assoc($r);

$name = $arr['name'];
//$sort = $arr['sort'];

?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html" charset="windows-1251" />
    <title>Printfolio intranet v.2</title>
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

    <div class="title_razd" style="text-align: center;">
      �������������� ��������� "<?=$name?>"
    </div>
    <table align=center width="1200" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <tr class="tab_query_tit"><td align="center"><a class="sublink" href="/acc/sprav/user_posts/">������ ���� ����������</a></td></tr>
      </tbody>
    </table>
    <table id="stamp-edit-table" align=center width="1200" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <td align="center">
          <form action="" name="postform" method="post" id="post-edit-form" enctype="multipart/form-data">
            <table cellspacing="3" cellpadding="4" border="0">
              <tbody>
                <tr>
                  <td align="right">��������:</td>
                  <td><span class="err">*</span></td>
                  <td><input name="post_name" id="post-name" type="text" size="30" value="<?=$name?>"/></td>
                </tr>
              <!--  <tr>
                  <td align="right">����������:</td>
                  <td><span class="err">*</span></td>
                  <td><input name="post_sort" id="post-sort" type="text" size="30" value="<?=$sort?>"/></td>
                </tr>-->
              </tbody>
          </table>
          </form>
          <table>
            <tbody>
              <tr>
                <td align="center">
                  <button class="users_frm_butt" onclick="save_post()">���������</button>
                </td>
                <td><span></span></td>
                <td>
                  <button class="users_frm_butt" onclick="backtolist()">������</button>
                </td>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tbody>
    </table>

    <script type="text/javascript">
      function backtolist() {
        window.location = "/acc/sprav/user_posts/";
      }

      function save_post() {
        var errors = '';
        var name = document.getElementById('post-name').value;
        if (name == null || name == '') {
          errors = errors + '������� �������� ���������! ';
        }
      /*  var sort = Number(document.getElementById('post-sort').value);
        if (sort == null || sort == '') {
          errors = errors + '������� ������� ����������! ';
        }
        if (isNaN(sort)) {
          errors = errors + '������� ���������� �� �������� ������! ';
        }*/
        var post_id = <?=$post_id?>;

        if (errors == '' || errors == null) {
          $.ajax({
            url: 'handler.php',
            method: 'POST',
            //data: {name: name, sort: sort, post_id: post_id},
            data: {name: name, post_id: post_id},
            dataType: 'html',
            async: false,
            success: function(data){
              if (data !== null && data !== '') {
                alert (data);
              } else {
                window.location = 'index.php';
              }

            }
          });
        } else {
          alert (errors);
        }

      }
    </script>
  </body>
</html>


<? ob_end_flush(); ?>
