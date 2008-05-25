<?php

class SmartestError{

	protected $_verboseType;
	protected $_exception;
	
	public function __construct($exception, $verboseType="Unknown or Miscellaneous"){
		$this->_exception = $exception;
		$this->_verboseType = $verboseType;
	}// $message, $type, @$this->errorCodes[$type]
	
	public function getMessage(){
		return $this->_exception->getMessage();
	}
	
	public function getVerboseType(){
		return $this->_verboseType;
	}
	
	public function getType(){
		return $this->_exception->getCode();
	}
	
	public function getBackTrace(){
	    return $this->_exception->getTrace();
	}

}