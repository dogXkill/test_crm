<?
//echo $_GET["test"];

// ���������� ����� ������� �� ���� � ����
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

$month = array(
	'������',
	"�������",
	"�����",
	"������",
	"���",
	"����",
	"����",
	"�������",
	"��������",
	"�������",
	"������",
	"�������"
	);


// ���� ������������ �������� ������ � java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");


// ��������� ���� �� ������� '23.05.1997' � '1997-05-23' ��� ����������� ���������� � ����
function date_switch($val) {
	$a = explode('.',$val);
	$str =@$a[2].'-'.@$a[1].'-'.@$a[0];
	return $str;
}


$new_query_id = 0;						// �� ������ ������������ �������

$a = $_REQUEST['arr'];				// ��������� ������� ���� ��������

//�������� type ���������
$query_men = "SELECT type FROM users WHERE uid=".$a['ed_us_id'];
$res_men = mysql_query($query_men);

if($r_men = mysql_fetch_array($res_men)) {
  $type_user = $r_men['type']; 
}

//echo "ttt".$a['edit'];
 /*
function replace_multiarray(&$item, $key){
     $item = str_replace("'", "\"", $item);
}
array_walk_recursive($a, 'replace_multiarray');

array_walk_recursive($a, function(&$item, $key) {
    $item = addslashes($item);
}); */
	// ----------------- ������, ��������� � ���������� ���������� ------------------
if (!isset($a['sphere_other'])) {
  $a['sphere_other'] = '';
}
	if($a['client_lst'] == 0) {		// � ������ �������� ������� "������"
			$query = sprintf("INSERT INTO clients(user_id,short, sphere, sphere_other, celi, celi_other, potrebnost, kak_uznali, kak_uznali_other,name,legal_address,postal_address,deliv_address,inn,kpp,okpo,comment,cont_pers,cont_tel,rs_acc,bank,bik,korr_acc,dogov_num,firm_tel,email,gen_dir,ogrn) VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $a['ed_us_id'], $a['client_sh'], $a['sphere'], $a['sphere_other'], $a['celi'], $a['celi_other'], $a['potrebnost'], $a['kak_uznali'], $a['kak_uznali_other'], $a['client_full'], $a['leg_add'], $a['post_add'], $a['deliv_add'], $a['inn'], $a['kpp'], $a['okpo'], $a['comment'], $a['cont_pers'], $a['cont_tel'], $a['rs'], $a['bank'], $a['bik'], $a['korr'], $a['dogov_num'], $a['firm_tel'], $a['email'], $a['gen_dir'], $a['ogrn']);
			mysql_query($query);
			$client = mysql_insert_id();		// �� ������ �������
	}
	else {			// �� ������� ������� �� ������
			$client = $a['client_lst'];
			$query = sprintf("SELECT uid FROM clients WHERE del=0 AND user_id=%d AND short='%s' LIMIT 1", $a['ed_us_id'],$a['client_sh']);
			$res = mysql_query($query);

			if( $client_id_oth = mysql_fetch_array($res) ) {	// ����� ���������� ��� �������������
				$query = sprintf("UPDATE clients SET name='%s', sphere='%s', sphere_other='%s', celi='%s', celi_other='%s', potrebnost='%s', kak_uznali='%s', kak_uznali_other='%s', legal_address='%s', postal_address='%s', deliv_address='%s', inn='%s', kpp='%s', okpo='%s', comment='%s', cont_pers='%s', cont_tel='%s', rs_acc='%s', bank='%s', bik='%s', korr_acc='%s', dogov_num='%s', firm_tel='%s', email='%s', gen_dir='%s', ogrn='%s' WHERE uid=%d", $a['client_full'], $a['sphere'], $a['sphere_other'], $a['celi'], $a['celi_other'], $a['potrebnost'], $a['kak_uznali'], $a['kak_uznali_other'], $a['leg_add'], $a['post_add'], $a['deliv_add'], $a['inn'], $a['kpp'], $a['okpo'], $a['comment'], $a['cont_pers'], $a['cont_tel'], $a['rs'], $a['bank'], $a['bik'], $a['korr'], $a['dogov_num'], $a['firm_tel'], $a['email'], $a['gen_dir'], $a['ogrn'], $client_id_oth['uid']);
				mysql_query($query);
				$client = $client_id_oth['uid'];

			} else {	// ���� � ������������ ���� ������ ������� - ��������

				$query = sprintf("INSERT INTO clients(user_id,short,sphere, sphere_other, celi, celi_other, potrebnost, kak_uznali, kak_uznali_other, name,legal_address,postal_address,deliv_address,inn,kpp,okpo,comment,cont_pers,cont_tel,rs_acc,bank,bik,korr_acc,dogov_num,firm_tel,email,gen_dir,ogrn) VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $a['ed_us_id'], $a['client_sh'], $a['sphere'], $a['sphere_other'], $a['celi'], $a['celi_other'], $a['potrebnost'], $a['kak_uznali'], $a['kak_uznali_other'], $a['client_full'], $a['leg_add'], $a['post_add'], $a['deliv_add'], $a['inn'], $a['kpp'], $a['okpo'], $a['comment'], $a['cont_pers'], $a['cont_tel'], $a['rs'], $a['bank'], $a['bik'], $a['korr'], $a['dogov_num'], $a['firm_tel'], $a['email'], $a['gen_dir'], $a['ogrn']);
				mysql_query($query);
				$client = mysql_insert_id();
			}

	}
	//---------------------------------------------------------------------------------

	$tpacc = $a['tpacc'];

// ################################################################################
// ############################  ����� ������  ####################################
// ################################################################################

if($a['edit'] == 'new') {

// ################################################################################
// #####################  ������ � ���� ������ ��� ��������  ######################


$month = array(
	'������',
	"�������",
	"�����",
	"������",
	"���",
	"����",
	"����",
	"�������",
	"��������",
	"�������",
	"������",
	"�������"
	);


$bod = '<html><head><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251" />';

$bod .= '<style type="text/css"><!--
body,table,td {font-family: Arial, Helvetica, sans-serif;font-size: 11px;color:#000000;}
body {padding:0px;padding-left:10px;margin:5px;background-color:#FFFFFF;}
.tab_main {width:800px;border:2px #FFFFFF solid;background-color:#EEEEEE;}
.td_title {background-color:#A9DFA8;height:25px;text-align:center;font-weight:bold;font-size:12px;vertical-align:middle;}
.td_title2 {background-color:#CBEEC8;height:18px;text-align:center;font-weight:bold;font-size:12px;vertical-align:middle;}
.td_left {text-align:right;width:50%;border-bottom:2px #FFFFFF solid;font-weight:bold;height:18px;}
.td_right {text-align:left;width:50%;border-bottom:2px #FFFFFF solid;padding-left:20px;height:18px;}
.td_empty {background-color:#FFFFFF;}
.tab2 {width:100%;}
.tab2_td1 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:60%;text-align:center;height:18px;}
.tab2_td2 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:20%;text-align:center;height:18px;}
.tab2_td3 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:20%;text-align:center;height:18px;}
.tab2_tdspace {height:70px;text-align:right;vertical-align:middle;background-color:#FFFFFF;}
.tab2_sub {width:400px;background-color:#EEEEEE;}
.tab2_sub_title {background-color:#CBEEC8;height:18px;text-align:center;font-weight:bold;font-size:11px;vertical-align:middle;border-right:1px #FFFFFF solid;	border-bottom:1px #FFFFFF solid;width:25%;}
.tab2_sub_td {text-align:center;width:25%;border-right:1px #FFFFFF solid;}
.tab3_td1 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:25%;text-align:center;height:18px;}
.tab3_td2 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:10%;text-align:center;height:18px;}
.tab3_td_nr {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;text-align:center;height:18px;}
.tab3_sub {width:600px;background-color:#EEEEEE;}
.tab3_sub_title {background-color:#CBEEC8;height:18px;	text-align:center;font-weight:bold;font-size:11px;vertical-align:middle;border-right:1px #FFFFFF solid;	border-bottom:1px #FFFFFF solid;width:36%;}
.tab3_sub_td {text-align:center;width:32%;border-right:1px #FFFFFF solid;}
--></style></head><body>';

$dat = date("j ").$month[date("n")-1].date(" Y").' �. '.date("G:i");

$bod .= '<table class="tab_main" cellpadding="0" cellspacing="0" align="center">
  <tr><td colspan="2" class="td_title">������ �� ���� '.$dat.', '.$a['user_full_name'].'</td></tr>';


if($a['client_full'])
	$bod .= '<tr>
    <td class="td_left">������� ����������� ������������:</td>
    <td class="td_right">'.$a['client_full'].'</td>
  </tr>';

if($a['leg_add'])
	$bod .= '<tr>
    <td class="td_left">����������� �����:</td>
    <td class="td_right">'.$a['leg_add'].'</td>
  </tr>';

if($a['post_add'])
	$bod .= '<tr>
    <td class="td_left">�����������/�������� �����:</td>
    <td class="td_right">'.$a['post_add'].'</td>
  </tr>';

  if($a['deliv_add'])
	$bod .= '<tr>
    <td class="td_left">����� ��������:</td>
    <td class="td_right">'.$a['deliv_add'].'</td>
  </tr>';

if($a['inn'])
	$bod .= '<tr>
    <td class="td_left">���:</td>
    <td class="td_right">'.$a['inn'].'</td>
  </tr>';

if($a['kpp'])
	$bod .= '<tr>
    <td class="td_left">���:</td>
    <td class="td_right">'.$a['kpp'].'</td>
  </tr>';

if($a['okpo'])
	$bod .= '<tr>
    <td class="td_left">����:</td>
    <td class="td_right">'.$a['okpo'].'</td>
  </tr>';

if(trim($a['rs']))
	$bod .= '<tr>
    <td class="td_left">�/�:</td>
    <td class="td_right">'.$a['rs'].'</td>
  </tr>';

if(trim($a['bank']))
	$bod .= '<tr>
    <td class="td_left">������ � �����:</td>
    <td class="td_right">'.$a['bank'].'</td>
  </tr>';

if(trim($a['bik']))
	$bod .= '<tr>
    <td class="td_left">���:</td>
    <td class="td_right">'.$a['bik'].'</td>
  </tr>';

if(trim($a['korr']))
	$bod .= '<tr>
    <td class="td_left">����/�:</td>
    <td class="td_right">'.$a['korr'].'</td>
  </tr>';

if(trim($a['dogov_num']))
	$bod .= '<tr>
    <td class="td_left">������� �:</td>
    <td class="td_right">'.$a['dogov_num'].'</td>
  </tr>';

if(trim($a['ogrn']))
	$bod .= '<tr>
    <td class="td_left">����:</td>
    <td class="td_right">'.$a['ogrn'].'</td>
  </tr>';

if(trim($a['firm_tel']))
	$bod .= '<tr>
    <td class="td_left">������� ��������:</td>
    <td class="td_right">'.$a['firm_tel'].'</td>
  </tr>';

if(trim($a['email']))
	$bod .= '<tr>
    <td class="td_left">E-Mail:</td>
    <td class="td_right">'.$a['email'].'</td>
  </tr>';

if(trim($a['gen_dir']))
	$bod .= '<tr>
    <td class="td_left">����������� ��������:</td>
    <td class="td_right">'.$a['gen_dir'].'</td>
  </tr>';

if(trim($a['cont_pers']))
	$bod .= '<tr>
    <td class="td_left">���������� ����:</td>
    <td class="td_right">'.$a['cont_pers'].'</td>
  </tr>';

if(trim($a['cont_tel']))
	$bod .= '<tr>
    <td class="td_left">���������� �������:</td>
    <td class="td_right">'.$a['cont_tel'].'</td>
  </tr>';


if(trim($a['typ_ord'])){

if($a['typ_ord'] == "1"){$typ_ord = "��� �����";}
if($a['typ_ord'] == "2"){$typ_ord = "�������";}
if($a['typ_ord'] == "3"){$typ_ord = "������� � ����";}

	$bod .= '<tr>
    <td class="td_left">��� ������:</td>
    <td class="td_right">'.$typ_ord.'</td>
  </tr>';
  }

if(trim($a['corsina_order_uid']))
	$bod .= '<tr>
    <td class="td_left">������ �� ����� � �������� ��������:</td>
    <td class="td_right"><a href=http://www.paketoff.ru/admin/shop/orders/view/?id='.$a['corsina_order_uid'].' target=_blank>'.$a['corsina_order_num'].'</a></td>
  </tr>';

if(trim($a['form_of_payment']))
if($a['form_of_payment'] == "1"){$form_of_payment = "��������";}
if($a['form_of_payment'] == "2"){$form_of_payment = "������";}
if($a['form_of_payment'] == "3"){$form_of_payment = "������ �� ���������";}
if($a['form_of_payment'] == "4"){$form_of_payment = "�� �����";}
	$bod .= '<tr>
    <td class="td_left">����� ������:</td>
    <td class="td_right">'.$form_of_payment.'</td>
  </tr>';

if(trim($a['deliv_id'])){
if($a['deliv_id'] == ""){$deliv_type = "�� �������";}
if($a['deliv_id'] == "1"){$deliv_type = "���������";}
if($a['deliv_id'] == "2"){$deliv_type = "�������� �� ���";}
if($a['deliv_id'] == "8"){$deliv_type = "�������� �� ��";}
if($a['deliv_id'] == "3"){$deliv_type = "�������";}
if($a['deliv_id'] == "5"){$deliv_type = "����";}
	$bod .= '<tr>
    <td class="td_left">��������:</td>
    <td class="td_right">'.$deliv_type.'</td>
  </tr>';}

$bod .= '<tr>
    <td colspan="2" class="td_empty">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">

    <table class="tab2" cellpadding="0" cellspacing="0" >
      <tr>
        <td colspan="3" class="td_title2">������� �����</td>
      </tr>
      <tr>
        <td class="tab2_td1"><strong>������������</strong></td>
        <td class="tab2_td2"><strong>����������</strong></td>
        <td class="tab2_td3"><strong>���� �� ��.</strong></td>
      </tr>';


for($i=0;$i<count($a['predmet']['list']);$i++) {
	$bod .= '<tr>
        <td class="tab2_td1">'.$a['predmet']['list'][$i]['name'].'</td>
        <td class="tab2_td2">'.$a['predmet']['list'][$i]['num'].'</td>
        <td class="tab2_td3">'.$a['predmet']['list'][$i]['price'].'</td>
      </tr>';
}

$bod .= '<tr>
<td colspan="3" class="tab2_tdspace">
<table class="tab2_sub" cellpadding="0" cellspacing="0" align="right">
<tr>';

if(trim($a['predmet']['acc_number']))
$bod .= '<td class="tab2_sub_title">����� �����</td>';

$bod .= '<td class="tab2_sub_title">����� �����</td>
<td class="tab2_sub_title">��������</td>
<td class="tab2_sub_title">����</td>
</tr><tr>';

if(trim($a['predmet']['acc_number']))
$bod .= '<td class="tab2_sub_td">'.$a['predmet']['acc_number'].'</td>';


$bod .= '<td class="tab2_sub_td">'.$a['predmet']['summ_acc'].' ������: '.$a['predmet']['skidka'].'%</td>
<td class="tab2_sub_td">'.$a['predmet']['opl']['summ'].'</td>
<td class="tab2_sub_td">'.$a['predmet']['dolg'].'</td></tr>';


$bod .= '</table></td></tr></table></td>
</tr><tr><td colspan="2">
<table class="tab2" cellpadding="0" cellspacing="0">
<tr><td colspan="7" class="td_title2">������������ �����������</td></tr>
<tr>
<td class="tab3_td1"><strong>���������</strong></td>
<td class="tab3_td1"><strong>������������</strong></td>
<td class="tab3_td2"><strong>����������</strong></td>
<td class="tab3_td2"><strong>���������</strong></td>
<td class="tab3_td2"><strong>����� �����</strong></td>
<td class="tab3_td2"><strong>��������</strong></td>
<td class="tab3_td2"><strong>����</strong></td>
</tr>';

for($i=0;$i<count($a['podr']['list']);$i++) {
	if(!trim($a['podr']['list'][$i]['num_acc']))
		$a['podr']['list'][$i]['num_acc'] = '-';
	$bod .= '<tr>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['podr_name'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['name'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['num'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['price'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['num_acc'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['opl'][$i]['summ'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['dolg'].'</td>
      </tr>';
}

$bod .= '<tr><td class="tab2_tdspace" colspan="7">
        <table class="tab2_sub" cellpadding="0" cellspacing="0" align="right">
            <tr>
              <td class="tab3_sub_title">����� �������������</td>
              <td class="tab3_sub_title">�������� (����.)</td>
              <td class="tab3_sub_title">���� (����.):</td>
            </tr>
            <tr>
              <td class="tab3_sub_td">'.$a['podr']['sebist'].'</td>
              <td class="tab3_sub_td">'.$a['podr']['opl_summ'].'</td>
              <td class="tab3_sub_td">'.$a['podr']['dolg_summ'].'</td>
            </tr>
        </table></td></tr></table></td></tr>';

if(trim($a['note']))
	$bod .= '<tr><td class="td_left">����������:</td>
    <td class="td_right">'.$a['note'].'</td></tr>';

$bod .= '</table></body></html>';

$tema = $a['client_sh'].' ( '.$a['client_full'].' ) , '.$a['user_full_name'];

$query = "SELECT * FROM mail ORDER BY uid";
$res_m = mysql_query($query);

$spis_mail = '';
while($r_m = mysql_fetch_array($res_m)) {
	$spis_mail .= $r_m['email'].',';
}

// ���������� ����� ���������
$query_men = "SELECT email, type FROM users WHERE uid=".$a['ed_us_id'];
$res_men = mysql_query($query_men);
if($r_men = mysql_fetch_array($res_men)) {
  $type_user = $r_men['type'];

  if( (trim($r_men['email'])) && (substr_count($spis_mail,trim($r_men['email']))==0)) {
    $spis_mail .= $r_men['email'].',';
  }  
}


$query = "INSERT INTO mail_temp(tema,komu,bod) VALUES('".mysql_escape_string($tema)."', '".mysql_escape_string(trim($spis_mail))."', '".mysql_escape_string($bod)."')";
mysql_query($query);

// #####################  ������ � ���� ������ ��� ��������  ######################
// ################################################################################

	// --------------------- ������ ------------------
	$query = "INSERT INTO queries(client_id,user_id,";
	$vals = $client.','.$a['user_id'].',';

 /*	if($tpacc && $a['predmet']['deliv_id']) { 						// ������� - ����� �����
		$query 	.= 'deliv_id,';
		$vals 	.= "'".$a['predmet']['deliv_id']."',";
	}    */

    if($tpacc && $a['predmet']['skidka']) {
		$query 	.= 'skidka,';
		$vals 	.= "'".$a['predmet']['skidka']."',";
	}
	$query 		.= 'prdm_sum_acc,';										// ������� - ����� �����
	$vals 		.= "'".$a['predmet']['summ_acc']."',";

	if($tpacc) {														// ������� - ������, ����
		$query 	.= 'prdm_opl,prdm_dolg,';
		$vals 	.= "'".$a['predmet']['opl']['summ']."','".$a['predmet']['dolg']."',";
	}

	$query		.= 'podr_sebist,';										// ���������� - ����� �������������
	$vals 		.= "'".$a['podr']['sebist']."',";

	if($tpacc) {														// ���������� - ������, ����
		$query 	.= 'podr_opl,podr_dolg,';
		$vals 	.= "'".$a['podr']['opl_summ']."','".$a['podr']['dolg_summ']."',";
	}

	$query 		.= 'note,date_query';									// ����������, ���� �������
	$vals 		.= "'".$a['note']."', NOW()";

  /*	if($tpacc && $a['predmet']['acc_number']) {
		$query	.= ',date_ready,ready';									// ���� ����������,������ ����������
		$vals		.= ",NOW(),'1'";
	}  */
	$query 		.= ',prj_ready,typ_ord,form_of_payment,deliv_id,corsina_order_uid,corsina_order_num';									// ��� ������, ����� ������
	$vals 		.= ",'0', ".$a['typ_ord'].", ".$a['form_of_payment'].", ".$a['deliv_id'].", '".$a['corsina_order_uid']."', '".$a['corsina_order_num']."'";


	$query .= ") VALUES(".$vals.")";


	mysql_query($query);
	echo mysql_error();
	$new_query_id = mysql_insert_id();

	// --------------------- ������ �������� ����� ------------------

	for($i=0;$i<count($a['predmet']['list']);$i++) {
    
		$query = sprintf("INSERT INTO obj_accounts(query_id,art_num,nn,name,num,price,r_price_our) VALUES(%d,'%s','%s','%s','%s','%s', '%s')",$new_query_id, $a['predmet']['list'][$i]['art_num'],($i+1), $a['predmet']['list'][$i]['name'], $a['predmet']['list'][$i]['num'], $a['predmet']['list'][$i]['price'],$a['predmet']['list'][$i]['price_our']);

				mysql_query($query);
	}

	//  ������ ����� ��� �������� �����
	if(isset($a['predmet']['opl']['list'])) {
		for($i=0;$i<count($a['predmet']['opl']['list']);$i++) {
			$query = sprintf("INSERT INTO payment_predm(query_id,nn,sum_accounts, number_pp,date_ready) VALUES(%d,%d,'%s','%s','%s')",
						$new_query_id,
						($i+1),
						$a['predmet']['opl']['list'][$i]['summ'],
						$a['predmet']['opl']['list'][$i]['num_pp'],
						date_switch($a['predmet']['opl']['list'][$i]['date']) );

			mysql_query($query);
}}
	// ------------------ ������ ����������� --------------------------


	for($i=0;$i<count($a['podr']['list']);$i++) {
		$query = sprintf("INSERT INTO contractors_list(query_id,nn,contr_id,name,price,num,opl,debt)  VALUES(%d,%d,%d,'%s','%s','%s','%s','%s')",
		$new_query_id,
		($i+1),
		$a['podr']['list'][$i]['podr'],
		$a['podr']['list'][$i]['name'],
		$a['podr']['list'][$i]['price'],
		$a['podr']['list'][$i]['num'],
		$a['podr']['list'][$i]['num_acc'],
		$a['podr']['opl'][$i]['summ'],
		$a['podr']['list'][$i]['dolg']);

				mysql_query($query);

		$new_contr_id = mysql_insert_id();


		// ���� ������ ��� �����������
		if(isset($a['podr']['opl'][$i]['list'])) {
			for($j=0;$j<count($a['podr']['opl'][$i]['list']);$j++) {
				$query = sprintf("INSERT INTO payment_podr(contr_id,nn,sum_accounts,number_pp,date_ready) VALUES(%d,%d,'%s','%s','%s')",
						$new_contr_id,
						($j+1),
						$a['podr']['opl'][$i]['list'][$j]['summ'],
						$a['podr']['opl'][$i]['list'][$j]['num_pp'],
						date_switch($a['podr']['opl'][$i]['list'][$j]['date']) );

					mysql_query($query);

	}}}



		// �������������� �������� �����
		$acc_num = trim($a['predmet']['acc_number']);
		if( ($acc_num == '���') || ($acc_num == 'no') || ($acc_num == '-') )
			$acc_num = 'none';
		else
			$acc_num = intval($acc_num);

		$acc_num = ( $acc_num === 'none' ) ? '�� �����' : $acc_num;

// ������� �������� � ������������ ������
$GLOBALS['_RESULT'] = array(
	"id"	=> $new_query_id,
);
}

// ################################################################################
// ########################  �������������� �������  ##############################
// ################################################################################

else {

		// -----------------��������� ������ ����� -----------------------
   //		if($tpacc) {		// �����, ���������
		  $acc_num = trim($a['predmet']['acc_number']);
		  /*	if( ($acc_num == '���') || ($acc_num == 'no') || ($acc_num == '-') )
				$acc_num = 'none';
			else
				$acc_num = $acc_num;
		}    */

         //echo $acc_num;


		// -------------------- ������� - queries --------------------
		$query = "UPDATE queries SET client_id=".$client.", ";


		if($tpacc) {	// ������� - ����� �����
      //��������� �� ��� ���� �������������
      if ($type_user != 'sup' && $type_user != 'meg' && $type_user != 'acc' && $type_user != 'adm') {
        $query .= (($acc_num === 'none') || ($acc_num > 0)) ? "prdm_num_acc='".$acc_num."'," : "prdm_num_acc=NULL,";
      }
			
		}

			// ������� - ����� �����
		$query .= "prdm_sum_acc='".$a['predmet']['summ_acc']."', ";
		$query .= "skidka='".$a['predmet']['skidka']."', ";
		// ������, ����
		$query .= ($tpacc) ? sprintf("prdm_opl='%s',prdm_dolg='%s',", $a['predmet']['opl']['summ'], $a['predmet']['dolg']) : '';

		$query .= "podr_sebist='".$a['podr']['sebist']."',";	// ���������� - ����� �������������

		// ���������� - ������, ����
		$query .= ($tpacc) ? sprintf("podr_opl='%s',podr_dolg='%s',", $a['podr']['opl_summ'], $a['podr']['dolg_summ']) : '';

		$query .= "note='".$a['note']."'";

	  	if($tpacc) {
			$query .= (($acc_num === 'none') || ($acc_num > 0)) ? ",date_ready=NOW(),ready='1'" : ",ready='0'";
		}
    $query .= ",typ_ord='".$a['typ_ord']."'";
    $query .= ",form_of_payment='".$a['form_of_payment']."'";
    $query .= ",deliv_id='".$a['deliv_id']."'";
	$query .= " WHERE uid=".$a['edit'];



		mysql_query($query);
       // echo $client." ".$a['edit'];
        echo mysql_error();

//	-------------------	������ �������� �����	------------------

		$query = "DELETE FROM obj_accounts WHERE query_id=".$a['edit'];
		mysql_query($query);



		for($i=0;$i<count($a['predmet']['list']);$i++) {
			$query = sprintf("INSERT INTO obj_accounts(query_id,nn,art_num, name,num,price, r_price_our) VALUES(%d,'%s','%s','%s','%s','%s','%s')",$a['edit'],($i+1), $a['predmet']['list'][$i]['art_num'], $a['predmet']['list'][$i]['name'], $a['predmet']['list'][$i]['num'], $a['predmet']['list'][$i]['price'], $a['predmet']['list'][$i]['price_our']);

		   mysql_query($query);
		}

		// ---------- ������ ������ ������ ��� �������� ����������� �� ������� ����������� --------------------
		$query = "SELECT b.email FROM queries as a, users as b WHERE a.uid=".$a['edit']." AND a.user_id=b.uid";
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);
		$user_mail = $r['email'];

		$arr_mail = array();
		$query = "SELECT email FROM mail";
		$res = mysql_query($query);

		while( $r = mysql_fetch_array($res) ) {
			$arr_mail[] = $r['email'];
		}
		if(!in_array($user_mail,$arr_mail))
			$arr_mail[] = $user_mail;

		$mail_list  = implode(' ', $arr_mail);
		// ----------------------------------------------------------------------------------------------------

		$query = "SELECT uid FROM payment_predm WHERE query_id=".$a['edit'];
		$res = mysql_query($query);
		$predm_opl_nums = mysql_num_rows($res);

		$predm_new_opl = count(@$a['predmet']['opl']['list']) - $predm_opl_nums;

		if( $predm_new_opl > 0 ) {

					$bod = '<html><head><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251" />';

					$bod .= '<style type="text/css"><!--
					body,table,td {font-family: Arial, Helvetica, sans-serif;font-size: 11px;color:#000000;}
					body {padding:0px;padding-left:10px;margin:5px;background-color:#FFFFFF;}
					--></style></head><body>';

					$dat = date("j ").$month[date("n")-1].date(" Y").' �. '.date("G:i");

					$bod .= '<strong>���������(��) '.$a['user_full_name'].'!</strong><br><br>'.
					$dat.', ��������� ������ �� '.$a['client_full'];
					if( trim(@$a['predmet']['acc_number']) ) {
						$bod .= ' �� ����� � \''.$a['predmet']['acc_number'].'\'';
					}
					$bod .= ' � ������� ';
					for( $i=0;$i<$predm_new_opl;$i++ ) {
						$bod .= $a['predmet']['opl']['list'][($predm_opl_nums+$i)]['summ'].' �.';
						$bod .= ( $i < ($predm_opl_nums) ) ? ', ' : '';
					}
					$bod .= '<br>������������� ������� �� ������� ������� ���������� \''.$a['predmet']['dolg'].' �\'.';
					$bod .= '</body></html>';

					$tema = '������ �� '.$a['client_full'];

					$query = sprintf( "INSERT INTO mail_temp(tema,komu,bod) VALUES('%s','%s','%s')", mysql_escape_string($tema), mysql_escape_string($mail_list), mysql_escape_string($bod) );
					mysql_query($query);
		}

		//  ������ ����� ��� �������� �����
		$query = "DELETE FROM payment_predm WHERE query_id=".$a['edit'];
		mysql_query($query);

		if(isset($a['predmet']['opl']['list'])) {
			for($i=0;$i<count($a['predmet']['opl']['list']);$i++) {
				$query = sprintf("INSERT INTO payment_predm(query_id,nn,sum_accounts, number_pp,date_ready) VALUES(%d,%d,'%s','%s','%s')",
							$a['edit'],
							($i+1),
							$a['predmet']['opl']['list'][$i]['summ'],
							$a['predmet']['opl']['list'][$i]['num_pp'],
							date_switch($a['predmet']['opl']['list'][$i]['date']) );

				mysql_query($query);

			}
		}


//	-------------------	������ �����������	------------------

		$arr_contr_id 		= array();
		$arr_contr_numsp 	= array();	// ������ ���������� �����, �� ����������

		// ������ �� ������ ����������� ��� �������� ����� �����
		$query = "SELECT uid FROM contractors_list WHERE query_id=".$a['edit']." ORDER BY nn";
		$res = mysql_query($query);
		while($r = mysql_fetch_array($res)) {
			$arr_contr_id[] = $r['uid'];
			$query = "SELECT uid FROM payment_podr WHERE contr_id=".$r['uid'];
			$res2 = mysql_query($query);
			$arr_contr_numsp[] = mysql_num_rows($res2);
		}


	// ##################################################################################################
	// ########################### �������� ���������� �� ������ �� ����� ###############################



	for($i=0;$i<count($a['podr']['list']);$i++) {
		// ���� ��������� ����� ������ ����������
		// ��������� � ������ ����� ����� ��� ������� ��������
		$cont_n = count(@$a['podr']['opl'][$i]['list']);		// ���������� ����� ����� ��� ����������

		$cont_s = @$arr_contr_numsp[$i];										// ���������� ����� �� �����

		$num_new_opl = $cont_n - $cont_s;

		if( $num_new_opl > 0 ) {
			for($n=0; $n < $num_new_opl; $n++) {

				$bod = '<html><head><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251" />';

				$bod .= '<style type="text/css"><!--
				body,table,td {font-family: Arial, Helvetica, sans-serif;font-size: 11px;color:#000000;}
				body {padding:0px;padding-left:10px;margin:5px;background-color:#FFFFFF;}
				--></style></head><body>';

				$dat = date("j ").$month[date("n")-1].date(" Y").' �. '.date("G:i");

				$bod .= '<strong>���������(��) '.$a['user_full_name'].'!</strong><br><br>'.
				$dat.', ���� ��������� ������ ���������� <strong>'.$a['podr']['list'][$i]['podr_name'].
				'</strong> �� ������� <strong>'.$a['client_full'].'</strong> �� \'<strong>'.$a['podr']['list'][$i]['num_acc'].'</strong>\' � ������� <strong>'.
				$a['podr']['opl'][$i]['list'][($cont_s+$n)]['summ'].' �.</strong>. <br>����� ���������� ��������� \'<strong>'.
				$a['podr']['opl'][$i]['list'][($cont_s+$n)]['num_pp'].'</strong>\'. ���� ����������� ����� ����������� �� ������� ������� ���������� <strong>'.$a['podr']['list'][$i]['dolg'].' �.</strong>';

				$tema = '������ ���������� '.$a['podr']['list'][$i]['podr_name'];

				$query = sprintf( "INSERT INTO mail_temp(tema,komu,bod) VALUES('%s','%s','%s')", mysql_escape_string($tema), mysql_escape_string($mail_list), mysql_escape_string($bod) );
				mysql_query($query);

			}
		}
	}
	// ########################### �������� ���������� �� ������ �� ����� ###############################
	// ##################################################################################################



		$arr_nums_opl = array();

		for($t=0;$t<count($arr_contr_id);$t++) {
			$query = "DELETE FROM payment_podr WHERE contr_id=".$arr_contr_id[$t];
			mysql_query($query);
		}


		$query = "DELETE FROM contractors_list WHERE query_id=".$a['edit'];
		mysql_query($query);



	for($i=0;$i<count($a['podr']['list']);$i++) {


		$query = sprintf("INSERT INTO contractors_list(query_id,nn,contr_id,name,price,num,acc_number,opl,debt)  VALUES(%d,%d,%d,'%s','%s','%s','%s','%s','%s')",
		$a['edit'],
		($i+1),
		$a['podr']['list'][$i]['podr'],
		$a['podr']['list'][$i]['name'],
		$a['podr']['list'][$i]['price'],
		$a['podr']['list'][$i]['num'],
		$a['podr']['list'][$i]['num_acc'],
		$a['podr']['opl'][$i]['summ'],
		$a['podr']['list'][$i]['dolg']);

		mysql_query($query);
		$new_contr_id = mysql_insert_id();


		// ���� ������ ��� �����������
		if(isset($a['podr']['opl'][$i]['list'])) {
			for($j=0;$j<count($a['podr']['opl'][$i]['list']);$j++) {
					$query = sprintf("INSERT INTO payment_podr(contr_id,nn,sum_accounts,number_pp,date_ready) VALUES(%d,%d,'%s','%s','%s')",
							$new_contr_id,
							($j+1),
							$a['podr']['opl'][$i]['list'][$j]['summ'],
							$a['podr']['opl'][$i]['list'][$j]['num_pp'],
							date_switch($a['podr']['opl'][$i]['list'][$j]['date']) );
						mysql_query($query);
			}
		}
	}

//echo mysql_error();
//echo "ttt".$a['edit'];
// ������� �������� � ������������ ������
$GLOBALS['_RESULT'] = array(
	"id"	=> 'edit',
);
}

?>