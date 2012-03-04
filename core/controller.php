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
     * @var ViewJet
     */
    public $view;

    /**
     * The current instance of the HttpResponse object
     * @var HttpResponse
     */
    public $response;

    /**
     * @var HttpRequest
     */
    public $request;

    /**
     * @var \ModelManager
     */
    public $model;

    /**
     * @var string
     * the current app name
     */
    protected $appName;

    /**
     * WARN ! If you need to create your own/self/personnal/other-type-of-reason-that-i-don't-know construct, your need to declare a parent::__construct() in
     * IF NOT, BLACKHOLE APPEAR !
     * @param $appName string
     *        the current app name of the controller
     */
    function __construct($appName){
        $this->jet = Jet::getInstance();
        $this->appName = $appName;

        $template = $this->jet->global['template'];

        $this->view = new $template($appName);
        $this->response = HttpResponse::getInstance();
        $this->request = new HttpRequest();
        $this->model = new ModelManager($appName);
    }

    /**
     * load requested files
     * We need to go deerper
     * @param $files
     *
     * @internal param $array /string $files
     */
    public function requireFiles($files){
        $this->jet->requireFiles($files, APPS.$this->appName.DR);
    }
}
?>