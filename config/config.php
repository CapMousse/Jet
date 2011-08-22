<?php
/**
*	ShwaarkFramework
*	A lightweigth and fast framework for developper who don't need hundred of files
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.1
*/

// set the current environement
$environment = 'dev'; 


// set the database option
$config['dev']  = array(
    'sql' 		=> true,
    'host'      => 'localhost',
    'log'       => 'root',
    'pass'      => 'root',
    'base'      => 'shwaarkframework',

    // activate caching
    'cache'     => false,
    'cache_dir' => 'cache/',

    // where is your web dir
    'web_dir'   => 'http://your.static.domain/dir/',

    // active the debug mode
    'show_debug_log'    => false,
    'log_all'           => false
);
