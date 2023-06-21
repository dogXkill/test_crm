<?php /* Smarty version 2.6.26, created on 2022-12-14 17:40:41
         compiled from main.tpl */ ?>
<table width="1100" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" class="title_razd" colspan=2><?php echo $this->_tpl_vars['page_name']; ?>
</td>
</tr>
<tr>
<td align="center">
<?php if ($this->_tpl_vars['menu_type'] == 'task' || $this->_tpl_vars['menu_type'] == 'task_edit'): ?>
<a href="/acc/logistic/courier_tasks.php?id=-1" class="sublink"><img src="/i/logistic.png" width="32" height="32" alt="" style="vertical-align:middle"></a> <a href="/acc/logistic/courier_tasks.php?id=-1" class="sublink">новое задание</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php endif; ?>
<a href="/acc/logistic/courier_tasks.php" class="sublink">архив</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if ($this->_tpl_vars['menu_type'] == 'courier'): ?><a href="/acc/logistic/couriers.php?id=-1" class="sublink">добавить исполнителя</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php endif; ?>
<?php if ($this->_tpl_vars['menu_type'] == 'courier' || $this->_tpl_vars['menu_type'] == 'task' || $this->_tpl_vars['menu_type'] == 'task_edit'): ?> <a href="/acc/logistic/couriers.php" class="sublink">исполнители</a><?php endif; ?>
</td>
<td width=200>выводить по:
<select name="lim" id="lim" onchange="lim()">
<option value="20" <?php if ($this->_tpl_vars['lim'] == '20'): ?>selected<?php endif; ?>>20</option>
<option value="40" <?php if ($this->_tpl_vars['lim'] == '40'): ?>selected<?php endif; ?>>40</option>
<option value="60" <?php if ($this->_tpl_vars['lim'] == '60'): ?>selected<?php endif; ?>>60</option>
<option value="80" <?php if ($this->_tpl_vars['lim'] == '80'): ?>selected<?php endif; ?>>80</option>
<option value="100" <?php if ($this->_tpl_vars['lim'] == '100'): ?>selected<?php endif; ?>>100</option>
<option value="1000000" <?php if ($this->_tpl_vars['lim'] == '1000000'): ?>selected<?php endif; ?>>все</option>
</select>
<?php echo '
<script>
function lim(){
var lim = $(\'#lim\').val();
window.location.href = "index.php?lim="+lim
}
</script>
'; ?>

</td>
</tr>
<tr>
  <td align="center" height="50" colspan=2><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['page']).".tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
</table>