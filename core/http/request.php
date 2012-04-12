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
*   Http Request class
*   A little of get or post ?
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/

class HttpRequest{
    /**
     * Contain the list of $_GET
     * @var null
     */
    public static $_get = null;

    /**
     * Contain the list of $_POST
     * @var null
     */
    public static $_post = null;

    /**
     * Contain the list of PUT
     * @var null
     */
    public static $_put = null;

    /**
     * Contain the list of DEL
     * @var null
     */
    public static $_del = null;

    /**
     * Contain the list of $_COOKIE
     * @var null
     */
    public static $_cookie = null;

    /**
     * Contain the root dir
     * @var null
     */
    public static $_root = null;
    
    /**
     * Get a value form the $_GET array
     * @param string $key
     * @return mixed|Boolean
     */
    public static function get($key){
        if(null == self::$_get){
            self::$_get = $_GET;
        }
        
        return isset(self::$_get[$key]) ? self::$_get[$key] : false;
    }
    
    /**
     * Get a value form the $_POST array
     * @param string $key
     * @return mixed|false
     */
    public static function post($key){
        if(null == self::$_post){
            self::$_post = $_POST;
        }
        
        return isset(self::$_post[$key]) ? self::$_post[$key] : false;
    }
    
    /**
     * Get a value form a PUT request
     * @param string $key
     * @return mixed|false
     */
    public static function put($key){
        if(null == self::$_put){
            self::$_put = parse_str(file_get_contents('php://input'));
        }
        
        return isset(self::$_put[$key]) ? self::$_put[$key] : false;
    }
    
    /**
     * Get a value form a DEL request
     * @param string $key
     * @return mixed|false
     */
    public static function del($key){
        if(null == self::$_del){
            self::$_del = parse_str(file_get_contents('php://input'));
        }
        
        return isset(self::$_del[$key]) ? self::$_del[$key] : false;
    }
    
    /**
     * Get a value form the $_COOKIE array
     * @param string $key
     * @return mixed|false
     */
    public static function cookie($key){
        if(null == self::$_cookie){
            self::$_cookie = $_COOKIE;
        }
        
        return isset(self::$_cookie[$key]) ? self::$_cookie[$key] : false;
    }
    
    /**
     * Get a value form the $_SERVER array
     * @param string $key
     * @return mixed|false
     */
    public static function server($key){
        return isset($_SERVER[$key]) ? $_SERVER[$key] : false;
    }
    
    /**
     * Get the server query string
     * @return string 
     */
    public static function getQueryString() {
        return isset($_SERVER['REQUEST_URI']) ? ltrim($_SERVER['REQUEST_URI'], '/') : '';
    }
    
    /**
     * Get the root dir
     * @return string;
     */
    public static function getRoot(){
        if(null == self::$_root){
            self::$_root = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/') + 1);
        }
        
        return self::$_root;
    }
}