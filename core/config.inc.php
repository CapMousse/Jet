<?php

$config['dev']['sql'] 		= true;
$config['dev']['host'] 		= 'localhost';
$config['dev']['log'] 		= 'root';
$config['dev']['pass'] 		= 'root';
$config['dev']['base'] 		= 'shwaarkframework';
$config['dev']['statics'] 	= 'http://shwaark.framework/statics/';
$config['dev']['debug'] 	= false;
$config['dev']['routes'] 	= array(
	'default' => 'front',
	'/other' => 'otherApp'
);


$environment = 'dev'; 