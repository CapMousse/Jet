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
        $global = array(),
        $environment,
        $apps = array(),
        $routes = array(),
        $modules = array(),
        $requires = array(),
        $uri_array = array(),
        $app = null,
        $infos = array();

    /**
     * @param string $environement 
     */
    function __construct($environement) {
        self::$environment = $environement;
    }
    
    /**
     * set and merge config type
     * 
     * @param array $config
     * @return void
     */
    public function setConfig($config){
        $config = $this->mergeEnvironment($config);
        
        if(isset($config['global'])){
            self::$global = $config['global'];
        }
        
        if(isset($config['apps'])){
            self::$apps = $config['apps'];
        }
        if(isset($config['routes'])){
            self::$routes = $config['routes'];
        }
        
        if(isset($config['orm'])){
            OrmConnector::$config = $config['orm'];
        }
        
        if(isset($config['modules'])){
            self::$modules = array_merge($config['modules'], self::$modules);
        }
        
        if(isset($config['requires'])){
            self::$requires = array_merge($config['requires'], self::$requires);
        }
    }
     /**
     * merge array of 'all' environment and current environment
     * 
     * @param   $array  array   the array with environment to be merge
     * @return  array
     */
    public function mergeEnvironment($array){        
        if(!isset($array[self::$environment]) && !isset($array['all'])){
            Log::save("Given array doesn't containt '".self::$environment."' or 'all' environements", Log::WARNING);
        }
        
        $returnArray = array();
        
        if(isset($array['all'])){
            $returnArray = $array['all'];
        }
        
        if(isset($array[self::$environment])){
            $returnArray = array_merge($array[self::$environment], $returnArray);
        }
        
        return $returnArray;
    }
    
    /**
     * launch the framework
     *
     * @return   void 
     */
    public function run(){        
        /**********************/
        /**** Parse routes ****/
        /**********************/
        
        Log::save('Begining route parsing');

        $this->parsePath();
        $this->defineApp();
        $this->getAppConfig();
        
        //parse all routes with curent URI
        Router::launch();
        
        //parse and load needed files and modules
        $this->requireFiles();
        $this->requireModules();

        //launch render
        return $this->render();
    }

    /**
     * parse current path to be used by the router
     *
     * @access  private static function
     * @return  Array/null
     */
    private function parsePath(){
        // get current adresse path
        $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');

        // check if current path is not root url or core url, else return array of current route
        self::$uri_array = (trim($path, '/') != '' && $path != "/".SELF) ? explode('/', trim($path, '/')) : null;
    }

    /**
     * Define app asked by the current uri
     *
     * @return  string/false
     */
    private function defineApp(){
        $routes = array();
        $uri = self::$uri_array;
        
        if(!is_array(self::$apps) || count(self::$apps) == 0){
            Log::save('Missing routes array in project/config.php', Log::FATAL);
            return;
        }
        
        if(!isset(self::$apps)){
            Log::save('No default app routes defined in config/routes.php', Log::FATAL);
            return;
        }

        if(!is_array($uri)){
            self::$app = self::$apps['default'].'/';
        }

        if(isset(self::$apps['/'.$uri[0]])){
            $app = self::$apps['/'.$uri[0]].'/';
            array_splice($uri, 0, 1);
        }else{
            $app = self::$apps['default'].'/';
        }

        self::$app = $app;
    }
    
    /**
     * @return void
     */
    private function getAppConfig(){        
        if(!self::$app){
            return false;
        }
        
        if(!is_file(PROJECT.'apps/'.self::$app.'config.php')){
            return false;
        }
        
        include(PROJECT.'apps/'.self::$app.'config.php');
        
        $this->setConfig($config);
    }
    
    /**
     * parse and load needed files
     * 
     * @return  void
     */
    private function requireFiles(){        
        foreach(self::$requires as $file){
            if(is_file(PROJECT.self::$app.$file)){
                include(PROJECT.self::$app.$file);
            }
        }
    }
    
    /**
     * parse and load needed modules
     * 
     * @return  void
     */
    private function requireModules(){
        $modules = array();
        
        foreach(self::$modules as $moduleName){
            if(is_dir(MODULES.$moduleName)){

                //include all nececary files
                foreach(glob(MODULES.$moduleName.'/*.php') as $file)
                    include($file);

                $name = ucfirst($moduleName);
                
                if(!class_exists($moduleName)){
                    Log::save("Module {$moduleName} don't have class with same name", Log::WARNING);
                    continue;
                }
           
                $modules[$name] = new $name();

                Log::save('Module loaded : '.$name);
            }
        }
        
        $this->modules = $modules;
    }
    
    /**
     * launch the render of the current page
     * 
     * @return  void
     */
    
    private function render(){
        $_currentApp = PROJECT.'apps/'.self::$app;
        $_currentController = $this->controller;
        $_currentAction = $this->action;
        $_currentOptions = $this->options;
        $_currentModules = self::$modules;
        
        // include the asked controller            
        Log::save('Asked controller and action : '.$_currentController.'->'.$_currentAction);            

        if(!is_file($_currentApp.'controllers/'.$_currentController.'.php')){
            Log::save('Controller file '.$_currentController.' does not exists on '.$_currentApp.'controllers/', Log::FATAL);
        }

        include($_currentApp.'controllers/'.$_currentController.'.php');
            
        
        // create the asked controller
        $controller = ucfirst($_currentController);
        
        if(!class_exists($controller)){
            Log::save('Controller class '.$controller.' is not declared on '.$_currentApp.'controllers/'.$_currentController.'.php', Log::FATAL);
        }
        
        $app = new $controller();
        
        if(count($_currentModules) > 0){
            foreach($_currentModules as $name => $object){
                $app->{$name} = $object;
            }
        }
        
        if($this->get('askRoute') === 404){
            $app->response->setStatus(404);
        }
        
        if(method_exists($app, 'before'.ucfirst($_currentAction)))
            $this->lauchAction($app, 'before'.ucfirst($_currentAction), $_currentOptions);
        
        $this->lauchAction($app, $_currentAction, $_currentOptions);
        
        if(method_exists($app, 'after'.ucfirst($_currentAction)))
            $this->lauchAction($app, 'after'.ucfirst($_currentAction), $_currentOptions);

        
        
        // check if our app need to be rendered
        Log::save('Render layout');
        $body = $app->view->render();
        
        Log::save('Render Http Response');
        $app->response->setBody($body);
        
        Log::save('Finish render');
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
    private function lauchAction($class, $method, $options = false){
        Log::save('LauchAction : '.$method);
        
        // lauch the asked action, with our options
        if($options)
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
    private function checkFile($file){
        $return = null;
        
        if(is_file(PROJECT.$file)){
            $return = array($file, null);
        }
        
        if(is_file(PROJECT.'apps/'.$this->get('app').$file)){
            $return = array(PROJECT.'apps/'.$this->get('app').$file, PROJECT.'apps/'.$this->get('app'));
        }
        
        return is_null($return) ? false : $return;
    }
    
    public static function set($name, $value){
        self::$infos[$name] = $value;
    }
    
    public static function get($name){      
        return isset(self::$infos[$name]) ? self::$infos[$name] : false;
    }
    
    public function __set($name, $value){
        self::set($name, $value);
    }
    
    public function __get($name){
        return self::get($name);
    }
}