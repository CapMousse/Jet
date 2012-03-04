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
        $csrfChecked = false;

    /**
     * Current appName
     * @var string
     */
    protected static $appName;

    /**
     * @param $appName string the current app name
     */
    public function __construct($appName){
        $this->checkCSRF();

        if(!is_null($appName)){
            self::$appName = $appName;
        }
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
        if(null === $this->csrfName && !isset($_SESSION['CSRF']) || $_SESSION['CSRF_TIME'] < microtime()){
            $time = microtime()+ ($time*60*1000);
            $token = sha1($time+microtime());
            $name = md5(microtime());

            $_SESSION['CSRF'] = $token;
            $_SESSION['CSRF_TIME'] = $time;
            $_SESSION['CSRF_NAME'] = $name;
            $this->csrfToken = $token;
            $this->csrfName = $name;
        }else{
            $this->csrfToken = $_SESSION['CSRF'];
            $this->csrfName = $_SESSION['CSRF_NAME'];
        }

        return '<input type="hidden" name="'.$this->csrfName.'" value="'.$this->csrfToken.'" />';
    }

    final public function checkCSRF(){
        if(Validation::method() == "POST" && isset($_SESSION['CSRF']) && !self::$csrfChecked){
            $csrfName = $_SESSION['CSRF_NAME'];
            $csrfTime = $_SESSION['CSRF_TIME'];
            $token = $_SESSION['CSRF'];

            if(!isset($_POST[$csrfName]) || $_POST[$csrfName] != $token || $csrfTime < microtime()){
                Log::save('CSRF attack', Log::WARNING);

                $response = HttpResponse::getInstance();
                $response->redirect(HttpRequest::getRoot().'error');
            }

            self::$csrfChecked = true;
        }
    }
}
?>