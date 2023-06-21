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
    <title>Отделы</title>
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
    $user_departments = array();
    $q = "SELECT * FROM user_departments ORDER BY sort ASC";
    $r = mysql_query("$q");

    while ($row = mysql_fetch_assoc($r))
    {
      $department = array();
      $department['ID'] = $row['id'];
      $department['NAME'] = $row['name'];
      $department['SORT'] = $row['sort'];
      $department['SUBMISSION'] = $row['submission'];
      $department['IS_DIVISION'] = $row['is_division'];

      array_push($user_departments, $department);
    }
    foreach ($user_departments as $key => $value) {
      $subm_id = $value['SUBMISSION'];
      foreach ($user_departments as $key2 => $value2) {
        if ($subm_id == $value2['ID']) {
          $user_departments[$key]['SUBMISSION_NAME'] = $value2['NAME'];
        }
      }
      unset($subm_id);
    }
    ?>
    <div class="title_razd" style="text-align: center;">
      Список отделов
    </div>
    <table align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <tr class="tab_query_tit"><td><a class="sublink" href="new.php">Создать новый отдел</a></td></tr>
      </tbody>
    </table>
    <table align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>

      </tbody>
    </table>
    <table id="user-departments-table" align=center width="1700" cellspacing="0" cellpadding="5" border="0" bgcolor="#F6F6F6">
      <tbody>
        <tr class="tab_query_tit">
        <!--  <td class="tab_query_tit" align="center">ID</td>-->
          <td class="tab_query_tit" align="center">Название отдела</td>
          <td class="tab_query_tit" align="center">Сортировка</td>
          <td class="tab_query_tit" align="center">Подчинение другому отделу</td>
          <td class="tab_query_tit" align="center">Обособленное подразделение</td>
          <td class="tab_query_tit" align="center">Действия</td>
        </tr>
        <?
        foreach ($user_departments as $key => $value) {
          ?>
          <tr id="department-<?=$value['ID']?>">
            <!--<td class="tab_td_marg" style="text-align: center;"><a href="edit.php?id=<?=$value['ID']?>"><?=$value['ID']?></a></td>-->
            <td class="tab_td_marg" style="text-align: center;"><a href="edit.php?id=<?=$value['ID']?>"><?=$value['NAME']?></a></td>
            <td class="tab_td_marg" style="text-align: center;"><?=$value['SORT']?></td>
            <td class="tab_td_marg" style="text-align: center;"><?=$value['SUBMISSION_NAME']?></td>
            <td class="tab_td_marg" style="text-align: center;"><?echo ($value['IS_DIVISION'] == 1) ? 'да' : 'нет';?></td>
            <td class="tab_td_marg" style="text-align: center;"><img onclick="delete_department(this.id);" id="department-del-<?=$value['ID']?>" data-name="<?=$value['NAME']?>" data-id="<?=$value['ID']?>" style="cursor: pointer;" src="/acc/i/del.gif"/></td>
          </tr>
          <?
        }
        ?>
      </tbody>
    </table>
    <script type="text/javascript">
      function delete_department(id) {
        department_id = document.getElementById(id).getAttribute('data-id');
        //var department_id = $('#' + id).attr('data-id');
        name = document.getElementById(id).getAttribute('data-name');
        //var name = $('#' + id).attr('data-name');
        var check = confirm('Вы действительно хотите удалить отдел "' + name + '"?');
        if (check == true) {
          $.ajax({
            url: 'delete_department.php',
            method: 'POST',
            data: {department_id: department_id},
            dataType: 'html',
            async: false,
            success: function(data){
              numb = Number(data);
              if (numb > 0) {
                alert ('Отдел "' + name + '" пока не может быть удален, т.к. в нем присутствуют активные сотрудники!');
              } else {
                document.getElementById('department-' + department_id).remove();
                alert('Отдел "' + name + '" удален');
              }

            }
          });
        }
      }
    </script>

  </body>
</html>



<? ob_end_flush(); ?>
