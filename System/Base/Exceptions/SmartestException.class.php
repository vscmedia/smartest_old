<?php

class SmartestException extends Exception{
	
	public function __construct($message, $code=100){
		parent::__construct();
		$this->code = $code;
		$this->message = $message;
	}
	
	public function setMessage($message){
	    $this->message = $message;
	}

}