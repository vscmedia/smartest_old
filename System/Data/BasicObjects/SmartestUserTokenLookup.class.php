<?php

class SmartestUserTokenLookup extends SmartestBaseUserTokenLookup{

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'utlookup_';
		$this->_table_name = 'UsersTokensLookup';
		
	}
	
}