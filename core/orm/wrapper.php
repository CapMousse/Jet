<?php

class OrmWrapper {
    public 
        $class,
        $connector,
        $tableName = "";
    
    public static
        $log = array();
    
    private
        $_isNew = false,
        $_distinct = false,
        $_resultSelector = array("*"),
        $_data = array(),
        $_dirty = array(),
        $_values = array(),
        $_join = array(),
        $_where = array(),
        $_limit = null,
        $_offset = null,
        $_order = null,
        $_orderBy = array(),
        $_groupBy = array();
    
    function __construct(){
        $this->connector = OrmConnector::getInstance();
        $this->parseTableName();
    }
    
    private function run(){
        if(!$this->connector){
            return false;
        }
        
        $query = $this->buildSelect();
        self::$log[] = $query;
        
        $query = $this->connector->prepare($query);
        $query->execute($this->_values);
        
        $rows = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    private function buildSelect(){
        return $this->joinIfNotEmpty(array(
            $this->buildSelectStart(),
            $this->buildJoin(),
            $this->buildWhere(),
            $this->buildGroupBy(),
            $this->buildOrderBy(),
            $this->buildLimit(),
            $this->buildOffset(),
        ));
    }
    
    private function buildSelectStart(){
        $resultColumns = join(', ', $this->_resultSelector);

        if ($this->_distinct) {
            $resultColumns = 'DISTINCT '.$resultColumns;
        }

        $fragment = "SELECT {$resultColumns} FROM " . $this->setQuotes($this->tableName);

        return $fragment;
    }
    
    private function buildJoin(){
        if(!count($this->_join)){
            return;
        }
        
        return join(" ", $this->_join);
    }
    
    private function buildWhere(){
        if(!count($this->_where)){
            return;
        }
        
        $return = array();
        
        foreach($this->_where as $where){
            $return[] = $where[0];
            $this->_values = array_merge($this->_values, $where[1]);
        }
        
        return "WHERE ".join(" AND ", $return);
    }
    
    private function buildGroupBy(){
        if(!count($this->_groupBy)){
            return;
        }
        
        return "GROUP BY ".join(",", $this->_groupBy);
    }
    
    private function buildOrderBy(){
        if(!count($this->_orderBy) || is_null($this->_order)){
            return;
        }
        
        return "ORBER BY ".join(",", $this->_orderBy).' '.$this->_order;
    }
    
    private function buildLimit(){
        if(is_null($this->_limit)){
            return;
        }
        
        return "LIMIT ".$this->_limit;
    }
    
    private function buildOffset(){
        if(is_null($this->_offset)){
            return;
        }
        
        return "OFFSET ".$this->_offset;
    }
    
    private function hydrate($data = array()){
        $this->data = $data;
        return $this;
    }
    
    private function joinIfNotEmpty($selectArray){
        $returnArray = null;
        
        foreach($selectArray as $select){
            if($select != ""){
                $returnArray[] = trim($select);
            }
        }
        
        return join(" ", $returnArray);
    }
    
    private function parseTableName(){
        $this->tableName = strtolower(preg_replace('/(?!^)[[:upper:]]/', '_\0', $this->class));
    }
    
    private function setQuotes($fragment){
        $parts = explode('.', $fragment);
        
        foreach($parts as &$part){
            $part = OrmConnector::$quoteSeparator . $part . OrmConnector::$quoteSeparator;
        }
        
        return join('.', $parts);
    }
    
    private function createInstance($row){
        $instance = new self($this->class);
        $instance->hydrate($row);
        return $instance;
    }
    
    public function where($columns, $statement, $value){
        if(!is_array($value)){
            $value = array($value);
        }
        
        if(is_array($columns)){
            array_map(array($this, 'setQuotes'), $columns);
        }else{
            $columns = $this->setQuotes($columns);
        }
        
        $this->_where[] = array(" $columns $statement ? ", $value);
        
        return $this;
    }
    
    public function whereId($id){
        $this->where("id", "=", $id);
        return $this;
    }
    
    public function join($type, $table, $condition){
        $type = trime("$type JOIN");
        $table = $this->setQuotes($table);
        
        if(is_array($condition)){
            list($firstCol, $operator, $secondCol) = $condition;
            $firstCol = $this->setQuotes($firstCol);
            $seconCol = $this->setQuotes($secondCol);
            $condition = "$firstCol $operator $seconCol";
        }
        
        $this->_join[] = "$type $table ON $condition";
        
        return $this;
    }
    
    public function limit($limit){
        $this->_limit = (int)$limit;
        return $this;
    }
    
    public function offset($offset){
        $this->_offset = (int)$offset;
        return $this;
    }
    
    public function distinct(){
        $this->_distinct = true;
        return $this;
    }
    
    public function findOne($id = null){
        if(!is_null($id)){
            $this->whereId($id);
        }
        $this->limit(1);
        $row = $this->run();
        
        if(empty($row)){
            return false;
        }
        
        return $this->createInstance($row[0]);
    }
    
    public function findMany(){
        $rows = $this->run();
        return array_map(array($this, 'createInstance'), $rows);
    }
    
    public function __get($name){
        return isset($this->_data[$name]) ? $this->_data[$name] : null;
    }
    
    public function __set($name, $value){
        $this->_data[$name] = $value;
        $this->_dirty[] = $name;
    }
    
    /*public function __isset(){
        
    }*/
}

?>
