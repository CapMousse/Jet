<?php
/**
*   Jet
*   A lightweigth and fast framework for developper who don't need hundred of files
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/

/**
*   Http Request class
*   A little of get or post ?
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/

class HttpRequest{
    public static function get($key){
        return isset($_GET[$key]) ? $_GET[$key] : false;
    }
    
    public static function post($key){
        return isset($_POST[$key]) ? $_POST[$key] : false;
    }
    
    public static function cookie($key){
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : false;
    }
    
    public static function server($key){
        return isset($_SERVER[$key]) ? $_SERVER[$key] : false;
    }
    
    public static function getQueryString() {
        return $_SERVER['QUERY_STRING'];
    }
}