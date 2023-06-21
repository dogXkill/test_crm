{if $smarty.get.id}<form action="{if $content}?id={$smarty.get.id}{/if}" method="post">
<table border="0" cellspacing="5" cellpadding="5">
<tr><td class="tab_first_col">ФИО исполнителя *</td><td class="tab_two_col"><input type="text" name="name"{if $content} value="{$content.name}"{/if} size=70/></td></tr>
<tr><td class="tab_first_col">Телефон</td><td class="tab_two_col"><input size=70 type="text" name="phone"{if $content} value="{$content.phone}"{/if}  /></td></tr>
<tr><td class="tab_first_col">Домашний адрес</td><td class="tab_two_col"><textarea name="address" class="frm_wdfull">{if $content}{$content.address}{/if}</textarea></td></tr>
<tr><td class="tab_first_col">Паспортные данные</td><td class="tab_two_col"><textarea name="passport1" class="frm_wdfull">{if $content}{$content.passport1}{/if}</textarea></td></tr>
<tr><td class="tab_first_col">Номер в/у</td><td class="tab_two_col"><input size=70 type="text" name="passport2"{if $content} value="{$content.passport2}"{/if}  /></td></tr>
<tr><td class="tab_first_col">Номер авто</td><td class="tab_two_col"><input size=70 type="text" name="auto_number"{if $content} value="{$content.auto_number}"{/if}  /></td></tr>
<tr><td class="tab_first_col">Базовый тариф</td><td class="tab_two_col"><input size=35 type="text" name="base_tarif" {if $content} value="{$content.base_tarif}"{/if}  /></td></tr>
<tr><td class="tab_first_col">Комментарий</td><td class="tab_two_col"><textarea size=70 name="comment" class="frm_wdfull">{if $content}{$content.comment}{/if}</textarea></td></tr>
<tr><td class="tab_first_col">Приоритетность</td><td class="tab_two_col"><input type=text size=2 style="width: 20px;" name="priority" class="frm_wdfull" value="{if $content}{$content.priority}{/if}"></td></tr>

<tr align="center"><td colspan="2"><input type="submit" value="{if $content}Изменить{else}Добавить{/if}" /></td></tr>
</table>
</form>
{/if}
{if $couriers}
<table>
<tr class="tab_query_tit">
<td class="tab_query_tit">Исполнитель</td>
<td class="tab_query_tit">Телефон</td>
<td class="tab_query_tit">Номер авто</td>
<td class="tab_query_tit">Базовый тариф</td>
<td class="tab_query_tit">Приоритет</td>
<td class="tab_query_tit" colspan="2">Операции</td>
</tr>
{foreach from=$couriers item=item}
<tr>
<td class="tab_td_norm">{$item.name}</td>
<td class="tab_td_norm">{$item.phone}</td>
<td class="tab_td_norm">{$item.auto_number}</td>
<td class="tab_td_norm">{$item.base_tarif}</td>
<td class="tab_td_norm">{$item.priority}</td>
<td class="tab_td_norm"><a href="?id={$item.id}" onmouseover="Tip('Редактировать')"><img width="20" height="20" src="../i/edit2.gif" /></a></td>
<td class="tab_td_norm"><a href="?del={$item.id}" onmouseover="Tip('Удалить')" onClick="return confirm('Вы уверены, что хотите удалить?');"><img widt="20" height="20" src="../i/del.gif" /></a></td>
</tr>
{/foreach}
</table>
{/if}