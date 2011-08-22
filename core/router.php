<?php
/**
*   ShwaarkFramework
*   A lightweigth and fast framework for developper who don't need hundred of files
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/ShwaarkFramework
*   @version 0.3
*/

/**
*   Router class
*   parse all route to find the matching one
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/ShwaarkFramework
*   @version 1
*/

class Router{
    protected static
        $routes = array(),
        $parsed_routes = array(),
        $default = array(),
        $error = array(),
        $authorized_paterns = array(
            ':any'      => '.+',
            ':slug'     => '[a-zA-Z0-9_-]+',
            ':aplha'    => '[a-zA-Z]+',
            ':num'      => '[0-9]+'
        );

    public static function launch(){
        self::$routes = Shwaark::$routes;
        self::$default = self::$routes['default'];
        unset(self::$routes['default']);
        
        if(isset(self::$routes['404'])){
            self::$error = self::$routes['404'];
            unset(self::$routes['404']);
        }
        
        self::parse();
        self::match();
    }
    
    protected static function parse(){
        
        # transform routes into usable routes for the router
        # thanks to Taluu (Baptiste Clavié) for the help
        
        foreach(self::$routes as $key => $value){
            $key = preg_replace('#\[(.+)\]:(.+)#', '(?<$1>:$2)', rtrim($key, '/'));
            $key = str_replace(array_keys(self::$authorized_paterns), array_values(self::$authorized_paterns), $key);
            self::$parsed_routes[$key] = $value;
        }
    }
    
    
    protected static function match(){
        
        Shwaark::set('controller', self::$default[CONTROLLER]);
        Shwaark::set('action', self::$default[ACTION]);
        
        if(!is_array(Shwaark::$uri_array) || count(Shwaark::$uri_array) == 0){
            debug::log("Empty user uri, render default");
            return;
        }
        
        $uri = trim(implode('/', Shwaark::$uri_array), "/");
        
        if(isset(self::$routes[$uri])){
            debug::log('Routed url '.$uri);
            
            Shwaark::set('controller', self::$routes[$uri][CONTROLLER]);
            Shwaark::set('action', self::$routes[$uri][ACTION]);
            return;
        }
        
        foreach (self::$parsed_routes as $route => $val){
            if (preg_match('#'.$route.'$#', $uri, $array)){
                debug::log('Routed url '.$route);

                $method_args = array();
                foreach($array as $name => $value){
                    if(!is_int($name)) $method_args[$name] = $value;
                }

                //now, let's rock!
                Shwaark::set('controller', self::$parsed_routes[$route][CONTROLLER]);
                Shwaark::set('action', self::$parsed_routes[$route][ACTION]);
                Shwaark::set('options', $method_args);
                
                return;
            }
        }
        // third, if no routes look like our uri, try the 404 route
        debug::log('Routed url 404 : '.$uri, true);
        
        if(count(self::$error) ==  0) return;
        
        Shwaark::set('controller', self::$error[CONTROLLER]);
        Shwaark::set('action', self::$error[ACTION]);

        return;
    }
}