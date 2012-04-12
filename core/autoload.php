<?php
/**
*   Jet
*   A lightweight and fast framework for developer who don't need hundred of files
*
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/

/**
*   Autoload function
*   Load all core class needed by the application
*
*   @package    Jet
*   @author     Jérémy Barbe
*   @license    BSD
*   @link       https://github.com/CapMousse/Jet
*
*/

spl_autoload_register(function($class){
    static $loaded = array();

    if(isset($loaded[$class])){
        return $loaded[$class];
    }

    if(!is_file(SYSPATH.lcfirst($class).EXT)){
        $file = strtolower(preg_replace('/(?!^)[[:upper:]]/', '/\0', $class));
    }else{
        $file = lcfirst($class);
    }


    if(!is_file(SYSPATH.$file.EXT)){
        $loaded[$class] = false;
    }
    else{
        require SYSPATH.$file.EXT;
        $loaded[$class] = true;
    }

    if(class_exists('Log')){
        Log::save('Autoload '.$class);
    }

    return $loaded[$class];
});