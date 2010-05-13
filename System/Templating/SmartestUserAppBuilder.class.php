<?php

class SmartestUserAppBuilder extends SmartestEngine{
    
    public function __construct(){
        
        $this->left_delimiter = '<'.'?sm:';
		$this->right_delimiter = ':?'.'>';
        
    }
    
}