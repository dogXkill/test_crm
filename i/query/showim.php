<?php
header('Content-Type: image/jpeg');
define('IMG_PATCH', '../i/users/');
if((isset($_GET['n'])) && $_GET['n']=1)
	readfile('../i/icons/no_im.gif');
elseif(isset($_GET['s']) && is_numeric($_GET['s']))
	readfile(IMG_PATCH.'small_'.$_GET['s'].'.jpeg');
elseif(isset($_GET['b']) && is_numeric($_GET['b']))
	readfile(IMG_PATCH.'big_'.$_GET['b'].'.jpeg');
?>