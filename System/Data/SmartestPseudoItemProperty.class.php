<?php

class SmartestPseudoItemProperty{
    
    protected $_id;
    
    public function setId($id){
	    $this->_id = $id;
	}
	
	public function getId(){
	    return $this->_id;
	}
    
}