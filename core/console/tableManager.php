<?php

/**
*   Jet
*   A lightweight and fast framework for developer who don't need hundred of files
*
*   @package    Jet
*   @author     Jérémy Barbe
*   @license    BSD
*   @link       https://github.com/CapMousse/Jet
*
*/


/**
 *   TableManager Class
 *
 *   @package   Jet
 *   @author    Jérémy Barbe
 *   @license   BSD
 *   @link      https://github.com/CapMousse/Jet
 */
class TableManager{
    public
        /**
         * Contain all index
         * @var array
         */
        $index = array(),
        /**
         * Contain all unique
         * @var array
         */
        $unique = array(),
        /**
         * The type of command
         * @var null|string
         */
        $type = null,
        /**
         * The name of the searched element (app/model)
         * @var null|string
         */
        $name = null;

    /**
     * Init the dbManager
     * @param string $option
     * @param null|string $type option Type
     * @param null|string $name element
     */
    public function __construct($option, $type = null, $name = null){
        $this->type = $type;
        $this->name = $name;

        //check the asked option type
        switch($option){
            case 'create':
                $this->parseModels('create');
            break;

            case 'migrate':
                $this->parseModels('migrate');
            break;

            case 'load':
                $this->parseModels('load');
                $this->loadData();
            break;

            case 'empty':
                $this->parseModels('remove');
            break;
        }
    }

    /**
     * Fin and scan all models from all app or the selected app
     * @param string $action
     * @return void
     */
    public function parseModels($action){
        //Only parse models from the selected app if not null
        if(!is_null($this->type) && $this->type != 'model'){
            //check if app and app models dir exists
            if(!is_dir(APPS.$this->name) || !is_dir(APPS.$this->name.DR.'models'.DR)){
                print "App ".$this->name." don't exists\n";
                exit(0);
            }

            //scan all models
            $this->scanModels(APPS.$this->name.DR.'models'.DR, $action);
        }else{
            if($action == 'remove' && is_null($this->name)){
                $this->deleteTable();
                return;
            }

            //check all app
            $apps = scandir(PROJECT.'apps'.DR);

            foreach($apps as $app){
                if($app != "." && $app != ".." && is_dir(PROJECT.'apps'.DR.$app) && is_dir(PROJECT.'apps'.DR.$app.DR.'models') ){
                    $this->scanModels(PROJECT.'apps'.DR.$app.DR.'models', $action);
                }
            }

            //check if models are defined on the require dir
            $modules = scandir(PROJECT.'requires'.DR);
            foreach($modules as $module){
                if($module != "." && $module != ".." && is_dir(PROJECT.'requires'.DR.$module) && is_dir(PROJECT.'requires'.DR.$module.DR.'models')){
                    $this->scanModels(PROJECT.'requires'.DR.$module.DR.'models', $action);
                }
            }

            if(is_dir(PROJECT.'models')){
                $this->scanModels(PROJECT.'models', $action);
                return;
            }
        }

        return;
    }

    /**
     * Find and get all models form an all
     * @param $modelDir
     * @param $action
     */
    public function scanModels($modelDir, $action){
        $models = scandir($modelDir);

        foreach($models as $model){
            if($model != "." && $model != ".." && !is_dir($modelDir.DR.$model)){
                include($modelDir.DR.$model);

                if($this->type == 'model' && $this->name != str_replace(EXT, '',$model)){
                    continue;
                }

                //get the class name
                $model = str_replace(EXT, '', ucfirst($model));

                //check if the class exists and a structure is defined for the model
                if(class_exists($model) && property_exists($model, 'structure')){
                    //launch the asked action for the model
                    switch($action){
                        case 'create':
                            $this->createTable($model);
                        break;

                        case 'migrate':
                            $this->migrateTable($model);
                        break;

                        case 'remove':
                            $this->deleteTable($model);
                        break;

                        case 'load':
                            if(property_exists($model, 'data')){
                                $this->loadModelData($model);
                            }
                        break;
                    }
                }
            }
        }
    }

    /**
     * Create the table for the specified model
     * @param Model $model
     */
    public function createTable($model){
        //Init the model
        $model = new $model();

        $name = str_replace(OrmConnector::$quoteSeparator, '', $model->tableName);
        $tables = $model->rawQuery("SHOW TABLES LIKE '".$name."'")->run();

        if($tables && count($tables) > 0){
            return;
        }

        //Prepare query
        $query = "CREATE TABLE ".$model->tableName." (";
        $columns = array();
        $createLog = array();

        //Create the ID column
        $columns[] = $model->getIdName()." INT NOT NULL AUTO_INCREMENT PRIMARY KEY";

        //Check all rows and make specific query
        /** @var $structure Array */
        foreach($model::$structure as $name => $column){
            $columns[] = $this->makeRowSQL($name, $column);
            $createLog[] = $name;
        }

        //create the index query
        if(count($this->index)){
            $columns[] = "INDEX (".join(',', $this->index).")";
        }

        //create the unique query
        if(count($this->unique)){
            $columns[] = "UNIQUE (".join(',', $this->unique).")";
        }

        //finish the query
        $query .= join(',', $columns)." )";

        //check if a engine is specified on the model
        if(property_exists($model, 'engine')){
            /** @var $engine String */
            $query .= " ENGINE = ".$model::$engine;
        }else{
            $query .= " ENGINE = INNODB";
        }

        //check if a engine is specified on the model
        if(property_exists($model, 'encode')){
            /** @var $encode String */
            $set = array_shift(explode('_', $model::$encode));
            $query .= " CHARACTER SET $set COLLATE ".$model::$encode;
        }else{
            $query .= " CHARACTER SET utf8 COLLATE utf8_general_ci";
        }

        //create the table
        $test = $model->reset()->rawQuery($query)->run(true);

        if(!$test){
            $errors = OrmWrapper::$log;
            $error = array_pop($errors);
            $sql = array_pop($errors);
            print_r($error->getMessage()."\n".$sql."\n");
            exit(0);
        }

        print "Table ".$model->tableName." created \n";
    }

    /**
     * Migrate a table
     * @param Model $model
     */
    public function migrateTable($model){
        //init the model
        $model = new $model();

        print "Migrating table ".$model->tableName."\n";

        //check all existing columns
        $rows = $model->rawQuery('DESCRIBE '.$model->tableName)->run();

        if(!$rows){
            print "You try to migrate a non created table :".$model->tableName."\n";
            return;
        }

        $rowsName = array();
        foreach($rows as $row){
            $rowsName[$row["Field"]] = false;
        }

        //prepare the query
        $query = "ALTER TABLE ".$model->tableName." ";
        $columns = array();

        //check all columns. Change them if already exists or create them
        /** @var $structure Array */
        $before = null;

        foreach($model::$structure as $name => $column){
            if(isset($rowsName[$name])){
                $columns[] = "CHANGE ".OrmConnector::$quoteSeparator . $name . OrmConnector::$quoteSeparator." ".$this->makeRowSQL($name, $column);
                $rowsName[$name] = true;
            }else{
                $columns[] = "ADD ".$this->makeRowSQL($name, $column). (!is_null($before) ? " AFTER ".OrmConnector::$quoteSeparator . $before . OrmConnector::$quoteSeparator : "");
            }

            $before = $name;
        }

        //create the index query
        if(count($this->index)){
            $columns[] = "ADD INDEX (".join(',', $this->index).")";
        }

        //create the unique query
        if(count($this->unique)){
            $columns[] = "ADD UNIQUE (".join(',', $this->unique).")";
        }

        //finish the query
        $alterQuery = $query.join(',', $columns);

        //migrate the table
        $test = $model->reset()->rawQuery($alterQuery)->run(true);

        if(!$test){
            $errors = OrmWrapper::$log;
            $error = array_pop($errors);
            $sql = array_pop($errors);
            print_r($error->getMessage()."\n".$sql."\n");
            exit(0);
        }

        //Drop all columns missing from the structure
        foreach($rowsName as $name => $verif){
            if(!$verif && $name != $model->getIdName()){
                $model->reset()->rawQuery('ALTER TABLE '.$model->tableName.' DROP '.$name)->run(true);
            }
        }

    }

    /**
     * Make the SQL query for the specified columns
     * @param string $name
     * @param array $column
     * @return string
     */
    public function makeRowSQL($name, $column){
        $columnQuery = OrmConnector::$quoteSeparator . $name . OrmConnector::$quoteSeparator." ";

        $columnQuery .= strtoupper($column[TYPE]);

        if(!empty($column[LENGTH])){
            $columnQuery .= '('.$column[LENGTH].')';
        }

        if(!empty($column[ATTR])){
            $columnQuery .= ' '.$column[ATTR];
        }

        if(!empty($column[ENCODE]) && in_array($column[TYPE], array('text', 'longtext', 'mediumtext', 'varchar', 'char', 'blob', 'mediumblob', 'longblob'))){
            $encode = array_shift(explode('_', $column[ENCODE]));
            $columnQuery .= ' CHARACTER SET '.$encode.' COLLATE '.$column[ENCODE];
        }

        if($column[ISNULL]){
            $columnQuery .= ' NULL';
        }else{
            $columnQuery .= ' NOT NULL';
        }

        if(!empty($column[DEFAULTVALUE])){
            if(in_array($column[DEFAULTVALUE], array("CURRENT_TIMESTAMP", 'NULL'))){
                $columnQuery .= ' DEFAULT '.$column[DEFAULTVALUE];
            }else{
                $columnQuery .= ' DEFAULT "'.$column[DEFAULTVALUE].'"';
            }
        }

        if($column[INDEX] == "PRIMARY KEY"){
            $columnQuery .= ' PRIMARY KEY';
        }else{
            switch($column[INDEX]){
                case 'UNIQUE':
                    $this->unique[] = $name;
                break;

                case "INDEX":
                    $this->index[] = $name;
                break;
            }
        }

        return $columnQuery;
    }

    /**
     * Delete a model table
     * @param Model|null $model
     */
    public function deleteTable($model = null){
        if(is_null($model)){
            $model = new Model();

            $tables = $model->rawQuery("SHOW TABLES")->run();
            foreach($tables as $table){
                $table = array_pop($table);
                @$model->rawQuery('DROP TABLE '.$table)->run(true);
                print "Table ".$table." deleted \n";
            }
        }else{
            $model = new $model();
            @$model->rawQuery('DROP TABLE '.$model->tableName)->run(true);
            print "Table ".$model->tableName." deleted \n";
        }

    }

    /**
     * Load fixtures from a model
     * @param Model $model
     */
    public function loadModelData($model){
        /** @var $data Array */

        $model = new $model();
        $datas = $model::$data;

        foreach($datas as $data){
            $model->rawQuery('INSERT INTO '.$model->tableName.' VALUES("", '.join(',',  array_fill(0, count($data), "?")).')', $data)->run(true);
        }

        print "Fixtures loaded for ".$model->tableName."\n";
    }

    /**
     * Load fixtures
     */
    public function loadData(){
        $model = new Model();

        include('../../fixtures.php');

        print "Load fixtures into database \n";

        /** @var $fixtures Array */
        foreach($fixtures as $table => $datas){
            $model->rawQuery('TRUNCATE TABLE '.$table)->run(true);

            foreach($datas as $data){
                $model->rawQuery('INSERT INTO '.$table.' VALUES("", '.join(',',  array_fill(0, count($data), "?")).')', $data)->run(true);
            }
        }

        print "Fixtures loaded (or not if table don't exists)\n";

    }
}