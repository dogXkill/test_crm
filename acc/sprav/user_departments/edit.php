<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

if (!isset($_GET['id']) || !is_numeric($_GET['id']) )
{
  header('Location: /acc/sprav/user_departments');
}

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
if ($user_access['sprav_access'] == '0' || empty($user_access['sprav_access'])) {
  header('Location: /');
}

$department_id = $_GET['id'];

$q = "SELECT * FROM user_departments WHERE id = $department_id";
$r = mysql_query("$q");
$arr = mysql_fetch_assoc($r);

$name = $arr['name'];
$sort = $arr['sort'];
$department_id = $arr['id'];
$submission = $arr['submission'];
$is_division = $arr['is_division'];
$other_departments = array();
$q = "SELECT * FROM user_departments ORDER BY SORT ASC";
$r = mysql_query("$q");
while ($row = mysql_fetch_row($r))
{
  if ($row[0] !== $department_id) {
    $other_department = array();
    $other_department['id'] = $row[0];
    $other_department['name'] = $row[1];
    $other_department['submission'] = $row[3];
    array_push($other_departments, $other_department);
  }
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html" charset="windows-1251" />
    <title>Редактирование отдела</title>
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
      Редактирование отдела "<?=$name?>"
    </div>
    <table align=center width="1200" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <tr class="tab_query_tit"><td align="center"><a class="sublink" href="/acc/sprav/user_departments/">Список всех отделов</a></td></tr>
      </tbody>
    </table>
    <table id="stamp-edit-table" align=center width="1200" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <td align="center">
          <form action="" name="departmentform" method="post" id="department-edit-form" enctype="multipart/form-data">
            <table cellspacing="3" cellpadding="4" border="0">
              <tbody>
                <tr>
                  <td align="right">Название:</td>
                  <td><span class="err">*</span></td>
                  <td><input name="department_name" id="department-name" type="text" size="30" value="<?=$name?>"/></td>
                </tr>
                <tr>
                  <td align="right">Сортировка:</td>
                  <td><span class="err">*</span></td>
                  <td><input name="department_sort" id="department-sort" type="text" size="30" value="<?=$sort?>"/></td>
                </tr>
                <tr>
                  <td align="right">Подчинение другому отделу:</td>
                  <td><span class="err"></span></td>
                  <td>
                    <select name="department_submission" id="department-submission">
                      <option value="0">нет</option>
                      <?
                      foreach ($other_departments as $key => $value) {
                        if ($value['id'] == $submission) {
                          $selected = ' selected';
                        } else {
                          $selected = '';
                        }
                        ?> <option value="<?=$value['id']?>" <?=$selected?>><?=$value['name']?></option> <?
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td align="right">Обособленное подразделение:</td>
                  <td><span class="err"></span></td>
                  <td>
                    <select name="is_division" id="is_division">
                      <option <?echo($is_division == 0)?' selected ':'';?> value="0">нет</option>
                      <option <?echo($is_division == 1)?' selected ':'';?> value="1">да</option>
                    </select>
                  </td>
                </tr>
              </tbody>
          </table>
          </form>
          <table>
            <tbody>
              <tr>
                <td align="center">
                  <button class="users_frm_butt" onclick="save_department()">Сохранить</button>
                </td>
                <td><span></span></td>
                <td>
                  <button class="users_frm_butt" onclick="backtolist()">Отмена</button>
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
        window.location = "/acc/sprav/user_departments/";
      }

      function save_department() {
        var errors = '';
        var name = $('#department-name').val();
        if (name == null || name == '') {
          errors = errors + 'Введите название отдела! ';
        }
        var sort = Number($('#department-sort').val());
        if (sort == null || sort == '') {
          errors = errors + 'Введите порядок сортировки! ';
        }
        if (isNaN(sort)) {
          errors = errors + 'Порядок сортировки не является числом! ';
        }
        var submission = $('#department-submission').val();
        var is_division = $('#is_division').val();
        var department_id = <?=$department_id?>;
        var data = {name: name, sort: sort, department_id: department_id, submission: submission, is_division: is_division};
        console.log(data);
        if (errors == '' || errors == null) {
          $.ajax({
            url: 'handler.php',
            method: 'POST',
            data: {name: name, sort: sort, department_id: department_id, submission: submission, is_division: is_division},
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
