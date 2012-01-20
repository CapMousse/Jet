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
*   Log class
*   Do you need to log something?
*
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/
class Log{
    const 
        INFO = 1,
        WARNING = 2,
        FATAL = 3;

    /**
     * The current instance of the core
     * @var Jet
     */
    public static $jet = null;

    /**
     * List of declared markers
     * @var array
     */
    public static $markers = array();

    /**
     * Start time
     * @var int
     */
    public static $start = 0;

    /**
     * List of message type
     * @var array
     */
    public static $messages = array(
        1 => 'Information',
        2 => 'Warning',
        3 => 'Fatal'
    );

    /**
     * Create a log error
     *
     * @param   string      $msg    msg you want to log
     * @param   int    $type   error type (INFO, WARNING, FATAL)
     * @return  null
     */
    public static function save($msg, $type = self::INFO){
        if(self::$jet === null){
            self::$jet = Jet::getInstance();
        }
        
        $bk = Debug_backtrace();

        foreach($bk as $trace){
            if(isset($trace['class']) && $trace['class'] != __CLASS__ && $trace['class'] != "Controller"){
                break;
            }
        }

        $url = HttpRequest::getQueryString();
        
        $caller = substr($trace['file'], strrpos($trace['file'], "/") + 1);
        $line = $trace['line'];
        $obj = (isset($trace['type']) ? $trace['class'].$trace['type']:'').$trace['function'];
        $perf = microtime() - self::$start;
        $time = new DateTime('now', new DateTimeZone('UTC'));
        
        $log = sprintf('%1s - %2s : %3s - %4s() - url %5s - file %6s - line %7s - time %8s', $time->format('D, d M Y H:i:s'), self::$messages[$type], $msg, $obj, $url, $caller, $line, $perf);

        self::$markers[] = $log;
        
        $debugLevel = isset(self::$jet->global['log']) ? self::$jet->global['log'] : 0;
        
        if($debugLevel == $type || $debugLevel == 0){
            switch($type){
                case self::FATAL:
                    $file = PROJECT.'logs/fatal.log';
                break;

                case self::WARNING:
                    $file = PROJECT.'logs/warning.log';
                break;

                case self::INFO:
                default:
                    $file = PROJECT.'logs/info.log';
                break;
            }

            error_log($log."\n", 3, $file);
        }
        
        if($type === self::FATAL){
            exit($log);
        }
    }

    /**
     * Set a info log
     * @param   string|array $msg 
     * @return  null
     */
    public static function info($msg){
        if(is_array($msg)){
            $msg = join(' ', $msg);
        }
        
        return self::save($msg, self::INFO);
    }

    /**
     * Set a warning log
     * @param   string|array $msg
     * @return  null
     */
    public static function warning($msg){
        if(is_array($msg)){
            $msg = join(' ', $msg);
        }
        
        return self::save($msg, self::WARNING);
    }

    /**
     * Set a fatal log
     * @param   string|array $msg
     * @return  null
     */
    public static function fatal($msg){
        if(is_array($msg)){
            $msg = join(' ', $msg);
        }
        
        return self::save($msg, self::FATAL);
    }
}
?>