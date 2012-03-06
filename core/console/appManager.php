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
 *   AppManager Class
 *
 *   @package Jet
 *   @author  Jérémy Barbe
 *   @license BSD
 *   @link     https://github.com/CapMousse/Jet
 */
class AppManager{

    /**
     * Init the AppManager
     * @param string $option
     * @param string $appName
     */
    public function __construct($option, $appName){

        //check the asked type of option
        switch($option){
            case 'create':
                $this->createApp($appName);
            break;

            case 'remove':
                $this->deleteApp($appName);
            break;
        }
    }

    /**
     * Create the asked APP
     * @param string $appName
     */
    private function createApp($appName){
        //Check if a app with the same name already exists
        if(is_dir(PROJECT.'apps'.DR.$appName)){
            print "App $appName already exists \n";
            exit(0);
        }

        //Make all app dir
        mkdir(PROJECT.'apps'.DR.$appName);
        mkdir(PROJECT.'apps'.DR.$appName.DR.'models');
        mkdir(PROJECT.'apps'.DR.$appName.DR.'views');
        mkdir(PROJECT.'apps'.DR.$appName.DR.'controllers');

        print "App $appName is created \n";
    }

    /**
     * Delete the asked APP
     * @param string $appName
     */
    private function deleteApp($appName){
        //Check if the asked app exists
        if(!is_dir(APPS.$appName)){
            print "App $appName don't exists \n";
            exit(0);
        }

        //Clean the database by removing linked app tables
        new TableManager('empty', 'app', $appName);

        //Delete all elements of the app
        $dir = PROJECT.'apps'.DR.$appName;
        $this->recursiveDelete($dir);

        print "App $appName deleted \n";
    }

    /**
     * Recursively delete all dir/files of the selected dir
     * @param string $dir
     */
    private function recursiveDelete($dir){
        if(is_dir($dir)){
            $objects = scandir($dir);

            foreach($objects as $element){
                if($element != "." && $element != ".."){
                    if(filetype($dir.''.DR.$element) == "dir"){
                        $this->recursiveDelete($dir.''.DR.$element);
                    }else{
                        unlink($dir.''.DR.$element);
                    }
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }
}