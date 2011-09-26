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

//Define important constant
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('SYSPATH', __DIR__.'/../core/');
define('PROJECT', __DIR__.'/../project/');
define('CONTROLLER', 0);
define('ACTION', 1);

//let's rock!
try{
/*
     _      ______ _______ _____   _____   ____   _____ _  __
    | |    |  ____|__   __/ ____| |  __ \ / __ \ / ____| |/ /
    | |    | |__     | | | (___   | |__) | |  | | |    | ' / 
    | |    |  __|    | |  \___ \  |  _  /| |  | | |    |  <  
    | |____| |____   | |  ____) | | | \ \| |__| | |____| . \ 
    |______|______|  |_| |_____/  |_|  \_\\____/ \_____|_|\_\
 
 */
    require(SYSPATH.'init.php');
}catch(Exception $e){
    echo $e;
}
?>