<?php

class SmartestDynamicDataSetCondition extends SmartestDataObject{
    
    protected function __objectConstruct(){
		
		// $this->addPropertyAlias('ModelId', 'itemclass_id');
		$this->_table_prefix = 'setrule_';
		$this->_table_name = 'SetRules';
		
	}
    
}