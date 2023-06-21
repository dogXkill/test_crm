<table width="1100" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" class="title_razd" colspan=2>{$page_name}</td>
</tr>
<tr>
<td align="center">
{if $menu_type eq "task" or $menu_type eq "task_edit"}
<a href="/acc/logistic/courier_tasks.php?id=-1" class="sublink"><img src="/i/logistic.png" width="32" height="32" alt="" style="vertical-align:middle"></a> <a href="/acc/logistic/courier_tasks.php?id=-1" class="sublink">новое задание</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{/if}
<a href="/acc/logistic/courier_tasks.php" class="sublink">архив</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
{if $menu_type eq "courier"}<a href="/acc/logistic/couriers.php?id=-1" class="sublink">добавить исполнителя</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{/if}
{if $menu_type eq "courier" or $menu_type eq "task" or $menu_type eq "task_edit"} <a href="/acc/logistic/couriers.php" class="sublink">исполнители</a>{/if}
</td>
<td width=200>выводить по:
<select name="lim" id="lim" onchange="lim()">
<option value="20" {if $lim eq "20"}selected{/if}>20</option>
<option value="40" {if $lim eq "40"}selected{/if}>40</option>
<option value="60" {if $lim eq "60"}selected{/if}>60</option>
<option value="80" {if $lim eq "80"}selected{/if}>80</option>
<option value="100" {if $lim eq "100"}selected{/if}>100</option>
<option value="1000000" {if $lim eq "1000000"}selected{/if}>все</option>
</select>
{literal}
<script>
function lim(){
var lim = $('#lim').val();
window.location.href = "index.php?lim="+lim
}
</script>
{/literal}
</td>
</tr>
<tr>
  <td align="center" height="50" colspan=2>{include file="`$page`.tpl"}</td>
</tr>
</table>
