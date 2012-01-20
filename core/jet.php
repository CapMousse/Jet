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
     * Contain the app list
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
            $returnArray = array_merge_recursive($array[$this->environment], $returnArray);
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
        
        //parse and load needed files
        $this->requireFiles();

        //launch render
        return $this->render();
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
     * Define app asked by the current uri
     *
     * @return  string/false
     */
    private function defineApp(){
        $uri = $this->uri_array;
        
        if(!is_array($this->apps) || count($this->apps) == 0){
            Log::save('Missing routes array in project/config.php', Log::FATAL);
            return;
        }
        
        if(!isset($this->apps)){
            Log::save('No default app routes defined in project/config.php', Log::FATAL);
            return;
        }

        if(!is_array($uri)){
            $this->app = $this->apps['default'].'/';
        }

        if(isset($this->apps[$uri[0]])){
            $app = $this->apps[$uri[0]].'/';
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
            return;
        }
        
        if(!is_file(PROJECT.'apps/'.$this->app.'config.php')){
            return;
        }

        include(PROJECT.'apps/'.$this->app.'config.php');
        
        $this->setConfig($config);
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
            if(is_file(PROJECT.'apps/'.$this->app.$file)){
                include(PROJECT.'apps/'.$this->app.$file);
            }elseif(is_file($dir.$file)){
                include($dir.$file);
            }else{
                Log::warning('Required file '.$dir.$file.' doens\'t exists');
            }
        }
    }
    
    /**
     * launch the render of the current page
     * If you read this, you loose the game
     * @return  void
     */
    
    private function render(){
        $_currentApp = PROJECT.'apps/'.$this->app;
        $_currentController = $this->controller;
        $_currentAction = $this->action;
        $_currentOptions = $this->options;
        
        // include the asked controller            
        Log::save('Asked controller and action : '.$_currentController.'->'.$_currentAction);            

        if(!is_file($_currentApp.'controllers/'.$_currentController.'.php')){
            Log::save('Controller file '.$_currentController.' does not exists on '.$_currentApp.'controllers/', Log::FATAL);
        }

        include($_currentApp.'controllers/'.$_currentController.'.php');

        $controller = ucfirst($_currentController);

        //check if controller class existe
        if(!class_exists($controller)){
            Log::save('Controller class '.$controller.' is not declared on '.$_currentApp.'controllers/'.$_currentController.'.php', Log::FATAL);
        }

        //init controller
        $app = new $controller();

        //check if client don't ask a broken link
        if($this->get('askedRoute') === 404){
            $app->response->setStatus(404);
        }

        $this->execute('beforeLaunchAction');
        $this->execute('before'.ucfirst($_currentAction));

        $this->lauchAction($app, $_currentAction, $_currentOptions);

        $this->execute('after'.ucfirst($_currentAction));
        $this->execute('afterLaunchAction');

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
     * @param   array $options
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
     * check if file exist in project dir or app dir
     *
     * @access  private
     * @param   $file   string  file to be check
     * @return  array|bool|null
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