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
            $environment,
            $app,
            $controller,
            $action,
            $modules = array(),
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

        //parse all routes with curent URI
        self::parseRoutes();
        
        //parse and load needed files
        self::requireFiles();
        
        //parse and load needed modules
        self::requireModules();

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
        if(!is_file('config/routes.php')){
            debug::log('Missing routes.php files to define apps in config/ dir', true, true);
            return;
        }

        include('config/routes.php');
        
        if(!is_array($routes)){
            debug::log('Missing routes array in config/routes.php', true, true);
            return;
        }
        
        $routes = self::mergeEnvironment($routes);
        
        if(count($routes) == 0){
            debug::log('No routes defined in config/routes.php', true, true);
            return;
        }

        if(!is_array(self::$uri_array)){
            return $routes['default'].'/';
        }

        if(isset($routes['/'.self::$uri_array[0]])){
            $app = $routes['/'.self::$uri_array[0]].'/';
            debug::log('Set app to '.$app);
            array_splice(self::$uri_array, 0, 1);
        }else{
            $app = $routes['default'].'/';
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
        // check and include route file app
        if(!is_file(APPS.self::$app.'config/routes.php')){
            debug::log('Missing routes file in '.APPS.self::$app.'config/', true, true);
            return;
        }
        
        include(APPS.self::$app.'config/routes.php');
        
        if(!is_array($routes)){
            debug::log('$routes is not an array in '.APPS.self::$app.'config/routes.php', true, true);
            return;
        }
             
        $routes = self::mergeEnvironment($routes);

        //define default controller & action and unset them from array for route control
        if(!isset($routes['default'][CONTROLLER]) || !isset($routes['default'][CONTROLLER])){
            debug::log('Default route must be declared in '.self::$app, true, true);
        }

        self::$controller = $routes['default'][CONTROLLER];
        self::$action = $routes['default'][ACTION];
        unset($routes['default']);
        
        if(!is_array(self::$uri_array) || count(self::$uri_array) == 0){
            debug::log("Empty user uri, render default");
            return;
        }

        // check if we have routes to parse
        // impode current uri for control
        $uri = trim(implode('/', self::$uri_array), "/");

        // first, check if current raw uri look exactly to one route
        if(isset($routes[$uri])){
            debug::log('Routed url '.$routes[$uri]);
            
            self::$controller = $routes[$uri][CONTROLLER];
            self::$action = $routes[$uri][ACTION];
            return;
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
                self::$controller = $routes[$route][CONTROLLER];
                self::$action = $routes[$route][ACTION];
                self::$options = $method_args;
                
                return;
            }
        }

        // third, if no routes look like our uri, try the 404 route
        if(isset($routes['404'])){
            debug::log('Routed url 404 : '.$uri, true);

            self::$controller = $routes['404'][CONTROLLER];
            self::$action = $routes['404'][ACTION];

            return;
        }
    }
    
    
    /**
     * requireFiles
     * 
     * parse and load needed files
     * 
     * @access  private static function
     * @return  void
     */
    private static function requireFiles(){
        $dir = null;
        $file = null;
        
        if(is_file('config/requires.php')){
            $file = 'config/requires.php';
        }
        
        if(is_file(APPS.self::$app.'config/requires.php')){
            $dir = APPS.self::$app;
            $file = APPS.self::$app.'config/requires.php';
        }
        
        if(is_null($file)){
            return;
        }
        
        include($file);

        if(!isset($requires) || !is_array($requires)){
            debug::log("Requires config file {$file} not contain a requires array", true);
            return;
        }
        
        $requires = self::mergeEnvironment($requires);
        
        foreach($requires as $file){
            if(is_file($dir.$file)){
                include($dir.$file);
            }
        }
    }
    
    /**
     * requireModules
     * 
     * parse and load needed modules
     * 
     * @access  private static function
     * @return  void
     */
    private static function requireModules(){
        $dir = null;
        $file = null;
        
        if(is_file('config/modules.php')){
            $file = 'config/modules.php';
        }
        
        if(is_file(APPS.self::$app.'config/modules.php')){
            $file = APPS.self::$app.'config/modules.php';
        }
        
        if(is_null($file)){
            return;
        }
        
        include($file);

        if(!isset($modules) || !isset($modules[self::$environment])){
            debug::log("Module config file {$file} not contain an array", true);
            return;
        }
        
        $modules = self::mergeEnvironment($modules);
        
        foreach($modules as $moduleName){
            if(is_dir(MODULES.$moduleName)){

                //include all nececary files
                foreach(glob(MODULES.$moduleName.'/*.php') as $file)
                    include($file);

                $name = ucfirst($moduleName);
                
                if(!class_exists($moduleName)){
                    debug::log("Module {$moduleName} don't have class with same name", true);
                    continue;
                }
           
                self::$modules[$name] = new $name();

                debug::log('Module loaded : '.$name);
            }
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
            debug::log('Controller file '.self::$controller.' does not exists on '.APPS.self::$app.'controllers/', true, true);
        }

        include(APPS.self::$app.'controllers/'.self::$controller.'.php');
            
        
        // create the asked controller
        $controller = ucfirst(self::$controller);
        
        if(!class_exists($controller)){
            debug::log('Controller class '.$controller.' is not declared on '.APPS.self::$app.'controllers/'.self::$controller.'.php', true, true);
        }
        
        $theApp = new $controller();
        
        if(count(self::$modules) > 0){
            foreach(self::$modules as $name => $object){
                $theApp->{$name} = $object;
            }
        }
        
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
    
    
    private static function mergeEnvironment($array){        
        if(!isset($array[self::$environment]) && !isset($array['all'])){
            debug::log("Given array dosen't containt ".self::$environment." and all environements", true, true);
        }
        
        $returnArray = array();
        
        if(isset($array['all'])){
            $returnArray = $array['all'];
        }
        
        if(!isset($array[self::$environment])){
            return $returnArray;
        }else{
            return array_merge($returnArray, $array[self::$environment]);
        }
    }
}