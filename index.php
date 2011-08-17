<?php
/**
*   ShwaarkFramework
*   A lightweigth and fast framework for developper who don't need hundred of files
* 
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link    https://github.com/CapMousse/ShwaarkFramework
*   @version 0.3
*/


/**
*   Index file
*   BECAUSE NO INDEX = DIVIDE BY ZERO
*
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link    https://github.com/CapMousse/ShwaarkFramework
*   @version 1.5
*/

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