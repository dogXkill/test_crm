
<img src="../../../i/vedomost.png" width="22" height="22" alt="" align="absmiddle" onclick="report_div()">
<a href="#" class=sublink onclick=report_div()>¬едомость</a>

<div id=report_div style="background-color:white; z-index:100; position:absolute;display:none; border: 1px black; border: 1px solid black;">

<table align=center cellpadding=10>
<tr>
<td>
√од: <select name=year_report id=year_report>
<option value="2014">2014</option>
<option value="2015">2015</option>
<option value="2016">2016</option>
<option value="2017">2017</option>
<option value="2018">2018</option>
<option value="2019">2019</option>
<option value="2020">2020</option>
</select>

ћес€ц:
<select name=month_report id=month_report>
<option value="01">€нварь</option>
<option value="02">февраль</option>
<option value="03">март</option>
<option value="04">апрель</option>
<option value="05">май</option>
<option value="06">июнь</option>
<option value="07">июль</option>
<option value="08">август</option>
<option value="09">сент€брь</option>
<option value="10">окт€брь</option>
<option value="11">но€брь</option>
<option value="12">декабрь</option>
</select>
</td>
<td><input type=button value=OK onclick="get_report()"></td>
<td>
<img src="../../i/del.gif" width="20" height="20" alt="" style="cursor:pointer" onclick=report_div()>
</td>

</tr>
</table>
</div>
<script>
function set_y_m_report(){
var date = new Date(),
year = date.getFullYear(),
month = date.getMonth()+1;
if (month<10) {month='0'+month;}
$("select#year_report").val(year)
$("select#month_report").val(month)

}
function get_report(){
year = $('#year_report').val();
month = $('#month_report').val();
window.location.href = '/acc/applications/timetable/report.php?type=<?=$type;?>&year='+year+'&month='+month;
}
function report_div(){
$('#report_div').toggle(250)
set_y_m_report()
}
</script>