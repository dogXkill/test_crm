<?
require_once("../../includes/db.inc.php");



//������� ������ ����������� � ������ ������ �� ������ �������, ��� � $key_field �������� �������� �����, �������� uid ��� id. $key_field ��� ������ ��������� � ������� ����
function get_all_types($key_clm, $tbl_clmns,$tbl_name,$where){
//���� ������ ������� �� ���� �����, �� ����������� ��������� ����� ���� ������� ������� �� ������
if($tbl_clmns == "*"){$q = "SELECT * FROM $tbl_name $where";}else{$q = "SELECT $key_clm, $tbl_clmns FROM $tbl_name $where"; $tbl_clmns = explode(",", $tbl_clmns);}
$get = mysql_query($q);
while($g =  mysql_fetch_assoc($get)){
//���� �������
$id = $g[$key_clm];
//���� ��� �������, �� ����� ����������, ���� �� ���, �� ����� ������ �������� �������
if($tbl_clmns == "*"){$arr[$id] = $g;}else{foreach($tbl_clmns as $clm){if($clm !== "")$arr[$id][$clm] = $g[$clm];}}
}
return $arr;
}


/*
echo "<pre>";
print_r($job_names_arr);
echo "</pre>";
*/
//���� ���������� �� ������, �� ��������� ��������������� ������
function form_sql_vst(){
if(!$items_on_page){$items_on_page = "300";}
//������ �����, ������ ��� ��������� ���� ������� �� �������� ����������
$str = $_SERVER['QUERY_STRING'];
parse_str($str);
//echo $str;
//������ �������� ��������, ������� �� ����� �������� � ������ � ������� job
$flds_app = "izd_v,izd_w,izd_b,izd_material,izd_color,art_id,izd_type";
$flds_app_arr = explode(",", $flds_app);

//������ �������� ��������, ������� �� ����� �������� � ������ � ������� applications
$flds_job = "num_sotr,num_ord,job";
$flds_job_arr = explode(",", $flds_job);

foreach($flds_app_arr as $val) {
//�������� ��������
$fld_val = ${$val};
//��������� ������ ������� � �������
if($fld_val !== "" && ${$val}){$vst .= " AND a.$val = '$fld_val'";}
}

foreach($flds_job_arr as $val) {
//�������� ��������
$fld_val = ${$val};
//��������� ������ ������� � �������
if($fld_val !== "" && ${$val}){$vst .= " AND j.$val = '$fld_val'";}
}

//�������� ��������� ����
if(is_numeric($month_num) && is_numeric($year_num)){
 $vst .= " AND j.cur_time LIKE '$year_num-$month_num%' ";
 $items_on_page = "10000";
}

if(!is_numeric($month_num) && is_numeric($year_num)){
 $vst .= " AND j.cur_time LIKE '$year_num-%' ";
 $items_on_page = "10000";
}

if(is_numeric($month_num) && !is_numeric($year_num)){
$cur_year = date("Y");
 $vst .= " AND j.cur_time LIKE '$cur_year-$month_num%' ";
 $items_on_page = "10000";
}

//����� ����� ���������� ��� ����� ����������
if ($nadomn == "1"){$vst .= " AND j.nadomn='1'";}
$q = "SELECT j.*, a.uid AS app_uid, a.* FROM job AS j, applications AS a WHERE 1 $vst AND a.num_ord=j.num_ord ORDER BY j.cur_time DESC LIMIT $items_on_page";
return $q;
}


//������ ������������� ������� ����� ���������� ���� �������, ���� ��������� (���-�� ����� ��� ��������� �����) �� �������� ����� GET ��������
function form_table($act,$type_act){
$q = form_sql_vst();
$jobs = mysql_query($q);

//print_r(mysql_fetch_assoc($jobs));

$num_of_work_total = "0";
$total_cost = "0";

$job_names_arr = get_all_types("id","name,price","job_names", "WHERE 1");


if($act == "table"){
$colours_arr = get_all_types("cid","colour,html_colour","colours", "WHERE 1");
$users_arr = get_all_types("job_id","surname","users", "WHERE 1");
$types_arr = get_all_types("tid","type","types", "WHERE 1");

$izd_materials_arr = get_all_types("tid","type","materials", "WHERE 1");
$izd_color_arr = get_all_types("cid","colour","colours", "WHERE 1");
//echo $q." --- <br>".mysql_error();  
?>
<table border=1 cellpadding=3 cellspacing=0 class="apps_tbl" style="width:1300px;"><tr>
<th align="center"># ����</th>
<th>���</th>
<th>����� ������</th>
<th>��� ������</th>
<th width="50">��� �������</th>
<th width="50">������</th>
<th align="center">��������</th>
<th align="center">����</th>
<th>�������� �����</th>
<th>����������</th>
<th>����</th>
<th align="center">���������</th>
<th align="center">������</th>
<th align="center">�����</th>
<th></th></tr>
<?
while($j = mysql_fetch_assoc($jobs)){
$j_uid = $j[j_uid];
$app_uid = $j[app_uid];
$num_sotr = $j[num_sotr];
$izd_type = $j[izd_type];
$app_type = $j[app_type];
$title = substr($j[title],0,55)."...";
$num_ord = $j[num_ord];
$ClientName = $j[ClientName];
$job = $j[job];
$num_of_work = $j[num_of_work];
$job_rate = $j["rate_".$job];
$order_price = $j[order_price];
//����� ����� �� ������ ������ ���� ��� �� ���� ������ �� ������ (�.�. 14 �����)
if($order_price>0 and $job !== "14"){
$job_rate = $order_price;}

if($order_price>0){$order_mark = "<span style=\"font-weight:bold;font-size:16px;color:red;\">!</span> ";}
if($job_rate == ""){$job_rate = $job_names_arr[$job][price];}
$size = $j[izd_w]."x".$j[izd_v]."x".$j[izd_b];

$izd_color = $j[izd_color];
if($izd_color == "" || $izd_color == "0" || !is_numeric($izd_color)){$izd_color_name = "<span class=red>�� �����</span>";}else{$izd_color_name = "<span class=bold>".$izd_color_arr[$izd_color][colour]."</span>";}
$izd_material = $j[izd_material];
if($izd_material == "" || $izd_material == "0" || !is_numeric($izd_material)){$izd_material_name = "<span class=red>�� �����</span>";}else{$izd_material_name = "<span>".$izd_materials_arr[$izd_material][type]."</span>";}
$cost = round($num_of_work*$job_rate, 2);
$nadomn = $j[nadomn];
$cur_time = new DateTime($j[cur_time]);
$cur_time = $cur_time->Format('d-m-Y G:i');
$num_of_work_total = $num_of_work_total+$num_of_work;
$total_cost = $total_cost+$cost;
?>
<tr id="td_<?=$j_uid;?>">
  <td align="center" id=num_sotr_<?=$j_uid;?>><?=$num_sotr;?></td>
  <td><?=$users_arr[$num_sotr][surname];?></td>
  <td align="center" id=num_ord_<?=$j_uid;?>><a href="/acc/applications/edit.php?uid=<?=$app_uid;?>" target="_blank"><span id="num_ord_<?=$j_uid;?>_span"><?=$num_ord;?></span></a></td>
  <td align="center"><?if($app_type == "2"){?>��������<?} if($app_type == "1"){?><a href="/acc/applications/edit.php?uid=<?=$app_uid;?>" target="_blank"><?=$ClientName;?></a><?}?></td>
  <td width="50"><?=$types_arr[$izd_type][type];?></td>
  <td><?=$size;?></td>
  <td><?=$izd_material_name;?></td>
  <td><?=$izd_color_name;?></td>
  <td id=job_<?=$j_uid;?>><?=$job_names_arr[$job][name];?> (<span id="job_<?=$j_uid;?>_span"><?=$job;?></span>)</td>
  <td align="center" id=num_of_work_<?=$j_uid;?>><?=$num_of_work?></td>
  <td align="center"><?=$order_mark?><?=$job_rate?></td>
  <td align="center"><?=$cost?></td>
  <td align="center"><?if($nadomn == "1"){echo "<img src=\"../../../i/house.png\">";}?></td>
  <td id=cur_time_<?=$j_uid;?>><?=$cur_time;?></td>
  <td align="center" id="edit_but_<?=$j_uid;?>"><img src="../../../i/edit_bg.png" onclick="edit('<?=$j_uid;?>')" style="cursor:pointer"> <img src="../../i/del.gif" style="cursor:pointer" onclick="del('<?=$j_uid;?>')"></td>
</tr>
<?
$nadomn = NULL; $job_rate = NULL; $cost = NULL; $order_mark = NULL; $order_price = NULL; $num_of_work = NULL;
}?>

<tr style="border:1px;font-weight:bold;">
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td align="center"><?=$num_of_work_total?></td>
  <td></td>
  <td align="center"><?=round($total_cost)?></td>
  <td align="center"></td>
  <td></td>
  <td></td>
</tr>

</table>
<?
}
 //echo $type_act;
if($act == "sum"){

while($j = mysql_fetch_assoc($jobs)){
$num_of_work = $j[num_of_work];
$job = $j[job];
$job_rate = $j["rate_".$job];
$order_price = $j[order_price];
//���� � ������� ������� �������� ���� �� ���� �����, �� ����� ������������� ���������� ��������
if($order_price>0 and $job !== "14"){$job_rate = $order_price;}
//���� ����� �� �����, �� ���������� �������, �������� �� ��������� � ������� job_names
if($job_rate == ""){$job_rate = $job_names_arr[$job][price];}
//if($job == "14" && $job_rate == ""){$job_rate = "0.5";}
$cost = round($num_of_work*$job_rate,2);
$num_of_work_total = $num_of_work_total+$num_of_work;
$total_cost = $total_cost+$cost;
}
$nadomn = NULL; $job_rate = NULL; $cost = NULL; $order_price = NULL;
if($type_act == "qty"){echo $num_of_work_total;}
if($type_act == "cost"){echo round($total_cost);}
}}

$act = $_GET["act"];
$type_act = $_GET["type_act"];
echo form_table($act,$type_act);
?>


