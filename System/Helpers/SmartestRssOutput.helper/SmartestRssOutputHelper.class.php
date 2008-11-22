<?php

// include 'XML/Serializer.php';

class SmartestRssOutputHelper{
    
    protected $_limit = 15;
    protected $_items = array();
    protected $_domObject;
    protected $_domRootTagElement;
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
        
        if(class_exists('DOMDocument')){
			
			$this->_domObject = new DOMDocument('1.0');
	        $this->_domObject->formatOutput = true;
			$this->_domObject->loadXML('<?xml version="1.0" encoding="UTF-8" ?'.'><!-- generator="Smartest '.SM_INFO_VERSION_NUMBER.'" --><rss version="2.0" />');
	    
	        $this->_domRootTagElement = $this->_domObject->getElementsByTagName('rss')->item(0);
    	    $channel = $this->_domObject->createElement("channel");
    	    $this->_domRootTagElement->appendChild($channel);
	    
    	    $author = $this->_domObject->createElement("author");
    	    $author_text = $this->_domObject->createTextNode($this->getAuthor());
    	    $author->appendChild($author_text);
	    
    	    $title = $this->_domObject->createElement("title");
    	    $title_text = $this->_domObject->createTextNode($this->getTitle());
    	    $title->appendChild($title_text);
	    
    	    $generator = $this->_domObject->createElement("generator");
    	    $generator_text = $this->_domObject->createTextNode('Smartest v'.SM_INFO_VERSION_NUMBER);
    	    $generator->appendChild($generator_text);
	    
    	    $channel->appendChild($author);
    	    $channel->appendChild($title);
    	    $channel->appendChild($generator);
	    
    	    $this->addItems();
	    
    	    return $this->_domObject->saveXml();
	    
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
        
        foreach($this->_items as $object){
        
            $channel = $this->_domObject->getElementsByTagName('channel')->item(0);
	        $item = $this->_domObject->createElement("item");
	        
	        $title = $this->_domObject->createElement("title");
	        $title_text = $this->_domObject->createTextNode($object->getTitle());
	        $title->appendChild($title_text);
	        
	        $description = $this->_domObject->createElement("description");
	        $description_text = $this->_domObject->createTextNode($object->getDescription());
	        $description->appendChild($description_text);
	    
	        $pubDate = $this->_domObject->createElement("pubDate");
	        $pubDate_text = $this->_domObject->createTextNode(date('r', $object->getDate()));
	        $pubDate->appendChild($pubDate_text);
	        
	        $link = $this->_domObject->createElement("link");
	        $link_text = $this->_domObject->createTextNode($object->getUrl());
	        $link->appendChild($link_text);
	        
	        $item->appendChild($title);
    	    $item->appendChild($description);
    	    $item->appendChild($link);
    	    $item->appendChild($pubDate);
	        
	        $channel->appendChild($item);
	    
        }
        
    }
    
}