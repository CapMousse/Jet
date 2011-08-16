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
$config['dev']['sql'] 		= true;
$config['dev']['host'] 		= 'localhost';
$config['dev']['log'] 		= 'root';
$config['dev']['pass'] 		= 'root';
$config['dev']['base'] 		= 'shwaarkframework';

// activate caching
$config['dev']['cache']		= false;
$config['dev']['cache_dir']	= 'cache/';

// where are your statics files
$config['dev']['statics'] 	= 'http://your.static.domain/dir/';

// active the debug mode
$config['dev']['show_debug_log']= true;
$config['dev']['log_all']	= true;

// your app routes
$config['dev']['routes'] 	= array(
	'default' => 'install'
);