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
*   Router class
*   parse all route to find the matching one
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/

class Router{
    /**
     * The current instance of the core
     * @var Jet
     */
    protected $jet = null;

    /**
     * The current routes list
     * @var array
     */
    protected $routes = array();

    /**
     * The routes list parsed to regexp
     * @var array
     */
    protected $parsed_routes = array();

    /**
     * The default route
     * @var array
     */
    protected $default = array();

    /**
     * Contain the 404 route
     * @var array
     */
    protected $error = array();
    
    /**
     * The list of authorized patterns in a route
     * @var array
     */
    protected $authorized_patterns = array(
        ':any'      => '.+',
        ':slug'     => '[a-zA-Z0-9\/_-]+',
        ':aplha'    => '[a-zA-Z]+',
        ':num'      => '[0-9]+'
    );

    /**
     * Get the core instance
     * Set the route list from the core object
     */
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
        
        if(isset($this->routes['notFound'])){
            $this->error = $this->routes['notFound'];
            unset($this->routes['notFound']);
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
            $key = preg_replace('#\[([a-zA-Z0-9]+)\]:([a-z]+)#', '(?<$1>:$2)', rtrim($key, '/'));
            $key = str_replace(array_keys($this->authorized_patterns), array_values($this->authorized_patterns), $key);
            $this->parsed_routes[$key] = $value;
        }
    }

    /**
     * Try to match the current uri with all routes
     * @return void
     */
    protected function match(){
        
        $this->jet->apps = $this->default;
        $this->jet->askedRoute = 'default';
        
        if(!is_array($this->jet->uri_array) || count($this->jet->uri_array) == 0){
            Log::save("Empty user uri, render default");
            return;
        }
        
        $uri = trim(implode('/', $this->jet->uri_array), "/");
        
        if(isset($this->routes[$uri])){
            Log::save('Routed url '.$uri);

            $this->jet->apps = $this->routes[$uri];
            $this->jet->askedRoute = $uri;
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
                $this->jet->apps = $this->parsed_routes[$route];
                $this->jet->options = $method_args;
                $this->jet->askedRoute = $uri;
                
                return;
            }
        }
        // third, if no routes look like our uri, try the 404 route
        Log::save('Routed url 404 : '.$uri, Log::WARNING);

        // Check if a 404 route exists. If not, render the default route
        if(count($this->error) ==  0) return;
        
        $this->jet->apps = $this->error;
        $this->jet->options = array('url' => $uri);
        $this->jet->askedRoute = "notFound";

        return;
    }
}