<?php

if($_SERVER['SERVER_ADDR'] == "127.0.0.1")
{
	// DEV PROPERTIES

	//use SQL?
	define("SQL", true);

	//server addresse for database
	define("HOST", "127.0.0.1");

	//login & password for database
	define("LOG","root");
	define("PASS","root");

	//database name
	define("BASE","portblog");

	//site addresse for static file (if static file are in other place)
	define("ROOT", "http://framework.loc/");

	//Enable debug mod
	define("DEBUG", false);

	//define the routes for all apps
	$routesApp = array(
		'default' => 'front'
	);
}
else
{
	// PRODUCTION PROPERTIES

	//use SQL?
	define("SQL", true);

	//server addresse for database
	define("HOST", "127.0.0.1");

	//login & password for database
	define("LOG","root");
	define("PASS","root");

	//database name
	define("BASE","portblog");

	//site addresse for static file (if static file are in other place)
	define("ROOT", "http://framework.loc/");

	//Enable debug mod
	define("DEBUG", false);

	//define the routes for all apps
	$routesApp = array();
}
?>