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
    public static $config;


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

        $uri_array = '';

        // check if current path is not root url or core url, else return array of current route
        if (trim($path, '/') != '' && $path != "/".SELF)
        {
            $uri_array = explode('/', trim($path, '/'));
        }

        // check if uri_array is not empty and check if it contain a core config route
        if(isset($uri_array[0])){
            if(array_key_exists('/'.$uri_array[0], $config['routes'])){
                $app = $config['routes']['/'.$uri_array[0]].'/';

                // check if whe have app routes and remove current route
                if(count($uri_array) > 1){
                    $uri_array[0] = "";
                }else
                    $uri_array = null;
            }
        }

        // define CURRENT_APP with asked app or default app
        DEFINE('CURRENT_APP', isset($app) ? $app : $config['routes']['default'].'/');

        // check and include route file app
        if(is_file(APPS.CURRENT_APP.'routes.php')){
            include(APPS.CURRENT_APP.'routes.php');

            //define default controller & action and unset them from array for route control
            $default_controller = isset($routes['default']['controller']) ? $routes['default']['controller'] : '' ;
            $default_action = isset($routes['default']['action']) ? $routes['default']['action'] : '';
            unset($routes['default']);
            $options = null;

            $debug['route'] = 'default';

            // check if we have routes to parse
            if(isset($uri_array[0])){

                // impode current uri for control
                $uri = trim(implode('/', $uri_array), "/");

                // first, check if current raw uri look exactly to one route
                if(isset($routes[$uri])){
                    debug::log('Routed url '.$routes[$uri]);
                    $controller = $routes[$uri]['controller'];
                    $action = $routes[$uri]['action'];
                }

                // second, check each routes
                if(!isset($controller) && !isset($action)){
                    foreach ($routes as $route => $val){

                        // don't parse config routes
                        if($route == '404' || $route == 'default') continue;
                        
                        $options = array();

                        // for each route, replace the :any, :alpha and :num by regex for control
                        $segmented_route = explode('/', $route);
                        
                        foreach($segmented_route as $small_route){                     
                            preg_match('#\[([a-zA-Z]+)\]#', $small_route, $result);
                            $options[] = isset($result[1]) ? $result[1] : null;
                        }
                        
                        $parsedRoute = preg_replace('#\[([a-zA-Z]+)\]#', '', $route);
                        $parsedKey = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', str_replace(':alpha', '([a-zA-Z]+)', $parsedRoute)));
                        
                        // try if current uri look like the parsed route
                        if (preg_match('#^'.$parsedKey.'$#', $uri, $array)){
                            debug::log('Routed url '.$route);
                            
                            $method_args = array();
                            foreach($options as $name => $value){
                                if(is_null($value)) continue;
                                
                                $method_args[$value] = $array[$name];
                            }
                            
                            //now, let's rock!
                            $controller = $routes[$route]['controller'];
                            $action = $routes[$route]['action'];
                            $options = $method_args;
                        }
                    }
                }

                // third, if no routes look like our uri, try the 404 route
                if(!isset($controller) && !isset($action)){
                    if(isset($routes['404'])){
                            debug::log('Routed url 404 : '.$route, true);
                            
                            $controller = $routes['404']['controller'];
                            $action = $routes['404']['action'];
                    }
                }
            }

            // if a controller already exists, keep it, else, load the default controller. Same for action
            $controller = isset($controller) ? $controller : $default_controller;
            $action = isset($action) ? $action : $default_action;
        }


        if(isset($controller) && isset($action)){
            // include the asked controller            
            debug::log('Asked controller and action : '.$controller.'->'.$action);            
            
            include(APPS.CURRENT_APP.'controllers/'.$controller.'.php');

            $debug['loadedControllers'][] = $controller;

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
}