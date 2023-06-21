<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

//define('IMG_PATCH', '/i/users/');
$auth = false;
$oper = $_GET['oper'];
$id='';

//if(isset($_GET['oper']) && ($_GET['oper'] == '')){$oper = 'new';}

$error = '';
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
require_once("../includes/im_rez.inc.php");

// функция mysql_real_escape_string только в более коротком виде
function m_es($str) {
	return mysql_real_escape_string($str);
}

function h_sp($str) {
	return htmlspecialchars($str);
}

$tpus = $user_type;		// тип пользователя

// --- СТАРЫЕ ДОСТУПЫ ---



if ($user_access['show_departments'] == '0' || empty($user_access['show_departments'])) {
	header("Location: /");
}


// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'acc') || ($user_type == 'adm')) ? 1 : 0;

				if(isset($_POST['save_us']) && trim($_POST['save_us'])) {
					$id = $_POST['save_id'];
					$oper = $_POST['oper'];




					if(!$error) {
						// РЕДАКТИРОВАНИЕ ПОЛЬЗОВАТЕЛЯ
						$give_date = $_POST['give_year'].'-'.$_POST['give_month'].'-'.$_POST['give_day'];

						$user_group = $_POST['user_group'];
						if ($user_group !== 'нет') {
							$q = "SELECT * FROM user_groups WHERE name = '$user_group'";
							$r = mysql_query("$q");
							$arr = mysql_fetch_assoc($r);
							$user_group_id = $arr['id'];
						} else {
							$user_group_id = 0;
						}
						$user_department = $_POST['user_department'];
						if ($user_department !== 'нет') {
							$q = "SELECT * FROM user_departments WHERE name = '$user_department'";
							$r = mysql_query("$q");
							$arr = mysql_fetch_assoc($r);
							$user_dep_id = $arr['id'];
						} else {
							$user_dep_id = 0;
						}





						$works_at_home = $_POST['at_home'];
						$accounting_user = $_POST['accounting_user'];
						$account_access = $_POST['account_access'];
						$account_access_dep = implode('|', $_POST['account_access_dep']);
						$account_access_group = implode('|', $_POST['account_access_group']);
						$list_access = $_POST['list_access'];
						$list_access_group = implode('|', $_POST['list_access_group']);
						$list_access_dep = implode('|', $_POST['list_access_dep']);
						$table_access = $_POST['table_access'];
						$table_access_group = implode('|', $_POST['table_access_group']);
						$table_access_dep = implode('|', $_POST['table_access_dep']);
						$manager_plan_access = $_POST['manager_plan_access'];
						$statistics_access = $_POST['statistics_access'];
						$application_response = $_POST['application_response'];
						$material_response = $_POST['material_response'];
						$edit_users = implode('|', $_POST['edit_users']);
						$show_departments = implode('|', $_POST['show_departments']);
                        $pr_year = $_POST['pr_year'];
                        $pr_month = $_POST['pr_month'];
                        $pr_day = $_POST['pr_day'];
                        $date_work = $pr_year . '-' . $pr_month . '-' . $pr_day;
						$show_pass = $_POST['show_pass'];
						$dism_day = $_POST['dism_day'];
						$dism_month = $_POST['dism_month'];
						$dism_year = $_POST['dism_year'];
						$dism_date = $dism_year . '-' . $dism_month . '-' . $dism_day;
                        $jobs_access = implode('|', $_POST['jobs_access_group']);
						$edit_shipments = $_POST['edit_shipments'];
						$show_pass = $_POST['show_pass'];
						$shop_access = $_POST['shop_access'];
						$tasks_access = $_POST['tasks_access'];
						$sprav_access = $_POST['sprav_access'];
						$plans_access = $_POST['plans_access'];
						$proizv_access = $_POST['proizv_access'];
						$proizv_access_type = $_POST['proizv_access_type'];
						$proizv_access_edit = $_POST['proizv_access_edit'];
						$logistics_access = $_POST['logistics_access'];
						$tabl_access = $_POST['tabl_access'];
						$order_access = $_POST['order_access'];
						$order_access_type = $_POST['order_access_type'];
						$order_access_edit = $_POST['order_access_edit'];
						$order_access_payment = $_POST['order_access_payment'];
						$payment_edit_num = $_POST['payment_edit_num'];
						$shipped_edit = $_POST['shipped_edit'];
						$main_info_access = $_POST['main_info_access'];
						$allow_edit_access = $_POST['allow_edit_access'];

                        $surname	=	$_POST['surname'];
                        $name	=	$_POST['name'];
                        $father	=	$_POST['father'];
                        $email	=	$_POST['email'];
                        $mobile	=	$_POST['mobile'];
                        $login	=	$_POST['login'];
                        $pass	=	$_POST['pass'];
                        $type	=	$_POST['type'];
                        $doljnost	=	$_POST['doljnost'];
                        $oklad	=	$_POST['oklad'];
                        $socoklad	=	$_POST['socoklad'];
                        $work_time	=	$_POST['work_time'];
                        $note	=	$_POST['note'];
						$amo_id=$_POST['amo_id'];


						if($oper == 'edit') {

                        $query = "UPDATE users SET user_group='$user_group_id', user_department='$user_dep_id', accounting_user='$accounting_user', works_at_home='$works_at_home',
                        account_access='$account_access', account_access_dep='$account_access_dep', account_access_group='$account_access_group', list_access='$list_access',
                        list_access_group='$list_access_group', list_access_dep='$list_access_dep', table_access='$table_access', table_access_group='$table_access_group',
                        table_access_dep='$table_access_dep',  manager_plan_access='$manager_plan_access', statistics_access='$statistics_access', application_response='$application_response',
                        material_response='$material_response', edit_users='$edit_users', show_departments='$show_departments', dismissal_date='$dism_date', jobs_access='$jobs_access',
                        edit_shipments='$edit_shipments', show_pass='$show_pass', shop_access='$shop_access', tasks_access='$tasks_access', sprav_access='$sprav_access',
                        plans_access='$plans_access', proizv_access='$proizv_access', proizv_access_type='$proizv_access_type', proizv_access_edit='$proizv_access_edit',
                        logistics_access='$logistics_access', tabl_access='$tabl_access', order_access='$order_access', order_access_type='$order_access_type', order_access_edit='$order_access_edit',
                        order_access_payment='$order_access_payment', payment_edit_num='$payment_edit_num', shipped_edit='$shipped_edit', main_info_access='$main_info_access', allow_edit_access='$allow_edit_access',
                        surname='$surname', name='$name', father='$father', date_work='$date_work', email='$email', mobile='$mobile', login='$login', pass='$pass', type='$type', doljnost='$doljnost',
                        oklad='$oklad', socoklad='$socoklad', work_time='$work_time', note='$note',amo_id='$amo_id' WHERE uid='$id'";
                                mysql_query($query);
                                echo mysql_error();

                                $referer=$_POST['referer'];
    					        header("Location: ".$referer);

					}

						// ДОБАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯ
						elseif($oper == 'new') {

						    $jid=mysql_query("SELECT MAX(job_id) FROM users WHERE job_id < '1200'");
                            $jid = mysql_fetch_array($jid);
						    $new_job_id = $jid[0]+1;

								$user_group = $_POST['user_group'];
								if ($user_group !== 'нет') {
									$q = "SELECT * FROM user_groups WHERE name = '$user_group'";
									$r = mysql_query("$q");
									$arr = mysql_fetch_assoc($r);
									$user_group_id = $arr['id'];
								} else {
									$user_group_id = 0;
								}
								$user_department = $_POST['user_department'];
								if ($user_department !== 'нет') {
									$q = "SELECT * FROM user_departments WHERE name = '$user_department'";
									$r = mysql_query("$q");
									$arr = mysql_fetch_assoc($r);
									$user_dep_id = $arr['id'];
								} else {
									$user_dep_id = 0;
								}
								$works_at_home = $_POST['at_home'];
								$accounting_user = $_POST['accounting_user'];
								$account_access = $_POST['account_access'];
								$account_access_dep = implode('|', $_POST['account_access_dep']);
								$account_access_group = implode('|', $_POST['account_access_group']);
								$list_access = $_POST['list_access'];
								$list_access_group = (isset($_POST['list_access_group']) && !empty($_POST['list_access_group'])) ? implode('|', $_POST['list_access_group']) : 0;
							//	$list_access_group = implode('|', $_POST['list_access_group']);
								$list_access_dep = (isset($_POST['list_access_dep']) && !empty($_POST['list_access_dep'])) ? implode('|', $_POST['list_access_dep']) : 0;
							//	$list_access_dep = implode('|', $_POST['list_access_dep']);
								$table_access = $_POST['table_access'];
								$table_access_group = implode('|', $_POST['table_access_group']);
								$table_access_dep = implode('|', $_POST['table_access_dep']);
								$manager_plan_access = $_POST['manager_plan_access'];
								$statistics_access = $_POST['statistics_access'];
								$application_response = $_POST['application_response'];
								$material_response = $_POST['material_response'];
								$edit_users = implode('|', $_POST['edit_users']);
								$show_departments = implode('|', $_POST['show_departments']);
								$jobs_access = (isset($_POST['jobs_access_group']) && !empty($_POST['jobs_access_group'])) ? implode('|', $_POST['jobs_access_group']) : 0;
								$edit_shipments = $_POST['edit_shipments'];
								$show_pass = $_POST['show_pass'];
								$shop_access = $_POST['shop_access'];
								$tasks_access = $_POST['tasks_access'];
								$sprav_access = $_POST['sprav_access'];
								$plans_access = $_POST['plans_access'];
								$proizv_access = $_POST['proizv_access'];
								$proizv_access_type = $_POST['proizv_access_type'];
								$proizv_access_edit = $_POST['proizv_access_edit'];
								$logistics_access = $_POST['logistics_access'];
								$tabl_access = $_POST['tabl_access'];
								$order_access = $_POST['order_access'];
								$order_access_type = $_POST['order_access_type'];
								$order_access_edit = $_POST['order_access_edit'];
								$order_access_payment = $_POST['order_access_payment'];
								$payment_edit_num = $_POST['payment_edit_num'];
                            	$shipped_edit = $_POST['shipped_edit'];
								$main_info_access = $_POST['main_info_access'];
								$allow_edit_access = $_POST['allow_edit_access'];
								$amo_id=$_POST['amo_id'];

							//	$query = sprintf("INSERT INTO users (user_group, user_department, accounting_user, works_at_home, account_access, account_access_dep, account_access_group, list_access, list_access_group, list_access_dep, table_access, table_access_group, table_access_dep, manager_plan_access, statistics_access, application_response, material_response, jobs_access, job_id, surname, name, father, date_birth, date_work, email, email_hom, icq, mobile, home_tel, agent_tel, address, passport_num, give, give_date, login, pass, type, doljnost, oklad, socoklad, note, edit_shipments, edit_users, show_departments, show_pass, shop_access, tasks_access, sprav_access, plans_access, proizv_access, proizv_access_type, proizv_access_edit, logistics_access, tabl_access, order_access, order_access_type, order_access_edit, order_access_payment, payment_edit_num, shipped_edit, main_info_access, allow_edit_access) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $user_group_id, $user_dep_id, $accounting_user, $works_at_home, $account_access, $account_access_dep, $account_access_group, $list_access, $list_access_group, $list_access_dep, $table_access, $table_access_group, $table_access_dep, $manager_plan_access, $statistics_access, $application_response, $material_response, $jobs_access, $new_job_id, $_POST['surn'],  $_POST['name'], $_POST['fat'], $_POST['br_year'].'-'.$_POST['br_month'].'-'.$_POST['br_day'],  $_POST['pr_year'].'-'.$_POST['pr_month'].'-'.$_POST['pr_day'], $_POST['email'], $_POST['email_home'], $_POST['icq'], $_POST['mobile'], $_POST['home_tel'], $_POST['agent_tel'], $_POST['address'], $_POST['passport_num'], $_POST['give'], $give_date, $_POST['login'], $_POST['pass'], $_POST['type'], $_POST['doljnost'], $_POST['oklad'], $_POST['socoklad'], $_POST['note'], $edit_shipments, $edit_users, $show_departments, $show_pass, $shop_access, $tasks_access, $sprav_access, $plans_access, $proizv_access, $proizv_access_type, $proizv_access_edit, $logistics_access, $tabl_access, $order_access, $order_access_type, $order_access_edit, $order_access_payment, $payment_edit_num, $shipped_edit, $main_info_access, $allow_edit_access);
                                  $query = "INSERT INTO users(
                                  job_id, administration, proizv, nadomn, surname, name, father, date_work, email,  mobile, login, pass, type, doljnost, oklad, socoklad, work_time, note,  user_department,
                                  user_group, works_at_home, accounting_user, account_access, account_access_dep, account_access_group, list_access, list_access_group, list_access_dep, table_access,
                                  table_access_group, table_access_dep, manager_plan_access, statistics_access, application_response, material_response, dismissal_date, jobs_access, edit_shipments,
                                  edit_users, show_departments, show_pass, main_info_access, tasks_access, plans_access, sprav_access, proizv_access, proizv_access_type, proizv_access_edit, logistics_access,
                                   sotr_access, tabl_access, order_access, order_access_type, order_access_edit, order_access_payment, payment_edit_num, shop_access, shipped_edit, allow_edit_access,amo_id)
                                   VALUES
                                    ('$new_job_id', '$administration', '$proizv', '$nadomn', '$surname', '$name', '$father', '$date_work', '$email', '$mobile', '$login', '$pass',
                                    '$type', '$doljnost', '$oklad', '$socoklad', '$work_time', '$note', '$user_dep_id', '$user_group', '$works_at_home', '$accounting_user',
                                    '$account_access', '$account_access_dep', '$account_access_group', '$list_access', '$list_access_group', '$list_access_dep', '$table_access',
                                    '$table_access_group', '$table_access_dep', '$manager_plan_access', '$statistics_access', '$application_response', '$material_response',
                                    '$dismissal_date', '$jobs_access', '$edit_shipments', '$edit_users', '$show_departments', '$show_pass', '$main_info_access', '$tasks_access',
                                    '$plans_access', '$sprav_access', '$proizv_access', '$proizv_access_type', '$proizv_access_edit', '$logistics_access', '$sotr_access',
                                    '$tabl_access', '$order_access', '$order_access_type', '$order_access_edit', '$order_access_payment', '$payment_edit_num', '$shop_access',
                                    '$shipped_edit', '$allow_edit_access','$amo_id')";

                        }
							mysql_query($query);
                              //  echo mysql_error()."<br>".$query;
							$new_id = mysql_insert_id();


						   $referer=$_POST['referer'];
					      header("Location: ".$referer);
						}
					}
$rand = microtime(true).rand();

$tmonth = date("m");
$tyear = date("Y");
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Сотрудники</title>
<link href="../style.css?cache=<?=$rand?>" rel="stylesheet" type="text/css" />
<link href="../includes/new.css?cache=<?=$rand?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script type="text/javascript" src="../includes/js/mask.js"></script>
</head>
<style>
	.none {
		display: none;
	}
	#department-filter {
		width: 250px;
	}
	.filter-select {
		padding: 2px 0px 3px 0px;
		border: 1px solid #cecece;
		background: white;
		border-radius: 8px;
		font-size: 18px;
	}
	.filter-input {
		padding: 3px;
		border: 1px solid #cecece;
		background: white;
		border-radius: 8px;
		font-size: 18px;
	}
	.table-tit {
		border: 1px solid black;
		border-collapse: collapse;
		font-weight: bold;
	}
	.table-td {
		border: 1px solid black;
		border-collapse: collapse;
		font-size: 14px;
	}
	.table-td-bold {
		border: 1px solid black;
		border-collapse: collapse;
		font-weight: bold;
	}
	.choose-all-btn {
		position: absolute;
		margin-top: 30px;
		margin-left: 10px;
	}
	.choose-all-btn:hover {
		cursor: pointer;
		text-decoration: underline;
	}
	.multi-select {
		width: 320px;
		height: 150px;
	}


</style>
<script language="JavaScript" type="text/javascript">

var oper = '<?=$oper;?>';

<?
if (isset($_GET['archive']) && $_GET['oper'] == 'archive') {
	$archive_link = '&archive=1&oper=archive';
} else {
	$archive_link = '';
}
?>
var archive_link = '<?=$archive_link;?>';
</script>

<style media="screen">
.dep_select_popup {
position: fixed;
left: 0;
top: 0px;
width: 100%;
height: 100%;
display: flex;
z-index: 10;
justify-content: center;
font-size: 16px;
}
.dep_popup_info {
position: fixed;
background-color: #EEEEEE;
top: 50px;
height: auto;
width: 450px;
border-radius: 15px;
border: 1px solid #000;
z-index: 20;
}
.dep_popup_head {
display: flex;
justify-content: center;
width: 100%;
font-size: 20px;
padding: 10px;
}
.dep_input {
width: 15px;
height: 15px;
cursor: pointer;
}
.dep_popup_tr {
padding: 2px;
}
.dep_popup_btn_cont {
display: flex;
justify-content: center;
}
.select_all_deps {
text-decoration: underline;
padding: 10px;
cursor: pointer;
}
.select_all_deps:hover {
opacity: 0.5;
}

#choose_dep_btn {
margin-top: 3px;
margin-left: 3px;
margin-bottom: 10px;
height: 40px;
width: auto;
padding: 5px;
font-size: 20px;
border-radius: 10px;
border: 1px solid;
padding-left: 20px;
padding-right: 20px;
cursor: pointer;
}
.popup_open_btn {
padding: 2px 0px 3px 0px;
border: 1px solid #cecece;
background: white;
border-radius: 8px;
font-size: 18px;
padding-left: 10px;
padding-right: 10px;
cursor: pointer;
position: relative;
}

@media (max-width: 800px) {
	.dep_select_popup {
		font-size: 13px;
	}
	.dep_popup_tr {
		padding: 1px;
	}
	.dep_popup_info {
		width: 330px;
	}
}
@media (max-width: 480px) {
	.dep_popup_info {
		width: 270px;
	}
}
</style>

<body>

<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>

<? require_once("../templates/top.php"); ?>
<table align=center width=750 border=0>

<tr>
<td colspan=3>
<br>
<?
$name_curr_page = 'users';
require_once("../templates/main_menu.php");?>
<table width=1200 border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">
<?
$tit = 'Пользователи';
if($oper == 'new')
	$tit = 'Новый пользователь';
if(isset($_GET['edit']))
	$tit = 'Редактирование пользователя';

?>
	<tr>
		<td align="center" class="title_razd"><?=@$tit?></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="10" cellpadding="0">
				<tr>
					<td width="350">

<?
// Табель
if ($user_access['accounting_user'] == 1 && $user_access['table_access'] == 1) {
  include("../applications/timetable/timetable_form.php");
}
// Ведомость
if ($user_access['accounting_user'] == 1 && $user_access['list_access'] == 1) {
  include("../applications/timetable/report_form.php");
}
?>

| <a href="users.php?oper=archive<? if(!$_GET['archive']) {echo '&archive=1'; } if ($_GET['surname']) {echo '&surname=' . $_GET['surname'];} if ($_GET['search_text']) {echo '&search_text=1';} if ($_GET['name']) {echo '&name=' . $_GET['name'];} if ($_GET['search_id']) {echo '&search_id=' . $_GET['search_id'];}?>" class="sublink" <? if($_GET["archive"]) {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>> <?if ($_GET['archive'] == 1) {echo 'Работающие';} else {echo 'Архивные';}?></a>




					 </td>
					<td width="350" style="display: flex; align-items: center;">
						<?if ($user_access['edit_users'] !== '0' && !empty($user_access['edit_users']) && $oper == "") {
							?>
							<a href="users.php?oper=new"><img src="../../i/add_user.png" width="32" height="32" alt="" valign=middle></a>
							<a href="users.php?oper=new" class="sublink" style="font-weight:bold;border-bottom:none; padding-left: 3px;">создать пользователя</a>
							<?}?>
					</td>
				</tr>
				<tr>


				<?
				if (isset($_GET['group']) && !isset($_GET['department']) ) {
				//	$group_add = '?group=' . $_GET['group'];
					$dep_add = '&group=' . $_GET['group'];
				}
				if (isset($_GET['department']) && !isset($_GET['group']) ) {
				//	$dep_add = '?group=' . $_GET['group'];
					$group_add = '&department=' . $_GET['department'];
				}
				if (isset($_GET['department']) && isset($_GET['group']) ) {
					$dep_add = '&group=' . $_GET['group'];
					$group_add = '&department=' . $_GET['department'];
				}




						$groups = array();
						$groups_q = "SELECT * FROM user_groups ORDER BY sort ASC";
						$groups_r = mysql_query("$groups_q");
						while($row = mysql_fetch_row($groups_r)) {
							$group = array();
							$group['group_id'] = $row[0];
							$group['group_name'] = $row[1];
							$group['group_sort'] = $row[2];
							array_push($groups, $group);
						}
						?>
						<td style="display: none;" id="group-filter">
							<select class="filter-select" onchange="location = this.value;">
								<option value="/acc/users/users.php?group=all<?=$group_add?>">Все группы</option>
						<?
							if(isset($_GET['group'])) {
								$get_group = $_GET['group'];
							}
							foreach ($groups as $key => $value) {
								$selected = ($get_group == $value['group_id']) ? 'selected' : '';
								?>
								<option value="/acc/users/users.php?group=<?=$value['group_id']?><?=$group_add?>" <?=$selected?>><?=$value['group_name']?></option>
								<?
					 		}
						?>
						</select>
					</td>
						<?
						$departments = array();
						$departments_q = "SELECT * FROM user_departments ORDER BY name ASC";
						$departments_r = mysql_query("$departments_q");
						while($row = mysql_fetch_row($departments_r)) {
							$department = array();
							$department['dep_id'] = $row[0];
							$department['dep_name'] = $row[1];
							$department['dep_sort'] = $row[2];
							array_push($departments, $department);
						}
						$deps = $departments;
						$deps_count = count($departments);
					 	$allowed_deps = explode('|', $user_access['show_departments']);


						?>
						<td style="display: none;">
							<select id="department-filter" class="filter-select" onchange="location = this.value;">
								<option value="/acc/users/users.php?department=all<?=$dep_add?>">Все отделы</option>
						<?
							if(isset($_GET['department'])) {
								$get_dep = $_GET['department'];
							}
							foreach ($departments as $key => $value) {
								$selected = ($get_dep == $value['dep_id']) ? 'selected' : '';
								?>
								<option value="/acc/users/users.php?department=<?=$value['dep_id']?><?=$dep_add?>" <?=$selected?>><?=$value['dep_name']?></option>
								<?
							}
						?>
						</select>
					</td>
					<td>
						<?
					 	if (count($allowed_deps) > 0) {
							if (isset($_GET['department']) && $_GET['department'] !== 'all' && count(explode('_', $_GET['department'])) > 0 && count(explode('_', $_GET['department'])) != $deps_count) {
								$text = 'Выбрано ' . count(explode('_', $_GET['department'])) . ' отделов';
							} elseif (!isset($_GET['department']) || count(explode('_', $_GET['department'])) == $deps_count) {
								$text = 'Все отделы';
							} else {
								$text = 'Выберите отдел...';
							}
							?>
							<button type="button" class="popup_open_btn" id="popup_open" data-open="0" name="button" return false; onclick="depPopup()"><?=$text?></button>
							<div class="dep_select_popup" style="display: none;" id="dep_select_popup" data-open="0" onclick="hidePopup()">
								<div class="dep_popup_info">
									<div class="dep_popup_head" style='cursor:move;padding-bottom: 5px;
    padding-top: 5px;
    margin-bottom: 0px;'>Выберите отделы</div>
									<?
									if (count(explode('_', $_GET['department'])) == $deps_count) {
										$text = 'снять все';
										$btnid = 'unset_all_deps';
									} elseif (!isset($_GET['department'])) {
										$text = 'снять все';
										$btnid = 'unset_all_deps';
									} else {
										$text = 'отметить все';
										$btnid = 'select_all_deps';
									}

									?>
									<div class="select_all_deps" style='padding-bottom: 5px;
    padding-top: 5px;'id="<?=$btnid?>" type="button" onclick="depPopup()"><?=$text?></div>
									<div class="dep_popup_list">
										<?
										$selected_deps = explode('_', $_GET['department']);
										foreach ($deps as $key => $dep) {
											if (isset($_GET['department']) && in_array($dep['dep_id'], $selected_deps) ) {
												$checked = ' checked ';
											} elseif (!isset($_GET['department'])) {
												$checked = ' checked ';
											} else {
												$checked = '';
											}
											?>
											<div class="dep_popup_tr" data-dep="<?=$dep['dep_id']?>">
												<input id="dep-<?=$dep['dep_id']?>" data-dep="<?=$dep['dep_id']?>" class="dep_input" type="checkbox" name="" value="" <?=$checked?>>
												<span><label style="cursor: pointer;" for="dep-<?=$dep['dep_id']?>"><?=$dep['dep_name']?></label></span>
											</div>
											<?
										}
										?>
									</div>
									<div class="dep_popup_btn_cont">
										<button id="choose_dep_btn" type="button" name="choose_dep_btn" onclick="depPopup();">Выбрать</button>
									</div>
								</div>
							</div>
							<?
						}
						?>
					</td>


		<td>
			<?
			$surname = iconv("utf-8", "cp1251", $_GET['surname']);
			$name = iconv("utf-8", "cp1251", $_GET['name']);
			$father = iconv("utf-8", "cp1251", $_GET['father']);
			$search_id = iconv("utf-8", "cp1251", $_GET['search_id']);
			?>

			<input class="filter-input" id="search-surname" onchange="search_fio()" type="text" value="<?=$surname?>" name="search_surname" placeholder="Фамилия"/>
		</td>
		<td>

			<input class="filter-input" id="search-name" onchange="search_fio()" type="text" value="<?=$name?>" name="search_name"/ placeholder="Имя">
		</td>

		<?
			if ($tpus != 'mng') {
				?>
				<td>
					<input class="filter-input" id="search-id" onchange="search_id()" type="text" value="<?=$search_id?>" name="search_father" placeholder="ID сотрудника"/>
				</td>
				<?
			}
		?>

		</tr>
	</table>
</td>
</tr>

<tr>


</tr>

<tr>
	<td align="center">
		<? if($auth) {
				if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['archive'] == "del_archive" ) {
					$date = date('Y-m-d');
					$query = "UPDATE users SET archive='1', dismissal_date='$date' WHERE uid=".$_GET['del'];
					mysql_query($query);
				}
                if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['archive'] == "restore" ) {
					$query = "UPDATE users SET archive='0', dismissal_date='0000-00-00' WHERE uid=".$_GET['del'];
					mysql_query($query);
				}
					if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['archive'] == "del_final" ) {
					$query = "DELETE FROM users WHERE uid=".$_GET['del'];
					mysql_query($query);
					@unlink($_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'small_'.$_GET['del'].'.jpeg');
					@unlink($_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'big_'.$_GET['del'].'.jpeg');
				}

				if($oper == "edit" || $oper == "new") {

					$groups = array();
					$groups_q = "SELECT * FROM user_groups ORDER BY sort ASC";
					$groups_r = mysql_query("$groups_q");
					while($row = mysql_fetch_row($groups_r)) {
						$group = array();
						$group['group_id'] = $row[0];
						$group['group_name'] = $row[1];
						$group['group_sort'] = $row[2];
						array_push($groups, $group);
					}

					$departments = array();
					$deps_q = "SELECT * FROM user_departments ORDER BY name ASC";
					$deps_r = mysql_query("$deps_q");
					while ($row = mysql_fetch_row($deps_r))
					{
						$dep = array();
						$dep['dep_id'] = $row[0];
						$dep['dep_name'] = $row[1];
						array_push($departments, $dep);
					}
					$jobs_headings = array();
					$jobs = array();
					$jobs_q = "SELECT * FROM job_names LEFT JOIN job_types ON job_names.job_type = job_types.id ORDER BY job_types.sort ASC";
					$jobs_r = mysql_query("$jobs_q");
					while ($row = mysql_fetch_row($jobs_r))
					{
						$job = array();
						$job['id'] = $row[0];
						$job['name'] = $row[1];
						$job['type'] = $row[2];
						if (!in_array($row[8], $jobs_headings)) {
							$job['hide'] = 1;
							array_push($jobs_headings, $row[8]);
							$job['full_name'] = '--- ' . $row[8] . ' ---';
							array_push($jobs, $job);
							$job['hide'] = 0;
						}
						$job_type = $row[2];
						$type_q = "SELECT * FROM job_types WHERE id = '$job_type'";
						$type_name = mysql_fetch_assoc(mysql_query("$type_q"));
						$job['full_name'] = $job['name'];
						array_push($jobs, $job);
					}

                if($oper == "edit") {
									if ($_GET['edit'] == '11' && $user_access['uid'] !== '11') {
										header('Location: /');
									}
									if ($_GET['edit'] == $user_access['uid'] && $user_access['uid'] !== '11') {
										header('Location: /');
									}
						$query = "SELECT * FROM users WHERE uid=".$_GET['edit'];
						$res = mysql_query($query);
						$r = mysql_fetch_array($res);
						$job_id = @$r['job_id'];
						$administration = @$r['administration'];
						$proizv = @$r['proizv'];
						$nadomn = @$r['nadomn'];
						$surname = $r['surname'];
						$name = $r['name'];
						$father = 	$r['father'];
						$date_br = explode('-',@$r['date_birth']);
						$date_pr = explode('-',@$r['date_work']);
						$f_users_tp = $r['type'];
						$f_email = $r['email'];
						$f_mobile = $r['mobile'];
						$f_home_tel = $r['home_tel'];
						$f_email_hom = $r['email_hom'];
						$f_icq = $r['icq'];
						$f_agent_tel = $r['agent_tel'];
						$f_passport_num = $r['passport_num'];
						$f_give = $r['give'];
						$date_give = explode('-',@$r['give_date']);
						$f_address = $r['address'];
						$f_note = $r['note'];
						$f_amo=$r['amo_id'];
						$login  = $r['login'];
                        $f_pass = $r['pass'];
   					    $f_repass = $r['pass'];
						$f_archive = $r['archive'];
						$user_group = $r['user_group'];
						$user_department = $r['user_department'];
                        $doljnost = $r['doljnost'];
						$works_at_home = $r['works_at_home'];
						$accounting_user = $r['accounting_user'];
						$account_access = $r['account_access'];
						$account_access_dep = explode('|', $r['account_access_dep']);
						$account_access_group = explode('|', $r['account_access_group']);
						$list_access = $r['list_access'];
						$list_access_group = explode('|', $r['list_access_group']);
						$list_access_dep = explode('|', $r['list_access_dep']);
						$table_access = $r['table_access'];
						$table_access_group = explode('|', $r['table_access_group']);
						$table_access_dep = explode('|', $r['table_access_dep']);
						$manager_plan_access = $r['manager_plan_access'];
						$statistics_access = $r['statistics_access'];
						$application_response = $r['application_response'];
						$material_response = $r['material_response'];
						$edit_users = explode('|', $r['edit_users']);
						$show_departments = explode('|', $r['show_departments']);
						$dismissal_date = ($r['dismissal_date'] == '0000-00-00') ? '' : $r['dismissal_date'];
						$jobs_access = explode('|', $r['jobs_access']);
						$edit_shipments = $r['edit_shipments'];
						$show_pass = $r['show_pass'];
						$shop_access = $r['shop_access'];
						$tasks_access = $r['tasks_access'];
						$sprav_access = $r['sprav_access'];
						$plans_access = $r['plans_access'];
						$proizv_access = $r['proizv_access'];
						$proizv_access_type = $r['proizv_access_type'];
						$proizv_access_edit = $r['proizv_access_edit'];
						$logistics_access = $r['logistics_access'];
						$tabl_access = $r['tabl_access'];
						$order_access = $r['order_access'];
						$order_access_type = $r['order_access_type'];
						$order_access_edit = $r['order_access_edit'];
						$order_access_payment = $r['order_access_payment'];
						$order_stat_access = $r['order_stat_access'];
						$payment_edit_num = $r['payment_edit_num'];
						$shipped_edit = $r['shipped_edit'];
						$main_info_access = $r['main_info_access'];
						$allow_edit_access = $r['allow_edit_access'];
					

						if (!in_array($user_department, explode('|', $user_access['edit_users'])) && $user_department !== '0') {
						 	header('Location: /');
						}
				  }
					if (empty($date_pr)) {
						$cur_date = date('Y-m-d');
						$date_pr = explode('-', $cur_date);

					}
					$char=array("-" => "", "(" => "", ")" => "", " " => "", ";" => "", "+" => "");
					$f_mobile = strtr($f_mobile, $char);
					$first_char = mb_substr($f_mobile, 0, 1);
					if ($first_char == 8) {
					//	$f_mobile = str_replace('8', '+7', $f_mobile);
					$f_mobile = substr_replace($f_mobile, '+7', 0, 1);
					} elseif ($first_char == 7) {
						$f_mobile = '+' . $f_mobile;
					} else {
						$f_mobile = '+7' . $f_mobile;
					}
				//получить amo_id 
				//$f_amo
				?>
				<form id="user_form" data-continue="1" action="" method="post" name="editus" enctype="multipart/form-data" style="border-top: 3px solid #3399CC; padding-top: 10px; margin-top: 10px;">
				<input name="save_id" type="hidden" value="<?=$_GET['edit']?>" />
				<input name="oper" type="hidden" value="<?=$oper?>" />
				<table border="0" cellspacing="3" cellpadding="4">

					<tr>
						<td align="right">Фамилия: <span class="err">*</span> </td>
						<td align="left"><input name="surname" type="text" class="users_frm" id="surname" value="<?=$surname?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Имя: <span class="err">*</span> </td>
						<td align="left"><input name="name" type="text" class="users_frm" id="name" value="<?=$name?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Отчество: <span class="err">*</span> </td>
						<td align="left"><input name="father" type="text" class="users_frm" id="father" value="<?=$father?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Основная должность:</td>
						<td align="left">
							<select class="users_tp" id="doljnost" onchange="" name="doljnost" >
								<option value="0" <?if($d['id']==""){echo "selected";}?>>выберите должность</option>
									<?
									$get_doljnost = mysql_query("SELECT * FROM doljnost ORDER BY name ASC");
									while($d = mysql_fetch_array($get_doljnost)) {
										?>
										<option value="<?=$d['id']; ?>" <?if($d['id']==$doljnost){echo "selected";}?>><?=$d['name'];?></option>
										<?}?>
								</select>
								<span><a href="/acc/sprav/user_posts/" target="_blank">редактировать в справочнике</a></span>
						</td>
					</tr>
					<tr>
						 <td align="right">Отдел: <span class="err">*</span></td>
						 <td align="left">
							 <select class="users_tp" name="user_department" id="user-department-select">
								 <option>нет</option>
								 <?

								 foreach ($departments as $key => $value) {
									 $selected = ($user_department == $value['dep_id']) ? 'selected' : '';
									 if (!in_array($value['dep_id'], explode('|', $user_access['edit_users']))) {
										 $style = 'display: none;';
									 } else {
										 $style = "";
									 }
									 ?>
									 <option data-dep=<?=$value['dep_id']?> <?=$selected?> style="<?=$style?>"><?=$value['dep_name']?></option>
									 <?
								 }
								 ?>
							 </select>
							 <span><a href="/acc/sprav/user_departments/" target="_blank">редактировать в справочнике</a></span>
						 </td>
				 </tr>
					<tr>
						<td align="right">База:</td>
						<td align="left"><input type="text" name=oklad id=oklad  class="users_frm" style="width:100px;" value="<?
						if ($oper !== 'new') {
							echo $r['oklad'];
						} else {
							echo 0;
						}

						?>"  /></td>
					</tr>
					<tr>
						<td align="right">Соцбаза:</td>
						<td align="left"><input type="text" name=socoklad id=socoklad  class="users_frm" style="width:100px;" value="<?
						if ($oper !== 'new') {
							echo $r['socoklad'];
						}	else {
							echo 0;
						}

						?>"  /></td>
					</tr>
					<tr>
						<td align="right">Рабочие часы:</td>
						<td align="left"><input type="text" name=work_time id=work_time  class="users_frm" style="width:100px;" value="<?=$r['work_time'];?>"  /></td>
					</tr>
					<tr>
						<td align="right"><label for="proizv">ID:</label></td>
						<td align="left">
							<input type=text class="users_frm" id=job_id name=job_id <?if($r['job_id'] !==""){echo "disabled";}?>  value="<?if ($r['job_id'] !== "" && $oper !== 'new'){echo $r['job_id'];}else{
								//echo $new_id[0];
								$jid=mysql_query("SELECT MAX(job_id) FROM users WHERE job_id < '1000'");
														$jid = mysql_fetch_array($jid);
								$new_job_id = $jid[0]+1;
								echo $new_job_id;
							}?>"/> номер присваивается автоматически
						</td>
					</tr>
					<tr>
						<td align="right">Логин:</td>
						<td align="left"><input name="login" type="text" class="users_frm" id="login" value="<?=$login?>" size="30" autocomplete="off" onchange="check_new_login()"/>
                        <input type="hidden" id="login_err" value="" />
                        </td>
					</tr>
					<tr>
						<td align="right">Пароль:</td>
						<td align="left"><input name="pass" type="password" class="users_frm" id="pass" value="<?=$f_pass?>" size="30" autocomplete="off" onmouseover="Tip('не менее 2 символов')"  /></td>
					</tr>
					<tr>
						<td align="right">Подтверждение:</td>
						<td align="left"><input name="repass" type="password" class="users_frm" id="repass" value="<?=$f_repass?>" autocomplete="off" size="30" /></td>
					</tr>

					<tr>
						<td align="right">E-mail:</td>
						<td align="left"><input name="email" type="text" class="users_frm" id="email" value="<?=h_sp($f_email)?>" size="30"  autocomplete="off" onchange="check_new_email()"/>
                        <input type="hidden" id="email_err" value="" />
                        </td>
					</tr>
					<tr>
						<td align="right">Мобильный тел.: <span class="err">*</span></td>
						<td align="left"><input name="mobile" type="text" class="users_frm" id="mobile" value="<?=$f_mobile?>" size="30"  autocomplete="off"/></td>
					</tr>
                    <?
					if(($oper == 'edit') || $id) {
						$sel = array();
						$tx = ' selected="selected"';
						$sel[0] = ($f_users_tp == 'oth') ? $tx : '';
						$sel[1] = ($f_users_tp == 'mng') ? $tx : '';
						$sel[2] = ($f_users_tp == 'acc') ? $tx : '';
						$sel[3] = ($f_users_tp == 'adm') ? $tx : '';
						$sel[4] = ($f_users_tp == 'meg') ? $tx : '';
						$sel[5] = ($f_users_tp == 'sup') ? $tx : '';
					}
					else
						$sel = array(' selected="selected"','','','','','');
					?>
					<tr style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
						<td align="right">Тип доступа в интранет:</td>
						<td align="left">
							<select class="users_tp" id="type" name="type" >
							  <option value="oth" <?=$sel[0]?>>Гость</option>
							  <option value="mng" <?=$sel[1]?>>Менеджер</option>
							  <option value="acc" <?=$sel[2]?>>Бухгалтер</option>
							  <option value="adm" <?=$sel[3]?>>Администратор</option>
							  <option value="meg" <?=$sel[4]?>>Мегаадмин</option>
							  <option value="sup" <?=$sel[5]?>>Суперадмин</option>
							</select>
						</td>
					</tr>

										 <tr style="display: none">
								 		 		<td align="right">Группа:</td>
								 				<td align="left">
													<select  class="users_tp" name="user_group" id="user-group-select">
														<option>нет</option>
														<?
														foreach ($groups as $key => $value) {
															$selected = ($user_group == $value['group_id']) ? 'selected' : '';
															?>

															<option <?=$selected?>><?=$value['group_name']?></option>
															<?
														}
														?>
													</select>
												</td>
										</tr>

									 <tr>
										 <td align="right">Сотрудник работает удаленно:</td>
										 <td align="left">
											 <? $checked = ($works_at_home == 1) ? 'checked' : ''; ?>
											 <input type="checkbox" name="at_home" onchange="doubleCheckboxes(this.id)" id="at_home_yes" value="1" <?=$checked?>>
											 <label for="at_home_yes">да</label>
											 <? $checked = ($works_at_home == 0) ? 'checked' : ''; ?>
											 <input type="checkbox" name="at_home" onchange="doubleCheckboxes(this.id)" id="at_home_no" value="0" <?=$checked?>>
											 <label for="at_home_no">нет</label>
										 </td>
									 </tr>

									 <tr style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>" id="jobs_access_tr" class="<?=$class?>">
										<td align="right" <?=$opacity?>>Допуск к добавлению работ:</td>
										<td align="left">
										<div style="display: none;"><?print_r($jobs);?></div>
											<select id="jobs_access_group" class="multi-select" name="jobs_access_group[]" multiple <?=$disabled?>>
												<option value="0">нет</option>
												<?
												foreach ($jobs as $key => $value) {
													if (in_array($value['id'], $jobs_access) && $value['hide'] != 1) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													if ($value['hide'] == 1) {
														$disabled = 'disabled';
														$value['id'] = '';
													} else {
														$disabled = '';
													}
													?><option value="<?=$value['id']?>" <?=$selected?> <?=$disabled?>><?=$value['full_name']?></option><?
												}
												?>
											</select>
											<span class="choose-all-btn" onclick="select_all_opt('jobs_access_group')">Выбрать все</span>
										</td>
									</tr>









									 <tr style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
									 	<td align="right" <?=$opacity?>>Учетный сотрудник:</td>
										<td align="left">
											<? $checked = ($accounting_user == 1) ? 'checked' : ''; ?>
											<input type="checkbox" onchange="accounting_checkbox(this.id)" <?=$opacity?> name="accounting_user" id="accounting_yes" value="1" <?=$checked?> <?=$ret_false?>>
											<label for="accounting_yes" <?=$opacity?>>да</label>
											<? $checked = ($accounting_user == 0) ? 'checked' : ''; ?>
											<input type="checkbox" onchange="accounting_checkbox(this.id)" <?=$opacity?> name="accounting_user" id="accounting_no" value="0" <?=$checked?> <?=$ret_false?>>
											<label for="accounting_no" <?=$opacity?>>нет</label>
										</td>
									 </tr>
								 <script type="text/javascript">
									 	function accounting_checkbox(id) {

											var account_access = <?echo ($account_access) ? 1 : 0;?>;
											var account_access_class = ((account_access == 1 || $('#account_access_yes').prop('checked') == true) ? '' : 'none');
											var list_access = <?echo ($list_access) ? 1 : 0; ?>;
											var list_access_class = ((list_access == 1 || $('#list_access_yes').prop('checked') == true) ? '' : 'none');
											var table_access = <?echo ($table_access) ? 1 : 0; ?>;
											var table_access_class = ((table_access == 1 || $('#table_access_yes').prop('checked') == true) ? '' : 'none');

											var checked = document.getElementById(id).checked;
											if (id == 'accounting_yes') {
												if (checked == true) {
													$('#accounting_no').prop('checked', false);
												} else {
													$('#accounting_yes').prop('checked', true);
												}

												  $('#accounting_tr').attr('class', '');
												  $('#deps_access_tr').attr('class', account_access_class);
												  $('#group_access_tr_account').attr('class', '');
												  $('#list_access_tr').attr('class', '');
												  $('#list_access_select_group').attr('class', list_access_class);
												  $('#list_access_select_dep').attr('class', list_access_class);
													$('#table_access_tr').attr('class', '');
													$('#table_access_select_group').attr('class', table_access_class);
													$('#table_access_select_dep').attr('class', table_access_class);
													$('#manager_plan_access_tr').attr('class', '');
													$('#statistics_access_tr').attr('class', '');


											} else {
												if (checked == true) {
													$('#accounting_yes').prop('checked', false);
												} else {
													$('#accounting_no').prop('checked', true);
												}

												$('#accounting_tr').attr('class', 'none');
												$('#deps_access_tr').attr('class', 'none');
												$('#list_access_tr').attr('class', 'none');
												$('#list_access_select_group').attr('class', 'none');
												$('#list_access_select_dep').attr('class', 'none');
												$('#table_access_tr').attr('class', 'none');
												$('#table_access_select_group').attr('class', 'none');
												$('#table_access_select_dep').attr('class', 'none');
												$('#manager_plan_access_tr').attr('class', 'none');
												$('#statistics_access_tr').attr('class', 'none');
												$('#group_access_tr_account').attr('class', 'none');
											}
										}
									 </script>


                                  <? $class = ($accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="manager_plan_access_tr" class="<?=$class?>" style="background-color: white; <?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"<?=$opacity?>>Доступ к плану менеджеров:</td>
										 <td align="left">
											 <? $checked = ($manager_plan_access == 1) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="doubleCheckboxes(this.id)" <?=$opacity?> name="manager_plan_access" id="manager_plan_access_yes" value="1" <?=$checked?> <?=$ret_false?>>
 											<label for="manager_plan_access_yes" <?=$opacity?>>да</label>
 											<? $checked = ($manager_plan_access == 0) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="doubleCheckboxes(this.id)" <?=$opacity?> name="manager_plan_access" id="manager_plan_access_no" value="0" <?=$checked?> <?=$ret_false?>>
 											<label for="manager_plan_access_no" <?=$opacity?>>нет</label>
										 </td>
									 </tr>

									 <? $class = ($accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="statistics_access_tr" class="<?=$class?>" style="background-color: white; <?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right" <?=$opacity?>>Доступ к общей статистике (в шапке):</td>
										 <td align="left">
											 <? $checked = ($statistics_access == 2) ? 'checked' : ''; ?>
											 <input type="checkbox" onchange="doubleCheckboxes(this.id)" name="statistics_access" id="statistics_access_2" value="2" <?=$checked?>>
											 <label for="statistics_access_2">всех</label>
											 <? $checked = ($statistics_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="statistics_access" id="statistics_access_1" value="1" <?=$checked?>>
												<label for="statistics_access_1">только своих</label>
												<? $checked = ($statistics_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="statistics_access" id="statistics_access_0" value="0" <?=$checked?>>
												<label for="statistics_access_0">нет доступа</label>
										 </td>
									 </tr>


									<? $class = ($accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="accounting_tr" class="<?=$class?>" style="background-color: white; <?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										<td align="right" <?=$opacity?>>Доступ к учету:</td>
										<td align="left">
											<? $checked = ($account_access == 1) ? 'checked' : ''; ?>
											<input type="checkbox" onchange="account_access_checkbox(this.id)" <?=$opacity?> name="account_access" id="account_access_yes" value="1" <?=$checked?> <?=$ret_false?>>
											<label for="account_access_yes" <?=$opacity?>>да</label>
											<? $checked = ($account_access == 0) ? 'checked' : ''; ?>
											<input type="checkbox" onchange="account_access_checkbox(this.id)" <?=$opacity?> name="account_access" id="account_access_no" value="0" <?=$checked?> <?=$ret_false?>>
											<label for="account_access_no" <?=$opacity?>>нет</label>
										</td>
									 </tr>
									 <script type="text/javascript">
										 function account_access_checkbox(id) {
										 var checked = document.getElementById(id).checked;
										 if (id == 'account_access_yes') {
											 if (checked == true) {
												 $('#account_access_no').prop('checked', false);
											 } else {
												 $('#account_access_yes').prop('checked', true);
											 }
											 $('#deps_access_tr').attr('class', '');
											 $('#group_access_tr_account').attr('class', '');
										 } else {
											 if (checked == true) {
												 $('#account_access_yes').prop('checked', false);
											 } else {
												 $('#account_access_no').prop('checked', true);
											 }
											 $('#deps_access_tr').attr('class', 'none');
											 $('#group_access_tr_account').attr('class', 'none');
										 }
									 }
									 </script>

									 <? $class = ($account_access == 1 && $accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="deps_access_tr" class="<?=$class?>" style="background-color: white; <?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
									 	<td align="right" <?=$opacity?>>Доступ к учету - отделы:</td>
										<td align="left">
											<select id="account_access_dep" class="multi-select" name="account_access_dep[]" multiple <?=$disabled?>>
												<option value="0">нет</option>
												<?
												foreach ($departments as $key => $value) {
													if (in_array($value['dep_id'], $account_access_dep)) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													?><option value="<?=$value['dep_id']?>" <?=$selected?>><?=$value['dep_name']?></option><?
												}
												?>
											</select>
											<span class="choose-all-btn" onclick="select_all_opt('account_access_dep')">Выбрать все</span>
										</td>
									 </tr>
									 <? $class = ($account_access == 1 && $accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="group_access_tr_account" class="<?=$class?>" style="background-color: white; display: none;">
									 	<td align="right" <?=$opacity?>>Доступ к учету - группы:</td>
										<td align="left">
											<select id="account_access_group" class="multi-select" name="account_access_group[]" multiple <?=$disabled?>>
												<option value="0">нет</option>
												<?
												foreach ($groups as $key => $value) {
													if (in_array($value['group_id'], $account_access_group)) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													?><option value="<?=$value['group_id']?>" <?=$selected?>><?=$value['group_name']?></option><?
												}
												?>
											</select>
											<span class="choose-all-btn" onclick="select_all_opt('account_access_group')">Выбрать все</span>
										</td>
									 </tr>

									 <? $class = ($accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="list_access_tr" class="<?=$class?>" style="background-color: white; <?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right" <?=$opacity?>>Доступ к ведомости:</td>
										 <td align="left">
											 <? $checked = ($list_access == 1) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="list_access_checkbox(this.id)" <?=$opacity?> name="list_access" id="list_access_yes" value="1" <?=$checked?> <?=$ret_false?>>
 											<label for="list_access_yes" <?=$opacity?>>да</label>
 											<? $checked = ($list_access == 0) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="list_access_checkbox(this.id)" <?=$opacity?> name="list_access" id="list_access_no" value="0" <?=$checked?> <?=$ret_false?>>
 											<label for="list_access_no" <?=$opacity?>>нет</label>
										 </td>
									 </tr>
									 <script>
										 function list_access_checkbox(id) {
											var checked = document.getElementById(id).checked;
											if (id == 'list_access_yes') {
												if (checked == true) {
													$('#list_access_no').prop('checked', false);
												} else {
													$('#list_access_yes').prop('checked', true);
												}
												$('#list_access_select_group').attr('class', '');
												$('#list_access_select_dep').attr('class', '');
											} else {
												if (checked == true) {
													$('#list_access_yes').prop('checked', false);
												} else {
													$('#list_access_no').prop('checked', true);
												}
												$('#list_access_select_group').attr('class', 'none');
												$('#list_access_select_dep').attr('class', 'none');
											}
										}
									 </script>
									 <? $class = ($list_access == 1 && $accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="list_access_select_group" class="<?=$class?>" style="background-color: white; display: none;">
										<td align="right" <?=$opacity?>>Доступ к ведомости - группы:</td>
 										<td align="left">
											<select id="list_access_group" class="multi-select" name="list_access_group[]" multiple <?=$disabled?>>
												<option value="0">нет</option>
												<?
												foreach ($groups as $key => $value) {
													if (in_array($value['group_id'], $list_access_group)) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													?><option value="<?=$value['group_id']?>" <?=$selected?>><?=$value['group_name']?></option><?
												}
												?>
											</select>
											<span class="choose-all-btn" onclick="select_all_opt('list_access_group')">Выбрать все</span>
										</td>
									 </tr>
									 <? $class = ($list_access == 1 && $accounting_user == 1) ? '' : 'none'; ?>
									<tr id="list_access_select_dep" class="<?=$class?>" style="background-color: white;">
									 <td align="right" <?=$opacity?>>Доступ к ведомости - отделы:</td>
										 <td align="left">
										 <select id="list_access_dep" class="multi-select" name="list_access_dep[]" multiple <?=$disabled?>>
											 <option value="0">нет</option>
											 <?
											 foreach ($departments as $key => $value) {
												 if (in_array($value['dep_id'], $list_access_dep)) {
													 $selected = 'selected';
												 } else {
													 $selected = '';
												 }
												 ?><option value="<?=$value['dep_id']?>" <?=$selected?>><?=$value['dep_name']?></option><?
											 }
											 ?>
										 </select>
										 <span class="choose-all-btn" onclick="select_all_opt('list_access_dep')">Выбрать все</span>
									 </td>
									</tr>






									 <? $class = ($accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="table_access_tr" class="<?=$class?>" style="background-color: white; <?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right" <?=$opacity?>>Доступ к табелю:</td>
										 <td align="left">
											 <? $checked = ($table_access == 1 && $accounting_user == 1) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="table_access_checkbox(this.id)" <?=$opacity?> name="table_access" id="table_access_yes" value="1" <?=$checked?> <?=$ret_false?>>
 											<label for="table_access_yes" <?=$opacity?>>да</label>
 											<? $checked = ($table_access == 0) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="table_access_checkbox(this.id)" <?=$opacity?> name="table_access" id="table_access_no" value="0" <?=$checked?> <?=$ret_false?>>
 											<label for="table_access_no" <?=$opacity?>>нет</label>
										 </td>
									 </tr>


									 <? $class = ($table_access == 1 && $accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="table_access_select_group" class="<?=$class?>" style="background-color: white; display: none;">
										<td align="right" <?=$opacity?>>Доступ к табелю - группы:</td>
 										<td align="left">
											<select id="table_access_group" class="multi-select" name="table_access_group[]" multiple <?=$disabled?>>
												<option value="0">нет</option>
												<?
												foreach ($groups as $key => $value) {
													if (in_array($value['group_id'], $table_access_group)) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													?><option value="<?=$value['group_id']?>" <?=$selected?>><?=$value['group_name']?></option><?
												}
												?>
											</select>
											<span class="choose-all-btn" onclick="select_all_opt('table_access_group')">Выбрать все</span>
										</td>
									 </tr>
									 <? $class = ($table_access == 1 && $accounting_user == 1) ? '' : 'none'; ?>
									 <tr id="table_access_select_dep" class="<?=$class?>" style="background-color: white; <?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										<td align="right" <?=$opacity?>>Доступ к табелю - отделы:</td>
 										<td align="left">
											<select id="table_access_dep" class="multi-select" name="table_access_dep[]" multiple <?=$disabled?>>
												<option value="0">нет</option>
												<?
												foreach ($departments as $key => $value) {
													if (in_array($value['dep_id'], $table_access_dep)) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													?><option value="<?=$value['dep_id']?>" <?=$selected?>><?=$value['dep_name']?></option><?
												}
												?>
											</select>
											<span class="choose-all-btn" onclick="select_all_opt('table_access_dep')">Выбрать все</span>
										</td>
									 </tr>






									 <tr id="application_response_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right">Ответственный за прием заявок на производство:</td>
										 <td align="left">
											 <? $checked = ($application_response == 1) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="application_response" id="application_response_yes" value="1" <?=$checked?>>
 											<label for="application_response_yes">да</label>
 											<? $checked = ($application_response == 0) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="application_response" id="application_response_no" value="0" <?=$checked?>>
 											<label for="application_response_no">нет</label>
										 </td>
									 </tr>

									 <tr id="application_response_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right">Создание и редактирование отправок:</td>
										 <td align="left">
											 <? $checked = ($edit_shipments == 1) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="edit_shipments" id="edit_shipments_yes" value="1" <?=$checked?>>
 											<label for="edit_shipments_yes">да</label>
 											<? $checked = ($edit_shipments == 0) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="edit_shipments" id="edit_shipments_no" value="0" <?=$checked?>>
 											<label for="edit_shipments_no">нет</label>
										 </td>
									 </tr>

									 <tr id="material_response_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right">Ответственный за поставку материалов на производство:</td>
										 <td align="left">
											 <? $checked = ($material_response == 1) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="material_response" id="material_response_yes" value="1" <?=$checked?>>
 											<label for="material_response_yes">да</label>
 											<? $checked = ($material_response == 0) ? 'checked' : ''; ?>
 											<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="material_response" id="material_response_no" value="0" <?=$checked?>>
 											<label for="material_response_no">нет</label>
										 </td>
									 </tr>

									 <tr id="order_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к общей информации:</td>
										 <td align="left">
											 <? $checked = ($main_info_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="main_info_access" id="main_info_access_yes" value="1" <?=$checked?>>
												<label for="main_info_access_yes">да</label>
												<? $checked = ($main_info_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="main_info_access" id="main_info_access_no" value="0" <?=$checked?>>
												<label for="main_info_access_no">нет</label>
										 </td>
									 </tr>
									 <tr id="order_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к разделу "Заказы":</td>
										 <td align="left">
											 <? $checked = ($order_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access" id="order_access_yes" value="1" <?=$checked?>>
												<label for="order_access_yes">да</label>
												<? $checked = ($order_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access" id="order_access_no" value="0" <?=$checked?>>
												<label for="order_access_no">нет</label>
										 </td>
									 </tr>
									 <?
									 $class = ($order_access == 1) ? '' : 'none';
									 ?>
									 <tr id="order_access_type_tr" class="<?=$class?>" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Просмотр заказов:</td>
										 <td align="left">
											 <? $checked = ($order_access_type == 2) ? 'checked' : ''; ?>
											 <input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_type" id="order_access_type_2" value="2" <?=$checked?>>
											 <label for="order_access_type_2">всех</label>
											 <? $checked = ($order_access_type == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_type" id="order_access_type_1" value="1" <?=$checked?>>
												<label for="order_access_type_1">только своих</label>
												<? $checked = ($order_access_type == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_type" id="order_access_type_0" value="0" <?=$checked?>>
												<label for="order_access_type_0">никаких</label>
										 </td>
									 </tr>
									 <?
									 $class = ($order_access == 1) ? '' : 'none';
									 ?>
									 <tr id="order_access_edit_tr" class="<?=$class?>" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right">Редактирование заказов:</td>
										 <td align="left">
											 	<? $checked = ($order_access_edit == 2) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_edit" id="order_access_edit_2" value="2" <?=$checked?>>
												<label for="order_access_edit_2">всех</label>
												<? $checked = ($order_access_edit == 1) ? 'checked' : ''; ?>
 												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_edit" id="order_access_edit_1" value="1" <?=$checked?>>
 												<label for="order_access_edit_1">своих</label>
												<? $checked = ($order_access_edit == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_edit" id="order_access_edit_0" value="0" <?=$checked?>>
												<label for="order_access_edit_0">никаких</label>
										 </td>
									 </tr>
									 <?
									 $class = ($order_access == 1) ? '' : 'none';
									 ?>
									 <tr id="order_access_payment_tr" class="<?=$class?>" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Проставление оплат:</td>
										 <td align="left">
											 <? $checked = ($order_access_payment == 2) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_payment" id="order_access_payment_2" value="2" <?=$checked?>>
												<label for="order_access_payment_2">все</label>
											 <? $checked = ($order_access_payment == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_payment" id="order_access_payment_1" value="1" <?=$checked?>>
												<label for="order_access_payment_1">свои</label>
												<? $checked = ($order_access_payment == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="order_access_payment" id="order_access_payment_0" value="0" <?=$checked?>>
												<label for="order_access_payment_0">никакие</label>
										 </td>
									 </tr>
									 <tr id="payment_edit_num_tr" class="<?=$class?>" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right">Проставление номера накладной:</td>
										 <td align="left">
											 <? $checked = ($payment_edit_num == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="payment_edit_num" id="payment_edit_num_yes" value="1" <?=$checked?>>
												<label for="payment_edit_num_yes">да</label>
												<? $checked = ($payment_edit_num == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="payment_edit_num" id="payment_edit_num_no" value="0" <?=$checked?>>
												<label for="payment_edit_num_no">нет</label>
										 </td>
									 </tr>

 									 <tr id="shipped_edit_tr" class="<?=$class?>" >
										 <td align="right">Ставить отметки об отгрузке:</td>
										 <td align="left">
											 <? $checked = ($shipped_edit == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="shipped_edit" id="shipped_edit_yes" value="1" <?=$checked?>>
												<label for="shipped_edit_yes">да</label>
												<? $checked = ($shipped_edit == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="shipped_edit" id="shipped_edit_no" value="0" <?=$checked?>>
												<label for="shipped_edit_no">нет</label>
										 </td>
									 </tr>

									 <tr id="shop_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к разделу "Магазин":</td>
										 <td align="left">
											 <? $checked = ($shop_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="shop_access" id="shop_access_yes" value="1" <?=$checked?>>
												<label for="shop_access_yes">да</label>
												<? $checked = ($shop_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="shop_access" id="shop_access_no" value="0" <?=$checked?>>
												<label for="shop_access_no">нет</label>
										 </td>
									 </tr>
									 <tr id="tasks_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к разделу "Задачи":</td>
										 <td align="left">
											 <? $checked = ($tasks_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="tasks_access" id="tasks_access_yes" value="1" <?=$checked?>>
												<label for="tasks_access_yes">да</label>
												<? $checked = ($tasks_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="tasks_access" id="tasks_access_no" value="0" <?=$checked?>>
												<label for="tasks_access_no">нет</label>
										 </td>
									 </tr>
									 <tr id="plans_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к разделу "Планировщик":</td>
										 <td align="left">
											 <? $checked = ($plans_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="plans_access" id="plans_access_yes" value="1" <?=$checked?>>
												<label for="plans_access_yes">да</label>
												<? $checked = ($plans_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="plans_access" id="plans_access_no" value="0" <?=$checked?>>
												<label for="plans_access_no">нет</label>
										 </td>
									 </tr>
									 <tr id="sprav_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к разделу "Справочники":</td>
										 <td align="left">
											 <? $checked = ($sprav_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="sprav_access" id="sprav_access_yes" value="1" <?=$checked?>>
												<label for="sprav_access_yes">да</label>
												<? $checked = ($sprav_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="sprav_access" id="sprav_access_no" value="0" <?=$checked?>>
												<label for="sprav_access_no">нет</label>
										 </td>
									 </tr>
									 <tr id="proizv_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к разделу "Производство":</td>
										 <td align="left">
											 <? $checked = ($proizv_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="proizv_access" id="proizv_access_yes" value="1" <?=$checked?>>
												<label for="proizv_access_yes">да</label>
												<? $checked = ($proizv_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="proizv_access" id="proizv_access_no" value="0" <?=$checked?>>
												<label for="proizv_access_no">нет</label>
										 </td>
									 </tr>
									 <?
									 $class = ($proizv_access == 1) ? '' : 'none';
									 ?>
									 <tr id="proizv_access_type_tr" class="<?=$class?>" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right">Просмотр заявок:</td>
										 <td align="left">
											 <? $checked = ($proizv_access_type == 2) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="proizv_access_type" id="proizv_access_type_2" value="2" <?=$checked?>>
												<label for="proizv_access_type_2">все</label>
												<? $checked = ($proizv_access_type == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="proizv_access_type" id="proizv_access_type_1" value="1" <?=$checked?>>
												<label for="proizv_access_type_1">свои</label>
												<? $checked = ($proizv_access_type == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="proizv_access_type" id="proizv_access_type_0" value="0" <?=$checked?>>
												<label for="proizv_access_type_0">никакие</label>
										 </td>
									 </tr>
									 <?
									 $class = ($proizv_access == 1) ? '' : 'none';
									 ?>
									 <tr id="proizv_access_edit_tr" class="<?=$class?>" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right">Редактирование заявок:</td>
										 <td align="left">
											 <? $checked = ($proizv_access_edit == 2) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="proizv_access_edit" id="proizv_access_edit_2" value="2" <?=$checked?>>
												<label for="proizv_access_edit_2">все</label>
											 <? $checked = ($proizv_access_edit == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="proizv_access_edit" id="proizv_access_edit_1" value="1" <?=$checked?>>
												<label for="proizv_access_edit_1">свои</label>
												<? $checked = ($proizv_access_edit == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="proizv_access_edit" id="proizv_access_edit_0" value="0" <?=$checked?>>
												<label for="proizv_access_edit_0">никакие</label>
										 </td>
									 </tr>

									 <tr id="logistics_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к разделу "Логистика":</td>
										 <td align="left">
											 <? $checked = ($logistics_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="logistics_access" id="logistics_access_yes" value="1" <?=$checked?>>
												<label for="logistics_access_yes">да</label>
												<? $checked = ($logistics_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="logistics_access" id="logistics_access_no" value="0" <?=$checked?>>
												<label for="logistics_access_no">нет</label>
										 </td>
									 </tr>

									 <tr id="tabl_access_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
										 <td align="right"> Доступ к разделу "Табл":</td>
										 <td align="left">
											 <? $checked = ($tabl_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="tabl_access" id="tabl_access_yes" value="1" <?=$checked?>>
												<label for="tabl_access_yes">да</label>
												<? $checked = ($tabl_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="tabl_access" id="tabl_access_no" value="0" <?=$checked?>>
												<label for="tabl_access_no">нет</label>
										 </td>
									 </tr>

									 <tr id="show_departments_tr" style="<?echo ($user_access['allow_edit_access'] == '1') ? : 'display: none !important;'?>">
									 	<td align="right" <?=$opacity?>>Просмотр сотрудников:</td>
										<td align="left">
											<select id="show_departments" class="multi-select" name="show_departments[]" multiple>
												<option value="0">нет</option>
												<?
												foreach ($departments as $key => $value) {
													if (in_array($value['dep_id'], $show_departments)) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													?><option value="<?=$value['dep_id']?>" <?=$selected?>><?=$value['dep_name']?></option><?
												}
												?>
											</select>
											<span class="choose-all-btn" onclick="select_all_opt('show_departments')">Выбрать все</span>
										</td>
									 </tr>

									 <?
									 if ($user_access['job_id'] == '10002') {
										 $allow_edit_users = '';
									 } else {
										 $allow_edit_users = 'display: none';
									 }
									 ?>
									 <tr id="show_departments_tr" style="<?=$allow_edit_users?>">
									 	<td align="right" <?=$opacity?>><b>Админ</b> - разрешить редактирование сотрудников:</td>
										<td align="left">
											<select id="edit_users" class="multi-select" name="edit_users[]" multiple>
												<option value="0">нет</option>
												<?
												foreach ($departments as $key => $value) {
													if (in_array($value['dep_id'], $edit_users)) {
														$selected = 'selected';
													} else {
														$selected = '';
													}
													?><option value="<?=$value['dep_id']?>" <?=$selected?>><?=$value['dep_name']?></option><?
												}
												?>
											</select>
											<span class="choose-all-btn" onclick="select_all_opt('edit_users')">Выбрать все</span>
										</td>
									 </tr>

									 <tr id="show_pass_tr" style="<?=$allow_edit_users?>">
										 <td align="right"><b>Админ</b> - показывать пароль:</td>
										 <td align="left">
											 <? $checked = ($show_pass == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="show_pass" id="show_pass_yes" value="1" <?=$checked?>>
												<label for="show_pass_yes">да</label>
												<? $checked = ($show_pass == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="show_pass" id="show_pass_no" value="0" <?=$checked?>>
												<label for="show_pass_no">нет</label>
										 </td>
									 </tr>
									 <tr id="allow_edit_access_tr" style="<?=$allow_edit_users?>">
										 <td align="right"><b>Админ</b> - доступ к проставлению прав:</td>
										 <td align="left">
											 <? $checked = ($allow_edit_access == 1) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="allow_edit_access" id="allow_edit_access_yes" value="1" <?=$checked?>>
												<label for="allow_edit_access_yes">да</label>
												<? $checked = ($allow_edit_access == 0) ? 'checked' : ''; ?>
												<input type="checkbox" onchange="doubleCheckboxes(this.id)" name="allow_edit_access" id="allow_edit_access_no" value="0" <?=$checked?>>
												<label for="allow_edit_access_no">нет</label>
										 </td>
									 </tr>

									 <tr>
										 <td align="right">Дата поступл. на работу:</td>
										 <td align="left">

										 <select class="users_day" name="pr_day" id="tt">
											 <?
											 $sel = ' selected="selected"';
											 if($id)
												 $sel = '';
											 for($i=1;$i<=31;$i++) {
												 if(($oper=='edit') && ($i == $date_pr[2]))
													 $sel = ' selected="selected"';
												 if (($oper == 'new') && ($i == $date_pr[2]))
													 $sel = ' selected="selected"';
												?>
											 <option value="<?=$i?>" <?=$sel?>><?=$i?></option>
											 <?
											 $sel = '';
											 } ?>
										 </select>
										 <select class="users_month" name="pr_month">
											 <?
											 $sel = ' selected="selected"';
											 if($id)
												 $sel = '';
											 for($i=0;$i<12;$i++) {
												 if(($oper=='edit') && (($i+1) == intval($date_pr[1])))
													 $sel = ' selected="selected"';
												 if(($oper=='new') && (($i+1) == intval($date_pr[1])))
													 $sel = ' selected="selected"';
											 ?>
											 <option value="<?=$i+1?>" <?=$sel?>><?=$month_sel[$i]?></option>
											 <? $sel = '';
											 } ?>
										 </select>
										 <select class="users_year" name="pr_year">
											 <?
											 $sel = ' selected="selected"';
											 if($id)
												 $sel = '';
											 for($i=1930;$i<=2025;$i++) {
												 if($oper=='edit') {
													 if($i == $date_pr[0])
														 $sel = ' selected="selected"';
												 }
												 if (($oper == 'new') && ($i == $date_pr[0]))
													 $sel = ' selected="selected"';

												 elseif($i == 2003)
													 $sel = ' selected="selected"';
											 ?>
											 <option value="<?=$i?>" <?=$sel?>><?=$i?></option>
											 <? $sel = '';
											 } ?>
										 </select>
										 </td>
									 </tr>
										<?
										if (!empty($dismissal_date) || $f_archive == 1) {
											$dismissal_date = explode('-', $dismissal_date);
											?>
											<tr>
												<td align="right">Дата увольнения:</td>
												<td align="left">
												<select class="user_day" name="dism_day" id="tt" >
													<?
													$sel = ' selected="selected"';
													if($id)
														$sel = '';
													for($i=1;$i<=31;$i++) {
														if(($oper=='edit') && ($i == $dismissal_date[2]))
															$sel = ' selected="selected"';
													?>
													<option value="<?=$i?>" <?=$sel?>><?=$i?></option>
													<?
													$sel = '';
													} ?>
												</select>
												<select class="users_month" name="dism_month" >
													<?
													$sel = ' selected="selected"';
													if($id)
														$sel = '';
													for($i=0;$i<12;$i++) {
														if(($oper=='edit') && (($i+1) == intval($dismissal_date[1])))
															$sel = ' selected="selected"';
													?>
													<option value="<?=$i+1?>" <?=$sel?>><?=$month_sel[$i]?></option>
													<?  $sel = '';
													} ?>
												</select>
												<select class="users_year" name="dism_year" >
													<?
													$sel = ' selected="selected"';
													if($id)
														$sel = '';
													for($i=1930;$i<=2025;$i++) {
														if($oper=='edit') {
															if($i == $dismissal_date[0])
																$sel = ' selected="selected"';
														}
														elseif($i == 1950)
															$sel = ' selected="selected"';
													 ?>
													<option value="<?=$i?>" <?=$sel?>><?=$i?></option>
													<? $sel = '';
													} ?>
												</select>
												</td>
											</tr>
											<?
										}
										?>


					<tr>
						<td colspan="2"> </td>
					</tr>

					<tr>
						<td align="right">Примечание:</td>
						<td align="left"><textarea name="note" rows="3" class="users_frm_txar"  style='    width: 320px;'><?=h_sp(@$f_note)?></textarea></td>
					</tr>
					<tr>
						<td align="right">Amo id:</td>
						<td align="left"><input type="text" class="users_frm" name="amo_id" value="<?=$f_amo?>"autocomplete="off" style='    width: 320px;'/></td>
					</tr>


					<tr>

						<td align="middle" colspan="2">
                            <input type="hidden" value="<?if($_SERVER['HTTP_REFERER']){echo $_SERVER['HTTP_REFERER'];}else{echo "users.php";}?>" name="referer"/>
							<input class="users_frm_butt" name="save_us" type="submit" value="Сохранить" onclick="return check();" />
							<input class="users_frm_butt" type="button" value="Отмена" onclick="history.back()" />
						</td>
					</tr>
				</table>
				</form>
				<?
				}
				else {
            //получаем в массив список должностей
            $get_doljnost = mysql_query("SELECT * FROM doljnost ORDER BY name ASC");
            //$d = mysql_fetch_row($get_doljnost);
            while ( $row = mysql_fetch_array($get_doljnost) ) {
            unset($row[0]);
            unset($row[1]);
           $d[$row[id]] = $row;
           }?>
			<table width="1400" border="0" cellpadding="3" cellspacing="2" bordercolor="#999999" style="border-collapse: collapse;/*border-spacing: 0px;*/">
				<tr class="table-tit">
					<td align="center" class="table-tit">Ф.И.О.</td>
					<td align="center" class="table-tit">Должность</td>
					<td align="center" class="table-tit">Отдел</td>
					<?
						if ($tpus == 'sup') {
							?>
							<td align="center" class="table-tit">База</td>

                        	<?
						}
					?>
							<td align="center" class="table-tit">ID</td>

					<td align="center" class="table-tit">Email</td>
					<td align="center" class="table-tit">Мобильный тел.</td>
					<?
						if ($user_access['edit_users'] !== '0' && !empty($user_access['edit_users'])) {
							if ($user_access['show_pass'] !== '0' || $user_access['job_id'] == '10002') {
								?><td align="center" class="table-tit">Логин, пароль</td><?
							}
							?>
							<td align="center" class="table-tit">Тип доступа</td>
							<td align="center" class="table-tit">Операция</td>
							<?
						}
					?>

				</tr>
				<?
				$more_query = array();

				if (isset($_GET['group']) && is_numeric($_GET['group']) ) {
					$group = $_GET['group'];
					$query_add = " user_group = $group ";
					array_push($more_query, $query_add);
				}

				if (isset($_GET['archive']) && is_numeric($_GET['archive'])) {
					$archive = $_GET['archive'];
					$query_add = " archive = 1 ";
					array_push($more_query, $query_add);
				} else {
					$query_add = " archive != 1 ";
					array_push($more_query, $query_add);
				}

				if (isset($_GET['archive'])) {
					$more_query .= ' WHERE archive = 1';
				} else {
					$more_query .= ' WHERE archive = 0';
				}

				// Обработка запроса поисковой строки
				if (isset($_GET['surname']) || isset($_GET['name']) || isset($_GET['father']))
				{
					$more_query = array();
					if (isset($_GET['archive'])) {
						array_push($more_query, ' archive = 1');
					} else {
						array_push($more_query, ' archive = 0');
					}

					if (isset($_GET['surname'])) {
						$surname_q = iconv("utf-8", "cp1251", " surname LIKE '%" . $_GET['surname'] . "%' ");
						array_push($more_query, $surname_q);
					}
					if (isset($_GET['name'])) {
						$name_q = iconv("utf-8", "cp1251", " name LIKE '%" . $_GET['name'] . "%' ");
						array_push($more_query, $name_q);
					}
					if (isset($_GET['father'])) {
						$father_q = iconv("utf-8", "cp1251", " father LIKE '%" . $_GET['father'] . "%' ");
					//	$father_q = " father LIKE '" . $_GET['father'] . "%' ";
						array_push($more_query, $father_q);
					}
					$more_query = ' WHERE ' . implode(' AND ', $more_query);
				//	echo $more_query;
				}

				if (isset($_GET['search_id'])) {
					$search_id = $_GET['search_id'];
					$more_query = " WHERE job_id LIKE '" . $search_id . "%' ";
				}


				if(isset($_GET['department']) && $_GET['department'] !== 'all' ) {
				  $department = explode('_', $_GET['department']);
				  $conditions = array();
				  foreach ($department as $key => $value) {
				    array_push($conditions, 'user_department = '.$value);
				  }
				  $conditions = implode(' OR ', $conditions);
				  $account_access = " AND ( $conditions  )";
				} else {
					if ($_GET['archive'] !== '1') {
						$account_access_dep = explode('|', $user_access['show_departments']);
						$account_access = array();
						foreach ($account_access_dep as $key => $value) {
							array_push($account_access, 'user_department = ' . $value);
						}
						$account_access = " AND (" . implode(' OR ', $account_access) . ")";
					}

				}
				$query = "SELECT * FROM users $more_query $account_access ORDER BY surname";
                //echo $query;
				$res = mysql_query($query);
				while($r_us = mysql_fetch_array($res)) {
					$fio = $r_us['surname'].' '.$r_us['name'].' '.$r_us['father'];
					if(trim($r_us['date_birth'])) {
						$tmp = explode('-',$r_us['date_birth']);
						$date_birth = $tmp[2].' '.$month[intval($tmp[1])-1].' '.$tmp[0].'г.';
					}
					else
						$date_birth = '---';

					if(trim($r_us['date_work'])) {
						$tmp = explode('-',$r_us['date_work']);
						$date_work = $tmp[2].' '.$month[intval($tmp[1])-1].' '.$tmp[0].'г.';
					}
					else
						$date_work = '---';

					switch($r_us['type']) {
						case 'adm':
							$user_header = 'администратор';
							break;
						case 'mng':
							$user_header = 'менеджер';
							break;
						case 'acc':
							$user_header = 'бухгалтер';
							break;
						case 'meg':
							$user_header = 'мегаадмин';
							break;
						case 'sup':
							$user_header = 'суперадмин';
							break;
						default:
							$user_header = 'гость';
					}
					$butt = '<a href="?edit='.$r_us['uid'].'&oper=edit">';
					$butt .= '<img  width="20" height="20" src="../i/edit2.gif" title="Редактировать" />';
					$butt .= '</a> ';
					if($_GET["proizv"] == "1"){
$butt .= '<a href="../../acc/applications/count/exp_csv.php?num_sotr='.$r_us['job_id'].'"><img src="../../i/export.png" width="24" height="24" alt="" valign=middle/></a>';
}
        if($_GET['archive'] == '1'){

					$butt .= '<span style="cursor: pointer;" name="restore" onclick="delUser('.$r_us['uid'].')">';
					$butt .= '<img width="20" height="20" name="restore" data-id='. $r_us['uid'] .' src="../i/pr_ok.gif" title="Восстановить из архива" />';
					$butt .= '</span>';

					$butt .= '<span style="cursor: pointer;" href="#" name="delFinal" onclick="delUser('.$r_us['uid'].')">';
					$butt .= '<img width="20" height="20" name="delFinal" data-id='. $r_us['uid'] .' src="../i/del.gif" title="Удалить окончательно" />';
					$butt .= '</span>';

          }else{

					$butt .= '<span style="cursor: pointer;" name="archive" onclick="delUser('.$r_us['uid'].')">';
					$butt .= '<img width="20" height="20" name="archive" data-id='. $r_us['uid'] .' src="../i/del.gif" title="Удалить в архив" />';
					$butt .= '</span>';
        }

				?>
				<tr id="user_<?=$r_us['uid']?>" style="<?echo ($r_us['archive'] == 1) ? 'opacity: 0.5;' : '' ;?>">
					<?
						//if ($tpus != 'mng') {
						if (in_array($r_us['user_department'], explode('|', $user_access['edit_users'])) || $user_access['job_id'] == '10002') {
							?>
							<td class="table-td-bold">
								<a href="?edit=<?=$r_us['uid']?>&oper=edit" onmouseover="Tip('Редактировать')" class="user_fio_link"><?=$fio?></a></td>
							<?
						} else {
							?>
							<td class="table-td-bold"><?=$fio?></td>
							<?
						}
					?>
				<?
										$user_dep_id = $r_us['user_department'];
										$dep_q = "SELECT * FROM user_departments WHERE id = $user_dep_id";
										$dep_r = mysql_fetch_assoc(mysql_query("$dep_q"));
										$user_department_name = $dep_r['name'];
										?>
										<td align="center" class="table-td"><?=$d[$r_us['doljnost']][name];?></td>
										<td align="center" class="table-td"><?=$user_department_name?></td>
										<?
										if ($tpus == 'sup') {
											?>
											<td align="center" class="table-td"><?=$r_us['oklad'];?></td>

                                            <?
										}
										?>
											<td align="center" class="table-td"><strong><?=$r_us['job_id'];?></strong></td>


										<td align="center" class="table-td"><strong><?=$r_us['email'];?></strong></td>

					<td align="center" class="table-td"><?=(trim($r_us['mobile'])) ? $r_us['mobile'] : '---'?></td>

					<?
					$sh_login = (trim($r_us['login']) && ($user_access['show_pass']=='1' || $user_access['job_id'] == '10002') && (in_array($r_us['user_department'], explode('|', $user_access['edit_users'])) || $user_access['job_id'] == '10002')) ?  $r_us['login'] : '---';
					$sh_pass = (trim($r_us['pass']) && ($user_access['show_pass']=='1' || $user_access['job_id'] == '10002') && (in_array($r_us['user_department'], explode('|', $user_access['edit_users'])) || $user_access['job_id'] == '10002')) ?  $r_us['pass'] : '---';
					?>
					<?
					if ($user_access['edit_users'] !== '0' && !empty($user_access['edit_users'])) {
						if ($user_access['show_pass'] == '1' || $user_access['job_id'] == '10002') {
								?><td align="center" class="table-td"><?=$sh_login?><br><?=$sh_pass;?></td><?
							}
						?>
						<td align="center" class="table-td"><?=$user_header?></td>
						<?
						if (in_array($r_us['user_department'], explode('|', $user_access['edit_users'])) || $user_access['job_id'] == '10002' ) {
							?><td class="table-td" align="center"><?=$butt?></td><?
						} else {
							?><td class="table-td" align="center"></td><?
						}
						?>

						<?
					}
					?>

				</tr>
			<? } } ?>
			</table>
	</td>
	<? } ?>
</tr>
</table>
<br><br>

</td>

</tr>
</table>

<script type="text/javascript">

var allowedDeps = JSON.parse('<?echo json_encode($allowed_deps);?>');
var deps = $('.dep_popup_tr');
for (var i = 0; i < deps.length; i++) {
  if (deps[i] !== undefined) {
    if (allowedDeps.indexOf(deps[i].attributes['data-dep'].value) < 0 ) {
      deps[i].remove();
    }
  }
}

function depPopup() {
  var btn = event.target;
  switch (btn.id) {
    case 'popup_open':
      var open = $('#popup_open').attr('data-open') == 0 ? 1 : 0;
      $('#popup_open').attr('data-open', open);
      if (open == 1) {
        selected_inputs = 0;
        inputs = $('.dep_input');
        for (i = 0; i <= inputs.length; i++) {
          if (inputs[i] !== undefined) {
            if (inputs[i].checked == true) {selected_inputs += 1};
          }
        }
        $('#popup_open').html('Выберите отдел... ');
        $('#dep_select_popup').attr('style', '');
        $('#dep_select_popup').attr('data-open', 1);
      } else {
        $('#dep_select_popup').attr('style', 'display: none;');
        $('#dep_select_popup').attr('data-open', 0);
        if (selected_inputs != 0 && selected_inputs != <?=$deps_count?>) {
          $('#popup_open').html('Выбрано ' + selected_inputs + ' отделов');
        } else if (selected_inputs == <?=$deps_count?>) {
          $('#popup_open').html('Все отделы ');
        } else {
          $('#popup_open').html('Выберите отдел... ');
        }
      }

      break;
    case 'choose_dep_btn':
      inputs = $('.dep_input');
      selected_inputs = [];
      for (i = 0; i <= inputs.length; i++) {
        if (inputs[i] !== undefined) {
          if (inputs[i].checked == true) {selected_inputs.push(inputs[i].attributes['data-dep'].value)};
        }
      }
      if (selected_inputs.length != 0) {
        selected_inputs.join('_');
        var err = false;
        var dep_path = selected_inputs.join('_');
      } else {
        alert('Выберите хотя бы один отдел');
        var err = true;
      }
      if (err == false) {
        <?
        if (stristr($_SERVER['QUERY_STRING'], 'department') ) {
          $gets = explode('&', $_SERVER['QUERY_STRING']);
          foreach ($gets as $key => $value) {
            if (stristr($value, 'department')) {unset($gets[$key]);}
          }
          $path = 'users.php?' . implode('&', $gets) . '&department=';
        } else {
          $path = 'users.php?' . $_SERVER['QUERY_STRING'] . '&department=' ;
        }
        ?>
        path = '<?=$path?>' + dep_path;
        window.location = path;
      }
      break;

    case 'select_all_deps':
      inputs = $('.dep_input');
      for (i = 0; i <= inputs.length; i++) {
        if (inputs[i] !== undefined) {
          inputs[i].checked = true;
        }
      }
      $('#select_all_deps').html('снять все');
      $('#select_all_deps').attr('id', 'unset_all_deps');
      break;
    case 'unset_all_deps':
      inputs = $('.dep_input');
      for (i = 0; i <= inputs.length; i++) {
        if (inputs[i] !== undefined) {
          inputs[i].checked = false;
        }
      }
      $('#unset_all_deps').html('отметить все');
      $('#unset_all_deps').attr('id', 'select_all_deps');
      break;
    default:

  }
}

function hidePopup() {
  if (event.target.id == 'dep_select_popup') {
    switch (event.target.attributes['data-open'].value) {
      case "1":

        inputs = $('.dep_input');
        selected_inputs = [];
        for (i = 0; i <= inputs.length; i++) {
          if (inputs[i] !== undefined) {
            if (inputs[i].checked == true) {selected_inputs.push(inputs[i].attributes['data-dep'].value)};
          }
        }
        if (selected_inputs.length != 0) {
          selected_inputs.join('_');
          var err = false;
          var dep_path = selected_inputs.join('_');
        } else {
          alert('Выберите хотя бы один отдел');
          var err = true;
        }
        if (err == false) {
          <?
          if (stristr($_SERVER['QUERY_STRING'], 'department') ) {
            $gets = explode('&', $_SERVER['QUERY_STRING']);
            foreach ($gets as $key => $value) {
              if (stristr($value, 'department')) {unset($gets[$key]);}
            }
            $path = 'users.php?' . implode('&', $gets) . '&department=';
          } else {
            $path = 'users.php?' . $_SERVER['QUERY_STRING'] . '&department=' ;
          }
          ?>
          path = '<?=$path?>' + dep_path;
          window.location = path;
          $('#dep_select_popup').attr('data-open', "0");
          $('#dep_select_popup').attr('style', 'display: none;');
          $('#popup_open').attr('data-open', "0");
        }
        break;
      case "0":
        $('#dep_select_popup').attr('data-open', 1);
        $('#dep_select_popup').attr('display', '');
        $('#popup_open').attr('data-open', 1);
        break;
    }
  }
}
$(".dep_popup_info").draggable();
</script>

<script src="../includes/js/users.js?rand=<?=rand(1,10000000);?>"></script>

</body>
</html>
<? ob_end_flush() ?>
