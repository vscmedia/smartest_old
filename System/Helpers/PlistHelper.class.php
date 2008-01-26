<?php

// wrapper for PList Class

include_once 'PList/class.plist.php';

class SmartestPlistHelper extends Plist{
    
    // string url
	protected $url;
	
	// DomDocument doc
	protected $doc;
	
	// string dateFormat
	protected $dateFormat;
	
	// integer timeZone
	protected $timeZone;
	
	// object dataArray
	protected $dataArray;
    
    public function __construct($filename = '', $dateFormat = null, $timeZone = 0){
        parent::Plist($filename = '', $dateFormat = null, $timeZone = 0);
    }
    
    public function load($filename = ''){

    }
    
    public function save($filename = ''){
        
    }
    
    public function set($name, $value){
        
    }
    
    public function get($name){
        
    }
    
}