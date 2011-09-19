<?php
/**
*	Jet
*	A lightweigth and fast framework for developper who don't need hundred of files
* 	
*	@package Jet
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/Jet
*	@version 1.1
*/

// set the database option
// if no db used, don't create/call model
$orm['dev']  = array(
    'use_db'    => true,
    'type'      => 'mysql',
    'host'      => 'localhost',
    'log'       => 'root',
    'pass'      => '',
    'base'      => 'Jet',
);
