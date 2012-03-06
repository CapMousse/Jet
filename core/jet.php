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
*   Jet class
*   The main core class
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/
final class Jet{
    /**
     * Contain the current instance of the core object
     * @var Jet
     */
    public static $instance = null;

    /**
     * Contain all action by listeners
     * @var array
     */
    public $actions = array();

    /**
     * Contain the global configuration object
     * @var array
     */
    public $global = array();

    /**
     * Contain the current used environment
     * @var String
     */
    public $environment;

    /**
     * Contain the routed app list
     * @var array
     */
    public $apps = array();

    /**
     * Contain the list of parsed $routes
     * @var array
     */
    public $routes = array();

    /**
     * Contain the Router object
     * @var Router
     */
    public $router = null;

    /**
     * Contain the list of required files
     * @var array
     */
    public $requires = array();

    /**
     * Contain the current sent URI on array format
     * @var array
     */
    public $uri_array = array();

    /**
     * Contain the current APP name
     * @var String|Null
     */
    public $app = null;

    /**
     * Contain vars of the setter/getter
     * @var array
     */
    public $infos = array();

    /**
     * Contain a list of insticiated controllers
     * @var array
     */
    private $controllerList = array();

    /**
     * Return the current jet instance
     * @static
     * @return Jet|null
     */
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
        
        if(isset($config['requires'])){
            $this->requires = array_merge($config['requires'], $this->requires);
        }
    }
     /**
     * merge array of 'all' environment and current environment
     * 
     * @param   $config  Object Config object
     * @return  array
     */
    public function mergeEnvironment($config){
        if(!call_user_func(array($config, $config->environment)) && !method_exists($config, 'all')){
            Log::save("Given config doesn't containt '".$this->environment."' or 'all' environements", Log::WARNING);
        }
        
        $returnArray = array();
        
        if(method_exists($config, 'all')){
            $returnArray = $config->all();
        }
        
        if($array = call_user_func(array($config, $config->environment))){
            $returnArray = array_merge_recursive($array, $returnArray);
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
        
        //parse all routes with curent URI
        $this->router = new Router();
        $this->router->launch();
        
        //parse and load needed files
        $this->requireFiles();

        //launch render
        $this->render();
    }

    /**
     * parse current path to be used by the router
     *
     * @access  private
     * @return  Array/null
     */
    private function parsePath(){
        // get current adresse path
        $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');

        // check if current path is not root url or core url, else return array of current route
        $this->uri_array = (trim($path, '/') != '' && $path != "/".SELF) ? explode('/', trim($path, '/')) : null;
    }

    /**
     * parse and load needed files
     * So obvious.
     * @param   null|string  $files  $file the fucking file name !
     * @param   null|string  $dir    the fucking dir where is the file name !
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
            if(is_file($dir.'requires/'.$file)){
                include($dir.'requires/'.$file);
            }elseif(is_file(PROJECT.'requires/'.$file)){
                include(PROJECT.'requires/'.$file);
            }else{
                Log::warning("Required file $file doens't exists");
            }
        }
    }
    
    /**
     * launch the render of the current page
     * If you read this, you loose the game
     * @return  void
     */
    
    private function render(){
        $apps = $this->apps;
        $response = HttpResponse::getInstance();

        //check if client don't ask a broken link
        if($this->get('askedRoute') === "notFound"){
            $response->setStatus(404);
        }

        $this->execute('beforeLaunchAction');

        $this->lauchController($apps);

        $this->execute('afterLaunchAction');

         // check if our app need to be rendered
        Log::save('Render layout');
        $this->execute('beforeRender');

        $template = $this->global['template'];
        $view = new $template();

        $body = $view->render();
        
        $this->execute('beforeMakeHttpResponse');
        Log::save('Render Http Response');
        $response->setBody($body);

        $this->execute('beforeSendHttpResponse');
        Log::save('Finish render');
        echo $response->send();
    }

    /**
     * Lauch all controllers
     * @param $apps array array of routed apps
     */
    private function lauchController($apps){
        $options = $this->options;

        foreach($apps as $app){
            $appName = $app[APP];
            $controllerName = $app[CONTROLLER];
            $action = $app[ACTION];

            if(!is_dir(APPS.$appName)){
                Log::fatal("App $appName doen't exists");
            }

            if(!is_file(APPS.$appName.DR.'controllers'.DR.lcfirst($controllerName).EXT)){
                Log::fatal("Controller $controllerName doen't exists");
            }

            //init controller
            if(!isset($this->controllerList[$controllerName])){
                include(APPS.$appName.DR.'controllers'.DR.lcfirst($controllerName).EXT);
                $controller = new $controllerName($appName);
                $this->controllerList[$controllerName] = $controller;
            }else{
                $controller = $this->controllerList[$controllerName];
            }

            if(method_exists($controller, 'before'.ucfirst($action))){
                $this->lauchAction($controller, 'before'.ucfirst($action), $options);
            }

            $this->lauchAction($controller, $action, $options);

            if(method_exists($controller, 'after'.ucfirst($action))){
                $this->lauchAction($controller, 'after'.ucfirst($action), $options);
            }
        }
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
                    call_user_func($action);
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
     * addAction add an action to the specified listener
     *
     * @access public
     * @param  $action {String} Listener name
     * @param  $class {Object} class name
     * @param  $method {String} method name
     * @param  $options {Array}  options name
     * @return Void
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
     * launch the specified action form class with sent options
     *
     * @access  private
     * @param   $class object : the object from the action
     * @param   $method string : the method to be launched
     * @param   array|bool $options
     *
     * @internal param array $option [optional] : the arguments for the method
     * @return  void
     */
    private function lauchAction($class, $method, $options = array()){
        Log::save('LauchAction : '.$method);
        
        // launch the asked action, with our options
        if($options)
            @call_user_func_array(array($class, $method), $options);
        else
            $class->$method();
    }

    /**
     * @param String $name
     * @param Mixed $value
     */
    public function set($name, $value){
        $this->infos[$name] = $value;
    }

    /**
     * @param String $name
     * @return Boolean|null
     */
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