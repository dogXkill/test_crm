<?
ob_start();
$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

$tpus = $user_type;		// ��� ������������

if($tpus == 'sup' or $tpus == 'acc' or $tpus == 'adm' and $_POST["code"] == "ffdfsdfdsgg234fefd"){
?>
<script>
function show_extra(id){

if (!$('#extra_block_'+id).is(":visible")){
$('#extra_block_'+id).show();
$('#arr'+id).attr("src","/i/arrow_up.png");
}else{$('#extra_block_'+id).hide();
$('#arr'+id).attr("src","/i/arrow_down.png");
}


}
</script>

<table width="1200" border="1" cellpadding="1" cellspacing="0" style="border:#A6A6A6 solid 1px;">
<tr>
<td style="font-weight: bold" align=center><b>�����</b></td>
<td style="font-weight: bold" align=center><b>�������</b></td>
<td style="font-weight: bold" align=center>����� �������</td>
<td style="font-weight: bold" align=center>��/��� �������</td>
<td style="font-weight: bold" align=center>��������</td>
<td style="font-weight: bold" align=center>����� ��������</td>
<td style="font-weight: bold" align=center>��/��� �������</td>
<td style="font-weight: bold" align=center>����� �������</td>
<td style="font-weight: bold" align=center>����� �������</td>
<td style="font-weight: bold" align=center>���� �����</td>
<td style="font-weight: bold" align=center>��������� �������</td>
<td style="font-weight: bold" align=center>��������� ������</td>
<td style="font-weight: bold" align=center>�����</td>
<td style="font-weight: bold" align=center>��/�����</td>
<td style="font-weight: bold" align=center>�������������</td>
</tr>
<tr>
<td align=center>01.2014</td>
<td align=center>45</td>
<td align=center>450000</td>
<td align=center>10000</td>
<td align=center>15</td>
<td align=center>1500000</td>
<td align=center>100000</td>
<td align=center>60</td>
<td align=center>195000</td>
<td align=center>32500</td>
<td align=center>300000</td>
<td align=center>5000</td>
<td align=center>500000</td>
<td align=center>8333</td>
<td align=center>3333</td>
</tr>
</table>


<table width=900 border=0>
<?
$tek_month = date("Y-m");
$month = mysql_query("SELECT SUM(prdm_sum_acc), COUNT(*), SUM(prdm_opl) FROM `queries` WHERE `date_query` LIKE '".$tek_month."%'");
$month = mysql_fetch_array($month);
$sr_summa=$month[0]/$month[1];
echo "<tr onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td><img src=\"/i/arrow_down.png\" id=arr1 style=\"cursor:pointer\" onclick=\"show_extra(1)\"> ����� ������ � <u>�������</u> ������</td><td><b>".round($month[1])."</b> �� ����� <b>".round($month[0])."</b> �. ����:".round($month[0]-$month[2])."</td><td>������� �����: <b>".round($sr_summa)."</b> �.</td></tr>";

//����������� �� ����� ������ � ������� ������
//���� ������, ����� �������, ������� �����, ������� ������ ���/������
echo "<tr id=extra_block_1 style=\"display:none\"><td colspan=3 style=\"border:#0099CC solid 2px;\">";

//���� �������
$month = mysql_query("SELECT typ_ord, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE `date_query` LIKE '".$tek_month."%' GROUP BY typ_ord");
echo "<table width=500 border=1 cellspacing=0 style=\"border:#A6A6A6 solid 1px;\"><tr><td align=center><b>��� ������</b></td><td align=center><b>����������</b></td><td align=center><b>�����</b></td><td align=center><b>������� �����</b></td></tr>";
while($row = mysql_fetch_array($month)){
if ($row[0] == "1"){$typ_ord = "��������";}
else if ($row[0] == "2"){$typ_ord = "�������";}
else if ($row[0] == "3"){$typ_ord = "�����";}
else {$typ_ord = "�� ����������";}
echo "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".round($row[2])."</td><td align=center>".round($row[2]/$row[1])."</td></tr>";
}
echo "</table><br>";

//����� ������
$month = mysql_query("SELECT form_of_payment, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE `date_query` LIKE '".$tek_month."%' GROUP BY form_of_payment");
echo "<table width=500 border=1 cellspacing=0 style=\"border:#A6A6A6 solid 1px;\"><tr><td align=center><b>����� ������</b></td><td align=center><b>����������</b></td><td align=center><b>�����</b></td><td align=center><b>������� �����</b></td></tr>";
while($row = mysql_fetch_array($month)){
if ($row[0] == "1"){$typ_ord = "��������";}
else if ($row[0] == "2"){$typ_ord = "������";}
else if ($row[0] == "3"){$typ_ord = "�� ���������";}
else if ($row[0] == "4"){$typ_ord = "������";}
else {$typ_ord = "�� ����������";}
echo "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".round($row[2])."</td><td align=center>".round($row[2]/$row[1])."</td></tr>";
}
echo "</table>";

echo"</td></tr>";




//����������� ������ � ������� ������
$prosl_month = date("Y-m",strtotime("-1 month"));
$prosl_month_day = date("Y-m-d h:i:s",strtotime("-1 month"));
$month = mysql_query("SELECT SUM(prdm_sum_acc), COUNT(*), SUM(prdm_opl) FROM `queries` WHERE `date_query` BETWEEN '".$prosl_month."-01 00:00:00' AND '".$prosl_month_day."'");
$month = mysql_fetch_array($month);
$sr_summa=$month[0]/$month[1];
echo "<tr onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td><img src=\"/i/arrow_down.png\" id=arr2 style=\"cursor:pointer\" onclick=\"show_extra(2)\">  � <u>�������</u> ������ �� ���� ����</td><td><b>".round($month[1])."</b> �� ����� <b>".round($month[0])."</b> �. ����:".round($month[0]-$month[2])."</td><td>������� �����: <b>".round($sr_summa)."</b> �.</td></tr>";

//����������� �� ����� ������ �� ����������� ������ �������� ������
//���� ������, ����� �������, ������� �����, ������� ������ ���/������
echo "<tr id=extra_block_2 style=\"display:none\"><td colspan=3 style=\"border:#0099CC solid 2px;\">";
$month = mysql_query("SELECT typ_ord, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE `date_query` BETWEEN '".$prosl_month."-01 00:00:00' AND '".$prosl_month_day."' GROUP BY typ_ord");
echo "<table width=500 border=1 cellspacing=0 style=\"border:#A6A6A6 solid 1px;\"><tr><td align=center><b>��� ������</b></td><td align=center><b>����������</b></td><td align=center><b>�����</b></td><td align=center><b>������� �����</b></td></tr>";
while($row = mysql_fetch_array($month)){
if ($row[0] == "1"){$typ_ord = "��������";}
else if ($row[0] == "2"){$typ_ord = "�������";}
else if ($row[0] == "3"){$typ_ord = "�����";}
else {$typ_ord = "�� ����������";}
echo "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".round($row[2])."</td><td align=center>".round($row[2]/$row[1])."</td></tr>";
}
echo "</table><br>";

//����� ������
$month = mysql_query("SELECT form_of_payment, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE `date_query` BETWEEN '".$prosl_month."-01 00:00:00' AND '".$prosl_month_day."' GROUP BY form_of_payment");
echo "<table width=500 border=1 cellspacing=0 style=\"border:#A6A6A6 solid 1px;\"><tr><td align=center><b>����� ������</b></td><td align=center><b>����������</b></td><td align=center><b>�����</b></td><td align=center><b>������� �����</b></td></tr>";
while($row = mysql_fetch_array($month)){
if ($row[0] == "1"){$typ_ord = "��������";}
else if ($row[0] == "2"){$typ_ord = "������";}
else if ($row[0] == "3"){$typ_ord = "�� ���������";}
else if ($row[0] == "4"){$typ_ord = "������";}
else {$typ_ord = "�� ����������";}
echo "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".round($row[2])."</td><td align=center>".round($row[2]/$row[1])."</td></tr>";
}
echo "</table>";

echo"</td></tr>";







//������� �����
$last_month = date("Y-m",strtotime("-1 month"));
$l_month = mysql_query("SELECT SUM(prdm_sum_acc), COUNT(*), SUM(prdm_opl) FROM `queries` WHERE `date_query` LIKE '".$last_month."%'");
$l_month = mysql_fetch_array($l_month);
$sr_summa=$l_month[0]/$l_month[1];
echo "<tr onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td><img src=\"/i/arrow_down.png\" id=arr3 style=\"cursor:pointer\" onclick=\"show_extra(3)\"> ����� ������ � <u>�������</u> ������</td><td><b>".round($l_month[1])."</b> �� ����� <b>".round($l_month[0])."</b> �. ����:".round($l_month[0]-$l_month[2])."</td><td>������� �����: <b>".round($sr_summa)."</b> �.</td></tr>";


//����������� �� ����� ������ � ������� ������
//���� ������, ����� �������, ������� �����, ������� ������ ���/������
echo "<tr id=extra_block_3 style=\"display:none\"><td colspan=3 style=\"border:#0099CC solid 2px;\">";

//���� �������
$month = mysql_query("SELECT typ_ord, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE `date_query` LIKE '".$last_month."%' GROUP BY typ_ord");
echo "<table width=500 border=1 cellspacing=0 style=\"border:#A6A6A6 solid 1px;\"><tr><td align=center><b>��� ������</b></td><td align=center><b>����������</b></td><td align=center><b>�����</b></td><td align=center><b>������� �����</b></td></tr>";
while($row = mysql_fetch_array($month)){
if ($row[0] == "1"){$typ_ord = "��������";}
else if ($row[0] == "2"){$typ_ord = "�������";}
else if ($row[0] == "3"){$typ_ord = "�����";}
else {$typ_ord = "�� ����������";}
echo "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".round($row[2])."</td><td align=center>".round($row[2]/$row[1])."</td></tr>";
}
echo "</table><br>";

//����� ������
$month = mysql_query("SELECT form_of_payment, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE `date_query` LIKE '".$last_month."%' GROUP BY form_of_payment");
echo "<table width=500 border=1 cellspacing=0 style=\"border:#A6A6A6 solid 1px;\"><tr><td align=center><b>����� ������</b></td><td align=center><b>����������</b></td><td align=center><b>�����</b></td><td align=center><b>������� �����</b></td></tr>";
while($row = mysql_fetch_array($month)){
if ($row[0] == "1"){$typ_ord = "��������";}
else if ($row[0] == "2"){$typ_ord = "������";}
else if ($row[0] == "3"){$typ_ord = "�� ���������";}
else if ($row[0] == "4"){$typ_ord = "������";}
else {$typ_ord = "�� ����������";}
echo "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".round($row[2])."</td><td align=center>".round($row[2]/$row[1])."</td></tr>";
}
echo "</table>";

echo"</td></tr>";








//����������� ������ � ���� ������ � ������� ����
$prosl_month = date("Y-m",strtotime("-1 year"));
$prosl_month_day = date("Y-m-d h:i:s",strtotime("-1 year"));
$month = mysql_query("SELECT SUM(prdm_sum_acc), COUNT(*), SUM(prdm_opl) FROM `queries` WHERE `date_query` BETWEEN '".$prosl_month."-01 00:00:00' AND '".$prosl_month_day."'");
$month = mysql_fetch_array($month);
$sr_summa=$month[0]/$month[1];
echo "<tr onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td><img src=\"/i/arrow_down.png\" id=arr4 style=\"cursor:pointer\" onclick=\"show_extra(4)\"> � <u>�������</u> ���� � ���� ������ �� ���� ����</td><td><b>".round($month[1])."</b> �� ����� <b>".round($month[0])."</b> �. ����:".round($month[0]-$month[2])."</td><td>������� �����: <b>".round($sr_summa)."</b> �.</td></tr>";


//����������� �� ����� ������ � ������� ������
//���� ������, ����� �������, ������� �����, ������� ������ ���/������
echo "<tr id=extra_block_4 style=\"display:none\"><td colspan=3 style=\"border:#0099CC solid 2px;\">";

//���� �������
$month = mysql_query("SELECT typ_ord, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE `date_query` BETWEEN '".$prosl_month."-01 00:00:00' AND '".$prosl_month_day."' GROUP BY typ_ord");
echo "<table width=500 border=1 cellspacing=0 style=\"border:#A6A6A6 solid 1px;\"><tr><td align=center><b>��� ������</b></td><td align=center><b>����������</b></td><td align=center><b>�����</b></td><td align=center><b>������� �����</b></td></tr>";
while($row = mysql_fetch_array($month)){
if ($row[0] == "1"){$typ_ord = "��������";}
else if ($row[0] == "2"){$typ_ord = "�������";}
else if ($row[0] == "3"){$typ_ord = "�����";}
else {$typ_ord = "�� ����������";}
echo "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".round($row[2])."</td><td align=center>".round($row[2]/$row[1])."</td></tr>";
}
echo "</table><br>";

//����� ������
$month = mysql_query("SELECT form_of_payment, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE `date_query` BETWEEN '".$prosl_month."-01 00:00:00' AND '".$prosl_month_day."' GROUP BY form_of_payment");
echo "<table width=500 border=1 cellspacing=0 style=\"border:#A6A6A6 solid 1px;\"><tr><td align=center><b>����� ������</b></td><td align=center><b>����������</b></td><td align=center><b>�����</b></td><td align=center><b>������� �����</b></td></tr>";
while($row = mysql_fetch_array($month)){
if ($row[0] == "1"){$typ_ord = "��������";}
else if ($row[0] == "2"){$typ_ord = "������";}
else if ($row[0] == "3"){$typ_ord = "�� ���������";}
else if ($row[0] == "4"){$typ_ord = "������";}
else {$typ_ord = "�� ����������";}
echo "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".round($row[2])."</td><td align=center>".round($row[2]/$row[1])."</td></tr>";
}
echo "</table>";

echo"</td></tr>";



//����� ����� �� �������, ����� �������� �� �������
$tek_date = date("Y-m-d");
$points_total = mysql_query("SELECT COUNT(*) FROM `courier_tasks` WHERE `date` LIKE '".$tek_date."%'");
$points_total = mysql_fetch_array($points_total);

echo "<tr onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td valign=top>����� ����� <u><b>�������</b></u></td><td valign=top colspan=2><b>".$points_total[0]."</b>��.<br></td></tr><tr><td colspan=3>";
$points_per_driver = mysql_query("SELECT courier_id AS courier_id, COUNT(*), id FROM `courier_tasks` WHERE `date` LIKE '".$tek_date."' GROUP BY courier_id");
//SELECT couriers.name AS name, SUM(prdm_sum_acc) AS prdm_sum_acc FROM queries, courier_tasks, couriers WHERE queries.courier_task_id=queries.id AND couriers.id=courier_id AND form_of_payment='1' AND date='$tek_date' GROUP BY courier_id

while($row = mysql_fetch_array($points_per_driver)){

echo $row[0]."<br>";
//$driver_name = mysql_query("SELECT name FROM `couriers` WHERE `id` = '".$row[0]."'");
//$driver_name = mysql_fetch_array($driver_name);
//$points_id = mysql_query("SELECT courier_tasks.id, SUM(prdm_sum_acc) AS prdm_sum_acc, couriers.name AS name FROM courier_tasks, queries, couriers WHERE courier_tasks.date LIKE '".$tek_date."' AND courier_tasks.id = '".$row[0]."' AND queries.");
//$points_id = mysql_fetch_array($points_id);
//$tek_date_formatted = date("d-m-Y");
//echo "<a href=\"/acc/logistic/task_list.php?courier_id=".$row[0]."&date=".$tek_date_formatted."\" target=_blank>".$driver_name[0]."</a> - <b>".$row[1]."</b>�� ".$nalik[0]."<br>";
}





//����� ����� �� �������, ����� �������� �� �������
$tek_date = date("Y-m-d");
$points_total = mysql_query("SELECT COUNT(*) FROM `courier_tasks` WHERE `date` LIKE '".$tek_date."%'");
$points_total = mysql_fetch_array($points_total);

echo "<tr onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td valign=top>����� ����� <u><b>�������</b></u></td><td valign=top colspan=2><b>".$points_total[0]."</b>��.<br></td></tr><tr><td colspan=3>";
$points_per_driver = mysql_query("SELECT courier_id, COUNT(*), id FROM `courier_tasks` WHERE `date` LIKE '".$tek_date."' GROUP BY courier_id");

while($row = mysql_fetch_array($points_per_driver)){
$driver_name = mysql_query("SELECT name FROM `couriers` WHERE `id` = '".$row[0]."'");
$driver_name = mysql_fetch_array($driver_name);
$points_id = mysql_query("SELECT id FROM `courier_tasks` WHERE `date` LIKE '".$tek_date."' AND courier_id = '".$row[0]."'");
$points_id = mysql_fetch_array($points_id);
$tek_date_formatted = date("d-m-Y");
echo "<a href=\"/acc/logistic/task_list.php?courier_id=".$row[0]."&date=".$tek_date_formatted."\" target=_blank>".$driver_name[0]."</a> - <b>".$row[1]."</b>�� ".$nalik[0]."<br>";
}
















//����� ����� �� ������, ����� �������� �� ������
$tomorrow_date = date("Y-m-d",strtotime("+1 day"));
$points_total = mysql_query("SELECT COUNT(*) FROM `courier_tasks` WHERE `date` LIKE '".$tomorrow_date."%'");
$points_total = mysql_fetch_array($points_total);

echo "<tr onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td valign=top>����� ����� <u><b>������</b></u></td><td valign=top colspan=2><b>".$points_total[0]."</b>��.<br></td></tr><tr><td colspan=3>";
$points_per_driver = mysql_query("SELECT courier_id, COUNT(*), id FROM `courier_tasks` WHERE `date` LIKE '".$tomorrow_date."' GROUP BY courier_id");

while($row = mysql_fetch_array($points_per_driver)){
$driver_name = mysql_query("SELECT name FROM `couriers` WHERE `id` = '".$row[0]."'");
$driver_name = mysql_fetch_array($driver_name);
$points_id = mysql_query("SELECT id FROM `courier_tasks` WHERE `date` LIKE '".$tomorrow_date."' AND courier_id = '".$row[0]."'");
$points_id = mysql_fetch_array($points_id);
$tomorrow_date_formatted = date("d-m-Y",strtotime("+1 day"));
echo "<a href=\"/acc/logistic/task_list.php?courier_id=".$row[0]."&date=".$tomorrow_date_formatted."\" target=_blank>".$driver_name[0]."</a> - <b>".$row[1]."</b>�� ".$nalik[0]."<br>";
}


//����� ����� �� ������, ����� �������� �� �����������
$tomorrow_date = date("Y-m-d",strtotime("+2 day"));
$points_total = mysql_query("SELECT COUNT(*) FROM `courier_tasks` WHERE `date` LIKE '".$tomorrow_date."%'");
$points_total = mysql_fetch_array($points_total);

echo "<tr onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td valign=top>����� ����� <u><b>�����������</b></u></td><td valign=top colspan=2><b>".$points_total[0]."</b>��.<br></td></tr><tr><td colspan=3>";
$points_per_driver = mysql_query("SELECT courier_id, COUNT(*), id FROM `courier_tasks` WHERE `date` LIKE '".$tomorrow_date."' GROUP BY courier_id");

while($row = mysql_fetch_array($points_per_driver)){
$driver_name = mysql_query("SELECT name FROM `couriers` WHERE `id` = '".$row[0]."'");
$driver_name = mysql_fetch_array($driver_name);
$points_id = mysql_query("SELECT id FROM `courier_tasks` WHERE `date` LIKE '".$tomorrow_date."' AND courier_id = '".$row[0]."'");
$points_id = mysql_fetch_array($points_id);
$tomorrow_date_formatted = date("d-m-Y",strtotime("+2 day"));
echo "<a href=\"/acc/logistic/task_list.php?courier_id=".$row[0]."&date=".$tomorrow_date_formatted."\" target=_blank>".$driver_name[0]."</a> - <b>".$row[1]."</b>�� ".$nalik[0]."<br>";
}





?>
</td></tr>
</table>
<?

}

ob_end_flush(); ?>