<?php

/**
 * Implements a mysql abstraction layer
 *
 * PHP version 5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Marcus Gilroy-Ware <marcus@vsccreative.com>
 */

if(!defined("UNSUPPORTED_QUERY")){
	define("UNSUPPORTED_QUERY", false);
}

require SM_ROOT_DIR.'System/Base/Exceptions/SmartestDatabaseException.class.php';

class SmartestMysql{

	protected $dblink;
	public $lastQuery;
	protected $connection_config;
	protected $cachedQueryHistory = array();
	protected $queryHistory = array();
	protected $id;
	protected $databaseName;
	protected $options = array();
	protected $queryHashes = array();
	protected $retrievalQueryTypes = array('SELECT', 'SHOW');
	protected $log = array();
	private $_request_id;
	
	public function __construct(SmartestParameterHolder $dbconfig){
	    
	    $this->connection_config = $dbconfig;
	    $this->r = SM_INFO_REVISION_NUMBER;
	    $this->_request_id = SmartestStringHelper::random(8);
	    
	    if($this->dblink = @mysql_connect($this->connection_config['host'], $this->connection_config['username'], $this->connection_config['password'])){
			
			$this->queryHistory = array();
			$this->rawQuery("SET NAMES 'utf8'");
			
			if(!mysql_select_db($this->connection_config['database'], $this->dblink)){
			    if($this->connection_config['short_name']) SmartestCache::clear('dbc_'.$this->connection_config['short_name'], true);
			    $e = new SmartestDatabaseException("Could not select DB: ".$database.". ".mysql_error($this->dblink), SmartestDatabaseException::SPEC_DB_ACCESS_DENIED);
			    $e->setUsername($this->connection_config['username']);
			    $e->setHost($this->connection_config['host']);
			    $e->setDatabase($this->connection_config['database']);
			    throw $e;
			}
			
			$this->lastQuery = "No queries made yet.";
			
		}else{
		    if($this->connection_config['short_name']) SmartestCache::clear('dbc_'.$this->connection_config['short_name'], true);
		    $e = new SmartestDatabaseException("Could not connect to MySQL. MySQL says: ".mysql_error().".", SmartestDatabaseException::CONNECTION_IMPOSSIBLE);
			$e->setUsername($this->connection_config['username']);
		    $e->setHost($this->connection_config['host']);
		    $e->setDatabase($this->connection_config['database']);
			throw $e;
		}
	}
	
	public function __destruct(){
	    $this->clearQueryHistoryCache();
	}
	
	protected function reconnect(){
        if($this->dblink = @mysql_connect($this->connection_config['host'], $this->connection_config['username'], $this->connection_config['password'])){
			$this->rawQuery("SET NAMES 'utf8'");
			if(mysql_select_db($this->connection_config['database'], $this->dblink)){
			    return true;
			}else{
			    if($this->connection_config['short_name']) SmartestCache::clear('dbc_'.$this->connection_config['short_name'], true);
			    $e = new SmartestDatabaseException("Could not select DB: ".$database." while trying to reconnect.".mysql_error($this->dblink), SmartestDatabaseException::SPEC_DB_ACCESS_DENIED);
			    $e->setUsername($this->connection_config['username']);
    		    $e->setHost($this->connection_config['host']);
    		    $e->setDatabase($this->connection_config['database']);
			    throw $e;
			}
		}else{
			return false;
		}
	}
	
	public function getConnectionName(){
	    return $this->connection_config['short_name'];
	}
	
	public function getTables($refresh=false){
		
		$sql = "SHOW TABLES FROM ".$this->connection_config['database'];
		$tables = $this->queryToArray($sql, $refresh);
		$table_names = array();
		
		foreach($tables as $tr){
			$table_names[] = $tr["Tables_in_".$this->connection_config['database']];
		}
		
		return $table_names;
		
	}
	
	public function getColumns($table, $refresh=false){
		
		$sql = "SHOW COLUMNS FROM `".$table.'`';
		$columns = $this->queryToArray($sql, $refresh);
		return $columns;
		
	}
	
	public function getColumnNames($table, $refresh=false){
		
		$sql = "SHOW COLUMNS FROM `".$table."`";
        $columns = $this->queryToArray($sql, $refresh);
		$names = array();
		
		foreach($columns as $column){
			$names[] = $column['Field'];
		}
		
		return $names;
		
	}
	
	public function rawQuery($querystring){
	    if(!$this->dblink && !$this->reconnect()){
	        if($this->connection_config['short_name']) SmartestCache::clear('dbc_'.$this->connection_config['short_name'], true);
	        throw new SmartestDatabaseException("Lost connection to to MySQL database and could not reconnect", SmartestDatabaseException::LOST_CONNECTION);
        }else{
	        
	        $result = mysql_query($querystring, $this->dblink);
    	    $this->recordLiveQuery($querystring);
    	    
    		if($result){
    			$this->id = mysql_insert_id($this->dblink);
    			return $result;
    		}else{
    			return false;
    		}
	    }
	}
	
	protected function getInsertId(){
		return $this->id;
	}
  
	public function howMany($querystring, $file='', $line=''){
	    
	    if(!$this->dblink && !$this->reconnect()){
	        
	        if($this->connection_config['short_name']) SmartestCache::clear('dbc_'.$this->connection_config['short_name'], true);
	        throw new SmartestDatabaseException("Lost connection to to MySQL database and could not reconnect", SmartestDatabaseException::LOST_CONNECTION);
        
        }else{
	    
		    if($result = @mysql_query($querystring, $this->dblink)){
			    $cardinality = @mysql_num_rows($result);
			    $this->recordLiveQuery($querystring);
			    return $cardinality;
		    }else{
			    return 0;
		    }
		
	    }
	}
	
	protected function getHashFromQuery($query){
	    $query = preg_replace('/\s{2,}/', ' ', $query);
	    $query = str_replace("\n", "", $query);
	    $query = str_replace(" = '", "='", $query);
	    $query = str_replace(" != '", "!='", $query);
	    $query = str_replace(' = "', '="', $query);
	    $query = str_replace(' != "', '!="', $query);
	    $hash = sha1($query);
	    return $hash;
	}
	
	public function clearQueryFromCache($query){
	    
	    $hash = $this->getHashFromQuery($query);
	    
	    if(isset($this->queryHashes[$hash])){
	        unset($this->queryHashes[$hash]);
        }
        
        $cache_name = 'SMCR'.$this->_request_id.$hash;
        return SmartestCache::clear($cache_name, true);
        
	}
	
	public function queryToArray($querystring, $refresh=false){
	    
		if(!$this->dblink && !$this->reconnect()){
	        
	        if($this->connection_config['short_name']) SmartestCache::clear('dbc_'.$this->connection_config['short_name'], true);
	        throw new SmartestDatabaseException("Lost connection to to MySQL database and could not reconnect", SmartestDatabaseException::LOST_CONNECTION);
        
        }else{
		    
		    if($this->queryReturnsData($querystring)){
		        
		        $result = $this->getSelectQueryResult($querystring, $refresh);
		        return $result;
		    
	        }else{
	            
	            throw new SmartestException("Unsupported ".$this->getQueryType($querystring)." query type in SmartestMysql::queryToArray(). Use SmartestMysql::rawQuery()", SM_ERROR_DB);
	            
	        }
		
	    }
	}
	
	public function queryFieldsToArrays($fields, $query, $refresh=false){
	    
	    $result = $this->queryToArray($query, $refresh);
	    
	    if(count($result)){
	        
	        $result_fields = array_keys($result[0]);
	        
	        foreach($fields as $f){
	            if(!in_array($f, $result_fields)){
	                throw new SmartestDatabaseException("SmartestMysql::queryFieldsToArrays() Requested field '".$f."' not found in returned fieldset: ".implode(', ', $result_fields), SmartestDatabaseException::LOST_CONNECTION);
	            }
	        }
	        
	        $return_data = array();
	        $i = 0;
	        
	        foreach($result as $record){
	            foreach($fields as $f){
	                $return_data[$f][$i] = $record[$f];
	            }
	            ++$i;
	        }
	        
	        return $return_data;
	        
	    }else{
	        
	        $r = array();
	        
	        foreach($fields as $f){
	            $r[$f] = array();
	        }
	        
	        return $r;
	        
	    }
	    
	}
	
	protected function getSelectQueryResult($query, $refresh=false){
	    
	    $hash = $this->getHashFromQuery($query);
	    
	    if(isset($this->queryHashes, $hash) && !$refresh){
	        
	        return $this->loadQueryDataFromCache($query);
	        
	    }else{
	        
	        $resultArray = array();
		    
		    $result = @mysql_query($query, $this->dblink);
		
		    for($i=0;$i<@mysql_num_rows($result);$i++){
    			$row = @mysql_fetch_array($result, MYSQL_ASSOC);
    			$resultArray[] = $row;
    		}
		    
		    $this->queryHashes[$hash] = 1;
    		$this->saveQueryDataToCache($query, $resultArray);
		
    		return $resultArray;
	        
	    }
	    
	}
	
	protected function queryReturnsData($querystring){
	    return in_array($this->getQueryType($querystring), $this->retrievalQueryTypes);
	}
	
	protected function loadQueryDataFromCache($query){
	    
	    $hash = $this->getHashFromQuery($query);
	    $cache_name = 'SMCR'.$this->_request_id.$hash;
	    // $cache_name = 'smartest_mysql_cached_result'.$hash;
	    
	    if(SmartestCache::hasData($cache_name, true)){
	        $result = SmartestCache::load($cache_name, true);
	        $this->recordCachedQuery($query);
	        return $result;
	    }else{
	        $result = $this->getSelectQueryResult($query, true);
	        $this->recordLiveQuery($query);
	        return $result;
	    }
	}
	
	protected function saveQueryDataToCache($query, $data){
	    
	    $hash = $this->getHashFromQuery($query);
	    
	    if(is_array($data)){
	        // $cache_name = 'smartest_mysql_cached_result'.$hash;
	        $cache_name = 'SMCR'.$this->_request_id.$hash;
	        SmartestCache::save($cache_name, $data, -1, true);
        }else{
            throw new SmartestDatabaseException("SmartestMysql::saveQueryDataToCache() expects array.", SmartestDatabaseException::INVALID_CACHE_DATA);
        }
	    
	}
	
	public function clearQueryHistoryCache(){
	    
	    foreach($this->queryHashes as $hash=>$bin){
	        // $cache_name = 'smartest_mysql_cached_result'.$hash;
	        $cache_name = 'SMCR'.$this->_request_id.$hash;
	        SmartestCache::clear($cache_name, true);
	    }
	    
	}
	
	public function specificQuery($wantedField, $qualifyingField, $qualifyingValue, $table){
	    
	    if(!$this->dblink){
		    $this->reconnect();
		}
	    
		$query = "SELECT $wantedField, $qualifyingField FROM $table WHERE $qualifyingField='$qualifyingValue' LIMIT 1";
		
		if($result = $this->rawQuery($query)){
			$value = @mysql_result($result, 0, $wantedField);
			return $value;
		}
	}
	
	protected function getQueryType($querystring){
        
        $bits = explode(' ', $querystring);
        if(isset($bits[0])){
            $first = $bits[0];
            return strtoupper($first);
        }else{
            return false;
        }
	}
	
	
	public function query($querystring, $file='', $line='') {
		switch ($this->getQueryType($querystring)){
			
			case 'UPDATE': // update and delete queries return number of affected rows.
			case 'DELETE':
				
				if($result = $this->rawQuery($querystring)){
					return @mysql_affected_rows($result);
				}
				
				break;

			case 'INSERT': // do insert query and return id of last newly inserted row.
				$result = $this->rawQuery($querystring);
        		return @mysql_insert_id($this->dblink);
				
				break;

			case 'SELECT': // select query returns data as array.
			case 'SHOW': // select query returns data as array.
				if($data = $this->queryToArray($querystring)){
					return $data;
				}else{
					return false;
				}
				
				break;

			default:
				return UNSUPPORTED_QUERY;
		}

	}
	
	protected function recordLiveQuery($querystring){
		
		if(strlen(mysql_error($this->dblink)) > 0){
			
			$errno = mysql_errno($this->dblink);
			$error = mysql_error($this->dblink);
			
			$error = "MySQL ERROR ".@mysql_errno($this->dblink) . ": " . @mysql_error($this->dblink);
			
			if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
			
			    $e = new SmartestDatabaseException('MySQL ERROR: '.@mysql_errno($this->dblink) . ": " . @mysql_error($this->dblink), SmartestDatabaseException::UNKNOWN_TYPE);
			
			    foreach($e->getTrace() as $event){
			        if($event['function'] == 'queryToArray' || $event['function'] == 'rawQuery'){
			            $e->setMessage($e->getMessage().'. Query: <code>'.$querystring.'</code> in '.$event['file'].' on line '.$event['line'].'.');
			            $e->setQuery($querystring);
			            $e->setClientErrorMessage($error);
			            $e->setClientErrorId($errno);
			            break;
			        }
			    
			    }
			
			    throw $e;
			
		    }
			
		}else{
		    
		    if(defined('SM_DEVELOPER_MODE') && constant('SM_DEVELOPER_MODE')){
		        
		        // There was no error, but we use the exceptions's backtrace
		        /* $e = new SmartestException('MySQL QUERY: ', SM_ERROR_DB);
		        $stack = $e->getTrace();
		        
		        foreach($stack as $key => $event){
		            $guiltykey = $key + 1;
			        if($event['function'] == 'queryToArray' || $event['function'] == 'rawQuery'){
			            $error = basename($stack[$guiltykey]['file']).' on line '.$stack[$guiltykey]['line'];
			            break;
			        }
			    } */
			    
			    $error = "Query OK";
			    
		    }else{
		    
			    $error = "Query OK";
			
		    }
		}
		
		$this->lastQuery = $querystring;
		$this->queryHistory[] = $querystring."; ".$error;
	    
	}
	
	protected function recordCachedQuery($querystring){
		
		$this->cachedQueryHistory[] = $querystring;
	    
	}
	
	// Only allows files in System/Install/SqlScripts/ to be run:
	public function executeSqlFile($full_file_path){ 
	    if(SmartestFileSystemHelper::isSafeFileName($full_file_path, SM_ROOT_DIR.'System/Install/SqlScripts/')){
	        
	        $sql = str_replace("\n", '', file_get_contents($full_file_path));
	        preg_match_all('/(CREATE|SHOW|DROP|DELETE|SELECT|INSERT|UPDATE|ALTER|GRANT)\s.+?;/i', $sql, $matches, PREG_PATTERN_ORDER);
	        $queries = $matches[0];
	        
	        foreach($queries as $q){
	            $this->rawQuery($q);
	        }
	        
	    }else{
	        throw new SmartestDatabaseException("The file ".$full_file_path." is outside the permitted storage area for SQL files: ".SM_ROOT_DIR.'System/Install/SqlScripts/', SmartestDatabaseException::INVALID_SQL_FILE_STORAGE_DIR);
	    }
	}
	
	// this function added because $lastQuery is going to become a protected property.
	public function getLastQuery(){
	    return $this->lastQuery;
	}
	
	public function getDebugInfo(){
		return array('live'=>$this->queryHistory, 'cached'=>$this->cachedQueryHistory);
	}
	
}