<?php

class ItemPropertyValueEditor extends SmartestSystemApplication{
    
    public function chooseItems(){
        
        $item = new SmartestCmsItem;
        
        if($item->find($this->getRequestParameter('item_id'))){
            
            $item->setDraftMode(true);
            $this->send($item, 'item');
            $property = new SmartestItemProperty;
            
            if($property->find($this->getRequestParameter('property_id'))){
                
                $this->send($property, 'property');
                
                if($property->getItemClassId() == $item->getItem()->getModelId()){
                    $this->send($property->getPossibleValues(), 'options');
                    $item->getPropertyValueByNumericKey($property->getId());
                    $this->send($item->getPropertyValueByNumericKey($property->getId())->getIds(), 'selected_ids');
                }else{
                    $this->addUserMessageToNextRequest("Item and property are from different models", SmartestUserMessage::ERROR);
                    $this->formForward();
                }
                
            }else{
                
                $this->addUserMessageToNextRequest("The property ID was not recognized", SmartestUserMessage::ERROR);
                $this->formForward();
                
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was not recognized", SmartestUserMessage::ERROR);
            $this->formForward();
            
        }
        
    }
    
    public function updateItemsSelection(){
        
        if(is_numeric($this->getRequestParameter('item_id'))){
            
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                
                if(is_numeric($this->getRequestParameter('property_id'))){
                
                    if($item->getModel()->hasPropertyWithId($this->getRequestParameter('property_id'))){
                        
                        $property = new SmartestItemProperty;
                        
                        if($property->find($this->getRequestParameter('property_id'))){
                        
                            if(is_array($this->getRequestParameter('items'))){
                                $ids = array_keys($this->getRequestParameter('items'));
                            }else{
                                $ids = array();
                            }
                        
                            $item->setPropertyValueByNumericKey($property->getId(), $ids);
                            $item->save();
                            $this->addUserMessageToNextRequest("The attached items for this property were successfully updated.", SmartestUserMessage::SUCCESS);
                            $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                        
                        }else{
                            
                            $this->addUserMessageToNextRequest("The property ID was not recognized.", SmartestUserMessage::ERROR);
                            
                        }
                        
                    }else{
                        
                        $this->addUserMessageToNextRequest("The model '".$item->getModel()->getName()."' does not have a property with that ID.", SmartestUserMessage::ERROR);
                        
                    }
                
                }else{
                    
                    $this->addUserMessageToNextRequest("The property ID was in an invalid format.", SmartestUserMessage::ERROR);
                    
                }
                
            }else{
                
                $this->addUserMessageToNextRequest("The item ID was not recognized.", SmartestUserMessage::ERROR);
                
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was in an invalid format.", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
        
    }
    
    public function chooseFiles(){
        
        $item = new SmartestCmsItem;
        
        if($item->find($this->getRequestParameter('item_id'))){
            
            $item->setDraftMode(true);
            $this->send($item, 'item');
            $property = new SmartestItemProperty;
            
            if($property->find($this->getRequestParameter('property_id'))){
                
                $this->send($property, 'property');
                
                if($property->getItemClassId() == $item->getItem()->getModelId()){
                    $options = $property->getPossibleValues();
                    $this->send($options, 'options');
                    $item->getPropertyValueByNumericKey($property->getId());
                    $ids = $item->getPropertyValueByNumericKey($property->getId())->getIds();
                    $this->send($ids, 'selected_ids');
                }else{
                    $this->addUserMessageToNextRequest("Item and property are from different models", SmartestUserMessage::ERROR);
                    $this->formForward();
                }
                
            }else{
                
                $this->addUserMessageToNextRequest("The property ID was not recognized", SmartestUserMessage::ERROR);
                $this->formForward();
                
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was not recognized", SmartestUserMessage::ERROR);
            $this->formForward();
            
        }
        
    }
    
    public function updateFilesSelection(){
        
        if(is_numeric($this->getRequestParameter('item_id'))){
            
            if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                
                if(is_numeric($this->getRequestParameter('property_id'))){
                
                    if($item->getModel()->hasPropertyWithId($this->getRequestParameter('property_id'))){
                        
                        $property = new SmartestItemProperty;
                        
                        if($property->find($this->getRequestParameter('property_id'))){
                        
                            if(is_array($this->getRequestParameter('items'))){
                                $ids = array_keys($this->getRequestParameter('items'));
                            }else{
                                $ids = array();
                            }
                            
                            $item->setPropertyValueByNumericKey($property->getId(), $ids);
                            $item->save();
                            $this->addUserMessageToNextRequest("The attached files for this property were successfully updated.", SmartestUserMessage::SUCCESS);
                            $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                        
                        }else{
                            
                            $this->addUserMessageToNextRequest("The property ID was not recognized.", SmartestUserMessage::ERROR);
                            
                        }
                        
                    }else{
                        
                        $this->addUserMessageToNextRequest("The model '".$item->getModel()->getName()."' does not have a property with that ID.", SmartestUserMessage::ERROR);
                        
                    }
                
                }else{
                    
                    $this->addUserMessageToNextRequest("The property ID was in an invalid format.", SmartestUserMessage::ERROR);
                    
                }
                
                $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                
            }else{
                
                $this->addUserMessageToNextRequest("The item ID was not recognized.", SmartestUserMessage::ERROR);
                
            }
            
        }else{
            
            $this->addUserMessageToNextRequest("The item ID was in an invalid format.", SmartestUserMessage::ERROR);
            
        }
        
        $this->formForward();
        
    }
    
    public function editAssetData($get){
	    
	    // get item id and property id
	    // load item property, draft info
	    $item_id = (int) $this->getRequestParameter('item_id');
	    $property_id = (int) $this->getRequestParameter('property_id');
	    
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        $property = new SmartestItemPropertyValueHolder;
	        
	        if($property->hydrate($property_id)){
	            
	            $property->setContextualItemId($item_id);
	            $asset = $property->getData()->getDraftContent();
	            
	            $existing_render_data = $property->getData()->getInfo(true);
	            
	            // print_r($asset);
	            
	            if(is_object($asset)){
	                
	                $this->send($property, 'property');
	                $this->send($item, 'item');
	                $this->send($item->getModel(), 'model');
	                
	                $type = $asset->getTypeInfo();
	                $this->send($type, 'asset_type');
	                $this->send($asset->__toArray(), 'asset');
	                
	                if(isset($type['param'])){

            	        $raw_xml_params = $type['param'];
                        $params = array();
            	        foreach($raw_xml_params as $rxp){
            	            
            	            if(isset($rxp['default'])){
            	                $params[$rxp['name']]['xml_default'] = $rxp['default'];
            	                $params[$rxp['name']]['value'] = $rxp['default'];
                            }else{
                                $params[$rxp['name']]['xml_default'] = '';
                                $params[$rxp['name']]['value'] = '';
                            }
                            
                            $params[$rxp['name']]['type'] = $rxp['type'];
                            $params[$rxp['name']]['asset_default'] = '';
            	        }
            	        
            	        $this->send($type, 'asset_type');

            	    }else{
            	        $params = array();
            	    }
            	    
            	    $asset_params = $asset->getDefaultParameterValues();
            	    
            	    foreach($params as $key=>$p){
            	        // default values from xml are set above.
            	        
            	        // next, set values from asset
            	        if(isset($asset_params[$key]) && strlen($asset_params[$key])){
            	            $params[$key]['value'] = $asset_params[$key];
            	            $params[$key]['asset_default'] = $asset_params[$key];
            	        }
            	        
            	        // then, override any values that already exist
            	        if(isset($existing_render_data[$key]) && strlen($existing_render_data[$key])){
            	            $params[$key]['value'] = $existing_render_data[$key];
            	        }
        	        }
        	        
        	        $this->send($params, 'params');
        	        
        	        // print_r($params);
	                
	            }
	            
	        }
	        
	    }
	    
	    
	}
	
	public function updateAssetData($get, $post){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    $property_id = (int) $this->getRequestParameter('property_id');
	    $values = is_array($this->getRequestParameter('params')) ? $this->getRequestParameter('params') : array();
	    $new_values = array();
	    
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        $property = new SmartestItemPropertyValueHolder;
	        
	        if($property->hydrate($property_id)){
	            
	            $property->setContextualItemId($item_id);
	            $value_object = $property->getData();
	            $asset = $value_object->getDraftContent();
	            
	            $existing_render_data = $value_object->getInfo(true);
	            
	            // $asset = new SmartestAsset;
	            
	            if(is_object($asset)){
	                
	                $type = $asset->getTypeInfo();
	                
	                if(isset($type['param'])){

            	        $raw_xml_params = $type['param'];
                        $params = array();
            	        
            	        foreach($raw_xml_params as $rxp){
            	            
            	            if(isset($rxp['default'])){
            	                $params[$rxp['name']]['xml_default'] = $rxp['default'];
            	                $params[$rxp['name']]['value'] = $rxp['default'];
                            }else{
                                $params[$rxp['name']]['xml_default'] = '';
                                $params[$rxp['name']]['value'] = '';
                            }
                            
                            $params[$rxp['name']]['type'] = $rxp['type'];
                            $params[$rxp['name']]['asset_default'] = '';
            	        }

            	    }else{
            	        $params = array();
            	    }
            	    
            	    $asset_params = $asset->getDefaultParameterValues();
            	    
            	    // print_r($values);
            	    
            	    foreach($params as $key=>$p){
            	        // default values from xml are set above.
            	        
            	        // next, set values from asset
            	        if(isset($asset_params[$key]) && strlen($asset_params[$key])){
            	            $v = $asset_params[$key];
            	        }
            	        
            	        // then, override any values that already exist
            	        if(isset($existing_render_data[$key]) && strlen($existing_render_data[$key])){
            	            $v = $existing_render_data[$key];
            	        }
            	        
            	        if(isset($values[$key])){
            	            $v = $values[$key];
            	        }
            	        
            	        // print_r($v);
            	        
            	        $value_object->setInfoField($key, $v);
            	        
        	        }
        	        
        	        $value_object->save();
        	        
	                $this->addUserMessageToNextRequest("The display parameters were updated", SmartestUserMessage::SUCCESS);
	                
	            }else{
	                
	                $this->addUserMessageToNextRequest("No asset is currently selected for this property", SmartestUserMessage::ERROR);
	                
	            }
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest("The property ID wasn't recognized", SmartestUserMessage::ERROR);
	            
	        }
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The item ID wasn't recognized", SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
    
}