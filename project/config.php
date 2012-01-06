<?php
/**
*	Jet
*	A lightweight and fast framework for developer who don't need hundred of files
* 	
*	@package Jet
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/Jet
*
*/

// set the current environement
$environment = 'dev'; 

// set the database option
$config['all'] = array(    
    // template engine, don't touch if you want the default templating system
    'global' => array(
        'template'   => 'ViewJet',
        'log'       => '0' // 0 = ALL, 3 = FATAL, 2 = WARNING, 1 = INFO
    )
);

$config['dev'] = array(
    //your app routes
    'apps' => array(
	'default' => 'install'
    ),
    
    'orm' => array(
        'use_db'    => true,
        'type'      => 'mysql',
        'host'      => 'localhost',
        'log'       => 'root',
        'pass'      => 'root',
        'base'      => 'Jet',
    ),
    
    'requires' => array('debugLog.php')
);