<?php

$routes = array(
	//How to define route?
	//'url' => array( 'controller' => 'controller name', 'action' => 'action name')

	//Keyword for url:
		// :any : any char
		// :num : only number
		// :alpha : only alphabetical char

	'default_controller' => array('controller' => 'home', 'action' => 'index'),
	'404' => array('controller' => 'home', 'action' => 'do404')
);

?>