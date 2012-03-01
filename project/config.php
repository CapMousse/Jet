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

/**
 *   A route is represented by a string followed by an array wich contain the controller and action
 *   The route can contant several keyword :
 *   - :any : all char accepted
 *   - :slug : alphabetical, numerical, '_' and '-' are allowed
 *   - :alpha : only alpha are allowed
 *   - :num : only number
 *
 *   Routes results are array of app/controller/action.
 *   You can ask any app of your project in all routes
 */

class Config{
    public $environment = "dev";

    public function all(){
        return array(
            // template engine, don't touch if you want the default templating system
            'global' => array(
                'template'   => 'ViewJet',
                'log'       => '0' // 0 = ALL, 3 = FATAL, 2 = WARNING, 1 = INFO
            ),

            //your routes
            'routes' => array(
                'default' => array(
                    //      appname     controller  action
                    array(  'install',  'Index',    'congrats'),
                    array(  'install',  'Index',    'contactForm')
                ),
                'notFound' => array(
                    array('install', 'Index', 'do404')
                ),
                'contact' => array(
                    array('install', 'Index', 'contactForm')
                )
            ),
        );
    }

    public function dev(){
        return array(
            'routes' => array(
                'dev/mode' => array(
                    array('install', 'Index', 'congrats')
                ),
                'test/:any/[id]:num/' => array(
                    array('install','Index', 'showId')
                ),
                'example/[id]:num/' => array(
                    array('install', 'Index', 'showExample')
                )
            ),

            'orm' => array(
                'use_db'    => true,
                'type'      => 'mysql',
                'host'      => 'localhost',
                'log'       => 'root',
                'pass'      => 'root',
                'base'      => 'Jet',

                //Socket for the cli client. Here is the Mac OS X sample
                'socket'    => 'mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock'
            ),

            'requires' => array('debugLog.php')
        );
    }
}