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
*   Cache class
*   Do you have memory?
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/

class HttpRequest{
    public
        $emulate = false, // if you want to develop with an always empty cache
        $cache_time = 350000; // default to 5 minutes

    private
        $cache_path = NULL;

    public function __construct(){
        if(is_null($this->cache_path)){
            $this->cache_path = is_null(Jet::$config['cache_dir']) ? BASEPATH.'cache/' : Jet::$config['cache_dir']; 
        }
    }


    /**
     * get
     *
     * read a cache file
     *
     * @access   public
     * @param   string   $cache   name of the cached data
     * @return   mixed    false or data
     */   
    public function get($cache){
        if(!is_file($this->cache_path.$cache))
                return false;

        $data_cache = file_get_contents($this->cache_path.$cache);
        $data_cache = unserialize($data_cache);

        if(time() > $this->cache_time || $this->emulate){
            @unlink($this->cache_path.$cache);
            return false;
        }

        return $data_cache['cache'];
    }


    /**
     * set
     *
     * save data to cache
     *
     * @access   public
     * @param   string   $cache   name of the cached data
     * @param   mixed   $data   data to be cached
     * @param   int      $time   add time to cached data
     * @return   bool
     */
    public function set($cache, $data){
        $data_cache = array(
            'cache' => $data,
            'time' => time() + $this->cache_time
        );

        $data_cache = str_replace("\n", "", serialize($data_cache));

        if(!file_put_contents($this->cache_path.$cache, $data_cache) || $this->emulate)
            return false;

        return true;
    }


    /**
     * delete
     *
     * deelte a cache file
     *
     * @access   public
     * @param   string/array   $cache   name of the cached data
     * @return   void
     */
    public function delete($cache){
        if(!isset($cache['0'])) 
            $cache = array($cache);

        foreach($cache as $name){
            if(!is_file($this->cache_path.$name))
                continue;

            if(!unlink($this->cache_path.$name))
                continue;
        }
    }
}