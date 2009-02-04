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
    
    public function __construct($message, $mysql_message=0){
        parent::__construct($message, SM_ERROR_DATABASE);
        $this->_db_error_type = $mysql_message;
    }
    
    public function getDbErrorType(){
        return $this->_db_error_type;
    }
    
}