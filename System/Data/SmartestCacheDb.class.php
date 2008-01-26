<?php

class SmartestCacheDb extends SmartestSqllite{
	
	public function __construct(){
		parent::__construct('System/Cache/Data/smartest_cache.db');
		$this->init();
		
	}
	
	protected function init(){
		
		$tables = $this->getTables();
		
		if(in_array('cache', $tables)){
			// table 'cache' exists - check structure
		}else{
			// table doesn't exist - this is probably a first run
			$sql = "CREATE TABLE cache(id CHAR(32) PRIMARY KEY, expiry INTEGER(10), is_smartest CHAR(1));COMMIT;";
			$this->rawQuery($sql);
		}
	}
	
	public function save(){
		
	}
	
	public function getExpiredCacheItems(){
		
	}
	
}