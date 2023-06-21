
<img src="../../i/clock.png" width="24" height="24" alt="" align="absmiddle" onclick="timetable_div()">
<a href="#" class=sublink onclick=timetable_div()>�������� ������������</a>

<div id=timetable_div style="background-color:white; z-index:100; position:absolute;display:none; border: 1px black; border: 1px solid black;">

<table align=center cellpadding=10>
<tr>
<td>
���: <select name=year_timetable id=year_timetable>
<option value="2014">2014</option>
<option value="2015">2015</option>
<option value="2016">2016</option>
<option value="2017">2017</option>
<option value="2018">2018</option>
<option value="2019">2019</option>
<option value="2020">2020</option>
</select>

�����:
<select name=month_timetable id=month_timetable>
<option value="01">������</option>
<option value="02">�������</option>
<option value="03">����</option>
<option value="04">������</option>
<option value="05">���</option>
<option value="06">����</option>
<option value="07">����</option>
<option value="08">������</option>
<option value="09">��������</option>
<option value="10">�������</option>
<option value="11">������</option>
<option value="12">�������</option>
</select>
</td>
<td><input type=button value=OK onclick="get_timetable()"></td>
<td>
<img src="../../i/del.gif" width="20" height="20" alt="" style="cursor:pointer" onclick=timetable_div()>
</td>

</tr>
</table>
</div>
<script>
function set_y_m_timetable(){
var date = new Date(),
year = date.getFullYear(),
month = date.getMonth()+1;
if (month<10) {month='0'+month;}
$("select#year_timetable").val(year)
$("select#month_timetable").val(month)

}
function get_timetable(){
year = $('#year_timetable').val();
month = $('#month_timetable').val();
window.location.href = '/acc/applications/timetable/?year='+year+'&month='+month+'&type=proizvodstvo';
}
function timetable_div(){
$('#timetable_div').toggle(250)
set_y_m_timetable()
}
</script>