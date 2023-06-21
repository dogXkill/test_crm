
<img src="../../i/clock.png" width="24" style="cursor:pointer" height="24" alt="" align="absmiddle" onclick="timetable_div()">
<a href="#" class=sublink onclick=timetable_div()>“абель</a>

<div id=timetable_div style="background-color:white; z-index:100; position:absolute;display:none; border: 1px black; border: 1px solid black;">

<table align=center cellpadding=10>
<tr>
<td>
√од: <select name=year_timetable id=year_timetable class=timetable_year>
<option value="2014">2014</option>
<option value="2015">2015</option>
<option value="2016">2016</option>
<option value="2017">2017</option>
<option value="2018">2018</option>
<option value="2019">2019</option>
<option value="2020">2020</option>
<option value="2021">2021</option>
<option value="2022">2022</option>
<option value="2023">2023</option>
<option value="2024">2024</option>
<option value="2025">2025</option>
</select>

ћес€ц:
<select name=month_timetable id=month_timetable class=timetable_month>
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
<td><input type=button value=OK onclick="get_timetable()"></td>
<td>
<img src="../../i/del.gif" width="20" height="20" alt="" style="cursor:pointer" onclick=timetable_div()>
</td>

</tr>
</table>
</div>
<script>
function timetable_div()

{

    tyear = '<?=$tyear;?>';
    tmonth = '<?=$tmonth;?>';
    $('.timetable_year').find('option[value='+tyear+']')?.attr('selected', true );
    $('.timetable_month').find('option[value='+tmonth+']')?.attr('selected', true );


    $('#timetable_div').toggle(250)

}
function get_timetable(){
year = $('#year_timetable').val();
month = $('#month_timetable').val();
window.open('/acc/applications/timetable/?year='+year+'&month='+month+'&type=<?=$type;?>', '_blank');

}

</script>
