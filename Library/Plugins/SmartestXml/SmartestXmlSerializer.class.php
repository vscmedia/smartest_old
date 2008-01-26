<?php

// A wrapper for PHP's DomDocument class that provides a similar functionality to
// PEAR's XML_Serializer, only better - XML_Unserializer is bad at serializing
// different kinds of repeating data in the same structure

// this class doesn't have a lot of options. Indents are always '  ' and 
// attributes are always contained in '__attributes' as an associative array

class SmartestXmlSerializer{

	protected $_dataArray;
	protected $_domObject;
    protected $_rootTagName;
	
	public function __construct($rootTag, $data='', $encoding='UTF-8'){
	    
	    $this->_rootTagName = $rootTag;
	    
	    if(class_exists('DOMDocument')){
			$this->_domObject = new DOMDocument('1.0');
	        $this->_domObject->formatOutput = true;
			$this->_domObject->loadXML('<?xml version="1.0" encoding="'.$encoding.'"?'.'><'.$this->_rootTagName.' />');
	    }
	    
	    if(is_array($data)){
			$this->_dataArray = $data;
	    }
	}
	
	public function setData(array $data){
		$this->_dataArray = $data;
	}
	
	public function serialize(){
	    
	    $rootTagElement = $this->_domObject->getElementsByTagName($this->_rootTagName)->item(0);
	    
	    try{
	    
	        foreach($this->_dataArray as $name => $data){
			    $this->addChild($name, $data, $rootTagElement);
	        }
	    
        } catch(DOMException $e){
            throw new SmartestException('XML serializing didn\'t work: '.$e->getMessage(), SM_ERROR_USER);
        }
	    
	    return $this->_domObject->saveXml();
	}
	
	protected function isRepeatedElementArray($data){
	    
	    if(is_array($data)){
	        
	        $nextKeyShouldBe = 0;
	        
	        foreach($data as $key => $value){
	            if($key == $nextKeyShouldBe){
	                $nextKeyShouldBe++;
	            }else{
	                return false;
	            }
	        }
	        
	        return true;
	        
	    }else{
	        return false;
	    }
	    
	}
    
	public function addChild($name, $data, $parent){
	    
	    if(is_array($data)){
            if(isset($data['_content'])){
                foreach($data as $key=>$data){
                    if($key != '_content'){
                        // any other elements will be attributes
                        $parent->setAttribute($key, $data);
                    }else{
                        $contentElement = $this->_domObject->createElement($name, $data);
                        // $parent->appendChild($contentElement);
                        $this->addChild($key, $data, $contentElement);
                    }
                }
            }else{
                // all children wil be elements
                if($this->isRepeatedElementArray($data)){
                    
                    $this->addChild($name, $data, $parent);
                    
                }else{
                    
                    foreach($data as $key => $data){
                    
                        if(substr($key, 0, 3) == 'xml'){
                            $parent->setAttribute($key, $data);
                        }else{
                    
                            $this->addChild($key, $data, $parent);
                        
                        }
                    
                        // $contentElement = $this->_domObject->createElement($name, $data);
                        // $parent->appendChild($contentElement);
                    
                    }
                
                }
            }
	    }else{
	        $this->addStringChild($name, $data, $parent);
	    }
	    
	    /* if($name != '__attributes' && $name != '__settings'){
			if(is_array($data)){
		    
			    // create container element
			    $containerElem = $this->_domObject->createElement($name);
			    $parent->appendChild($containerElem);
		    
			    // loop through data adding child elements
			    foreach($data as $childName => $childData){
					$this->addElement($childName, $childData, $containerElem);
			    }
		    
			}else{
				// 
			    if(preg_match('/^[\w\/\?!_\*=\)\(:;\.,-]{0,255}$/', $data)){
					// the data is just a normal string - no spaces or special chars
					$element = $this->_domObject->createElement($name, $data);
			    }else{
					// make a CDATA element
					$element = $this->_domObject->createElement($name);
					$cdata = $this->domObject->createCDATASection($data);
					$element->appendChild($cdata);
			    }
			}
	    
	    }else if($name == '__attributes' && is_array($data)){
			// parse and add attributes
			foreach($data as $attributeName => $attributeValue){
				$parent->setAttribute($attributeName, $attributeValue);
			}
	    } */
	
	}
	
	protected function addStringChild($name, $data, $parent){
	    if(is_string($data)){
	        if(preg_match('/^[\w\/\?!_\*=\)\(:;\.,-]{0,255}$/', $data)){
				// the data is just a normal string - no spaces or special chars
				$element = $this->_domObject->createElement($name, $data);
				$parent->appendChild($element);
		    }else{
				// make a CDATA element
				$element = $this->_domObject->createElement($name);
				$cdata = $this->_domObject->createCDATASection($data);
				$element->appendChild($cdata);
				$parent->appendChild($element);
		    }
	    }
	}
	
	function getUnserializedData(){
		
	}
	
}

?>
