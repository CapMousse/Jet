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
*   Jet class
*   The main core class
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/
class Jet{
    public static
        $config = array(),
        $environment,
        $routes = array(),
        $modules = array(),
        $uri_array = array(),
        $infos = array();


    /**
     * run
     *
     * launch the framework
     *
     * @access   static method
     * @return   void 
     */
    public static function run(){
        
        #set the root url to easy access, thanks to Taluu
        self::set('web_url', substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1));
        
        /**********************/
        /**** Parse routes ****/
        /**********************/
        
        debug::log('Begining route parsing');

        self::$uri_array = self::parsePath();

        self::set('app', self::defineApp());

        //parse all routes with curent URI
        self::getRoutes();
        Router::launch();
        
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
        $routes = array();
        $uri = self::$uri_array;
        
        if(!is_file(CONFIG.'routes.php')){
            debug::log('Missing routes.php files to define apps in config/ dir', true, true);
            return;
        }

        include(CONFIG.'routes.php');
        
        if(!is_array($routes) || count($routes) == 0){
            debug::log('Missing routes array in config/routes.php', true, true);
            return;
        }
        
        $routes = self::mergeEnvironment($routes);
        
        if(!isset($routes['default'])){
            debug::log('No default app routes defined in config/routes.php', true, true);
            return;
        }

        if(!is_array($uri)){
            return $routes['default'].'/';
        }

        if(isset($routes['/'.$uri[0]])){
            $app = $routes['/'.$uri[0]].'/';
            array_splice($uri, 0, 1);
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
    private static function getRoutes(){
        $_currentApp = APPS.self::get('app');
        // check and include route file app
        if(!is_file($_currentApp.'config/routes.php')){
            debug::log('Missing routes file in '.$_currentApp.'config/', true, true);
            return;
        }
        
        include($_currentApp.'config/routes.php');
        
        if(!is_array($routes)){
            debug::log('$routes is not an array in '.$_currentApp.'config/routes.php', true, true);
            return;
        }
             
        self::$routes = self::mergeEnvironment($routes);
        
        //check if default controller/action exists
        if(count(self::$routes['default']) != 2){
            debug::log('Default route must be declared in '.self::get('app'), true, true);
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
        if(!$fileInfo = self::checkFile('config/requires.php')){
            return;
        }
        
        include($fileInfo[0]);

        if(!isset($requires) || !is_array($requires)){
            debug::log("Requires config file {$fileInfo[0]} not contain a requires array");
            return;
        }
        
        $requires = self::mergeEnvironment($requires);
        
        foreach($requires as $file){
            if(is_file($fileInfo[1].$file)){
                include($fileInfo[1].$file);
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
        if(!$file = self::checkFile('config/modules.php')){
            return;
        }
        
        include($file[0]);

        if(!isset($modules)){
            debug::log("Module config file {$file[0]} not contain an array");
            return;
        }
        
        $modulesList = self::mergeEnvironment($modules);
        $modules = array();
        
        foreach($modulesList as $moduleName){
            if(is_dir(MODULES.$moduleName)){

                //include all nececary files
                foreach(glob(MODULES.$moduleName.'/*.php') as $file)
                    include($file);

                $name = ucfirst($moduleName);
                
                if(!class_exists($moduleName)){
                    debug::log("Module {$moduleName} don't have class with same name", true);
                    continue;
                }
           
                $modules[$name] = new $name();

                debug::log('Module loaded : '.$name);
            }
        }
        
        self::set('modules', $modules);
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
        $_currentApp = APPS.self::get('app');
        $_currentController = self::get('controller');
        $_currentAction = self::get('action');
        $_currentOptions = self::get('options') ? self::get('options') : array();
        $_currentModules = self::get('modules') ? self::get('modules') : array();
        
        // include the asked controller            
        debug::log('Asked controller and action : '.$_currentController.'->'.$_currentAction);            

        if(!is_file($_currentApp.'controllers/'.$_currentController.'.php')){
            debug::log('Controller file '.$_currentController.' does not exists on '.$_currentApp.'controllers/', true, true);
        }

        include($_currentApp.'controllers/'.$_currentController.'.php');
            
        
        // create the asked controller
        $controller = ucfirst($_currentController);
        
        if(!class_exists($controller)){
            debug::log('Controller class '.$controller.' is not declared on '.$_currentApp.'controllers/'.$_currentController.'.php', true, true);
        }
        
        $theApp = new $controller();
        
        if(count($_currentModules) > 0){
            foreach($_currentModules as $name => $object){
                $theApp->{$name} = $object;
            }
        }
        
        if(method_exists($theApp, 'before'.ucfirst($_currentAction)))
            self::lauchAction($theApp, 'before'.ucfirst($_currentAction), $_currentOptions);
        
        self::lauchAction($theApp, $_currentAction, $_currentOptions);
        
        if(method_exists($theApp, 'after'.ucfirst($_currentAction)))
            self::lauchAction($theApp, 'after'.ucfirst($_currentAction), $_currentOptions);

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
        
        // lauch the asked action, with our options
        if(!is_null($options))
            @call_user_func_array(array($class, $method), $options);
        else
            $class->$method();
    }
    
    /**
     * checkFile
     * 
     * check if file exist in project dir or app dir
     * 
     * @access  private static function
     * @param   $file   string  file to be check
     * @return  $file/false
     */
    private static function checkFile($file){
        $return = null;
        
        if(is_file(PROJECT.$file)){
            $return = array($file, null);
        }
        
        if(is_file(APPS.self::get('app').$file)){
            $return = array(APPS.self::get('app').$file, APPS.self::get('app'));
        }
        
        return is_null($return) ? false : $return;
    }
    
     /**
     * mergeEnvironment
     * 
     * merge array of 'all' environment and current environment
     * 
     * @access  private static function
     * @param   $array  array   the array with environment to be merge
     * @return  array
     */
    public static function mergeEnvironment($array){        
        if(!isset($array[self::$environment]) && !isset($array['all'])){
            debug::log("Given array doesn't containt '".self::$environment."' or 'all' environements", true, true);
        }
        
        $returnArray = array();
        
        if(isset($array['all'])){
            $returnArray = $array['all'];
        }
        
        if(isset($array[self::$environment])){
            $returnArray = array_merge($returnArray, $array[self::$environment]);
        }
        
        return $returnArray;
    }
    
    public static function set($name, $value){
        self::$infos[$name] = $value;
    }
    
    public static function get($name){      
        return isset(self::$infos[$name]) ? self::$infos[$name]  : false;
    }
}