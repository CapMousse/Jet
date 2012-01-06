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
*   HTTP Header class
*   Create header the simple way
*    
*   @package    Jet
*   @author     Jérémy Barbe
*   @license    BSD
*   @link       https://github.com/CapMousse/Jet
*
*/

class HttpHeader{            
    protected 
        $header = null,
        $value = array(),
        $erase = false,
        $sent = false;

    /**
     * Header constructor
     *
     * @param   string $header    type of header (Cache-Control, expire...)
     * @param   string $value     value of the header
     * @param   bool   $erase     erase header if already exists
     */
    function __construct($header, $value = null, $erase = false){
        $this->header = $header;
        $this->value[$value] = 1;
        $this->erase = $erase;
       Log::save("Set header ".$header." : ".$value);
    }
    
    /**
     * Add a value to the current header
     * 
     * @param   string $value    value to be added
     */
    public function addValue($value){
        $this->value[$value] = 1;
    }
    
    /**
     * Remove a value from the current header
     * 
     * @param   string $value    value to be removed
     */
    public function removeValue($value){
        unset($this->value[$value]);
    }
    
    /**
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
