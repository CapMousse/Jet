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
*   Bridge class
* 
*   Definition of the bridge between the framework & the selected view engine
*   Inspired of Tamed Framework project of Taluu 
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/


abstract class ViewBridge{
    const
        INFO_NAME = 1,
        INFO_VERSION = 2,
        INFO_ENGINE = 3,
        INFO_ALL = 4;
    
    protected function _assign(){}
    
    abstract public function bind($name, &$value);
    
    public function load(){}
    
    final public function render(){
        $this->_assign();
        return $this->_render();
    }
    
    abstract protected function _render();
    
    abstract public function getInfo($infos = self::INFO_ALL);
}
?>