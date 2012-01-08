<?php
/**
*   Jet
*   A lightweight and fast framework for developer who don't need hundred of files
*
*   @package  Jet
*   @author   Jérémy Barbe
*   @license  BSD
*   @link     https://github.com/CapMousse/Jet
*/

/**
*   ModelManager class
*   Simple class to load model
*
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*/
class ModelManager{
    public
        $jet,
        $models = array();

    function __construct(){
        $this->jet = Jet::getInstance();
    }


    public function load($file){
        $_currentApp = PROJECT.'apps/'.$this->jet->app;
        $_className = ucfirst($file);

        //Control if model file exists
        if(!isset($this->models[$_className])){
            if(!is_file($_currentApp.'models/'.$file.'.php')){
                Log::save("The asked model <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />", Log::FATAL);
                return false;
            }

            include($_currentApp.'models/'.$file.'.php');
            $this->models[$_className] = true;
        }

        Log::save('Model loaded : '.$file);

        //return the instantiate model
        return new $_className();
    }
}