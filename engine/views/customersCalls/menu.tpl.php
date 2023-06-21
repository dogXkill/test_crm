<?php if ($menuItems) { ?>
<div id="moduleSubmenu" class="clear">
	<?php
		foreach ($menuItems as $key => $menuItem) {
			$active = $menuItem['active'] ? ' active' : '';
			$i = $menuItem['ico'] ? '<i class="' . $menuItem['ico'] . '"></i>' : '';
			
			echo '<div class="moduleSubmenu_item' . $active . '"><a href="' . $menuItem['link'] . '">' . $i . $menuItem['title'] . '</a></div>';
		}
	?>
</div>
<?php } ?>