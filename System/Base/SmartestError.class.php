<?php

class SmartestError{

	protected $verboseType;
	protected $message;
	protected $code;
	
	public function __construct($message="[No Error Message Given]", $code=100, $verboseType="Unknown or Miscellaneous"){
		$this->code = $code;
		$this->message = $message;
		$this->verboseType = $verboseType;
	}
	
	public function getMessage(){
		return $this->message;
	}
	
	public function getVerboseType(){
		return $this->verboseType;
	}
	
	public function getType(){
		return $this->code;
	}

}