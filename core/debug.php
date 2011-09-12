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
*   Debug class
*   Do you need to log something?
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/
class Debug{
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
        if(!isset(self::$start)){
            self::$start = microtime();
        }
        
        $bk = Debug_backtrace();
        
        if(strpos($bk[0]['file'], 'controller.php') !== FALSE){
            $file = $bk[1];
        }else{
            $file = $bk[0];
        }
        
        $caller = substr($file['file'], strrpos($file['file'], "/") + 1);
        $line = $file['line'];
        $time = microtime() - self::$start;
        $data = array($data, $caller, $line, $time);
        
        self::$markers[] = array(
            $important,
            $data
        );

        if($important){
            error_log($data[0]." ".$data[1]." ".$data[2]." (".$data[3].")");
        }
        
        if($crash){
            ob_end_clean ();
            exit("<html><body><h1>Error detected, please alert the administrator</h1><hr><p>".$data[0]." <em>line ".$data[2]."</em>  file <strong>".$data[1]." </strong> (".$data[3].")</p></body></html>");
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
        echo '<div id="DebugButton" style="background: black; color: white; cursor: pointer; padding: 2px 5px; font: 12px arial; position: fixed; top: 0px; right: 0px">Show Debug log</div>';
        echo '<div id="DebugBar" style="display: none; position: fixed; top: 18px; right: 0px; background: #eee; border: 1px solid #666; padding: 5px; max-height: 300px; overflow: auto;">';
        foreach(self::$markers as $marker){
            if(!$marker[0] && !self::$log_all){
                continue;
            }
            
            $data = $marker[1];

            echo $data[0]." <em>line ".$data[2]."</em>  file <strong>".$data[1]." </strong> (".$data[3].")<br />";
        }
        echo '</div>';
        echo '<script>var a = document.getElementById("DebugBar"), b = document.getElementById("DebugButton"); b.onclick = function(e){ if(a.style.display == "none") a.style.display = "block"; else a.style.display = "none"; }</script>';
    }
}
?>