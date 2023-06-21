<?php

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");


 if($auth) {
                if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['archive'] == "del_archive" ) {
                    $query = "UPDATE users SET archive='1' WHERE uid=".$_GET['del'];
                    mysql_query($query);
                }
                if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['archive'] == "restore" ) {
                    $query = "UPDATE users SET archive='0' WHERE uid=".$_GET['del'];
                    mysql_query($query);
                }
                    if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['archive'] == "del_final" ) {
                    $query = "DELETE FROM users WHERE uid=".$_GET['del'];
                    mysql_query($query);
                    @unlink($_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'small_'.$_GET['del'].'.jpeg');
                    @unlink($_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'big_'.$_GET['del'].'.jpeg');
                }



                ?>