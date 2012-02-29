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
*   Jet "template"
*   Make the block magic!
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/


class ViewJet extends ViewBridge{
    public static
        $layout = null,
        $blocks = array();
    
    protected 
        $jet = null,
        $blockName = null,
        $_vars = array(),
        $_version = "1.0";

    /**
     * @param $appName string
     */
    function __construct($appName = null){
        parent::__construct($appName);

        $this->jet = Jet::getInstance();
    }
    
    /**
     * load
     *
     * load the asked view. Important for display data... or not
     * We need to go deerper.
     *
     * @access   protected
     * @param   string   $file      name of the view file
     * @param    array    $options    data used by the view
     * @return   void 
     */   
    public function load($file, $options = null){
        $_currentApp = PROJECT.'apps/'.$this->appName.DR;
        //Control if options is defined, if yes, construct all var used in templates

        if(null != $options){
            foreach($options as $name => $var) ${$name} = $var;
        }

        if(is_file($_currentApp.'views'.DR.$file.EXT)){
            include($_currentApp.'views'.DR.$file.EXT);
        }else if(is_file(PROJECT.'views'.DR.$file.EXT)){
            include(PROJECT.'views'.DR.$file.EXT);
        }else{
            Log::save("The asked view <b>$file</b> doesn't exists in <b>".get_class($this).".php</b> on $_currentApp", Log::FATAL);
            return;
        }

        Log::save('Loaded view : '.$file);
    }
    
    /**
     * beginBlock
     *
     * Start to cache rendered html to make the final render
     *
     * @access   public
     * @param   string   $block   name of the block
     * @return   void
     */   
    public function beginBlock($block){
        $this->blockName = $block;

        if(!isset(self::$blocks[$block])){
            self::$blocks[$block] = null;
        }

        ob_start();
    }

    /**
     * endBlock
     *
     * save the cached html to the block name
     * if the parameters is empty, end the last beginBlock
     *
     * @access   public
     * @param   string   $block   name of the block
     * @return   void
     */   
    public function endBlock($block = null){

        $value = ob_get_clean();

        $block = is_null($block) ? $this->blockName : $block;
        self::$blocks[$block] .= $value;
    }


    /**
     * getBlock
     *
     * give the asked rendered block
     *
     * @access   public
     * @param   string   $block   name of the block
     * @return   string
     */   
    public function getBlock($block){
        return isset(self::$blocks[$block]) ? self::$blocks[$block] : '';
    }


    /**
     * issetBlock
     *
     * check if the asked block exists
     *
     * @access   public
     * @param   string   $block   name of the block
     * @return   bool
     */   
    public function issetBlock($block){
            return isset(self::$blocks[$block]) ? true : false;
    }

    /**
     * destroyBlock
     *
     * delete the asked block
     * ALL YOUR BASE ARE BELONG TO US
     *
     * @access   public
     * @param   string   $block   name of the block
     * @return   void
     */   
    public function destroyBlock($block){
        unset(self::$blocks[$block]);
    }

    /**
     * slugify
     *
     * Remove and replace all special caractère to be url friendly
     * That's magical !
     *
     * @access   public
     * @param   string   $text   the text to be slugify
     * @return   string
     */   
    public function slugify($text){
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');

        if (function_exists('iconv'))
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
            return 'n-a';

        return $text;
    }
    
    /**
     * setLayout
     *
     * @access   public
     * @param    string/bool    $layout
     * @return   void
     */   
    public function setLayout($layout){
        self::$layout = $layout;
    }

    /**
     * hasLayout
     *
     * @access   public
     * @return   bool
     */
    public function hasLayout(){
        return !is_null(self::$layout);
    }
    

    /**
     * render
     *
     * @access   public
     * @return   string
     */
    public function _render(){
        ob_start();
        
        if($this->hasLayout())
            $this->load(self::$layout);
        
        
        $return = ob_get_clean();
        
        return $return;
    }
    
    public function getInfo($info = self::INFO_ALL) {
        $return = array();
        
        if ($info & self::INFO_NAME) {
          $return[] = 'Jet';
        }

        if ($info & self::INFO_VERSION) {
          $return[] = $this->_version;
        }

        if ($info & self::INFO_ENGINE) {
          $return[] = NULL;
        }

        return join(",", $return);
    }
    
    public function get($name){
        return isset($this->_vars[$name]) ? $this->_vars[$name] : '';
    }
    
    public function set($name, $value){
        return $this->_vars[$name] = $value;
    }
    
    public function __set($name, $value){
        return $this->set($name, $value);
    }
    
    public function __get($name){
        return $this->get($name);
    }
    

    /*
     * @ignore
     */
    public function bind($name, &$value){}
}
?>