136
a:4:{s:8:"template";a:1:{s:14:"task_list1.tpl";b:1;}s:9:"timestamp";i:1678744598;s:7:"expires";i:1678748198;s:13:"cache_serials";a:0:{}}<html>
<head>

<style>
body, p, td, input, select, textarea
{
  font: 16px Verdana, Geneva, Arial, Helvetica, sans-serif;
}

   .round {
    border-radius: 20px;
    box-shadow: 0 0 0 3px red, 0 0 13px #333;

	  background-color: white;
   }
   .href {
     font-size: 15px;
	 font-weight: bold;
	 color: black;
	 text-decoration: none;
   }

</style>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=cc64a72b-8539-43fc-85b0-cdf3d05fc153" type="text/javascript"></script>
<script>
function printer(){
task_date = $("#task_date").val();
mail_text = $("#tasks").val();
mail_text = mail_text.replace(/[&\/\\#,+()$~%'"*?{}]/g,'');
if(mail_text !== ""){
$.ajax({
          type: 'POST',
          url: 'add_tasks_mail_temp.php',
          data: '&task_date='+task_date+'&mail_text='+mail_text,
          success: function(data) {
            //alert(data);
          },
          error:  function(xhr, str){
	    alert('�������� ������: ' + xhr.responseCode);
          }
        });}
window.print();

}



function change_shipped_status(query_id){

if($("#"+query_id+"_shipped").is(':checked')){shipped = '1';}else{shipped = '0';}

                    if($.isNumeric(query_id) && $.isNumeric(shipped)) {

                            $.ajax({
                                type: 'GET',
                                url: '../backend/change_shipped_status.php?shipped='+shipped+'&query_id='+query_id,
                                dataType: 'html',
                                async: false,
                                success: function(data){idata = data;}
                            });

                    }
}


</script>

<title>������� ���� �� 13-03-2023 ��� �������� ���� ����������� </title>
</head>
<body>

<h3>������� ���� 13-03-2023 ��������: �������� ���� ����������� <img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()"> ������������ 14.03.23 00:56 </h3>

<div style="font-size:9px;border: 1px solid; color: black; padding: 5px; width:782px;">

1) ���� ����� � �������� ������� �������������� <u>������ �� ���������</u>. ���������� �������� � �������� ��������� ����� - ��������������� ��������.
�������� ����������� ����� �� ���������, ��������� ������� ������������ �� ���� ����.<br>
2) ��� ����������� �������� � ������ ����� ����� �� - ��������� �������� � ������� ������� ������������ ������ ������������ ����������� ������ ���������<br>
3) �������� ������ �� 1 ��� �� �������� ��������� ������� � ������������ � �������. ��� ������������� ��������� ���� � ������� ����, �������� ������ �������������� ����������� �� ������� � ������������ �� ������ ����, � ��� ����� ��������� ��������� - ����������<br>
4) ��� �������������, �������� ��������� ���� "�� ������" ����������<br>
5) ���� �������������� �������� ����� ����� ��� ���� ��������� � ����� ��, �� ������ ����������� ���� ������������ � ���� �� ���� ������������ �������� �������������, �� ������� ������<br>
6) �������� ������ ����� ��� ��������� � �������, � ��������������� � ������������ � ������� ������. ����� ��������� � ������� ����� �������� ������ �������� �������<br>
7) ��� ���������� � ������� ������������ ���� ������ ��� ������������, ��������, <b>� �������� ����������</b>, ����� �������� ���� �� �������������� ����������� ������ ���������, ��� ��������� ����������� ����������, ��� ���� �������� ���������� ������� � ����<br>
<b>8) ��� �������� ����� ����� ��, ����������� ����� ������ ���� ������� ����������� ������������, ��������� � ����������� ����������, ����, ���� �� ������� � �������<br>
9) ����������� ����� ����� � �� � ����������� ���� � ������ ���������. ���������� ���� ����������� ��, ������ ��������������� ���������� ��������</b>
</div>
<br>
<table width=800 border=0><tr><td>
<!--<div style="position:relative;"><img src="../i/moscow_map.png" border="0" />
</div>-->
<!--new_maps-->

<script>
function load_map(){
let params = (new URL(document.location)).searchParams; 
  $.ajax({
            url: "/acc/logistic/check_ymaps.php",
            type: "GET",
            data: {courier_id:params.get("courier_id"),date:params.get("date")},
            cache: false,
            success: function(html){
				$("#maps1").html(html);
			}
			});
			}
load_map();
</script>

<div id='list_maps'></div>
<div id="maps1"></div>

</td></tr></table>
����� �����: <strong>1</strong><br>
����� ��������: 45�<br>
��������������� �������������� ��������: 1500�<br>
�����: <strong>45�</strong>

<div style="page-break-after:always"></div>




������������ 14.03.23 00:56

<div style="page-break-after:always"></div>

<table width=800>




<tr>

<td valign="top" style="border: 2px  solid  black;">
<a name="1"></a>
<strong>#</strong> <b>1</b><br/>
<strong>����������:</strong> <b><a href="http://crm.upak.me/acc/logistic/courier_tasks.php?id=34135">����</a></b>





����� ������: <b><a href="https://crm.upak.me/acc/query/query_send.php?show=49086" target="_blank">49086</a></b>


<input type="checkbox" id="49086_shipped"   onchange="change_shipped_status('49086')"  />

<br/>
<br>
����� ������: <span style="font-weight: bold; color: #00D600">
�������� c����: <b>45 �.</b></span>
 <br>
<br>

<br>����� � ���������: <b>45 �.</b><br>�������������� ��������: <b>0 �. (��������������)</b>
<br>
<br>


<strong>�����:</strong> 3<br/>
<strong>���������� ����:</strong> �����<br/>
<strong>���������� ��������:</strong> +75555555564<br/>

&nbsp;


<table style="border: #C0C0C0 solid 1px;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5" width="500">
<thead><tr><th>���.</th><th>��������</th><th>���-��</th><th>�����</th></tr></thead><tbody>
	<tr>
				<td>147</td>
				<td>147 ��������� ��� ������� / ���� 17x5x10, �����</td>
				<td>1</td>
				<td>130/�5</td>
			</tr>
		

		

		

		

		

			</tbody></table>
<br/><br/>
<strong>���������:</strong> ������� ����� ���������� (+79032479926)<br/><br/>
���� �������: ___________________ / _______________________  <br/><br/><br/>
</td>
</tr>

</table>

<input type="hidden" id=task_date value="������� ���� �� 13-03-2023 �� �������� ���� ����������� ">
<textarea id=tasks style="width:1px;height:1px;">

������� ���� �� 13-03-2023<br/>
�����������: �������� ���� �����������<br>
������������ 14.03.23 00:56

<br><br>����� �����: 1<br>
����� ��������: 45�<br>
��������������� �������������� ��������: 1500�<br>
�����: 45�
<br><br>
����: ����<br/>
<br>
����� ������:
�������� c����: 45 �. <br>
<br>
<br>����� � ���������: 45 �.<br>�������������� ��������: 0 �. (��������������)
<br>
<br>

�����: 3<br/>
���������� ����: �����<br/>
���������� ��������: +75555555564<br/>

���������: ������� ����� ���������� (+79032479926)<br/><br/><br/><br/><br/>


</textarea>
<img src="/i/printer.png" style="cursor: pointer;" alt="" onclick="printer()">


</body>

<style>
#map{width: 794px; height: 784px; padding: 0; margin: 0;}
</style>
<script>

</script>



</html>