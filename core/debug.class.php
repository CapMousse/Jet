<?php
/**
*	ShwaarkFramework
*	A lightweigth and fast framework for developper who don't need hundred of files
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.1
*/

/**
*	Debug class
*	Do you need to log something?
* 	
*	@package SwhaarkFramework
*	@author  Jérémy Barbe
*	@license BSD
*	@link 	 https://github.com/CapMousse/ShwaarkFramework
*	@version 1.2
*/
class debug{
    private static $markers = array();
    
    public static $start;
    public static $log_all;

    /**
     * log
     *
     * add a log data to the log pile
     *
     * @access	static method
     * @param	string	$data		data you want to log
     * @param   bool    $important  default : false, set the loged info as important
     * @param   bool    $crash      default : false, stop rendering
     * @return	void 
     */	
    public static function log($data, $important = false, $crash = false){
        $bk = debug_backtrace();
        
        $caller = (isset($bk[1]['class']) && $bk[1]['class'] === "Controller") ?
            substr($bk[1]['file'], strrpos($bk[1]['file'], "/") + 1) : 
            substr($bk[0]['file'], strrpos($bk[0]['file'], "/") + 1) ;
        
        $line = (isset($bk[1]['class']) && $bk[1]['class'] === "Controller") ? 
            $bk[1]['line'] : $bk[0]['line'];
        
        $line = $bk[1]['line'];
        $time = microtime() - self::$start;
        $data = $data.' '.$caller.' at line '.$line.' ('.$time.')';
        
        if(self::$log_all || $important){
            self::$markers[] = $data;
        }

        if($important){
            error_log($data);
        }
        
        if($crash && !Shwaark::$config['show_debug_log']){
            exit('Error detected, please alert the administrator');
        }
    }

    /**
     * displayLog
     *
     * show log pile
     *
     * @access	static method
     * @return	void 
     */
    public static function displayLog(){
        echo '<h1>DEBUG</h1><ul>';
        foreach(self::$markers as $marker){
            echo '<li>';
            print_r($marker);
            echo '</li>';
        }
        echo '</ul>';
    }
}
?>