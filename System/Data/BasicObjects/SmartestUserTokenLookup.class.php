<?php

class SmartestUserTokenLookup extends SmartestDataObject{

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'utl_';
		$this->_table_name = 'UsersTokensLookup';
		
	}
	
}