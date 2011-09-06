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
*   View class
*   Make the block magic!
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*   @version 1
*/


class View{
    private $blocks = array();
    private $blockName = null;

    /**
     * createBlock
     *
     * alias of beginBlock
     *
     * @access   public
     * @param   string   $block   name of the block
     * @return   void
     */   
    public function createBlock($block){
        $this->beginBlock($block);
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

        if(!isset($this->blocks[$block]))
                $this->blocks[$block] = null;

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
            $this->blocks[$block] .= $value;
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
        return isset($this->blocks[$block]) ? $this->blocks[$block] : '';
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
            return isset($this->blocks[$block]) ? true : false;
    }

    /**
     * destroyBlock
     *
     * delete the asked block
     *
     * @access   public
     * @param   string   $block   name of the block
     * @return   void
     */   
    public function destroyBlock($block){
        unset($this->blocks[$block]);
    }

    /**
     * getVar
     *
     * return a defined public var from the controller
     *
     * @access   public
     * @param   string   $var   name of the var
     * @return   string
     */   
    public function getVar($var){
        global $theApp;

        return isset($theApp->$var) ? $theApp->$var : '';
    }

    /**
     * slugify
     *
     * Remove and replace all special caractère to be url friendly
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
}
?>