<?php

class OrmWrapper {
    public 
        $class,
        $connector,
        $tableName = "";
    
    private
        $_idSelector = "id",
        $_isNew = false,
        $_distinct = false,
        $_resultSelector = array("*"),
        $_data = array(),
        $_values = array(),
        $_join = array(),
        $_where = array(),
        $_limit = null,
        $_offset = null,
        $_order = null,
        $_orderBy = array(),
        $_groupBy = array();
    
    /*
     * Wrapper constructor
     * 
     * Set the connector to database
     * Get and set the table name
     */
    function __construct(){
        $this->connector = OrmConnector::getInstance();
        $this->parseTableName();
    }
    
    /*
     * parseTableName
     * 
     * Get the current model name and parse to table name
     * @return void
     */
    private function parseTableName(){
        $this->class = get_called_class();
        $this->tableName = strtolower(preg_replace('/(?!^)[[:upper:]]/', '_\0', $this->class));
    }
    
    /*
     * Run
     * 
     * Lauch the current query for selection 
     * 
     * @return array
     */
    private function run(){
        if(!$this->connector){
            return false;
        }
        
        $query = $this->buildSelect();
        Debug::log($query);
        
        $query = $this->connector->prepare($query);
        $query->execute($this->_values);
        
        $rows = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    /*
     * buildSelect
     * 
     * Create the query used by the run method
     * 
     * @return string
     */
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
    
    /*
     * buildSelectStart
     * 
     * Create the start query for run method
     * 
     * @return string
     */
    private function buildSelectStart(){
        $resultColumns = join(', ', $this->_resultSelector);

        if ($this->_distinct) {
            $resultColumns = 'DISTINCT '.$resultColumns;
        }

        $fragment = "SELECT {$resultColumns} FROM " . $this->setQuotes($this->tableName);

        return $fragment;
    }
    
    /*
     * buildJoin
     * 
     * Create the join query for run method
     * 
     * @return string
     */
    private function buildJoin(){
        if(!count($this->_join)){
            return;
        }
        
        return join(" ", $this->_join);
    }
    
    /*
     * buildWhere
     * 
     * Create the where query for run method
     * 
     * @return string
     */
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
    
    /*
     * buildGroupBy
     * 
     * Create the Group By query for run method
     * 
     * @return string
     */
    private function buildGroupBy(){
        if(!count($this->_groupBy)){
            return;
        }
        
        return "GROUP BY ".join(",", $this->_groupBy);
    }
    
    /*
     * buildOrderBy
     * 
     * Create the Order By query for run method
     * 
     * @return string
     */
    private function buildOrderBy(){
        if(!count($this->_orderBy) || is_null($this->_order)){
            return;
        }
        
        return "ORBER BY ".join(",", $this->_orderBy).' '.$this->_order;
    }
    
    /*
     * buildLimit
     * 
     * Create the Limit query for run method
     * 
     * @return string
     */
    private function buildLimit(){
        if(is_null($this->_limit)){
            return;
        }
        
        return "LIMIT ".$this->_limit;
    }
    
    /*
     * buildOffset
     * 
     * Create the Offset query for run method
     * 
     * @return string
     */
    private function buildOffset(){
        if(is_null($this->_offset)){
            return;
        }
        
        return "OFFSET ".$this->_offset;
    }
    
    /*
     * buildInsert
     * 
     * Create the Insert query
     * 
     * @return string
     */
    private function buildInsert(){
        $listFields = array_map(array($this, "setQuotes"), array_keys($this->_data));
        $values = $this->createPlaceholder($this->_data);
        $query = "INSERT INTO ".$this->setQuotes($this->tableName)." (".join(", ", $listFields).") VALUES ($values)";
        
        return $query;
    }
     
    /*
     * buildUpdate
     * 
     * Create the Update query
     * 
     * @return string
     */
    private function buildUpdate(){
        $listFields = array();
        
        foreach($this->_data as $field => $value){
            $listFields[] = "$field = ?";
        }
        
        $query = "UPDATE ".$this->setQuotes($this->tableName)." SET ".join(", ", $listFields)." WHERE ".$this->_idSelector." = ?";
        
        return $query;
    }
    
    /*
     * hydrate
     * 
     * Hydrate the current model with send data
     * 
     * @param   array $data     array of data 
     * @return  current model
     */
    private function hydrate($data = array()){
        $this->_data = $data;
        $this->_data = $data;
        return $this;
    }
    
    /*
     * joinIfNotEmpty
     * 
     * Join different element from array to a string
     * 
     * @param   array $joinArray     array of data 
     * @return  string
     */
    private function joinIfNotEmpty($joinArray){
        $returnArray = null;
        
        foreach($joinArray as $select){
            if(!empty($select)){
                $returnArray[] = trim($select);
            }
        }
        
        return join(" ", $returnArray);
    }
    
    /*
     * setQuotes
     * 
     * Add specific db quote to sent fragment
     * 
     * @param   mixed $fragment    $fragment to be quote
     * @return  string
     */
    private function setQuotes($fragment){
        $parts = explode('.', $fragment);
        
        foreach($parts as &$part){
            $part = OrmConnector::$quoteSeparator . $part . OrmConnector::$quoteSeparator;
        }
        
        return join('.', $parts);
    }
   
    /*
     * createInstance
     * 
     * Create instance from current model with specified row
     * 
     * @param   array $row
     * @return  Model
     */
    private function createInstance($row){
        $instance = new $this->class;
        $instance->hydrate($row);
        return $instance;
    }
    
    /*
     * createPlaceholder
     * 
     * Create placeholder for data used in query
     * 
     * @param   array $dataArray
     * @return  string
     */
    private function createPlaceholder($dataArray){
        $number = count($dataArray);
        
        return join(",", array_fill(0, $number, "?"));
    }
    
    /*
     * getId
     * 
     * Get id of current model
     * 
     * @return  int
     */
    public function getId(){
        return $this->__get($this->_idSelector);
    }
    
    /*
     * setIdName
     * 
     * Set id column name for this model
     * 
     * @return  current model
     */
    public function setIdName($name){
        $this->_id = $name;
        
        return $this;
    }
    
    /*
     * where
     * 
     * Create a where condition
     * 
     * @param   string $column      the column to be compared
     * @param   string $statement   type of comparison
     * @param   mixed  $value       value of comparison
     * @return  current model
     */
    public function where($column, $statement, $value){
        if(!is_array($value)){
            $value = array($value);
        }
        
        $column = $this->setQuotes($column);
        
        $this->_where[] = array(" $column $statement ? ", $value);
        
        return $this;
    }
    
    /*
     * whereId
     * 
     * compare id
     * 
     * @param   int $id     $id to be checked
     * @return  current model
     */
    public function whereId($id){
        $this->where($this->_idSelector, "=", $id);
        return $this;
    }
    
    /*
     * join
     * 
     * Create a join query
     * 
     * @param   string $type        type of join
     * @param   string $table       table to be join
     * @param   array/string $condition   condition of the join
     * @return  current model
     */
    public function join($type, $table, $conditions){
        $type = trim(strtoupper($type)." JOIN");
        $table = $this->setQuotes($table);
        
        $this->_join[] = "$type $table ON ".$this->listJoinCondition($conditions);
        
        return $this;
    }
    
    /*
     * listJoinCondition
     * 
     * @param   array/string    $conditions
     * @return  string
     */
    private function listJoinCondition($conditions){
        if(is_array($conditions)){
            return join(" AND ", $conditions);
        }
        
        return $conditions;
    }
    
    /*
     * Limit
     * 
     * Set a limit to the query
     * 
     * @param   int $limit
     * @return  current model
     */
    public function limit($limit){
        $this->_limit = (int)$limit;
        return $this;
    }
    
    /*
     * Offset
     * 
     * Set a offet to the query
     * 
     * @param   int $offet
     * @return  current model
     */
    public function offset($offset){
        $this->_offset = (int)$offset;
        return $this;
    }
    
    /*
     * Distinct
     * 
     * Set a distinct keyword to the query
     * 
     * @return  current model
     */
    public function distinct(){
        $this->_distinct = true;
        return $this;
    }
    
    /*
     * Create
     * 
     * Create a new model
     * 
     * @param   array $data   data to be insert in the model
     * @return  current model
     */
    public function create($data){
        $this->_isNew = true;
        
        if(is_array($data)){
            $this->hydrate($data);
        }
        
        return $this;
    }
    
    /*
     * findOne
     * 
     * find the first elem of query
     * 
     * @param   array $id   search id
     * @return  model/false
     */
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
    
    /*
     * findMany
     * 
     * find all elem of query
     * 
     * @return  model/false
     */
    public function findMany(){
        $rows = $this->run();
        return $rows ? array_map(array($this, 'createInstance'), $rows) : false;
    }
    
    /*
     * save
     * 
     * save the current model
     * 
     * @return  boolean
     */
    public function save(){
        if(!$this->connector){
            return false;
        }
        
        $query = "";
        $values = array_values($this->_data);
        
        if($this->_isNew){
            $query = $this->buildInsert();
        }else{
            if(!count($values)){
                return true;
            }
            
            $query = $this->buildUpdate();
            $values[] = $this->getId();
        }
        
        Debug::log($query);
        
        try{
            $query = $this->connector->prepare($query);
            $success = $query->execute($values);
        }catch(Exception $e){
            Debug::log($e);
        }
        
        if($this->_isNew){
            $this->_isNew = false;
            
            if(is_null($this->getId())){
                $this->__set[$this->_idSelector] = $this->connector->lastInsertId();
            }
        }
        
        return $success;
    }
    
    /*
     * save
     * 
     * save the current model
     * 
     * @return  boolean
     */
    public function delete(){
        $query = "DELETE FROM ".$this->setQuotes($this->tableName)." WHERE ".$this->setQuotes($this->_idSelector)." = ?";
        $params = array($this->getId());
        
        Debug::log($query);
        
        try{
            $exec = $this->connector->prepare($query);
            $success = $exec->execute($params);
        }catch(Exception $e){
            Debug::log($e);
        }
        
        return $success;
       
    }
    
    public function __get($name){
        return isset($this->_data[$name]) ? $this->_data[$name] : null;
    }
    
    public function __set($name, $value){
        $this->_data[$name] = $value;
    }
    
    /*public function __isset(){
        
    }*/
}

?>
