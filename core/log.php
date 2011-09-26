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
*   Log class
*   Do you need to log something?
*
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/
class Log{
    const 
        INFO = 1,
        WARNING = 2,
        FATAL = 3;
        
    public static
        $markers = array(),
        $start = 0,
        $messages = array(
            1 => 'Information',
            2 => 'Warning',
            3 => 'Fatal'
        );

    /**
     * Create a log error
     * @param    string   $msg      msg you want to log
     * @param    bool     $type     error type (INFO, WARNING, FATAL)
     * @return   boolean
     */
    public static function save($msg, $type = self::INFO){        
        $bk = Debug_backtrace();

        foreach($bk as $trace){
            if(isset($trace['class']) && $trace['class'] != __CLASS__ && $trace['class'] != "Controller"){
                break;
            }
        }

        $url = HttpRequest::getQueryString();
        $url = $url == "" ? "/" : $url;
        
        $caller = substr($trace['file'], strrpos($trace['file'], "/") + 1);
        $line = $trace['line'];
        $obj = (isset($trace['type']) ? $trace['class'].$trace['type']:'').$trace['function'];
        $perf = microtime() - self::$start;
        $time = new DateTime('now', new DateTimeZone('UTC'));
        
        $log = sprintf('%1s - %2s : %3s - %4s() - url %5s - file %6s - line %7s - time %8s', $time->format('D, d M Y H:i:s'), self::$messages[$type], $msg, $obj, $url, $caller, $line, $perf);

        self::$markers[] = $log;
        
        $debugLevel = isset(Jet::$global['log']) ? Jet::$global['log'] : 0;
        
        if($debugLevel == $type || $debugLevel == 0){
            error_log($log."\n", 3, PROJECT.'logs/errors.log');
        }
        
        if($type === 3){
            exit($log);
        }
    }

    /**
     * Set a info log
     * @param  string/array $msg
     * @return boolean
     */
    public static function info($msg){
        if(is_array($msg)){
            $msg = joint(' ', $msg);
        }
        
        return self::save($msg, self::INFO);
    }

    /**
     * Set a warning log
     * @param  string/array $msg
     * @return boolean
     */
    public static function warning($msg){
        if(is_array($msg)){
            $msg = joint(' ', $msg);
        }
        
        return self::save($msg, self::WARNING);
    }

    /**
     * Set a fatal log
     * @param  string/array $msg
     * @return boolean
     */
    public static function fatal($msg){
        if(is_array($msg)){
            $msg = joint(' ', $msg);
        }
        
        return self::save($msg, self::FATAL);
    }
}
?>