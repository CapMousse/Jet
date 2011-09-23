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
*   @version 1.1
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
     * launch the framework
     *
     * @return   void 
     */
    public static function run(){        
        /**********************/
        /**** Parse routes ****/
        /**********************/
        
        Debug::log('Begining route parsing');

        self::$uri_array = self::parsePath();
        self::set('app', self::defineApp());

        //parse all routes with curent URI
        self::getRoutes();
        Router::launch();
        
        //parse and load needed files and modules
        self::requireFiles();
        self::requireModules();

        //launch render
        return self::render();
    }

    /**
     * parse current path to be used by the router
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
     * Define app asked by the current uri
     *
     * @return  string/false
     */
    private static function defineApp(){
        $routes = array();
        $uri = self::$uri_array;
        
        if(!is_file(PROJECT.'config/routes.php')){
            Debug::log('Missing routes.php files to define apps in config/ dir', true, true);
            return;
        }

        include(PROJECT.'config/routes.php');
        
        if(!is_array($routes) || count($routes) == 0){
            Debug::log('Missing routes array in config/routes.php', true, true);
            return;
        }
        
        $routes = self::mergeEnvironment($routes);
        
        if(!isset($routes['default'])){
            Debug::log('No default app routes defined in config/routes.php', true, true);
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
     * parse current URI to fetch all routes
     *
     * @return  void
     */
    private static function getRoutes(){
        $_currentApp = PROJECT.'apps/'.self::get('app');
        // check and include route file app
        if(!is_file($_currentApp.'config/routes.php')){
            Debug::log('Missing routes file in '.$_currentApp.'config/', true, true);
            return;
        }
        
        include($_currentApp.'config/routes.php');
        
        if(!is_array($routes)){
            Debug::log('$routes is not an array in '.$_currentApp.'config/routes.php', true, true);
            return;
        }
             
        self::$routes = self::mergeEnvironment($routes);
        
        //check if default controller/action exists
        if(count(self::$routes['default']) != 2){
            Debug::log('Default route must be declared in '.self::get('app'), true, true);
        }
    }
    
    
    /**
     * parse and load needed files
     * 
     * @return  void
     */
    private static function requireFiles(){        
        if(!$fileInfo = self::checkFile('config/requires.php')){
            return;
        }
        
        include($fileInfo[0]);

        if(!isset($requires) || !is_array($requires)){
            Debug::log("Requires config file {$fileInfo[0]} not contain a requires array");
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
     * parse and load needed modules
     * 
     * @return  void
     */
    private static function requireModules(){        
        if(!$file = self::checkFile('config/modules.php')){
            return;
        }
        
        include($file[0]);

        if(!isset($modules)){
            Debug::log("Module config file {$file[0]} not contain an array");
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
                    Debug::log("Module {$moduleName} don't have class with same name", true);
                    continue;
                }
           
                $modules[$name] = new $name();

                Debug::log('Module loaded : '.$name);
            }
        }
        
        self::set('modules', $modules);
    }
    
    /**
     * launch the render of the current page
     * 
     * @return  void
     */
    
    private static function render(){        
        $_currentApp = PROJECT.'apps/'.self::get('app');
        $_currentController = self::get('controller');
        $_currentAction = self::get('action');
        $_currentOptions = self::get('options') ? self::get('options') : array();
        $_currentModules = self::get('modules') ? self::get('modules') : array();
        
        // include the asked controller            
        Debug::log('Asked controller and action : '.$_currentController.'->'.$_currentAction);            

        if(!is_file($_currentApp.'controllers/'.$_currentController.'.php')){
            Debug::log('Controller file '.$_currentController.' does not exists on '.$_currentApp.'controllers/', true, true);
        }

        include($_currentApp.'controllers/'.$_currentController.'.php');
            
        
        // create the asked controller
        $controller = ucfirst($_currentController);
        
        if(!class_exists($controller)){
            Debug::log('Controller class '.$controller.' is not declared on '.$_currentApp.'controllers/'.$_currentController.'.php', true, true);
        }
        
        $app = new $controller();
        
        if(count($_currentModules) > 0){
            foreach($_currentModules as $name => $object){
                $app->{$name} = $object;
            }
        }
        
        if(method_exists($app, 'before'.ucfirst($_currentAction)))
            self::lauchAction($app, 'before'.ucfirst($_currentAction), $_currentOptions);
        
        self::lauchAction($app, $_currentAction, $_currentOptions);
        
        if(method_exists($app, 'after'.ucfirst($_currentAction)))
            self::lauchAction($app, 'after'.ucfirst($_currentAction), $_currentOptions);

        
        
        // check if our app need to be rendered
        Debug::log('Render layout');
        $body = $app->view->render();
        
        Debug::log('Render Http Response');
        
        if(Jet::$config['show_Debug_log']){
            $body .= Debug::getLog();
        }
        
        $app->response->setBody($body);
        
        echo $app->response->send();       
    }
    
    /** 
     * lauch the specified action form class with sent options
     * 
     * @access  private static function
     * @param   $class object : the object from the action
     * @param   $method string : the method to be launched
     * @param   $option array [optional] : the arguments for the method
     * @return  void
     */
    private static function lauchAction($class, $method, $options = null){
        Debug::log('LauchAction : '.$method);
        
        // lauch the asked action, with our options
        if(!is_null($options))
            @call_user_func_array(array($class, $method), $options);
        else
            $class->$method();
    }
    
    /**
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
        
        if(is_file(PROJECT.'apps/'.self::get('app').$file)){
            $return = array(PROJECT.'apps/'.self::get('app').$file, PROJECT.'apps/'.self::get('app'));
        }
        
        return is_null($return) ? false : $return;
    }
    
     /**
     * merge array of 'all' environment and current environment
     * 
     * @param   $array  array   the array with environment to be merge
     * @return  array
     */
    public static function mergeEnvironment($array){        
        if(!isset($array[self::$environment]) && !isset($array['all'])){
            Debug::log("Given array doesn't containt '".self::$environment."' or 'all' environements", true, true);
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