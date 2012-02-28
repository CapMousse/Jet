<?php

class User extends Model
{
    public static
        $structure = array(
            //name                  type        length      default     encode              attr    null    index
            'name'      =>  array(  'varchar',  '255',      '',         'utf8_general_ci',  '',     false,  ''),
            'password'  =>  array(  'varchar',  '40',       '',         'utf8_general_ci',  '',     false,  ''),
            'mail'      =>  array(  'varchar',  '80',       '',         'utf8_general_ci',  '',     false,  '')
        ),
        $engine = "INNODB";
}