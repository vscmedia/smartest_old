<?php

class ItemsAjax extends SmartestSystemApplication{

    public function simpleItemTextSearch(){
	    
	    $db = SmartestDatabase::getInstance('SMARTEST');
	    $sql = "SELECT * FROM Items WHERE (item_site_id='".$this->getSite()->getId()."' OR item_shared=1) AND item_deleted='0' AND (item_name LIKE '%".$this->getRequestParameter('query')."%' OR item_search_field LIKE '%".$this->getRequestParameter('query')."%') ORDER BY item_name";
	    $result = $db->queryToArray($sql);
	    $items = array();
	    
	    foreach($result as $r){
	        $item = new SmartestItem;
	        $item->hydrate($r);
	        $items[] = $item;
	    }
	    
	    $this->send($items, 'items');
	    
	}
	
	public function tagItem(){
	    
	    $item = new SmartestItem;
	    
	    if($item->find($this->getRequestParameter('item_id'))){
	        
	        if($item->tag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	            echo 'true';
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function unTagItem(){
	    
	    $item = new SmartestItem;
	    
	    if($item->find($this->getRequestParameter('item_id'))){
	        
	        if($item->untag($this->getRequestParameter('tag_id'))){
	            header('HTTP/1.1 200 OK');
	            echo 'true';
	        }
	        
	    }else{
	        
	        header('HTTP/1.1 404 Not Found');
	        
	    }
	    
	}
	
	public function getTextIpvAutoSuggestValues(){
	    
	    $p = new SmartestItemProperty;
	    
	    if($p->find($this->getRequestParameter('property_id'))){
	        $this->send($p->getSuggestionsForFormBasedOnIncomplete($this->getRequestParameter('str'), $this->getSite()->getId()), 'values');
	    }else{
	        $this->send(array(), 'values');
	    }
	    
	}
	
	public function regularizeItemClassProperty(){
	    
	    $property = new SmartestItemProperty;
	    
	    if($property->find($this->getRequestParameter('property_id'))){
	        
	        foreach($property->getStoredValues($this->getSite()->getId()) as $ipv){
	            $new_live_value_object = SmartestDataUtility::objectize($ipv->getRawValue(), $property->getDatatype());
	            $new_draft_value_object = SmartestDataUtility::objectize($ipv->getRawValue(true), $property->getDatatype());
	            $ipv->setValue($new_live_value_object->getStorableFormat());
	            $ipv->setDraftValue($new_draft_value_object->getStorableFormat());
	            $ipv->save();
	        }
	        
	        $this->send(true, 'success');
	        
	    }else{
	        
	        $this->send(false, 'success');
	        
	    }
	    
	}

}