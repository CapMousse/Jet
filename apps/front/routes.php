<?php

$routes = array(
	//How to define route?
	//'url' => array( 'controller' => 'controller name', 'action' => 'action name')

	//Keyword for url:
		// :any : any char
		// :num : only number
		// :alpha : only alphabetical char

	'default' => array('controller' => 'home', 'action' => 'index'),
	'404' => array('controller' => 'home', 'action' => 'do404'),
	'test/[id]:num/:alpha/[test]:any' => array('controller' => 'home', 'action' => 'singleArticle')
);

?>