<?php
/**
*   Jet
*   A lightweigth and fast framework for developper who don't need hundred of files
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 0.3
*/

/**
*   Routes files
*    
*   A route is represented by a string followed by an array wich contain the controller and action
*   The route can contant several keyword : 
*   - :any : all char accepted
*   - :slug : alphabetical, numerical, '_' and '-' are allowed
*   - :alpha : only alpha are allowed
*   - :num : only number
*/

$routes['all'] = array(
    'default' => array('index', 'congrats'),
    'contact' => array('index', 'contactForm')
);

$routes['dev'] = array(
    'dev/mode' => array('index', 'congrats'),
    'test/:any/[id]:num/' => array('index', 'showId')
)

?>