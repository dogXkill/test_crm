<html>

<head>
  <title>������������� �������� �� ������� / ��������</title>
<? $str = $_SERVER['QUERY_STRING'];
parse_str($str);
 ?>

</head>

<body>
<a href="index.php">� ������� ����</a>

<form action="" method=get>

<select name="type" id="type">
<option value="by_regions" <?if($type=="by_regions"){echo " selected";}?>>�� ��������</option>
<option value="by_cities" <?if($type=="by_cities"){echo " selected";}?>>�� 174 ���������� �������</option>

</select>

�������� ������ ������� �
<select id=year_from name=year_from>
<option value="2010" <?if($year_from=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($year_from=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($year_from=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($year_from=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($year_from=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($year_from=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($year_from=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($year_from=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($year_from=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($year_from=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($year_from=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($year_from=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($year_from=="2022"){echo " selected";}?>>2022</option>




</select>


<select name="typ_ord" id="typ_ord">
<option value="0" <?if($typ_ord=="0"){echo " selected";}?>>��� ������</option>
<option value="1" <?if($typ_ord=="1"){echo " selected";}?>>��� �����</option>
<option value="2" <?if($typ_ord=="2"){echo " selected";}?>>�������</option>
<option value="3" <?if($typ_ord=="3"){echo " selected";}?>>������� � ����</option>
</select>

����������:
<select name="sort_by" id="sort_by">
<option value="num_ord" <?if($sort_by=="num_ord"){echo " selected";}?>>���������� �������</option>
<option value="viruchka" <?if($sort_by=="viruchka"){echo " selected";}?>>�������</option>
<option value="sredn_ord" <?if($sort_by=="sredn_ord"){echo " selected";}?>>������� �����</option>
</select>

<input type="hidden" name="act" value="do" /><input type=submit value=">>>">

 </form>


<?
 require_once("../acc/includes/db.inc.php");




 if($act == "do"){

 if($typ_ord == "0"){$typ_ord_vst = "";}else{$typ_ord_vst = " AND q.typ_ord = '$typ_ord' ";}


 if($type == "by_regions"){
$region_arr = array(
array("��������", "������������"),
array("�������������", "�����������"),
array("������������", "���������"),
array("������������", "��������"),
array("��������", "������"),
array("������������", "��������"),
array("�������������", "���������"),
array("�����������", "�������"),
array("�����������", "�������"),
array("����������", "�������"),
array("���������", "�������"),
array("���������������", "�����������"),
array("���������", "������"),
array("����������", "�������������-����������"),
array("�����������", "��������"),
array("���������", "�����"),
array("�����������", "��������"),
array("����������", "������"),
array("�������", "�����"),
array("�������������", "���������"),
array("��������", "������"),
array("�����������", "�������"),
array("����������", "������"),
array("����������", "��������"),
array("�������������", "������ ��������"),
array("������������", "��������"),
array("�������������", "�����������"),
array("������", "����"),
array("������������", "��������"),
array("���������", "���"),
array("����������", "�����"),
array("��������", "�����"),
array("���������", "�����"),
array("����������", "������-��-����"),
array("���������", "������"),
array("���������", "������"),
array("�����������", "�������"),
array("�����������", "����-���������"),
array("������������", "������������"),
array("����������", "��������"),
array("����������", "������"),
array("��������", "����"),
array("���������", "������"),
array("�����������", "���������"),
array("�����������", "���������"),
array("���������", "����"),
array("�����������", "���������"),
array("������", "������"),
array("�����", "�����-�������"),
array("��������", "���"),
array("�������", "����-���"),
array("��������", "���������"),
array("���������", "����������"),
array("���������", "�����"),
array("���������-��������", "�������"),
array("��������", "������"),
array("���������-���������", "��������"),
array("�������", "�������������"),
array("����", "���������"),
array("����", "�����������"),
array("����� ��", "������-���"),
array("��������", "�������"),
array("������", "������"),
array("�������� ������", "�����������"),
array("���������", "������"),
array("����", "�����"),
array("��������", "������"),
array("�������", "������"),
array("�����", "�������"),
array("�������", "���������"),
array("���������", "�������"),
array("�������������", "���������"),
array("������������", "����������"),
array("����������", "�����������"),
array("��������������", "����������"),
array("�����������", "���������"),
array("��������", "������-���"),
array("�����-����������", "�����-��������"),
array("���������", "�������"),
array("�����-��������", "��������")

);
}

if($type == "by_cities"){
$region_arr = array(
array("������", ""),
array("�����-���������", ""),
array("�����������", ""),
array("������������", ""),
array("������ ��������", ""),
array("������", ""),
array("���������", ""),
array("����", ""),
array("������", ""),
array("������-��-����", ""),
array("���", ""),
array("����������", ""),
array("�������", ""),
array("�����", ""),
array("���������", ""),
array("���������", ""),
array("�������", ""),
array("������", ""),
array("��������", ""),
array("������", ""),
array("�������", ""),
array("���������", ""),
array("�������", ""),
array("���������", ""),
array("���������", ""),
array("�����������", ""),
array("���������", ""),
array("�����", ""),
array("��������", ""),
array("��������", ""),
array("�����������", ""),
array("������", ""),
array("���������", ""),
array("���������� �����", ""),
array("�����", ""),
array("�����", ""),
array("������", ""),
array("���������", ""),
array("��������", ""),
array("�����������", ""),
array("����", ""),
array("�����", ""),
array("�����������", ""),
array("����", ""),
array("����������", ""),
array("����-���", ""),
array("�����", ""),
array("������������", ""),
array("������", ""),
array("�������", ""),
array("��������", ""),
array("������", ""),
array("��������", ""),
array("����", ""),
array("������ �����", ""),
array("�����������", ""),
array("�����������", ""),
array("������", ""),
array("��������", ""),
array("��������", ""),
array("������", ""),
array("�������", ""),
array("���������", ""),
array("������", ""),
array("�������", ""),
array("���", ""),
array("�������", ""),
array("�����������", ""),
array("��������", ""),
array("������", ""),
array("��������", ""),
array("������������", ""),
array("�������������", ""),
array("�����������", ""),
array("��������", ""),
array("������������", ""),
array("������-���", ""),
array("�����", ""),
array("��������", ""),
array("���������", ""),
array("�����������-��-�����", ""),
array("����������", ""),
array("�������", ""),
array("�����", ""),
array("���������", ""),
array("������", ""),
array("����", ""),
array("������������", ""),
array("�������", ""),
array("�������", ""),
array("������� ��������", ""),
array("������", ""),
array("������ �����", ""),
array("������", ""),
array("�����", ""),
array("�������", ""),
array("����-���������", ""),
array("�����", ""),
array("�����������", ""),
array("�������", ""),
array("��������", ""),
array("������", ""),
array("�������", ""),
array("������������", ""),
array("�������������-����������", ""),
array("��������", ""),
array("���������", ""),
array("����������", ""),
array("�����������", ""),
array("�������", ""),
array("������������", ""),
array("�������-���������", ""),
array("��������", ""),
array("������������", ""),
array("�����������", ""),
array("�����", ""),
array("�����", ""),
array("�������", ""),
array("�������", ""),
array("�������", ""),
array("���������", ""),
array("��������", ""),
array("������", ""),
array("��������", ""),
array("���������", ""),
array("�������", ""),
array("��������", ""),
array("������", ""),
array("����������", ""),
array("����������", ""),
array("����������", ""),
array("�����������", ""),
array("�������", ""),
array("��������������", ""),
array("�������", ""),
array("��������", ""),
array("ٸ�����", ""),
array("��������", ""),
array("��������", ""),
array("������������", ""),
array("������������", ""),
array("���������", ""),
array("�������", ""),
array("�����", ""),
array("�������", ""),
array("�������-�����", ""),
array("����� �������", ""),
array("������������", ""),
array("������������", ""),
array("�����������", ""),
array("������������", ""),
array("�������", ""),
array("���������", ""),
array("�����", ""),
array("���������", ""),
array("���������", ""),
array("������������", ""),
array("������", ""),
array("�������", ""),
array("����", ""),
array("�������", ""),
array("��������", ""),
array("������", ""),
array("�������", ""),
array("������", ""),
array("������", ""),
array("�������", ""),
array("����", ""),
array("������� �����", ""),
array("��������������", ""),
array("������������", "")
 );

}


foreach($region_arr as $rgn => $massiv)
{

$obl = $massiv[0];
$city = $massiv[1];

if($type == "by_cities"){
$q = "SELECT COUNT(*), SUM(q.prdm_sum_acc) FROM clients AS c, queries AS q WHERE (legal_address LIKE '% $obl %' OR postal_address LIKE '% $obl %' OR deliv_address LIKE '% $obl %') AND c.uid = q.client_id AND q.date_query > '$year_from-01-01 00:00:00' $typ_ord_vst";

}
else{
 $q = "SELECT COUNT(*), SUM(q.prdm_sum_acc) FROM clients AS c, queries AS q WHERE (legal_address LIKE '%$obl%' OR postal_address LIKE '%$obl%' OR deliv_address LIKE '%$obl%' OR legal_address LIKE '%$city%' OR postal_address LIKE '%$city%' OR deliv_address LIKE '%$city%') AND c.uid = q.client_id AND q.date_query > '$year_from-01-01 00:00:00' $typ_ord_vst";

}

$reg = mysql_query($q);


$r = mysql_fetch_array($reg);

$num_ord = $r[0];
$viruchka = round($r[1]);
if($viruchka > 0 and $num_ord > 0){
$sredn_ord = round($viruchka/$num_ord);}else{$sredn_ord = "-";}

$regions_stat[$obl]["city"] .= $city;
$regions_stat[$obl]["num_ord"] .= $num_ord;
$regions_stat[$obl]["viruchka"] .= $viruchka;
$regions_stat[$obl]["sredn_ord"] .= $sredn_ord;



}




function cmp_function_desc($a, $b){

  $sort_by = $_GET["sort_by"];
  if ($a[$sort_by] == $b[$sort_by]) {
    return 0;
  }

  return ($a[$sort_by] > $b[$sort_by]) ? -1 : 1;
}

uasort($regions_stat, 'cmp_function_desc');

echo "<table border=1 cellpadding=10><tr><td><b>#</b></td><td><b>�������/����/����������</b></td><td><b>�����</b></td><td><b>���-�� �������</b></td><td><b>�������</b></td><td><b>������� �����</b></td></tr>";
$i = 0;  
foreach($regions_stat as $obl => $val)
{

$i = $i + 1;
//echo $regions_stat[$obl][num_ord];
$city = $regions_stat[$obl][city];
$num_ord = $regions_stat[$obl][num_ord];
$viruchka = $regions_stat[$obl][viruchka];
$sredn_ord = $regions_stat[$obl][sredn_ord];
echo "<tr><td>$i</td><td align=center>$obl</td><td align=center>$city</td><td align=center>$num_ord</td><td align=center>$viruchka</td><td align=center>$sredn_ord</td></tr>";

}
echo "</table>";

   /* echo "<pre>";
    print_r($regions_stat);
    echo "<pre>"; */

  }
?>
 </body>

</html>
