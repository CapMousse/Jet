<?php
/**
*   Jet
*   A lightweight and fast framework for developer who don't need hundred of files
*    
*   @package SwhaarkFramework
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/

/**
*   Form validator
*   Don't waste time on small things
*    
*   @package Jet
*   @author  Jérémy Barbe
*   @license BSD
*   @link     https://github.com/CapMousse/Jet
*
*/


class Validation{
    public $validatedInputs = array();

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
     * @param    string $returnType
     * @return   \Validation
     */
    function __construct($returnType = 'array'){
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
     * @param   string  $name   name of current input
     * @return  \Validation
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
     * @param   string  $rule   rule name
     * @param   mixed   $value  rule value
     * @return  \Validation
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
     * @return  \Validation
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
     * @param   mixed   $type   type of input
     * @return  \Validation
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
     * @param   int     $length     length of input
     * @return  \Validation
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
     * @param   int     $length     length of input
     * @return  \Validation
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
     * @return  array|bool|string (array/string) = false / true
     */
    public function validate(){
        if($_SERVER['REQUEST_METHOD'] !== "POST" && $_SERVER['REQUEST_METHOD'] !== "GET")
            return false;
         
        foreach($this->inputs as $name => $input){
            $current = isset($_POST[$name]) ? $_POST[$name] : (isset($_GET[$name]) ? $_GET[$name] : false);
            $this->content[$name]['value'] = $current;
            
            if((!$current || empty($current)) && isset($input['required'])){
                $this->error[$name]['required'] = false;
            }
            
            if(isset($input['maxLength']) && strlen($current) > $input['maxLength']){
                $this->error[$name]['maxLength'] = false;
            }
            
            if(isset($input['minLength']) && strlen($current) < $input['minLength']){
                $this->error[$name]['minLength'] = false;
            }
            
            if(isset($input['type'])){
                if($input['type'] == "mail" && !filter_var($current, FILTER_VALIDATE_EMAIL)){
                    $this->error[$name]['mail'] = false;
                }
                if($input['type'] == "number" && !filter_var($current, FILTER_VALIDATE_FLOAT)){
                    $this->error[$name]['number'] = false;
                }
                if($input['type'] == "ip" && !filter_var($current, FILTER_VALIDATE_IP)){
                    $this->error[$name]['ip'] = false;
                }
                if($input['type'] == "url" && !filter_var($current, FILTER_VALIDATE_URL)){
                    $this->error[$name]['url'] = false;
                }
            }

            if(!isset($this->error[$name])){
                $this->validatedInputs[$name] = $current;
            }
        }
        
        if(count($this->error) > 0){
            $return = array_merge_recursive($this->content, $this->error);
            return $this->returnType == "JSON" ?  json_encode($return) : $return;
        }
        
        return true;
    }

    public function getInputs(){
        return $this->validatedInputs;
    }
}
?>