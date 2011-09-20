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
        $halt = false,
        $body = "",
        $length = 0,
        $headers = array(),
        $cacheControl = array();          
    
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
    
    /**
     * Response constructor
     * 
     * @param   int $status status code to respond
     */
    function __construct($status = null){
        if(!is_null($status) && isset(self::$statusList[$status])){
            $this->status = $status;
        }
    }
    
    /**
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
        if(isset($this->headers[$header]) && !$erase){
            $this->headers[$header]->addValue($value);
        }else{
            $this->headers[$header] = new HttpHeader($header, $value, $erase, $code);
        }
        
        return $this->headers[$header];
    }
    
    /**
     * removeHeader
     * 
     * @param   string  $header     the header to be removed
     * 
     * @return  void
     * 
     */
    public function removeHeader($header){
        if(isset($this->headers[$header])){
            unset($this->headers[$header]);
        }
    }
    
    /**
     * Send all headers
     */
    public function sendHeaders(){        
        foreach($this->headers as $header){
            $header->send();
        }
        
        if(count($this->cacheControl) !== 0){
            $this->sendCacheControlHeader();
        }
    }
    
    /**
     * Send the Cache-Control header
     */
    private function sendCacheControlHeader(){
        $cacheControlHeader = array();
        foreach($this->cacheControl as $name => $value){
            $cacheControlHeader[] = is_null($value) ? $name : $name."=".$value;
        }
        
        $cacheControlHeader[] = "cache";
        $cacheControlHeader = new HttpHeader("Cache-Control", join(", ", $cacheControlHeader), true);
        $cacheControlHeader->send();
    }
    
    /**
     * Set a value for the Cache-Control header
     * 
     * @param   string  $name   name of the property
     * @param   string  $value  value of the property
     */
    public function addCacheControl($name, $value = null){
        $this->cacheControl[$name] = $value;
    }
    
    /**
     * Remove a value from the Cache-Control header
     * 
     * @param   string  $name   name of the property
     */
    public function removeCacheControl($name){
        unset($this->cacheControl[$name]);
    }
    
    /**
     * Set the current status
     * 
     * @param   int  $status    the status code 
     */
    public function setStatus($status){
        if(array_key_exists($status, self::$statusList)) return; 
        
        $header = sprintf('%1$s %2$d %3$s', $_SERVER['SERVER_PROTOCOL'], $status, self::$statusList[$status]);
        $this->setHeader($header);
        
        $this->status = $status;
    }
    
    /**
     * Get the current status
     * 
     * @retuen  the current status code 
     */
    public function getStatus(){
        return $this->status;
    }
    
    /**
     * Set the response body
     * 
     * @param   string $value   the body of the response
     */
    public function setBody($value){
        $this->body = $value;
        $this->setHeader("Content-Length", strlen($this->body), true);
    }
    
    /**
     * setPublic
     * 
     * Set the http cache public
     * 
     * @return  void
     */
    public function setPublic($set = true){
        if($set){
            $this->addCacheControl('public');
        }else{
            $this->removeCacheControl('public');
        }
    }
    
    /**
     * setPrivate
     * 
     * Set the http cache private
     * 
     * @return  void
     */
    public function setPrivate($set = true){
        if($set){
            $this->addCacheControl('private');
        }else{
            $this->removeCacheControl('private');
        }
    }
    
    /**
     * setCacheControl
     * 
     * Set the Cache-Control max age
     * 
     * @param   $maxAge   expire time in second
     * @return  void
     */
    public function setMaxAge($maxAge = null){
        if(null === $maxAge){
            $this->removeCacheControl('max-age');
        }else{
            $this->addCacheControl('max-age', $maxAge);
        }
    }
    
    /**
     * setServerMaxAge
     * 
     * Set the Cache-Control procy max age
     * 
     * @param   $maxAge   expire time in second
     * @return  void
     */
    public function setServerMaxAge($maxAge){
        if(null === $maxAge){
            $this->removeCacheControl('s-maxage');
        }else{
            $this->addCacheControl('s-maxage', $maxAge);
        }
    }
    
    /**
     * setMustRevalidate
     * 
     * Set the Cache-Control must revalidate
     * 
     * @param   bool $must  activate/disactivate
     * @return  void
     */
    public function setMustRevalidate($revalidate = true){
        if(!$revalidate){
            $this->removeCacheControl('must-revalidate');
        }else{
            $this->addCacheControl('must-revalidate');
        }
    }
    
    /**
     * setProxyRevalidate
     * 
     * Set the Cache-Control proxy revalidate
     * 
     * @param   bool $revalidate    set/unset the proxy-revalidate
     * @return  void
     */
    public function setProxyRevalidate($revalidate = true){
        if(!$revalidate){
            $this->removeCacheControl('proxy-revalidate');
        }else{
            $this->addCacheControl('proxy-revalidate');
        }
    }
    
    /**
     * setLastModified
     * 
     * Set the last modified date for cache
     * 
     * @param   int $time   timestamp
     * @return  void
     */
    public function setLastModified($time = null){
        if(null === $time){
            $this->removeHeader("Last-Modified");
            return;
        }
        
        
        $date = date(DATE_RFC1123, $time);
        $this->setHeader("Last-Modified", "$date GMT");
        
        $lastModifiedHeader = HttpRequest::server('HTTP_IF_MODIFIED_SINCE');
        if($time === strtotime(HttpRequest::server('HTTP_IF_MODIFIED_SINCE'))){
            $this->halt = 304;
            return;
        }
    }
    
    /**
     * setEtag
     * 
     * Set the Etag header
     * 
     * @param   strin $tag   the Etag hash (must be unique)
     * @return  void
     */
    public function setEtag($tag = null, $type = 'strong'){
        if(null === $tag){
            $this->removeHeader("Etag");
            return;
        }
        
        if(!in_array($type, array('strong', 'weak'))){
            debug::log("Etag type atribut don't exists", true);
            return;
        }
        
        $tag = '"'.$tag.'"';
        if($type === "weak"){
            $tag = "W/".$tag;
        }
        
        $this->setHeader("ETag", $tag);
        
        $etagHeader = HttpRequest::server('HTTP_IF_NONE_MATCH');
        if($etagHeader){
            $etags = preg_split('@\s*,\s*@', $etagHeader);
            if (in_array($tag, $etags) || in_array('*', $etags)){
                $this->halt = 304;
                return;
            }
        }
    }
    
    /**
     * setExpire
     * 
     * Set the Expire header
     * 
     * @param   $time   expire time in second
     * @return  void
     */
    public function setExpires($time = null){
        if(null === $time){
            $this->removeHeader("Expires");
        }else{
            $date = date(DATE_RFC1123, time()+$time);
            $this->setHeader("Expires", $date, true);
        }
    }
    
    /**
     * redirect
     * 
     * @param $address string url of redirect
     * @param $type int type of redirect
     * @param $time int time before redirecting
     * @return void
     */
    public function redirect($address, $type = 304, $time = 0){
        if(!array_key_exists($type, self::$statusList)){
            return false;
        }
        
        $header = "Location";
        $value = $address;
        
        if(0 !== $time){
            $header = "Refresh";
            $value = $time . ';url=' . $address;
        }
        
        $this->setStatus($type);
        $this->setHeader($header, $value);
        $this->sendHeaders();
        
        if(!$time){
            //prevent framework from redering
            exit();
        }
    }
    
    /**
     * Send header and body to the client
     * 
     * @pram string $body
     */
    public function send(){
        if($this->halt !== false){
            $this->setStatus($this->halt);
        }
        
        $this->sendHeaders();
        
        if(304 !== $this->halt){
            return $this->body;
        }
    }
}
?>
