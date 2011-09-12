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
        $value = null,
        $erase = false,
        $status = 200,
        $sent = false;        
    
    /*
     * Response constructor
     * 
     * @param   int $status status code to respond
     */
    function __construct($header, $value = null, $erase = false, $code = 200){
        $this->header = $header;
        $this->value = $value;
        $this->erase = $erase;
        $this->code = $code;
    }
    
   public function send(){
       if($this->sent){
           return;
       }
       
       $header = is_null($this->value) ? $this->header : $this->header." : ".$this->value;

       Debug::log("Send header ".$header);
       header($header, $this->erase, $this->status);
       
       $this->sent = true;
   }
}
?>
