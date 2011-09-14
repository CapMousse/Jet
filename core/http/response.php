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
*   HTTP Response class
*   Good response for good people
*    
*   @package    Jet
*   @author     Jérémy Barbe
*   @license    BSD
*   @link       https://github.com/CapMousse/Jet
*   @version    1
*/

class HttpResponse{
    const
        OK = 200,
        ERROR = 500,
        NOT_FOUND = 404;
    
    protected 
        $httpVersion = "1.1",
        $status = 200,
        $body = "",
        $length = 0,
        $headers = array();          
    
    protected static 
        $statusList = array(
            100 => '100 Continue',
            101 => '101 Switching Protocols',
            200 => '200 OK',
            201 => '201 Created',
            202 => '202 Accepted',
            203 => '203 Non-Authoritative Information',
            204 => '204 No Content',
            205 => '205 Reset Content',
            206 => '206 Partial Content',
            300 => '300 Multiple Choices',
            301 => '301 Moved Permanently',
            302 => '302 Found',
            303 => '303 See Other',
            304 => '304 Not Modified',
            305 => '305 Use Proxy',
            306 => '306 (Unused)',
            307 => '307 Temporary Redirect',
            400 => '400 Bad Request',
            401 => '401 Unauthorized',
            402 => '402 Payment Required',
            403 => '403 Forbidden',
            404 => '404 Not Found',
            405 => '405 Method Not Allowed',
            406 => '406 Not Acceptable',
            407 => '407 Proxy Authentication Required',
            408 => '408 Request Timeout',
            409 => '409 Conflict',
            410 => '410 Gone',
            411 => '411 Length Required',
            412 => '412 Precondition Failed',
            413 => '413 Request Entity Too Large',
            414 => '414 Request-URI Too Long',
            415 => '415 Unsupported Media Type',
            416 => '416 Requested Range Not Satisfiable',
            417 => '417 Expectation Failed',
            500 => '500 Internal Server Error',
            501 => '501 Not Implemented',
            502 => '502 Bad Gateway',
            503 => '503 Service Unavailable',
            504 => '504 Gateway Timeout',
            505 => '505 HTTP Version Not Supported'
        );
    
    /*
     * Response constructor
     * 
     * @param   int $status status code to respond
     */
    function __construct($status = null){
        if(!is_null($status) && isset(self::$statusList[$status])){
            $this->status = $status;
        }
    }
    
    /*
     * Set header for client
     * 
     * @param   string  $header     the header to be send
     * @param   string  $value      value of the header
     * @param   boolean $erase      erase header if already exists
     * @param   int     $code       the status code of the header
     * 
     * @return  current header
     * 
     */
    public function setHeader($header, $value = null, $erase = false, $code = 200){
        if(!isset($this->header[$header]) || $erase){
            $this->header[$header] = new HttpHeader($header, $value, $erase, $code);
        }
        
        return $this->header[$header];
    }
    
    /*
     * Send all headers
     */
    public function sendHeaders(){
        if(headers_sent()){
            Debug::log("Headers already send", true, true);
        }
        
        foreach($this->headers as $header){
            $header->send();
        }
    }
    
    /*
     * Set the current status
     * 
     * @param   int  $status    the status code 
     */
    public function setStatus($status){
        if(!isset(self::$statusList[$status])){
            Debug::log("Status ".$status." Unknown", true, true);
        }
        
        $header = sprintf('%1$s %2$d %3$s', $_SERVER['SERVER_PROTOCOL'], $status, self::$statusList[$status]);
        $this->setHeader($header, null, true, $status);
        $this->status = $status;
    }
    
    /*
     * Get the current status
     * 
     * @retuen  the current status code 
     */
    public function getStatus(){
        return $this->status;
    }
    
    /*
     * Set the response body
     * 
     * @param   string $value   the body of the response
     */
    public function setBody($value){
        $this->body = $value;
    }
    
    /*
     * Send header and body to the client
     */
    public function send(){
        $this->sendHeaders();
        
        return $this->body;
    }
}
?>
