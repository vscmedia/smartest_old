<?php

class SmartestUserMessage{
	
	private $_message;
	private $_type;
	
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