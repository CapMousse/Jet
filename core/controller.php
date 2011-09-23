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
     * load the asked model. 
     * @param    string $model      name of the model file
     * @return   Model/false
     */   
    protected function loadModel($file){
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
     * load the asked controller. 
     * SUP DAWG. I HEARD YOU LOVE CONTROLLER, SO WE PUT A CONTROLLER IN YOUR CONTROLLER SO YOU CAN CONTROL WHILE YOU CONTROL
     * @param    string   $file      name of the controller file
     * @return   Controller/false
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
     * load the asked module
     * @param   string   $names      names of all wanted modules
     * @return  void
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
            if(!class_exists($moduleName)){
                Debug::log("Module {$moduleName} don't have class with same name", true);
            }else{
                $this->{$name} = new $name();
            }
            
            Debug::log('Module loaded : '.$name);
        }
    }
}
?>