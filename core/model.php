<?php

class Model extends OrmWrapper{
    
    function __construct($className){
        $this->class = $className;
        
        parent::__construct();
    }    
    
}

?>