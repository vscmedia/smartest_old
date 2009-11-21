<?php

// PFFR, or Password File Format Reader is a fast, and above all scaleable class for reading files using formatting similar to the /etc/passwd file on *nix systems.
// Smartest uses this format to store information about large, changeable sets of like objects

class SmartestPffrFile{
    
    protected $_file;
    protected $_data;
    
    public function __construct($full_file_path){
        
        if(is_file($full_file_path)){
            
            $this->_file = new SmartestFile;
            $this->_file->loadFile($full_file_path);
            
        }else{
            throw new SmartestException("The Password file was not found: ".$full_file_path);
        }
        
    }
    
    public function getLines(){
        
        $lines = explode("\n", trim($this->_file->getContent()));
        
        foreach($lines as $k=>$l){
            if($l{0} == '#'){
                unset($lines[$k]);
            }
        }
        
        return array_values($lines);
        
    }
    
    public function getFields(){
        
        $lines = $this->getLines();
        $first_line = $lines[0];
        
        $parts = explode(':', $first_line);
        return $parts;
        
    }
    
    public function getRawDataLines(){
        
        $lines = $this->getLines();
        array_shift($lines);
        return $lines;
        
    }
    
    public function getData(){
        
        $field_names = $this->getFields();
        $data = $this->getRawDataLines();
        $final = array();
        
        foreach($data as $line){
            $r = $this->_prepareLine($line, $field_names);
            $final[$r[$field_names[0]]] = $r;
        }
        
        return $final;
        
    }
    
    protected function _prepareLine($line, $field_names){
        
        $data = explode(':', $line);
        
        if(count($data) == count($field_names)){
            return array_combine($field_names, $data);
        }else{
            throw new SmartestException("Incorrect field count in data");
        }
        
    }
    
}