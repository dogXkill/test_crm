<?
$user_type = 0;
$user_email = '';

if(@$_GET['auth'] == 'exit') {
    setcookie('user_des');
    setcookie('pass_des');
    setcookie("auto_redirect", "1");
    $user_id = $_GET["user_id"];
    if($user_id !== ""){
    $q = "UPDATE users SET auto_logout = '1' WHERE uid = '$user_id'";
    mysql_query($q);}

  header("Location: /");

}
else {	// авторизация
    $user = @$_COOKIE['user_des'];
    $pass = @$_COOKIE['pass_des'];
}
if(!empty($_POST['auth_in'])) {
    $user = $_POST['in_user'];
    $pass = $_POST['in_pass'];
}
if($user_id = check_user($user,$pass)) {
    $tmp = str_replace('/','',$_SERVER['REQUEST_URI']);
    if($tmp == '' || $tmp == 'index.php') {
        setcookie('user_des', $user, time() + 1209600);  // 14 дней
        setcookie('pass_des', $pass, time() + 1209600);
        if($_COOKIE['auto_redirect'] == "1"){
        header("Location: /acc/query/");}
        setcookie("login_autocomplete", $user);
        setcookie("auto_redirect", "0");
    }
   	$auth = true;
}

// Проверка логина и пароля в базе
function check_user ($login, $pass) {
    global $user_type,$user_email,$user_id;
    if(!trim($login) || !trim($pass))
        return false;
    $res = mysql_query("SELECT uid,email,type,name,surname FROM users WHERE login='$login' AND pass='$pass' AND archive = '0'");
    //$res = mysql_query($query);
    echo mysql_error();
    if($r = mysql_fetch_array($res)) {
    $user_type = $r['type'];
    $user_email = $r['email'];
    $user_id = $r['uid'];



        //if($user_type != 'oth')
            return $r['uid'];
    }
    else
        return false;
}

// Информация о пользователе

$user_des = empty($_COOKIE['user_des']) ? $user : $_COOKIE['user_des'];
$pass_des = empty($_COOKIE['pass_des']) ? $pass : $_COOKIE['pass_des'];

$q = "SELECT * FROM users WHERE login = '$user_des' AND pass = '$pass_des'";
$r = mysql_fetch_assoc(mysql_query("$q"));
$user_access = array();
$user_access['uid'] = $r['uid'];
$user_access['name'] = $r['name'];
$user_access['surname'] = $r['surname'];
$user_access['job_id'] = $r['job_id'];																		// ID пользователя
$user_access['user_department'] = $r['user_department'];									// ID отдела
$user_access['doljnost'] = $r['doljnost'];																// ID должности
$user_access['accounting_user'] = $r['accounting_user'];									// Учетный сотрудник. 1 - да, 0 - нет.
$user_access['account_access'] = $r['account_access'];										// Доступ к учету. 1 - да, 0 - нет.
$user_access['account_access_dep'] = $r['account_access_dep'];						// Доступ к учету (отделы). ID отделов, разеделенные символом "|".
$user_access['account_access_group'] = $r['account_access_group'];				// Доступ к учету (группы). ID групп, разделенные символом "|".
$user_access['list_access'] = $r['list_access'];													// Доступ к ведомости. 1 - да, 0 - нет.
$user_access['list_access_group'] = $r['list_access_group'];							// Доступ к ведомости (группы). ID групп, разделенные символом "|".
$user_access['list_access_dep'] = $r['list_access_dep'];									// Доступ к ведомости (отделы). ID отделов, разделенные символом "|".
$user_access['table_access'] = $r['table_access'];												// Доступ к табелю. 1 - да, 0 - нет.
$user_access['table_access_group'] = $r['table_access_group'];						// Доступ к табелю (группы). ID групп, разделенные символом "|".
$user_access['table_access_dep'] = $r['table_access_dep'];								// Доступ к табелю (отделы). ID отделов, разделенные символом "|".
$user_access['manager_plan_access'] = $r['manager_plan_access'];					// Доступ к плану менеджеров. 1 - да, 0 - нет.
$user_access['statistics_access'] = $r['statistics_access'];							// Доступ к общей статистике. 1 - да, 0 - нет.
$user_access['application_response'] = $r['application_response'];				// Ответственный за прием заявок на производство. 1 - да, 0 - нет.
$user_access['material_response'] = $r['material_response'];							// Ответственный за поставку материалов на производство. 1 - да, 0 - нет.
$user_access['jobs_access'] = $r['jobs_access'];													// Допуск к начислению работ. ID работы, разделенные символом "|".
$user_access['edit_shipments'] = $r['edit_shipments'];										// Создание и редактирование отправок
$user_access['show_departments'] = $r['show_departments'];								// Список отделов, чьих сотрудников можно просматривать.
$user_access['edit_users'] = $r['edit_users'];														// Может добавлять, редактировать и удалять в архив сотрудников.
$user_access['show_pass'] = $r['show_pass'];															// Доступ к просмотру пароля. 1 - да, 0 - нет.
$user_access['shop_access'] = $r['shop_access'];													// Доступ к разделу "Магазин". 1 - да, 0 - нет.
$user_access['tasks_access'] = $r['tasks_access'];												// Доступ к разделу "Задачи". 1 - да, 0 - нет.
$user_access['plans_access'] = $r['plans_access'];												// Доступ к разделу "Планировщик". 1 - да, 0 - нет.
$user_access['sprav_access'] = $r['sprav_access'];												// Доступ к разделу "Справочники". 1 - да, 0 - нет.
$user_access['proizv_access'] = $r['proizv_access'];											// Доступ к разделу "Производство". 1 - да, 0 - нет.
$user_access['proizv_access_type'] = $r['proizv_access_type'];						// Тип доступа к заявкам на производстве. 1 - ко всем, 0 - только к своим.
$user_access['proizv_access_edit'] = $r['proizv_access_edit'];						// Доступ к редактированию заявок на производстве. 1 - да, 0 - нет.
$user_access['logistics_access'] = $r['logistics_access'];								// Доступ к разделу "Логистика". 1 - да, 0 - нет.
$user_access['sotr_access'] = $r['sotr_access'];													// Доступ к разделу "Сотрудники". 1 - да, 0 - нет.
$user_access['tabl_access'] = $r['tabl_access'];													// Доступ к разделу "Табл". 1 - да, 0 - нет.
$user_access['order_access'] = $r['order_access'];												// Доступ к разделу "Заказы". 1 - да, 0 - нет.
$user_access['order_access_type'] = $r['order_access_type'];							// Тип доступа к заказам. 2 - ко всем, 1 - только к своим, 0 - нет доступа.
$user_access['order_access_edit'] = $r['order_access_edit'];							// Доступ к редактированию заказов. 1- да, 0 - нет.
$user_access['order_access_payment'] = $r['order_access_payment'];				// Доступ к проставлению оплат по заказам. 1- да, 0 - нет.
$user_access['payment_edit_num'] = $r['payment_edit_num'];								// Редактирование номера накладной. 1 - да, 0 - нет.
$user_access['main_info_access'] = $r['main_info_access'];								// Доступ к общей информации (на главной). 1 - да, 0 - нет.
$user_access['allow_edit_access'] = $r['allow_edit_access'];              // Доступ к проставлению прав другим сотрудника
$user_access['shipped_edit'] = $r['shipped_edit'];              // Доступ к отметке проведения накладной


$_COOKIE['job_id'] = $r['job_id'];
$_COOKIE['uid'] = $r['uid'];
$_COOKIE['order_access'] = $r['order_access'];
$_COOKIE['order_access_type'] = $r['order_access_type'];
$_COOKIE['shop_access'] = $r['shop_access'];
$_COOKIE['tasks_access'] = $r['tasks_access'];
$_COOKIE['plans_access'] = $r['plans_access'];
$_COOKIE['sprav_access'] = $r['sprav_access'];
$_COOKIE['proizv_access'] = $r['proizv_access'];
$_COOKIE['logistics_access'] = $r['logistics_access'];
$_COOKIE['list_access'] = $r['list_access'];
$_COOKIE['sotr_access'] = $r['sotr_access'];
$_COOKIE['tabl_access'] = $r['tabl_access'];
$_COOKIE['show_departments'] = $r['show_departments'];
$_COOKIE['main_info_access'] = $r['main_info_access'];
$_COOKIE['allow_edit_access'] = $r['allow_edit_access'];
$_COOKIE['statistics_access'] = $r['statistics_access'];
$_COOKIE['shipped_edit'] = $r['shipped_edit'];
$_COOKIE['name'] =  $r['name'];
$_COOKIE['surname'] =  $r['surname'];


$q = mysql_query("SELECT is_division FROM user_departments WHERE id = " . $user_access['user_department']);
if ($q) {
    $str = mysql_fetch_assoc($q);
    // Является ли сотрудником подразделения
    $user_access['is_in_division'] = $str['is_division'];
}

if (!empty($user_access['is_in_division'])) {
    // Соответствие отдела подразделению ставим пока вручную
    // В дальнейшем в разделе отправок можно заменить номера филиалов на отделы со статусом "Обособленное подразделение"
    // И убрать ручное соответствие в count/add.php count/index.php и в разделе отправок
    switch ($user_access['user_department']) {
        case 22:
            $user_access['division_id'] = 1;
            break;
        case 23:
            $user_access['division_id'] = 2;
            break;
    }
}

?>
