<?php
/**
*   Jet
*   A lightweight and fast framework for developer who don't need hundred of files
* 
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link    https://github.com/CapMousse/Jet
*
*/


/**
*   Index file
*   BECAUSE NO INDEX = DIVIDE BY ZERO
*
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link    https://github.com/CapMousse/Jet
*
*/

//Prevent direct access to index.php
if(strpos($_SERVER['REQUEST_URI'], 'index.php') !== false){
    $addr = str_replace('index.php', '', $_SERVER['REQUEST_URI']);
    header('location: '.$addr, false, 301);
    exit();
}

//require the constant file where are defined constant for the framework
require('constant.php');

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