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
*   Bridge class
* 
*   Definition of the bridge between the framework & the selected view engine
*   Inspired of Tamed Framework project of Taluu 
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/


abstract class ViewBridge{
    const
        INFO_NAME = 1,
        INFO_VERSION = 2,
        INFO_ENGINE = 3,
        INFO_ALL = 4;

    private
        $csrfToken = null,
        $csrfName = null;

    private static
        $instance = array();


    public static function getInstance() {
        $class = get_called_class();
        if (!isset(self::$instance[$class])) {
            self::$instance[$class] = new $class();
        }
        return self::$instance[$class];
    }

    public function __construct(){
        $this->checkCSRF();
    }
    
    protected function _assign(){}
    
    final public function render(){
        $this->_assign();
        return $this->_render();
    }
    
    abstract protected function _render();
    
    abstract public function getInfo($infos = self::INFO_ALL);
    
    abstract public function bind($name, &$value);
    
    abstract public function __get($name);
    
    abstract public function __set($name, $value);

    /**
     * Prevent Cross-Site Request Forgery
     * @param int $time
     *          time in minutes before token destroy
     * @return string
     */
    final public function getCSRF($time = 5){
        if(null === $this->csrfName){
            $time = microtime()+ ($time*60*1000);
            $token = sha1($time+microtime());
            $name = md5(microtime());

            $_SESSION['CSRF'] = $token;
            $_SESSION['CSRF_TIME'] = $time;
            $_SESSION['CSRF_NAME'] = $name;
            $this->csrfToken = $token;
            $this->csrfName = $name;
        }

        return '<input type="hidden" name="'.$this->csrfName.'" value="'.$this->csrfToken.'" />';
    }

    final public function checkCSRF(){
        if(Validation::method() == "POST" && isset($_SESSION['CSRF'])){
            $csrfName = $_SESSION['CSRF_NAME'];
            $csrfTime = $_SESSION['CSRF_TIME'];
            $token = $_SESSION['CSRF'];

            if(!isset($_POST[$csrfName]) || $_POST[$csrfName] != $token || $csrfTime < microtime()){
                Log::save('CSRF attack', Log::FATAL);
            }
        }
    }
}
?>