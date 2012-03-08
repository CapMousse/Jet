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
spl_autoload_register(function($class){
    static $loaded = array();
    
    if(isset($loaded[$class])){
        return $loaded[$class];
    }

    if(!is_file(SYSPATH.lcfirst($class).EXT)){
        $file = strtolower(preg_replace('/(?!^)[[:upper:]]/', '/\0', $class));
    }else{
        $file = lcfirst($class);
    }


    if(!is_file(SYSPATH.$file.EXT)){
        $loaded[$class] = false;
    }
    else{
        require SYSPATH.$file.EXT;
        $loaded[$class] = true;
    }

    Log::save('Autoload '.$class);
    return $loaded[$class];
});

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