<?php

class SmartestUserToken extends SmartestDataObject{

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'token_';
		$this->_table_name = 'UserTokens';
		
	}
	
}