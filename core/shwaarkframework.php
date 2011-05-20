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
debug::$start = microtime();

ob_start('ob_gzhandler');

// load framework user config file
require(BASEPATH.'config.inc.php');

// get the asked config environment
$config = $config[$environment];

//create the Static constant, for statics files
define('STATICS', $config['statics']);

session_start();

/***********************************************/
/**** Include class framework and init them ****/
/***********************************************/


// load the abstract controler class, used to be extend by user controller
require(BASEPATH.'controller.class.php');

// load the view controler class used by templates
require(BASEPATH.'view.class.php');

// don't necesary load orm class if no sql needed
if($config['sql']){
	require(BASEPATH.'idiorm.class.php');
	require(BASEPATH.'paris.class.php');
	ORM::configure('mysql:host='.$config['host'].';dbname='.$config['base']);
	ORM::configure('username', $config['log']);
	ORM::configure('password', $config['pass']);
}

if($config['cache']){
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

require(BASEPATH.'core.class.php');
Shwaark::$config = $config;
Shwaark::run($config);


if($config['debug']){
	debug::displayLog();
}


ob_end_flush();
