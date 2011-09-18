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
*   HTTP Header class
*   Create header the simple way
*    
*   @package    Jet
*   @author     Jérémy Barbe
*   @license    BSD
*   @link       https://github.com/CapMousse/Jet
*   @version    1
*/

class HttpHeader{            
    protected 
        $header = null,
        $value = array(),
        $erase = false,
        $sent = false;        
    
    /*
     * Header constructor
     * 
     * @param   string $header    type of header (Cache-Control, expire...)
     * @param   string $value     value of the header
     * @param   bool   $erase     erase header if already exists
     * @param   int    $code      current page status
     */
    function __construct($header, $value = null, $erase = false){
        $this->header = $header;
        $this->value[$value] = 1;
        $this->erase = $erase;
       Debug::log("Set header ".$header." : ".$value);
    }
    
    /*
     * addValue
     * 
     * @param   string $value    add a value to the header
     */
    public function addValue($value){
        $this->value[$value] = 1;
    }
    
    /*
     * removeValue
     * 
     * @param   string $value    remove a value from the header
     */
    public function removeValue($value){
        unset($this->value[$value]);
    }
    
    /*
     * send
     * 
     * send the current header
     */
    public function send(){
       if($this->sent){
           return;
       }
       
       $header = count($this->value) === 0 ? $this->header : $this->header.": ".join(",", array_keys($this->value));
       header($header, $this->erase);
       
       $this->sent = true;
   }
}
?>
