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
*   Init file
*   Sometime we need to lauch the core
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/
session_start();

//register a autoload for core file
require('autoload.php');

Log::$start = microtime();

// load framework user config file
require(PROJECT.'config'.EXT);
$config = new Config();

/***********************************************/
/**** Include class framework and init them ****/
/***********************************************/

// init the KORE KLASS

$jet = Jet::getInstance();

/**
 * Var loaded from the config.php file
 * @var string $environment
 */
$jet->setEnvironment($config->environment);

/**
 * Var loaded from the config.php file
 * @var array $config
 */
$jet->setConfig($config);
$jet->run();