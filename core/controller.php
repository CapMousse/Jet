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
*   @version 1
*/
abstract class Controller{
    public 
        $view,
        $response,
        $request,
        $status = HttpResponse::OK,
        $models = array();

    //if you want to made your own __construct, add parent::__construct() to your code
    function __construct(){
        $this->view = new Jet::$config['template']();
        $this->response = new HttpResponse($this->status);
        $this->request = new HttpRequest();
    }

    /**
     * loadModel
     *
     * load the asked model. 
     *
     * @access   protected
     * @param   string   $file      name of the model file
     * @param    bool    $factoring    do your want to return a factory model? - default true
     * @return   false/Model Name/Factory model 
     */   
    protected function loadModel($file, $factoring = true){
        $_currentApp = PROJECT.'apps/'.Jet::get('app');
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
        
        Debug::log('Model loaded : '.$file);

        //return the intentiate model
        return new $_className();
    }

    /**
     * loadController
     *
     * load the asked controller. 
     * 
     * SUP DAWG. I HEARD YOU LOVE CONTROLLER, SO WE PUT A CONTROLLER IN YOUR CONTROLLER SO YOU CAN CONTROL WHILE YOU CONTROL
     * 
     *
     * @access   protected
     * @param   string   $file      name of the controller file
     * @return   false/object
     */   
    protected function loadController($file){
        $_currentApp = PROJECT.'apps/'.Jet::get('app');
        
        if(!is_file($_currentApp.'controllers/'.$file.'.php')){
            Debug::log("The asked controller <b>$file</b> doesn't exists in <b>".get_called_class()."</b> <br />", true, true);
            return false;
        }

        include($_currentApp.'controllers/'.$file.'.php');

        Debug::log('Controller loaded : '.$file);

        $controller = ucfirst($file);
        return new $controller();
    }

    /**
     * loadModule
     *
     * load the asked module with all attached files. 
     *
     * @access   protected
     * @param   array/string   $names      names of all wanted modules
     * @return   void
     */   
    protected function loadModule($name){
        //check if we have a array of name or convert it to array
        if(!is_string($name)) return;
        
        //check if module and module conf exists
        if(is_dir(PROJECT.'modules/'.$name)){

            //include all nececary files
            foreach(glob(PROJECT.'modules/'.$name.'/*.php') as $file)
                include($file);
            
            $name = ucfirst($name);
            $this->{$name} = new $name();

            Debug::log('Module loaded : '.$name);
        }
    }
}
?>