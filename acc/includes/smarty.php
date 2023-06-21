<?php
//define("path", "F:/home/192.168.1.100/www/acc/");
//define("path", 'C:/www/OSPanel/domains/mylocalhost/acc/');



define("path", "/home/crmu660633/test.upak.me/docs/acc/");

//-Smarty
//define("SMARTY_DIR", path . "includes/Smarty/libs/");
define("SMARTY_DIR", path . "includes/Smarty/libs/");


require_once SMARTY_DIR . "Smarty.class.php";

class Smarty_project extends Smarty
{
  function __construct()
  {
    $this->Smarty();

    $this->template_dir = path . "templates/Smarty/";
    $this->compile_dir  = path . "includes/Smarty/templates_c/";
    $this->config_dir   = path . "includes/Smarty/configs/";
    $this->cache_dir    = path . "includes/Smarty/cache/";

    $this->caching = false;
  }

  function Smarty_project()
  {
    $this->__construct();
  }
}
//Smarty-

?>