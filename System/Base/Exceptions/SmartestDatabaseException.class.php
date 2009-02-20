<?php

class SmartestDatabaseException extends SmartestException{
    
    const UNKNOWN_TYPE = 0;
    const CONNECTION_IMPOSSIBLE = 1;
    const SPEC_DB_ACCESS_DENIED = 2;
    const LOST_CONNECTION = 4;
    const INVALID_CACHE_DATA = 8;
    const INVALID_SQL_FILE_STORAGE_DIR = 16;
    const INVALID_CONNECTION_NAME = 32;
    
    protected $_db_error_type;
    protected $_username;
    protected $_database;
    protected $_host;
    protected $_query;
    protected $_client_error_message;
    protected $_client_error_id;
    
    public function __construct($message, $mysql_message=0){
        parent::__construct($message, SM_ERROR_DATABASE);
        $this->_db_error_type = $mysql_message;
    }
    
    public function getDbErrorType(){
        return $this->_db_error_type;
    }
    
    public function getUsername(){
        return $this->_username;
    }
    
    public function setUsername($u){
        $this->_username = $u;
    }
    
    public function getDatabase(){
        return $this->_database;
    }
    
    public function setDatabase($d){
        $this->_database = $d;
    }
    
    public function getHost(){
        return $this->_host;
    }
    
    public function setHost($h){
        $this->_host = $h;
    }
    
    public function setQuery($q){
        $this->_query = $q;
    }
    
    public function getQuery(){
        return $this->_query;
    }
    
    public function setClientErrorMessage($m){
        $this->_client_error_message = $m;
    }
    
    public function getClientErrorMessage(){
        return $this->_client_error_message;
    }
    
    public function setClientErrorId($id){
        $this->_client_error_id = $id;
    }
    
    public function getClientErrorId(){
        return $this->_client_error_id;
    }
    
}