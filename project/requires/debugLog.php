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
*   A debug log tool
*   And a example of module file
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/

class DebugLog{
    private 
        $response, 
        $jet;
    
    function __construct(){        
        $this->response = HttpResponse::getInstance();
        $this->jet = Jet::getInstance();
        $this->jet->addAction('beforeRender', $this, 'showLogBar');
    }
    
    /**
     * i love to write comments. That's so funny !
     */
    public function showLogBar(){
        $return = '<div id="debug-bar" style="position: absolute; top: 0px; right: 0px; width: 250px; background-image: linear-gradient(bottom, rgb(209,209,209) 5%, rgb(245,245,245) 77%);background-image: -o-linear-gradient(bottom, rgb(209,209,209) 5%, rgb(245,245,245) 77%);background-image: -moz-linear-gradient(bottom, rgb(209,209,209) 5%, rgb(245,245,245) 77%);background-image: -webkit-linear-gradient(bottom, rgb(209,209,209) 5%, rgb(245,245,245) 77%);background-image: -ms-linear-gradient(bottom, rgb(209,209,209) 5%, rgb(245,245,245) 77%);background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0.05, rgb(209,209,209)),color-stop(0.77, rgb(245,245,245))); border-radius: 0 0 4px 4px;"><a href="#" style="display: inline-block;border-right: 1px solid #eee; padding: 0 5% 0 5%; color: black; text-decoration: none; text-shadow: 0px 1px 1px #FFF" onclick="var a=document.getElementById(\'log-block\'); a.style.display == \'none\' ? a.style.display = \'block\' : a.style.display = \'none\'; return false">Show debug log</a><a href="#" style="display: inline-block; padding: 0 5% 0 5%; color: black; text-decoration: none; text-shadow: 0px 1px 1px #FFF" onclick="var a=document.getElementById(\'orm-block\'); a.style.display == \'none\' ? a.style.display = \'block\' : a.style.display = \'none\'; return false">Show ORM log</a></div>';
        
        $return .= '<div id="log-block" style="display: none"><ul style="font-size: 12px;list-style: none; list-style-position: inside; padding: 0; margin: 0">';
        foreach(Log::$markers as $marker){
            $return .= "<li>$marker</li>";
        }
        $return .= "</ul></div>";
        
        $return .= '<div id="orm-block" style="display: none"><ul style="font-size: 12px;list-style: none; list-style-position: inside; padding: 0; margin: 0">';
        foreach(OrmWrapper::$log as $marker){
            $return .= "<li>$marker</li>";
        }
        $return .= "</ul></div>";
        
        $this->response->setBody($return);
    }
}

new DebugLog();
?>
