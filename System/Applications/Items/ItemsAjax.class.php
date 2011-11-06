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
	    
	    // echo '<code>/ajax:datamanager/regularizeItemClassProperty?property_id='.$this->getRequestParameter('property_id').'</code><br />';
	    
	    if($property->findBy('webid', $this->getRequestParameter('property_id'))){
	        
	        $num_values_affected = 0;
	        
	        try{
	        
    	        foreach($property->getStoredValues($this->getSite()->getId()) as $ipv){
	            
    	            $save = false;
	            
    	            if(strlen($ipv->getRawValue()) && $new_live_value_object = SmartestDataUtility::objectize($ipv->getRawValue(), $property->getDatatype())){
    	                if($ipv->getRawValue() != $new_live_value_object->getStorableFormat()){
    	                    // echo "Changed live value from ".$ipv->getRawValue()." to ".$new_live_value_object->getStorableFormat().", ";
    	                    $new_raw_value = $new_live_value_object->getStorableFormat();
    	                    if(strlen($new_raw_value)){
    	                        $ipv->_setContent($new_raw_value, false);
    	                        $save = true;
	                        }
                        }
    	            }
	            
    	            if(strlen($ipv->getRawValue(true)) && $new_draft_value_object = SmartestDataUtility::objectize($ipv->getRawValue(true), $property->getDatatype())){
    	                if($ipv->getRawValue(true) != $new_draft_value_object->getStorableFormat()){
    	                    // echo "Changed draft value from ".$ipv->getRawValue(true)." to ".$new_draft_value_object->getStorableFormat()."<br />";
    	                    $new_raw_value = $new_draft_value_object->getStorableFormat();
    	                    if(strlen($new_raw_value)){
    	                        $ipv->_setContent($new_raw_value);
    	                        $save = true;
	                        }
                        }
    	            }
	            
    	            // Only save the ones that need changing
    	            if($save){
    	                $ipv->save();
    	                ++$num_values_affected;
                    }
	            
    	        }
	        
    	        if($num_values_affected > 0){
    	            $this->send(2, 'status');
    	            $this->send($num_values_affected, 'num_changed_values');
    	            $property->setLastRegularized(time());
    	            $property->setStorageMigrated(1);
    	            $property->save();
                }else{
                    $this->send(1, 'status');
                }
            
            }catch(SmartestException $e){
                
                $this->send(0, 'status');
                $this->send($e->getMessage(), 'status_message');
                
            }
	        
	    }else{
	        
	        $this->send(0, 'status');
	        
	    }
	    
	}
	
	public function updateItemClassPropertyOrder(){
	    
	    $model = new SmartestModel;
	    if($model->find($this->getRequestParameter('class_id'))){
	        
	        $ids = explode(',', $this->getRequestParameter('property_ids'));
	        $properties = $model->getPropertiesForReorder();
	        
	        if(count($ids) == count($properties)){
	            
	            foreach($ids as $position=>$property_id){
	                $properties[$property_id]->setOrderIndex($position);
	                $properties[$property_id]->save();
	            }
	            
	            $model->refreshProperties();
	            
	        }
	        
	    }
	    
	    exit;
	    
	}

}