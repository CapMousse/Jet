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
*   Router class
*   parse all route to find the matching one
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
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

    /**
     * Launch the route parsing
     * @return void
     */
    public static function launch(){
        self::$routes = Jet::$routes;
        self::$default = self::$routes['default'];
        unset(self::$routes['default']);
        
        if(isset(self::$routes['404'])){
            self::$error = self::$routes['404'];
            unset(self::$routes['404']);
        }
        
        self::parse();
        self::match();
    }

    /**
     * Parse all routes for uri matching
     * @return void
     */
    protected static function parse(){
        
        # transform routes into usable routes for the router
        # thanks to Taluu (Baptiste Clavié) for the help
        
        foreach(self::$routes as $key => $value){
            $key = preg_replace('#\[(.+)\]:(.+)#', '(?<$1>:$2)', rtrim($key, '/'));
            $key = str_replace(array_keys(self::$authorized_paterns), array_values(self::$authorized_paterns), $key);
            self::$parsed_routes[$key] = $value;
        }
    }

    /**
     * Try to match the current uri with all routes
     * @return void
     */
    protected static function match(){
        
        Jet::set('controller', self::$default[CONTROLLER]);
        Jet::set('action', self::$default[ACTION]);
        
        if(!is_array(Jet::$uri_array) || count(Jet::$uri_array) == 0){
            Debug::log("Empty user uri, render default");
            return;
        }
        
        $uri = trim(implode('/', Jet::$uri_array), "/");
        
        if(isset(self::$routes[$uri])){
            Debug::log('Routed url '.$uri);
            
            Jet::set('controller', self::$routes[$uri][CONTROLLER]);
            Jet::set('action', self::$routes[$uri][ACTION]);
            return;
        }
        
        foreach (self::$parsed_routes as $route => $val){
            if (preg_match('#'.$route.'$#', $uri, $array)){
                Debug::log('Routed url '.$route);

                $method_args = array();
                foreach($array as $name => $value){
                    if(!is_int($name)) $method_args[$name] = $value;
                }

                //now, let's rock!
                Jet::set('controller', self::$parsed_routes[$route][CONTROLLER]);
                Jet::set('action', self::$parsed_routes[$route][ACTION]);
                Jet::set('options', $method_args);
                
                return;
            }
        }
        // third, if no routes look like our uri, try the 404 route
        Debug::log('Routed url 404 : '.$uri, true);
        
        if(count(self::$error) ==  0) return;
        
        Jet::set('controller', self::$error[CONTROLLER]);
        Jet::set('action', self::$error[ACTION]);

        return;
    }
}