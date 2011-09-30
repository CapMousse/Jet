<?php

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

$config['all'] = array(
    'routes' => array(
        'default' => array('index', 'congrats'),
        '404' => array('index', 'do404'),
        'contact' => array('index', 'contactForm')
    ),
    
    'require' => array()
);

$config['dev'] = array(
    'routes' => array(
        'dev/mode' => array('index', 'congrats'),
        'test/:any/[id]:num/' => array('index', 'showId')
    )
);