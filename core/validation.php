<?php
/**
*   ShwaarkFramework
*   A lightweigth and fast framework for developper who don't need hundred of files
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/ShwaarkFramework
*   @version 0.3
*/

/**
*   Form validator
*   Don't waste time on small things
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/ShwaarkFramework
*   @version 1
*/


class Validation{
    private 
        $inputs = array(),
        $current = null,
        $error = array(),
        $content = array(),
        $returnType = 'array';
    
    
        
    /**
     * __construct
     *
     * set the default return type
     *
     * @access   public
     * @return   voir
     */   
    public function __construct($returnType = 'array'){
        $this->returnType = $returnType;
    }
    
    /**
     * method
     *
     * return the current used method
     *
     * @access   static method
     * @return   request method/false 
     */   
    public static function method(){
        if(isset($_SERVER['REQUEST_METHOD'])){
            return $_SERVER['REQUEST_METHOD'];
        }
        
        return false;
    }
    
    /**
     * add
     *
     * add an input to current validation
     *
     * @access  public  method
     * @param   $name   name of current input
     * @return  current object
     */   
    public function add($name){
        $this->inputs[$name] = array();
        $this->current = $name;
        
        return $this;
    }
    
    /**
     * set
     *
     * add a rule to current input
     *
     * @access  public  method
     * @param   $rule   rule name
     * @param   $value  rule value
     * @return  current object
     */   
    public function set($rule, $value){
        $this->inputs[$this->current][$rule] = $value;
        
        return $this;
    }
    
    /**
     * required
     *
     * set the current input to required state
     *
     * @access  public  method
     * @return  current object
     */   
    public function required(){
        $this->set('required', true);
        
        return $this;
    }
    
    /**
     * type
     *
     * set the current input type
     *
     * @access  public  method
     * @param    $type   type of input
     * @return  current object
     */       
    public function type($type){
        $this->set('type', $type);
        
        return $this;
    }
    
    /**
     * maxLength
     *
     * set the current input max length
     *
     * @access  public  method
     * @param   $length length of input
     * @return  current object
     */   
    public function maxLength($length){
        $this->set('maxLength', $length);
        
        return $this;
    }
    
    /**
     * minLength
     *
     * set the current input min length
     *
     * @access  public  method
     * @param   $length length of input
     * @return  current object
     */ 
    public function minLength($length){
        $this->set('minLength', $length);
        
        return $this;
    }
    
    /**
     * validate
     *
     * validate the current set of inputs
     *
     * @access  public  method
     * @return  (array/string) = false / true
     */ 
    public function validate(){
        if($_SERVER['REQUEST_METHOD'] !== "POST" && $_SERVER['REQUEST_METHOD'] !== "GET")
            return false;
         
        foreach($this->inputs as $name => $conf){
            $current = isset($_POST[$name]) ? $_POST[$name] : (isset($_GET[$name]) ? $_GET[$name] : false);
            $this->content[$name]['value'] = $current;
            
            if((!$current || empty($current)) && isset($conf['required'])){
                $this->error[$name]['required'] = true;
            }
            
            if(isset($conf['maxLength']) && strlen($current) > $conf['maxLength']){
                $this->error[$name]['maxLength'] = true;
            }
            
            if(isset($conf['minLength']) && strlen($current) < $conf['minLength']){
                $this->error[$name]['minLength'] = true;
            }
            
            if(isset($conf['type'])){
                if($conf['type'] == "mail" && !filter_var($current, FILTER_VALIDATE_EMAIL)){
                    $this->error[$name]['mail'] = true;
                }
                if($conf['type'] == "number" && !filter_var($current, FILTER_VALIDATE_FLOAT)){
                    $this->error[$name]['number'] = true;
                }
                if($conf['type'] == "ip" && !filter_var($current, FILTER_VALIDATE_IP)){
                    $this->error[$name]['ip'] = true;
                }
                if($conf['type'] == "url" && !filter_var($current, FILTER_VALIDATE_URL)){
                    $this->error[$name]['url'] = true;
                }
            }
        }
        
        if(count($this->error) > 0){
            $return = array_merge_recursive($this->content, $this->error);
            return $this->returnType == "JSON" ?  json_encode($return) : $return;
        }
        
        return true;
    }
}
?>