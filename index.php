<?php
	//Define the root path for the route rewrite module
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	//Define the root path for the core files
	define('BASEPATH', str_replace("\\", "/", 'core/'));

	//Define the root path for the apps
	define('APPS', str_replace("\\", "/", 'apps/'));

	//Define the root path for the apps
	define('MODULES', str_replace("\\", "/", 'modules/'));

	//Define the root path for internal links
	define('ROOT', trim('/', 'http://'.$_SERVER['HTTP_HOST']));

	//let's rock!
	require(BASEPATH.'ShwaarkFramework.php');
?>