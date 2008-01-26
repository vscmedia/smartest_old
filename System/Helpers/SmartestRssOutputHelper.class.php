<?php

// include 'XML/Serializer.php';

class SmartestRssOutputHelper{
    
    protected $_limit = 15;
    protected $_items = array();
    protected $_domObject;
    protected $_title;
    protected $_author;
    protected $_data_array = array();
    
    public function __construct($data){
        if(is_array($data)){
            $this->_items = $data;
            if(count($data)){
                
            }
        }else{
            // do nothing
        }
    }
    
    public function getXml(){
        // $xml = new DomDocument;
        
        
        
        // if(class_exists('DOMDocument')){
		//	$this->_domObject = new DOMDocument('1.0');
	    //    $this->_domObject->formatOutput = true;
		//	$this->_domObject->loadXML();
	    // }
	    
	    // $rss = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?'.'><!-- generator="Smartest 0.6.5" --><rss version="2.0" />');
	    
	    // $channel = new SimpleXMLElement("<channel />");
	    
	    // $rootTagElement = $this->_domObject->getElementsByTagName('rss')->item(0);
	    // $channel = $this->_domObject->createElement("channel");
	    // $rootTagElement->appendChild($channel);
	    // return $this->_domObject->saveXml();
        // $rss = 
        
        $this->_data_array = array();
        
        $options = array(
          XML_SERIALIZER_OPTION_INDENT        => '  ',
          XML_SERIALIZER_OPTION_RETURN_RESULT => true,
          "defaultTagName" => "item"
        );
        
        $serializer = new XML_Serializer($options);
        
        $this->_data_array['channel'] = array(
            'author'=>$this->getAuthor(),
            'title'=>$this->getTitle(),
            'generator'=>'Smartest v 0.6.5'
        );
        
        $this->addItems();
        
        $serializer->setOption("rootName", "rss");
        // $serializer->setOption("scalarAsAttributes", true);
        $serializer->setOption("rootAttributes", array("version" => '0.9'));
        
        if($serializer->serialize($this->_data_array)){
            return $serializer->getSerializedData();
        }
    }
    
    public function send(){
        header("Cache-Control: public, must-revalidate\r\n");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\r\n");
        header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ). ' GMT'."\r\n");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: text/xml; charset=utf-8');
        echo $this->getXml();
        exit;
    }
    
    public function setLimit($limit){
        if(is_numeric($limit)){
            $this->_limit = ceil($limit);
        }
    }
    
    public function getTitle(){
        return $this->_title;
    }
    
    public function setTitle($t){
        $this->_title = $t;
    }
    
    public function getAuthor(){
        return $this->_author;
    }
    
    public function setAuthor($t){
        $this->_author = $t;
    }
    
    public function addItems(){
        
        $data = array();
        
        foreach($this->_items as $object){
            $item = array();
            $item['title'] = $object->getTitle();
            $item['description'] = $object->getDescription();
            $item['pubDate'] = date('r', $object->getDate());
            $this->_data_array['channel'][] = $item;
        }
        
        return $data;
    }
    
}