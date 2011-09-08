<?php
/**
*   Jet
*   A lightweigth and fast framework for developper who don't need hundred of files
* 
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link    https://github.com/CapMousse/Jet
*   @version 1
*/


/**
*   Index file
*   BECAUSE NO INDEX = DIVIDE BY ZERO
*
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link    https://github.com/CapMousse/Jet
*   @version 1
*/

//Define the root path for the route rewrite module
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('WEB_DIR', str_replace('\\', '/', dirname(__FILE__)));
define('TOP', WEB_DIR.'/../');

//Define important dir
define('SYSPATH', TOP.'core/');
define('PROJECT', TOP.'project/');
define('CONFIG', PROJECT.'config/');
define('APPS', PROJECT.'apps/');
define('MODULES', PROJECT.'modules/');
define('VIEWS', PROJECT.'views/');

define('CONTROLLER', 0);
define('ACTION', 1);

//let's rock!
require(SYSPATH.'init.php');
?>