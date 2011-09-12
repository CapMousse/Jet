<?php
/**
*	Jet
*	A lightweigth and fast framework for developper who don't need hundred of files
* 	
*	@package Jet
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/Jet
*	@version 1.1
*/

// set the current environement
$environment = 'dev'; 


// set the database option
$config['dev']  = array(
    
    //ORM config (if no db used, don't create/call model)
    'type'      => 'mysql',
    'host'      => 'localhost',
    'log'       => 'root',
    'pass'      => 'root',
    'base'      => 'Jet',

    // activate caching
    'cache'     => false,
    'cache_dir' => 'cache/',

    // where are hosted your static files (not obligatory)
    'static'   => 'http://your.static.domain/dir/',

    // active the Debug mode
    'show_Debug_log'    => true,
    'log_all'           => true
);
