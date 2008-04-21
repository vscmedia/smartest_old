<?php

class SmartestUserMessage{
	
	private $_message;
	private $_type;
	
	const INFO = 1;
	const SUCCESS = 2;
	const WARNING = 4;
	const ERROR = 8;
	const FAIL = 8;
	const ACCESSDENIED = 16;
	const ACCESS_DENIED = 16;
	
	public function __construct($message, $type){
		$this->_message = $message;
		$this->_type = $type;
	}
	
	public function getMessage(){
		return $this->_message;
	}
	
	public function getType(){
		return $this->_type;
	}
	
}