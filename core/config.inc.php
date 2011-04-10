<?php

if($_SERVER['SERVER_ADDR'] == "127.0.0.1")
{
	// DEV PROPERTIES
	define("HOST", "127.0.0.1");
	define("LOG","root");
	define("PASS","root");
	define("BASE","portblog");
	define("ROOT", "http://framework.loc/");
	define("TITRE", 'Test framework');
	define("DEBUG", false);
	define("SQL", true);

	define("TEMPLATE_PAGE", "template_page.php");
	define("MODELE_DEFAUT", "home.php");
	$routesApp = array(
		'default' => 'front',
		'/backoffice' => 'back'	
	);
}
else
{
	// PRODUCTION PROPERTIES
	define("HOST", "127.0.0.1");
	define("LOG","root");
	define("PASS","root");
	define("BASE","framework");
	define("ROOT", "http://framework.loc/");
	define("TITRE", 'Test framework');
	define("DEBUG", FALSE);
	define("SQL", true);

	define("TEMPLATE_PAGE", "template_page.php");
	define("MODELE_DEFAUT", "home.php");
}
?>