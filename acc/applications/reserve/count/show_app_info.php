<?if ($_GET["code"] == "fdsfds8fu883832ije99089fs"){
$num_ord = $_GET["num_ord"];?>
<img src="../../i/del.gif" width="20" align=right height="20" alt="" style="cursor:pointer"  onclick="show_app_info(<?=$num_ord?>)">

����� ������ #<?=$ord_num;?>
<table width=500 style="border: inherit 1px; border-width: 1px" border=1 cellspacing=0 cellpadding=3 align=center>
<tr>
<td align=center class="tab_query_tit"><b>�������� ������</b></td>
<td align=center class="tab_query_tit"><b>���������� ���������</b></td>
<td align=center class="tab_query_tit"><b>% ���������� ������</b></td>
</tr><?
require_once("../../includes/db.inc.php");
$select_ord = "SELECT job, SUM(num_of_work) FROM job WHERE num_ord='$num_ord' GROUP BY job";
$select_ord = mysql_query($select_ord);
while($rows = mysql_fetch_row($select_ord)){

$job = $rows[0];
if ($job == "1"){$job_name = "���������"; }
if ($job == "2"){$job_name = "�������"; }
if ($job == "3"){$job_name = "��������";}
if ($job == "4"){$job_name = "������";}
if ($job == "5"){$job_name = "����� �� �����";}
if ($job == "6"){$job_name = "��� �� �����";}
if ($job == "7"){$job_name = "�������� �������";}
if ($job == "8"){$job_name = "�������� ��������";}
if ($job == "9"){$job_name = "�������� �� ����� (�����)";}
if ($job == "10"){$job_name = "�������� �� ����� (���)";}
if ($job == "11"){$job_name = "��������";}
if ($job == "12"){$job_name = "������� ��� � �������";}
if ($job == "13"){$job_name = "������ ���������� �����";}
if ($job == "14"){$job_name = "������ ���������";}
if ($job == "15"){$job_name = "����� � ��������";}
if ($job == "16"){$job_name = "������";}
?>
<tr>
<td align=center class="tab_td_norm"><?=$job_name?></td>
<td align=center class="tab_td_norm"><?=$rows[1]?></td>
<td align=center class="tab_td_norm">%</td>
</tr>

<?}?></table><?}?>