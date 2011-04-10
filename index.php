<?php
	//Define the root path for the route rewrite module
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	//Define the root path for the core files
	define('BASEPATH', str_replace("\\", "/", 'core/'));

	//Define the root path for the apps
	define('APPS', str_replace("\\", "/", 'apps/'));

	//Define the root path for static files (css, img...)
	define('STATIC', str_replace("\\", "/", 'static/'));

	//let's rock!
	require(BASEPATH.'ShwaarkFramework.php');
?>