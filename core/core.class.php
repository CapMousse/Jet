<?php
/**
*	ShwaarkFramework
*	A lightweigth and fast framework for developper who don't need hundred of files
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
            $app,
            $routes,
            $controller,
            $action,
            $options = null;

    private static
        $uri_array = array();


    /**
     * run
     *
     * launch the framework
     *
     * @access	static method
     * @return	void 
     */
    public static function run(){
        /**********************/
        /**** Parse routes ****/
        /**********************/
        
        debug::log('Begining route parsing');

        self::$uri_array = self::parsePath();

        self::$app = self::defineApp();

        // check and include route file app
        if(!is_file(APPS.self::$app.'routes.php')){
            debug::log('No routes defined for '.self::$app, true, true);
        }
        include(APPS.self::$app.'routes.php');

        self::$routes = $routes;

        //parse all routes with curent URI
        self::parseRoutes();

        //launch render
        return self::render();
    }

    /**
     * function parsePath
     *
     * parse current URI to array
     *
     * @access  private static function
     * @return  Array/null
     */
    private static function parsePath(){
        // get current adresse path
        $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');

        // check if current path is not root url or core url, else return array of current route
        return (trim($path, '/') != '' && $path != "/".SELF) ? explode('/', trim($path, '/')) : null;
    }

    /**
     * function defineApp
     *
     * parse current URI to array
     *
     * @access  private static function
     * @return  string/false
     */
    private static function defineApp(){
        if(!is_array(self::$uri_array)){
            return self::$config['routes']['default'].'/';
        }

        if(isset(self::$config['routes']['/'.self::$uri_array[0]])){
            $app = self::$config['routes']['/'.self::$uri_array[0]].'/';
            debug::log('Set app to '.$app);
            array_splice(self::$uri_array, 0, 1);
        }else{
            $app = self::$config['routes']['default'].'/';
        }

        return $app;
    }

    /**
     * function parseRoutes
     *
     * parse current URI to fetch all routes
     *
     * @access  private static function
     * @return  void
     */
    private static function parseRoutes(){

        //define default controller & action and unset them from array for route control
        if(!isset(self::$routes['default'][CONTROLLER]) || !isset(self::$routes['default'][CONTROLLER])){
            debug::log('Default route must be declared in '.self::$app, true, true);
        }

        self::$controller = self::$routes['default'][CONTROLLER];
        self::$action = self::$routes['default'][ACTION];
        unset(self::$routes['default']);
        
        if(!is_array(self::$uri_array) || count(self::$uri_array) == 0){
            debug::log("Empty user uri, render default");
            return;
        }

        // check if we have routes to parse
        // impode current uri for control
        $uri = trim(implode('/', self::$uri_array), "/");

        // first, check if current raw uri look exactly to one route
        if(isset(self::$routes[$uri])){
            debug::log('Routed url '.self::$routes[$uri]);
            
            self::$controller = self::$routes[$uri][CONTROLLER];
            self::$action = self::$routes[$uri][ACTION];
            return;
        }

        // second, check each routes
        foreach (self::$routes as $route => $val){

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
                self::$controller = self::$routes[$route][CONTROLLER];
                self::$action = self::$routes[$route][ACTION];
                self::$options = $method_args;
                
                return;
            }
        }

        // third, if no routes look like our uri, try the 404 route
        if(isset(self::$routes['404'])){
            debug::log('Routed url 404 : '.$path, true);

            self::$controller = self::$routes['404'][CONTROLLER];
            self::$action = self::$routes['404'][ACTION];

            return;
        }
    }
    
    
    
    /**
     * render
     * 
     * launch the render
     * 
     * @access  private static function
     * @param   $controller string : the class to be instanciated
     * @param   $action string : the method to be launched
     * @param   $options array [optional] : the arguments for the method
     * @return  void
     */
    
    private static function render(){
        // include the asked controller            
        debug::log('Asked controller and action : '.self::$controller.'->'.self::$action);            

        if(!is_file(APPS.self::$app.'controllers/'.self::$controller.'.php')){
            debug::log('Controller '.self::$controller.' does not exists', true, true);
        }

        include(APPS.self::$app.'controllers/'.self::$controller.'.php');
            
        // create the asked controller
        $controller = ucfirst(self::$controller);
        $theApp = new $controller();
        
        if(method_exists($theApp, 'before'.ucfirst(self::$action)))
            self::lauchAction($theApp, 'before'.ucfirst(self::$action), self::$options);
        
        self::lauchAction($theApp, self::$action, self::$options);
        
        if(method_exists($theApp, 'after'.ucfirst(self::$action)))
            self::lauchAction($theApp, 'after'.ucfirst(self::$action),self:: $options);

        // check if we our app need to be rendered
        if($theApp->hasLayout()){
            debug::log('Render layout');
            $theApp->render();
        }        
    }
    
    /**
     * lauchAction
     * 
     * lauch the specified action form class with sent options
     * 
     * @access  private static function
     * @param   $class object : the object from the action
     * @param   $method string : the method to be launched
     * @param   $option array [optional] : the arguments for the method
     * @return  void
     */
    private static function lauchAction($class, $method, $options = null){
        debug::log('LauchAction : '.$method);
        
        $options = is_null($options) ? array() : $options;
        // lauch the asked action, with our options
        if(!is_null($options))
            @call_user_func_array(array($class, $method), $options);
        else
            $class->$method();
    }
}