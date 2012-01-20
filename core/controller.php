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
*   Controller abstract class
*   the controller model
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/
abstract class Controller{

    /**
     * The jet core current instance
     * @var Jet
     */
    public $jet = null;

    /**
     * Contain the current instance of the template manager
     * @var ViewBridge
     */
    public $view;

    /**
     * The current instance of the HttpResponse object
     * @var HttpResponse
     */
    public $response;

    /**
     * @var \HttpRequest
     */
    public $request;

    /**
     * @var \ModelManager
     */
    public $model;

    /**
     * WARN ! If you need to create your own/self/personnal/other-type-of-reason-that-i-don't-know construct, your need to declare a parent::__construct() in
     * IF NOT, BLACKHOLE APPEAR !
     */
    function __construct(){
        $this->jet = Jet::getInstance();
        $this->view = new $this->jet->global['template']();
        $this->response = HttpResponse::getInstance();
        $this->request = new HttpRequest();
        $this->model = new ModelManager();
    }

    /**
     * load the asked controller. 
     * SUP DAWG. I HEARD YOU LOVE CONTROLLER, SO WE PUT A CONTROLLER IN YOUR CONTROLLER SO YOU CAN CONTROL WHILE YOU CONTROL
     * @param    string   $file      name of the controller file
     * @return   Controller/false
     */   
    protected function loadController($file){
        $_currentApp = PROJECT.'apps/'.$this->jet->app;
        
        if(!is_file($_currentApp.'controllers/'.$file.'.php')){
            Log::save("The asked controller <b>$file</b> doesn't exists in <b>".get_called_class()."</b> <br />", Log::FATAL);
            return false;
        }

        include($_currentApp.'controllers/'.$file.'.php');

        Log::save('Controller loaded : '.$file);

        $controller = ucfirst($file);
        return new $controller();
    }

    /**
     * load requested files
     * We need to go deerper
     * @param $files
     *
     * @internal param $array /string $files
     */
    public function requireFiles($files){
        $this->jet->requireFiles($files, PROJECT.'apps/'.$this->jet->app);
    }
}
?>