<?php

// PFFR, or Password File Format Reader is a fast, and above all scaleable class for reading files using formatting similar to the /etc/passwd file on *nix systems.
// Smartest uses this format to store information about large, changeable sets of like objects

class SmartestPffrFile{
    
    protected $_file;
    protected $_data;
    
    public function __construct($full_file_path){
        
        if(is_file($file)){
            
            $this->_file = new SmartestFile;
            $this->_file->loadFile($full_file_path);
            
        }
        
    }
    
}