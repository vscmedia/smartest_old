<?php

class SmartestUserToken extends SmartestBaseUserToken{

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'token_';
		$this->_table_name = 'UserTokens';
		
	}
	
}