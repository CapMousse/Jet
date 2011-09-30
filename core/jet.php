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
        $instance = null;
    
    public 
        $actions = array(),
        $global = array(),
        $environment,
        $apps = array(),
        $routes = array(),
        $router = null,
        $modules = array(),
        $requires = array(),
        $uri_array = array(),
        $app = null,
        $infos = array();
    
    public static function getInstance() {
        if(self::$instance === null){
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /**
     * Set the current used environment
     * @param string $environment
     * @return void
     */
    public function setEnvironment($environment){
        $this->environment = $environment;
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
            $this->global = $config['global'];
        }
        
        if(isset($config['apps'])){
            $this->apps = $config['apps'];
        }
        if(isset($config['routes'])){
            $this->routes = $config['routes'];
        }
        
        if(isset($config['orm'])){
            OrmConnector::$config = $config['orm'];
        }
        
        if(isset($config['modules'])){
            $this->modules = array_merge($config['modules'], $this->modules);
        }
        
        if(isset($config['requires'])){
            $this->requires = array_merge($config['requires'], $this->requires);
        }
    }
     /**
     * merge array of 'all' environment and current environment
     * 
     * @param   $array  array   the array with environment to be merge
     * @return  array
     */
    public function mergeEnvironment($array){        
        if(!isset($array[$this->environment]) && !isset($array['all'])){
            Log::save("Given array doesn't containt '".$this->environment."' or 'all' environements", Log::WARNING);
        }
        
        $returnArray = array();
        
        if(isset($array['all'])){
            $returnArray = $array['all'];
        }
        
        if(isset($array[$this->environment])){
            $returnArray = array_merge($array[$this->environment], $returnArray);
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
        $this->router = new Router();
        $this->router->launch();
        
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
        $this->uri_array = (trim($path, '/') != '' && $path != "/".SELF) ? explode('/', trim($path, '/')) : null;
    }

    /**
     * Define app asked by the current uri
     *
     * @return  string/false
     */
    private function defineApp(){
        $routes = array();
        $uri = $this->uri_array;
        
        if(!is_array($this->apps) || count($this->apps) == 0){
            Log::save('Missing routes array in project/config.php', Log::FATAL);
            return;
        }
        
        if(!isset($this->apps)){
            Log::save('No default app routes defined in config/routes.php', Log::FATAL);
            return;
        }

        if(!is_array($uri)){
            $this->app = $this->apps['default'].'/';
        }

        if(isset($this->apps['/'.$uri[0]])){
            $app = $this->apps['/'.$uri[0]].'/';
            array_splice($uri, 0, 1);
        }else{
            $app = $this->apps['default'].'/';
        }

        $this->app = $app;
    }
    
    /**
     * @return void
     */
    private function getAppConfig(){        
        if(!$this->app){
            return false;
        }
        
        if(!is_file(PROJECT.'apps/'.$this->app.'config.php')){
            return false;
        }
        
        include(PROJECT.'apps/'.$this->app.'config.php');
        
        $this->setConfig($config);
    }
    
    /**
     * parse and load needed files
     * So obvious. 
     * @return  void
     */
    public function requireFiles($files = null, $dir = null){
        if(null === $files){
            $files = $this->requires;
        }
        
        if(null === $dir){
            $dir = PROJECT.'requires/';
        }
        
        if(!is_array($files)){
            $files = array($files);
        }
        
        
        foreach($files as $file){
            if(is_file($dir.$file)){
                include($dir.$file);
            }else{
                Log::warning('Required file '.$dir.$file.' doens\'t exists');
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
        
        foreach($this->modules as $moduleName){
            if(is_dir(PROJECT.'modules/'.$moduleName)){

                //include all nececary files
                foreach(glob(PROJECT.'modules/'.$moduleName.'/*.php') as $file)
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
     * If you read this, you loose
     * @return  void
     */
    
    private function render(){
        $_currentApp = PROJECT.'apps/'.$this->app;
        $_currentController = $this->controller;
        $_currentAction = $this->action;
        $_currentOptions = $this->options;
        $_currentModules = $this->modules;
        
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
        
        $this->execute('beforeLaunchAction');
        
        if(method_exists($app, 'before'.ucfirst($_currentAction)))
            $this->lauchAction($app, 'before'.ucfirst($_currentAction), $_currentOptions);
        
        $this->execute('launchAction');
        $this->lauchAction($app, $_currentAction, $_currentOptions);
        
        if(method_exists($app, 'after'.ucfirst($_currentAction)))
            $this->lauchAction($app, 'after'.ucfirst($_currentAction), $_currentOptions);

         // check if our app need to be rendered
        Log::save('Render layout');
        $this->execute('beforeRender');
        $body = $app->view->render();
        
        $this->execute('beforeSendHttpResponse');        
        Log::save('Render Http Response');
        $app->response->setBody($body);
        
        Log::save('Finish render');
        echo $app->response->send();        
    }
    
    /**
     * execute all fake listener bind to asked action
     * 
     * @access public
     * @param  string $actionName
     * @return void
     */
    public function execute($actionName){
        if(isset($this->actions[$actionName])){
            $actions = $this->actions[$actionName];
            
            foreach($actions as $action){
                if(!is_array($action)){
                    $action();
                }else{
                    $class = $action['class'];
                    $method = $action['method'];
                    $options = isset($action['options']) ? $action['options'] : false;
                    
                    $this->lauchAction($class, $method, $options);
                }
            }
        }
    }
    
    /**
     * addAction add an action to the specified "listener"
     * 
     * @access public
     * @param  string $action the listener
     * @param  object $class the object from the action
     * @param  string $method  the method to be launched
     * @param  array  $option the arguments for the method
     * @return void
     */
    public function addAction($action, $class, $method = null, $options = FALSE){
        Log::save("Added a listerner at $action", Log::INFO);
        if($method === NULL){
            $this->actions[$action][] = $class;
        }else{
            $this->actions[$action][] = array(
                'class' => $class,
                'method' => $method,
                'options' => $options
            );
        }
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
    
    public function set($name, $value){
        $this->infos[$name] = $value;
    }
    
    public function get($name){      
        return isset($this->infos[$name]) ? $this->infos[$name] : false;
    }
    
    public function __set($name, $value){
        $this->set($name, $value);
    }
    
    public function __get($name){
        return $this->get($name);
    }
}