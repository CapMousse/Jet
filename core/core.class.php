<?php
/**
*	ShwaarkFramework
*	A lightwave and fast framework for developper who don't need hundred of files
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.1
*/

/**
*	Shwaark class
*	The main core class
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.2
*/
class Shwaark{
    public static 
            $config,
            $controller,
            $action,
            $options;    

    /**
     * run
     *
     * launch the framework
     *
     * @access	static method
     * @return	void 
     */
    public static function run($config){
        /**********************/
        /**** Parse routes ****/
        /**********************/
        
        debug::log('Begining route parsing');
        
        // get current adresse path
        $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');

        // check if current path is not root url or core url, else return array of current route
        $uri_array = (trim($path, '/') != '' && $path != "/".SELF) ? explode('/', trim($path, '/')) : '';
        
        // check if uri_array is not empty and check if it contain a core config route
        if(is_array($uri_array)){
            if(array_key_exists('/'.$uri_array[0], $config['routes'])){
                $app = $config['routes']['/'.$uri_array[0]].'/';
                unset($uri_array[0]);
            }
        }

        // define CURRENT_APP with asked app or default app
        DEFINE('CURRENT_APP', isset($app) ? $app : $config['routes']['default'].'/');

        // check and include route file app
        try{
            include(APPS.CURRENT_APP.'routes.php');
        }catch(Exception $e){
            exit('No route defined for '.CURRENT_APP);
        }

        //define default controller & action and unset them from array for route control
        $controller = isset($routes['default'][0]) ? $routes['default'][0] : '' ;
        $action = isset($routes['default'][1]) ? $routes['default'][1] : '';
        unset($routes['default']);
        
        if(!is_array($uri_array))
            return self::render($controller, $action);

        // check if we have routes to parse
        // impode current uri for control
        $uri = trim(implode('/', $uri_array), "/");

        // first, check if current raw uri look exactly to one route
        if(isset($routes[$uri])){
            debug::log('Routed url '.$routes[$uri]);
            
            $controller = $routes[$uri][0];
            $action = $routes[$uri][1];
            
            return self::render($controller, $action);
        }

        // second, check each routes
        foreach ($routes as $route => $val){

            // don't parse config routes
            if($route == '404') continue;

            // for each route, replace the :any, :alpha and :num by regex for control
            // check if asked route have argument name for action
            $parsedRoute = preg_replace('#\[([a-zA-Z_-]+)\]:(any|num|alpha)#', '(?<$1>:$2)', $route);

            $search = array(':any',':num',':alpha');
            $replace = array('([a-zA-Z0-9_-]+)','([0-9]+)','([a-zA-Z_-]+)');
            $parsedRoute = str_replace($search, $replace, $parsedRoute);

            // try if current uri look like the parsed route
            if (preg_match('#^'.$parsedRoute.'$#', $uri, $array)){
                debug::log('Routed url '.$route);

                $method_args = array();
                foreach($array as $name => $value){
                    if(is_int($name)) continue;                                
                    $method_args[$name] = $value;
                }

                //now, let's rock!
                $controller = $routes[$route][0];
                $action = $routes[$route][1];
                $options = $method_args;
                
                return self::render($controller, $action, $options);
            }
        }

        // third, if no routes look like our uri, try the 404 route
        if(isset($routes['404'])){
            debug::log('Routed url 404 : '.$route, true);

            $controller = $routes['404'][0];
            $action = $routes['404'][1];

            return self::render($controller, $action);
        }
    }
    
    public static function render($controller, $action, $options = null){
        // include the asked controller            
        debug::log('Asked controller and action : '.$controller.'->'.$action);            

        include(APPS.CURRENT_APP.'controllers/'.$controller.'.php');

        // create the asked controller
        $theApp = new $controller();

        $options = is_null($options) ? array() : $options;
        // lauch the asked action, with our options
        if(!is_null($options))
            @call_user_func_array(array($theApp, $action), $options);
        else
            $theApp->$action();

        // check if we our app need to be rendered
        if($theApp->hasLayout()){
            debug::log('Render layout');
            $theApp->render();
        }        
    }
}