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
*   Controller abstract class
*   the controller model
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1.1
*/
abstract class Controller{
    
    public 
        $jet = null,
        $view,
        $response,
        $request,
        $models = array();

    /**
     * WARN ! If you need to create your own/self/personnal/other-type-of-reason-that-i-don't-know construct, your need to declare a parent::__construct() in
     * IF NOT, BLACKHOLE APPEAR !
     */
    function __construct(){
        $this->jet = Jet::getInstance();
        $this->view = new $this->jet->global['template']();
        $this->response = HttpResponse::getInstance();
        $this->request = new HttpRequest();
    }

    /**
     * load the asked model. If your are to dumb to understand, suicide yourself.
     * @param $file
     *
     * @internal param string $model name of the model file
     * @return   Model/false
     */   
    protected function loadModel($file){
        $_currentApp = PROJECT.'apps/'.$this->jet->app;
        $_className = ucfirst($file);
        
        //Control if model file exists
        if(!isset($this->models[$_className])){
            if(!is_file($_currentApp.'models/'.$file.'.php')){
                trigger_error("The asked model <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />", true, true);
                return false;
            }

            include($_currentApp.'models/'.$file.'.php');
            $this->models[$_className] = true;   
        }
        
        Log::save('Model loaded : '.$file);

        //return the intentiate model
        return new $_className();
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