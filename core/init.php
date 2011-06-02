<?php	
/**
*	ShwaarkFramework
*	A lightwave and fast framework for developper who don't need hundred of files
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.1
*/

// load the debug class, used to log all events
require(BASEPATH.'debug.class.php');

// load framework user config file
require(BASEPATH.'config.inc.php');
$config = $config[$environment];

debug::$log_all = $config['log_all'];
debug::$start = microtime();
debug::log('Init framework');

ob_start('ob_gzhandler');

//create the Static constant, for statics files
define('STATICS', $config['statics']);

session_start();

/***********************************************/
/**** Include class framework and init them ****/
/***********************************************/


// load the abstract controler class, used to be extend by user controller
debug::log('Load controller');
require(BASEPATH.'controller.class.php');

// load the view controler class used by templates
debug::log('Load view');
require(BASEPATH.'view.class.php');

// don't necesary load orm class if no sql needed
if($config['sql']){
    debug::log('Load model');
    require(BASEPATH.'idiorm.class.php');
    require(BASEPATH.'paris.class.php');
    ORM::configure('mysql:host='.$config['host'].';dbname='.$config['base']);
    ORM::configure('username', $config['log']);
    ORM::configure('password', $config['pass']);
}

if($config['cache']){
    debug::log('Load cache');
    require(BASEPATH.'cache.class.php');
}

/*
     _      ______ _______ _____   _____   ____   _____ _  __
    | |    |  ____|__   __/ ____| |  __ \ / __ \ / ____| |/ /
    | |    | |__     | | | (___   | |__) | |  | | |    | ' / 
    | |    |  __|    | |  \___ \  |  _  /| |  | | |    |  <  
    | |____| |____   | |  ____) | | | \ \| |__| | |____| . \ 
    |______|______|  |_| |_____/  |_|  \_\\____/ \_____|_|\_\
 
 */

debug::log('Load core');
require(BASEPATH.'core.class.php');
Shwaark::$config = $config;
Shwaark::run();


if($config['show_debug_log']){
    debug::displayLog();
}


ob_end_flush();