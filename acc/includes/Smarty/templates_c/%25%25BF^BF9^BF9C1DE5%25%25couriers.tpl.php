<?php /* Smarty version 2.6.26, created on 2014-11-26 17:48:09
         compiled from couriers.tpl */ ?>
<?php if ($_GET['id']): ?><form action="<?php if ($this->_tpl_vars['content']): ?>?id=<?php echo $_GET['id']; ?>
<?php endif; ?>" method="post">
<table border="0" cellspacing="5" cellpadding="5">
<tr><td class="tab_first_col">ФИО исполнителя *</td><td class="tab_two_col"><input type="text" name="name"<?php if ($this->_tpl_vars['content']): ?> value="<?php echo $this->_tpl_vars['content']['name']; ?>
"<?php endif; ?> size=70/></td></tr>
<tr><td class="tab_first_col">Телефон</td><td class="tab_two_col"><input size=70 type="text" name="phone"<?php if ($this->_tpl_vars['content']): ?> value="<?php echo $this->_tpl_vars['content']['phone']; ?>
"<?php endif; ?>  /></td></tr>
<tr><td class="tab_first_col">Домашний адрес</td><td class="tab_two_col"><textarea name="address" class="frm_wdfull"><?php if ($this->_tpl_vars['content']): ?><?php echo $this->_tpl_vars['content']['address']; ?>
<?php endif; ?></textarea></td></tr>
<tr><td class="tab_first_col">Паспортные данные</td><td class="tab_two_col"><textarea name="passport1" class="frm_wdfull"><?php if ($this->_tpl_vars['content']): ?><?php echo $this->_tpl_vars['content']['passport1']; ?>
<?php endif; ?></textarea></td></tr>
<tr><td class="tab_first_col">Номер в/у</td><td class="tab_two_col"><input size=70 type="text" name="passport2"<?php if ($this->_tpl_vars['content']): ?> value="<?php echo $this->_tpl_vars['content']['passport2']; ?>
"<?php endif; ?>  /></td></tr>
<tr><td class="tab_first_col">Номер авто</td><td class="tab_two_col"><input size=70 type="text" name="auto_number"<?php if ($this->_tpl_vars['content']): ?> value="<?php echo $this->_tpl_vars['content']['auto_number']; ?>
"<?php endif; ?>  /></td></tr>
<tr><td class="tab_first_col">Базовый тариф</td><td class="tab_two_col"><input size=35 type="text" name="base_tarif" <?php if ($this->_tpl_vars['content']): ?> value="<?php echo $this->_tpl_vars['content']['base_tarif']; ?>
"<?php endif; ?>  /></td></tr>
<tr><td class="tab_first_col">Комментарий</td><td class="tab_two_col"><textarea size=70 name="comment" class="frm_wdfull"><?php if ($this->_tpl_vars['content']): ?><?php echo $this->_tpl_vars['content']['comment']; ?>
<?php endif; ?></textarea></td></tr>
<tr><td class="tab_first_col">Приоритетность</td><td class="tab_two_col"><input type=text size=2 style="width: 20px;" name="priority" class="frm_wdfull" value="<?php if ($this->_tpl_vars['content']): ?><?php echo $this->_tpl_vars['content']['priority']; ?>
<?php endif; ?>"></td></tr>

<tr align="center"><td colspan="2"><input type="submit" value="<?php if ($this->_tpl_vars['content']): ?>Изменить<?php else: ?>Добавить<?php endif; ?>" /></td></tr>
</table>
</form>
<?php endif; ?>
<?php if ($this->_tpl_vars['couriers']): ?>
<table>
<tr class="tab_query_tit">
<td class="tab_query_tit">Исполнитель</td>
<td class="tab_query_tit">Телефон</td>
<td class="tab_query_tit">Номер авто</td>
<td class="tab_query_tit">Базовый тариф</td>
<td class="tab_query_tit">Приоритет</td>
<td class="tab_query_tit" colspan="2">Операции</td>
</tr>
<?php $_from = $this->_tpl_vars['couriers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
<tr>
<td class="tab_td_norm"><?php echo $this->_tpl_vars['item']['name']; ?>
</td>
<td class="tab_td_norm"><?php echo $this->_tpl_vars['item']['phone']; ?>
</td>
<td class="tab_td_norm"><?php echo $this->_tpl_vars['item']['auto_number']; ?>
</td>
<td class="tab_td_norm"><?php echo $this->_tpl_vars['item']['base_tarif']; ?>
</td>
<td class="tab_td_norm"><?php echo $this->_tpl_vars['item']['priority']; ?>
</td>
<td class="tab_td_norm"><a href="?id=<?php echo $this->_tpl_vars['item']['id']; ?>
" onmouseover="Tip('Редактировать')"><img width="20" height="20" src="../i/edit2.gif" /></a></td>
<td class="tab_td_norm"><a href="?del=<?php echo $this->_tpl_vars['item']['id']; ?>
" onmouseover="Tip('Удалить')" onClick="return confirm('Вы уверены, что хотите удалить?');"><img widt="20" height="20" src="../i/del.gif" /></a></td>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
<?php endif; ?>