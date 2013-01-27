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
	        $this->send($p->getSuggestionsForFormBasedOnIncomplete(addslashes($this->getRequestParameter('str')), $this->getSite()->getId()), 'values');
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
	
	public function createNewItemFromItemEditForm(){
	    
	    $item_id = $this->getRequestParameter('host_item_id');
	    $property_id = $this->getRequestParameter('property_id');
	    $new_item_name = $this->getRequestParameter('name');
	    
	    $item = SmartestCmsItem::retrieveByPk($item_id);
        
        if(is_object($item)){
	        
	        $property = new SmartestItemProperty;
	        
	        if($property->find($property_id)){
	            
	            $input_data = new SmartestParameterHolder('Edit item field '.$property->getName());
	            $input_data->setParameter('id', 'item_property_'.$property->getId());
                $input_data->setParameter('name', 'item['.$property->getId().']');
                
                $property_model_id = $property->getForeignKeyFilter();
                $model = new SmartestModel;
                
                if($model->find($property_model_id)){
                    
                    $classname = $model->getClassName();
                    
                    if(class_exists($classname)){
                        
                        $new_item = new $classname;
                        $new_item->setName($new_item_name);
                        $new_item->setSlug(SmartestStringHelper::toSlug($new_item_name), $this->getSite()->getId());
                        $new_item->setSiteId($this->getSite()->getId());
                        $new_item->setIsPublic(false);
                        $new_item->save();
                        
                        if($this->getUser()->hasToken('author_credit')){
                            $new_item->addAuthorById($this->getUser()->getId());
                        }
                        
                        $item->setPropertyValueByNumericKey($property->getId(), $new_item->getId());
                        
                    }
                }
                
                $this->send($input_data, '_input_data');
                $this->send($new_item, 'value');
                $this->send($property, 'property');
	            
	            $test_string = "create a new item called '".$new_item_name."' and assign it as the draft value for property ".$property->getName()." of item ".$item->getName();
                $this->send($test_string, 'string');
    	        
	        }
	    
        }
	    
	}
	
	public function setItemNameFromInPlaceEditField(){
	    
	    $item_id = $this->getRequestParameter('item_id');
	    $item = SmartestCmsItem::retrieveByPk($item_id);
        
        if(is_object($item)){
            if($this->getUser()->hasToken('modify_items')){
                
                if(strlen($this->getRequestParameter('new_name'))){
                    $item->getItem()->setName($this->getRequestParameter('new_name'));
                }
                
                $item->getItem()->setModified(time());
                $item->getItem()->save();
            }
            
            header('HTTP/1.1 200 OK');
            echo $item->getItem()->getName();
            
        }
        
        exit;
	    
	}
	
	public function setItemSlugFromInPlaceEditField(){
	    
	    $item_id = $this->getRequestParameter('item_id');
	    $item = SmartestCmsItem::retrieveByPk($item_id);
        
        if(is_object($item)){
            
            if($this->getUser()->hasToken('edit_item_name')){
                if(strlen($this->getRequestParameter('new_slug'))){
                    $item->getItem()->setSlug(SmartestStringHelper::toSlug($this->getRequestParameter('new_slug')));
                }else{
                    if(!strlen($item->getItem()->getSlug())){
                        $item->getItem()->setSlug(SmartestStringHelper::toSlug($item->getItem()->getName()), true);
                    }
                }
                $item->getItem()->setModified(time());
                $item->getItem()->save();
            }
            
            header('HTTP/1.1 200 OK');
            echo $item->getItem()->getSlug();
            
        }
        
        exit;
	    
	}

}