<?php

$dir = '/home/crmu660633/test.upak.me/docs/engine';


define('ENGINEPATH', $dir);
define('ABSPATH', dirname(ENGINEPATH));
define('COREPATH', $dir . '/core');
define('CONTROLLERSPATH', ENGINEPATH . '/controllers');
define('CLASSESPATH', ENGINEPATH . '/classes');
define('CONFIGSPATH', ENGINEPATH . '/configs');
define('HELPERSPATH', ENGINEPATH . '/helpers');
define('STRINGSPATH', ENGINEPATH . '/strings');
define('TEMPLATEPATH', ENGINEPATH . '/views');
define('ASSETSPATH', ABSPATH . '/assets');

define('SITENAME', 'Printfolio intranet v.3');
define('SITE_URL', 'https://crm.upak.me');

define('MYSQLI_HOST', 'localhost');
define('MYSQLI_USER', '');//user
define('MYSQLI_PASS', '');//pass
define('MYSQLI_DB', '');

define('CURRENT_TIME', time());
