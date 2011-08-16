<?php
//Define the root path for the route rewrite module
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

//Define the root path for the core files
define('SYSPATH', str_replace("\\", "/", 'core/'));

//Define the root path for the apps
define('APPS', str_replace("\\", "/", 'apps/'));

//Define the root path for the apps
define('MODULES', str_replace("\\", "/", 'modules/'));

//Define the root path for root views
define('VIEWS', str_replace("\\", "/", 'views/'));

//Define the root path for internal links
define('ROOT', 'http://'.$_SERVER['HTTP_HOST']);

define('CONTROLLER', 0);
define('ACTION', 1);

//let's rock!
require(SYSPATH.'init.php');
?>