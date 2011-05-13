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


// set the database option
$config['dev']['sql'] 			= true;
$config['dev']['host'] 			= 'localhost';
$config['dev']['log'] 			= 'root';
$config['dev']['pass'] 			= 'root';
$config['dev']['base'] 			= 'shwaarkframework';

// activate caching
$config['dev']['cache']			= true;
$config['dev']['cache_dir']		= 'cache/';

// where are your statics files
$config['dev']['statics'] 		= 'http://shwaark.framework/statics/';

// active the debug mode
$config['dev']['debug'] 		= false;

// your app routes
$config['dev']['routes'] 		= array(
	'default' => 'front',
	'/other' => 'otherApp'
);



// set the current config environement
$environment = 'dev'; 