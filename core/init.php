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
*   Init file
*   Sometime we need to lauch the core
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/ShwaarkFramework
*   @version 1.3
*/

// load the debug class, used to log all events
require(SYSPATH.'debug.class.php');

// load framework user config file
require('config/config.php');

debug::$start = microtime();
debug::log('Init framework');

ob_start('ob_gzhandler');


session_start();

/***********************************************/
/**** Include class framework and init them ****/
/***********************************************/

// load the KORE KLASS
debug::log('Load core');
require(SYSPATH.'core.class.php');
Shwaark::$environment = $environment;
Shwaark::$config = Shwaark::mergeEnvironment($config);

debug::$log_all = isset(Shwaark::$config['log_all']) ? Shwaark::$config['log_all'] : false;
define('STATICS', Shwaark::$config['statics']);
//create the Static constant, for statics files

// Get current environment config

// load the abstract controler class, used to be extend by user controller
debug::log('Load controller');
require(SYSPATH.'controller.class.php');

// load the view controler class used by templates
debug::log('Load view');
require(SYSPATH.'view.class.php');

// don't necesary load orm class if no sql needed
if(Shwaark::$config['sql']){
    debug::log('Load model');
    require(SYSPATH.'idiorm.class.php');
    require(SYSPATH.'paris.class.php');
    ORM::configure('mysql:host='.Shwaark::$config['host'].';dbname='.Shwaark::$config['base']);
    ORM::configure('username', Shwaark::$config['log']);
    ORM::configure('password', Shwaark::$config['pass']);
}

if(Shwaark::$config['cache']){
    debug::log('Load cache');
    require(SYSPATH.'cache.class.php');
}

/*
     _      ______ _______ _____   _____   ____   _____ _  __
    | |    |  ____|__   __/ ____| |  __ \ / __ \ / ____| |/ /
    | |    | |__     | | | (___   | |__) | |  | | |    | ' / 
    | |    |  __|    | |  \___ \  |  _  /| |  | | |    |  <  
    | |____| |____   | |  ____) | | | \ \| |__| | |____| . \ 
    |______|______|  |_| |_____/  |_|  \_\\____/ \_____|_|\_\
 
 */
Shwaark::run();


if(Shwaark::$config['show_debug_log']){
    debug::displayLog();
}


ob_end_flush();