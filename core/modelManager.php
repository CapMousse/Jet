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
    /**
     * The core current instance
     * @var Jet
     */
    private $jet;

    /**
     * The list of models already loaded
     * @var array
     */
    private static $models = array();

    /**
     * Get the Jet core instance
     * @param $appName string the current AppName
     */
    function __construct($appName){
        $this->jet = Jet::getInstance();
        $this->appName = $appName;
    }

    /**
     * Check if the model exist, load and instantiate him
     * @param String $file
     * @return Model
     */
    public function load($file){
        $file = lcfirst($file);
        $_currentApp = APPS.$this->appName.DR;
        $_className = ucfirst($file);

        //Control if model file exists
        if(!isset(self::$models[$_className])){
            if(!is_file($_currentApp.'models'.DR.$file.EXT) && !is_file(PROJECT.'models'.DR.$file.EXT)){
                Log::save("The asked model <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> <br />", Log::FATAL);
                return false;
            }

            if(is_file($_currentApp.'models'.DR.$file.EXT)){
                include($_currentApp.'models'.DR.$file.EXT);
            }else{
                include(PROJECT.'models'.DR.$file.EXT);
            }

            self::$models[$_className] = true;
        }

        Log::save('Model loaded : '.$file);

        //return the instantiate model
        return new $_className();
    }
}