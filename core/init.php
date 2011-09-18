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
*   Init file
*   Sometime we need to lauch the core
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/
session_start();

//register a autoload for core file
spl_autoload_register(function($class){
    static $loaded = array();
    
    if(isset($loaded[$class])){
        return $loaded[$class];
    }
    
    $file = strtolower(preg_replace('/(?!^)[[:upper:]]/', '/\0', $class));
    
    
    if(!is_file(SYSPATH.$file.'.php')){
        $loaded[$class] = false;
    }
    else{
        require SYSPATH.$file.'.php';
        $loaded[$class] = true;
    } 
    
    Debug::log('Autoload '.$class);
    return $loaded[$class];
});


Debug::log('Init framework');

// load framework user config file
require(PROJECT.'config/global.php');

/***********************************************/
/**** Include class framework and init them ****/
/***********************************************/

// init the KORE KLASS
Jet::$environment = $environment;
Jet::$config = Jet::mergeEnvironment($config);

// init orm
require(PROJECT.'config/orm.php');
OrmConnector::$config = Jet::mergeEnvironment($orm);


Debug::$log_all = isset(Jet::$config['log_all']) ? Jet::$config['log_all'] : false;

Jet::run();