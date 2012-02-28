<?php

class ModelExample extends Model{

    public static
        $structure = array(
            //name                  type        length      default     encode              attr    null    index
            'title'     =>  array(  'text',     '',         '',         'utf8_general_ci',  '',     false,  ''),
            'content'   =>  array(  'varchar',  '255',      '',         'utf8_general_ci',  '',     false,  '')
        ),
        $engine = "INNODB";
}

?>
