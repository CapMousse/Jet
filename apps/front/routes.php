<?php

$routes = array(
	'default_controller' => array('controller' => 'home', 'action' => 'index'),
	'404' => array('controller' => 'home', 'action' => 'do404'),
	'home/index' => array('controller' => 'home', 'action' => 'index'),
	'home/:num' => array('controller' => 'home', 'action' => 'testAny'),
	'home/:num/:num/:any' => array('controller' => 'home', 'action' => 'testAny'),
	'test/test/Test' => array('controller' => 'home', 'action' => 'index')
);

?>