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
    protected
        $jet = null,
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
    
    function __construct() {
        $this->jet = Jet::getInstance();
        $this->routes = $this->jet->routes;
    }

    /**
     * Launch the route parsing
     * @return void
     */
    public function launch(){
        $this->default = $this->routes['default'];
        unset($this->routes['default']);
        
        if(isset($this->routes['404'])){
            $this->error = $this->routes['404'];
            unset($this->routes['404']);
        }
        
        $this->parse();
        $this->match();
    }

    /**
     * Parse all routes for uri matching
     * @return void
     */
    protected function parse(){
        
        # transform routes into usable routes for the router
        # thanks to Taluu (Baptiste Clavié) for the help
        
        foreach($this->routes as $key => $value){
            $key = preg_replace('#\[(.+)\]:(.+)#', '(?<$1>:$2)', rtrim($key, '/'));
            $key = str_replace(array_keys($this->authorized_paterns), array_values($this->authorized_paterns), $key);
            $this->parsed_routes[$key] = $value;
        }
    }

    /**
     * Try to match the current uri with all routes
     * @return void
     */
    protected function match(){
        
        $this->jet->controller = $this->default[CONTROLLER];
        $this->jet->action = $this->default[ACTION];
        $this->jet->askRoute = 'default';
        
        if(!is_array($this->jet->uri_array) || count($this->jet->uri_array) == 0){
            Log::save("Empty user uri, render default");
            return;
        }
        
        $uri = trim(implode('/', $this->jet->uri_array), "/");
        
        if(isset($this->routes[$uri])){
            Log::save('Routed url '.$uri);
            
            $this->jet->controller = $this->routes[$uri][CONTROLLER];
            $this->jet->action = $this->routes[$uri][ACTION];
            $this->jet->askRoute = $uri;
            return;
        }
        
        foreach ($this->parsed_routes as $route => $val){
            if (preg_match('#'.$route.'$#', $uri, $array)){
                Log::save('Routed url '.$route);

                $method_args = array();
                foreach($array as $name => $value){
                    if(!is_int($name)) $method_args[$name] = $value;
                }

                //now, let's rock!
                $this->jet->controller = $this->parsed_routes[$route][CONTROLLER];
                $this->jet->action = $this->parsed_routes[$route][ACTION];
                $this->jet->options = $method_args;
                $this->jet->askRoute = $uri;
                
                return;
            }
        }
        // third, if no routes look like our uri, try the 404 route
        Log::save('Routed url 404 : '.$uri, Log::WARNING);
        
        if(count($this->error) ==  0) return;
        
        $this->jet->controller = $this->error[CONTROLLER];
        $this->jet->action = $this->error[ACTION];
        $this->jet->options = array('url' => $uri);
        $this->jet->askRoute = 404;

        return;
    }
}