<?php
/**
*   ShwaarkFramework
*   A lightweigth and fast framework for developper who don't need hundred of files
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/ShwaarkFramework
*   @version 0.3
*/

/**
*   Debug class
*   Do you need to log something?
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/ShwaarkFramework
*   @version 1.2
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
     * @access   static method
     * @param   string   $data      data you want to log
     * @param   bool    $important  default : false, set the loged info as important
     * @param   bool    $crash      default : false, stop rendering
     * @return   void 
     */   
    public static function log($data, $important = false, $crash = false){
        $bk = debug_backtrace();

        foreach(debug_backtrace() as $file){
            if(isset($file['class']) && $file['class'] != "debug"){
                break;
            }
        }
        
        $caller = substr($file['file'], strrpos($file['file'], "/") + 1);
        
        $line = $file['line'];
        $time = microtime() - self::$start;
        $data = "{$data} <em>{$caller} at line {$line} ({$time})</em>";
        
        self::$markers[] = array(
            $important,
            $data
        );

        if($important){
            error_log($data);
        }
        
        if($crash){
            exit("<html><body><h1>Error detected, please alert the administrator</h1><hr><p>{$data}</p></body></html>");
        }
    }

    /**
     * displayLog
     *
     * show log pile
     *
     * @access   static method
     * @return   void 
     */
    public static function displayLog(){
        echo '<h1>DEBUG</h1><ul>';
        foreach(self::$markers as $marker){
            if(!$marker[0] && !self::$log_all){
                continue;
            }

            echo '<li>';
            print_r($marker[1]);
            echo '</li>';
        }
        echo '</ul>';
    }
}
?>