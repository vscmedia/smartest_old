<?php

// include_once 'System/Helpers/ItemsHelper.class.php';
// include_once "Managers/SchemasManager.class.php";

class Sets extends SmartestSystemApplication{

	var $itemsManager;
	
	public function __smartestApplicationInit(){
		// $this->itemsManager = new ItemsHelper();
		// $this->SchemasManager = new SchemasManager();	
	}
	
	public function startPage($get){
		// $sets = $this->manager->getSets($this->getSite()->getId());   
		
		$du = new SmartestDataUtility;
		$sets = $du->getDataSets(false, $this->getSite()->getId());
		$this->setTitle("Data Sets");
		$this->send($sets, 'sets');
		
	}
	
	public function getItemClassSets($get){
	    
	    if(is_numeric($this->getRequestParameter('class_id'))){
	        
	        $model_id = $this->getRequestParameter('class_id');
	        $model = new SmartestModel;
	        
	        if($model->find($model_id)){
	        
	            $sets = $model->getDataSets($this->getSite()->getId());
	        
	            $this->setTitle("Sets of ".$model->getPluralName());
    		    $this->send($sets, 'sets');
    		    $this->send($model, 'model');
    		    $this->send($this->getUser()->hasToken('create_remove_properties'), 'can_edit_properties');
    		    $this->send((bool) count($model->getMetaPages()), 'has_metapages');
    		
		    }else{
		        
		        $this->addUserMessageToNextRequest("The model ID was not recognized.", SmartestUserMessage::ERROR);
		        $this->redirect('smartest/models');
		        
		    }
	        
	    }
	    
	}
	
	public function deleteSetConfirm(){
	    
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find($this->getRequestParameter('set_id'))){
	        $this->send($set, 'set');
	    }else{
	        $this->addUserMessageToNextRequest("The set ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
	}
	
	public function deleteSet(){
	    
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find($this->getRequestParameter('set_id'))){
	        $set->delete();
	        $this->addUserMessageToNextRequest("The set was successfully deleted.", SmartestUserMessage::SUCCESS);
	        $this->formForward();
	    }else{
	        $this->addUserMessageToNextRequest("The set ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	    }
	    
		// $set_id = mysql_real_escape_string($get['set_id']);
		// return $this->manager->deleteSet($set_id);		
	}
	
	public function addSet($get){		

        $du = new SmartestDataUtility;
        
        $set_name = $this->getRequestParameter('set_name');
		
		if(is_numeric($this->getRequestParameter('class_id'))){
		    
		    $model_ids = $du->getModelIds();
		    
		    if(in_array($this->getRequestParameter('class_id'), $model_ids)){
		        
		        $this->send(false, 'allow_choose_model');
		        $model = new SmartestModel;
		        $model->find($this->getRequestParameter('class_id'));
		        $this->send($model, 'model');
		        
		        if($this->getRequestParameter('add_item')){
		            $item = new SmartestItem;
		            if($item->find($this->getRequestParameter('add_item'))){
		                if($item->getItemclassId() == $model->getId()){
		                    $this->send($item->getId(), 'add_item_id');
		                }
		            }
	            }
	            
	            if($this->getRequestParameter('from')){
	                
	                if($this->getRequestParameter('from') == 'editItem'){
	                    
	                    if(is_numeric($this->getRequestParameter('itemproperty_id'))){
	                        
	                        $property = new SmartestItemProperty;
	                        
	                        if($property->find($this->getRequestParameter('itemproperty_id'))){
	                            
	                            if($property->getDatatype() == 'SM_DATATYPE_CMS_ITEM_SET'){
	                                
	                                // var_dump($model->getId());
	                                
	                                $item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'));
	                                
	                                if($property->getItemclassId() == $item->getModelId()){
	                                    $this->send($item, 'item');
	                                    $this->send($property, 'property');
	                                }else{
	                                    $this->addUserMessage('The property you have selected is for a different model than the item selected.', SmartestUserMessage::WARNING);
	                                }
	                                
	                            }else{
	                                $this->addUserMessage('The property you have selected is not a data set property', SmartestUserMessage::WARNING);
	                            }
	                            
	                        }else{
	                            
	                            $this->addUserMessage('The property you have selected cannot be found', SmartestUserMessage::ERROR);
	                            
	                        }
	                        
	                    }
	                    
	                }
	                
	            }
		        
	        }else{
	            $models = $du->getModels();
    		    $this->send($models, 'models');
    		    $this->send(true, 'allow_choose_model');
	        }
	        
		}else{
		    $models = $du->getModels(false, $this->getSite()->getId());
		    $this->send($models, 'models');
		    $this->send(true, 'allow_choose_model');
		}
		
		$this->send('Unnamed data set', 'start_name');
		$this->setTitle('Create a new item set');
		
		/* if(is_numeric($this->getRequestParameter('model_id'))){
			$properties = $this->itemsManager->getItemClassProperties($this->getRequestParameter('model_id')); 		
		} */
		
		// return array("type"=>$this->getRequestParameter('type') ,"model_id"=>$this->getRequestParameter('model_id') ,"set_name"=>$set_name ,"models"=> $models, "properties"=>$properties);		
	}
	
	public function insertSet($get, $post){
	    
	    $new_set_name = SmartestStringHelper::toVarName($this->getRequestParameter('set_name'));
	    
	    $set = new SmartestCmsItemSet;
	    $model = new SmartestModel;
	    
	    if(strlen($new_set_name) && !$set->hydrateBy('name', $new_set_name)){
	        
	        if($model->find($this->getRequestParameter('set_model_id'))){
	        
	            $set->setName($new_set_name);
	            $set->setLabel($this->getRequestParameter('set_name'));
	            $set->setType($this->getRequestParameter('set_type'));
	            $set->setItemclassId($this->getRequestParameter('set_model_id'));
	            $set->setSiteId($this->getSite()->getId());
	            $shared = $this->getRequestParameter('set_shared') ? 1 : 0;
	            $set->setShared($shared);
	            $set->save();
	        
            }else{
                
                $this->addUserMessageToNextRequest('The specified model ID was not found', SmartestUserMessage::ERROR);
                $this->formForward();
                
            }
	        
	        if($this->getRequestParameter('from') == 'editItem'){
                
                if(is_numeric($this->getRequestParameter('for_item_property_id'))){
                    
                    $property = new SmartestItemProperty;
                    
                    if($property->find($this->getRequestParameter('for_item_property_id'))){
                        
                        if($property->getDatatype() == 'SM_DATATYPE_CMS_ITEM_SET'){
                            
                            $item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('for_item_id'));
                            
                            if($property->getItemclassId() == $item->getModelId()){
                                $item->setPropertyValueByNumericKey($property->getId(), $set->getId());
                                $item->save();
                                $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                            }else{
                                $this->addUserMessageToNextRequest('The property you are adding a set for is for a different model than the item selected.', SmartestUserMessage::WARNING);
                                $this->redirect('/datamanager/editItem?item_id='.$item->getId());
                            }
                            
                        }else{
                            $this->addUserMessageToNextRequest('The property for which you are adding a set is not a data set property', SmartestUserMessage::WARNING);
                            $this->formForward();
                        }
                        
                    }else{
                        
                        // $this->addUserMessage('The property you have selected cannot be found', SmartestUserMessage::ERROR);
                        
                    }
                    
                }
                
            }else{
	        
    	        if($set->getType() == 'DYNAMIC'){
    	            $this->addUserMessageToNextRequest("Your set has been successfully created. Now you probably want to give it some rules.", SmartestUserMessage::SUCCESS);
                }else{
                    if($this->getRequestParameter('add_item_id')){
                        $item_id = (int) $this->getRequestParameter('add_item_id');
                        // add the item to the set
                        $set->addItems(array($item_id));
                        $this->redirect('/datamanager/editItem?item_id='.$item_id);
                    }else{
                        $this->addUserMessageToNextRequest("Your set has been successfully created. Now you probably want to decide what goes in there.", SmartestUserMessage::SUCCESS);
                    }
                }
            
            }
            
	        $this->redirect('/sets/editSet?set_id='.$set->getId());
	        
	    }else{
	        $this->addUserMessageToNextRequest("Error: Your set could not be created because the name you supplied was already taken or invalid.", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/sets');
	    }
	    
	    
	}

	public function editSet($get, $post){  
	
		$set_id = $this->getRequestParameter('set_id');
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find($set_id)){
	        
	        if($set->getType() == "DYNAMIC"){
	            
	            $this->send($this->getUser()->getAllowedSites(), 'sites');
	            $this->send($set->getModel()->getAvailableSortProperties(), 'properties');
	            $this->send($set, 'set');
	            $this->send(true, 'show_form');
	            $this->send(SmartestQuery::RANDOM, 'random_value');
	            $this->send($this->getUser()->hasToken('edit_set_name'), 'can_edit_set_name');
	            $this->setTitle('Edit dynamic set');
	            
	            $formTemplateInclude = "editSet.dynamic.tpl";
	        
	        }else{
	            
	            $set_members = $set->getMembers(SM_QUERY_ALL_DRAFT, null, $this->getSite()->getId());
	            
	            // fetch all item ids
	            $all_items = $set->getModel()->getSimpleItems($this->getSite()->getId(), SM_QUERY_ALL_DRAFT, '', $set->getMemberIds(null, null, $this->getSite()->getId()));
	            
	            $this->send($set, 'set');
	            $this->send($set_members, 'members');
	            $this->send($all_items, 'non_members');
	            $this->send($this->getUser()->hasToken('edit_set_name'), 'can_edit_set_name');
	            $this->setTitle('Edit static set');
	            
	            $formTemplateInclude = "editSet.static.tpl";
	            
	        }
	        
	        $this->send($set->getModel()->isShared() && ($set->getSiteId() == $this->getSite()->getId()), 'show_shared');
	        $this->send($set->getModel(), 'model');
	        $this->send($formTemplateInclude, 'formTemplateInclude');
	        
	    }else{
	        $this->send(false, 'show_form');
	        $this->addUserMessage('The Set ID was not recognised', SmartestUserMessage::ERROR);
	    }
	    
	}
	
	public function editDynamicSetConditions(){
	    
	    $set_id = $this->getRequestParameter('set_id');
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find($set_id)){
	        
	        if($set->getType() == "DYNAMIC"){
	            $conditions = $set->getConditions();
	            $this->send($set->getModel(), 'model');
	            $this->send($conditions, 'conditions');
	            $this->send($set, 'set');
	            $this->send($set->getModel()->getProperties(), 'properties');
	        }else{
	            $this->addUserMessage('The set is not dynamic', SmartestUserMessage::ERROR);
	        }
	        
	    }else{
	        $this->addUserMessage('The set ID was not recognised', SmartestUserMessage::ERROR);
	    }
	    
	}
	
	public function updateDynamicSet($get, $post){
	    
	    if($this->requestParameterIsSet('set_id')){
	        
	        $set_id = $this->getRequestParameter('set_id');
	        
	        $set = new SmartestCmsItemSet;
	        
	        if($set->find($set_id)){
	            
	            $set->setLabel($this->getRequestParameter('set_label'));
	            
	            if($this->getUser()->hasToken('edit_set_name')){
	                $set->setName(SmartestStringHelper::toVarName($this->getRequestParameter('set_name')));
                }
                
	            $set->setSortField($this->getRequestParameter('set_sort_field'));
	            // $set->setDataSourceSiteId($this->getRequestParameter('set_data_source_site_id'));
	            if($set->getModel()->isShared()){
	                $set->setShared(($this->requestParameterIsSet('set_shared') && $this->getRequestParameter('set_shared')) ? 1 : 0);
                }
	            
	            $direction = ($this->getRequestParameter('set_sort_direction') == 'DESC') ? 'DESC' : 'ASC';
	            
	            $set->setSortDirection($direction);
	            $set->save();
	            
	            $success = true;
	            
	        }else{
	            
	            $success = false;
	            
	        }
	    }
	    
	    /* if(is_array($this->getRequestParameter('conditions'))){
	        
	        foreach($this->getRequestParameter('conditions') as $c_id => $c_post_data){
	            
	            if($c_post_data['operator'] == 8 || $c_post_data['operator'] == 9){
    	            $property_id = '_SMARTEST_ITEM_TAGGED';
    	        }else{
    	            $property_id = $c_post_data['property_id'];
    	        }
	            
	            $c = new SmartestDynamicDataSetCondition;
	            
	            if($c->hydrate($c_id)){
	                $c->setItempropertyId($property_id);
	                $c->setOperator($c_post_data['operator']);
	                $c->setValue($c_post_data['value']);
	                $c->save();
	            }
	        }
	    } 
	    
	    if(@$this->getRequestParameter('add_new_condition')){
	        
	        if($this->getRequestParameter('new_condition_operator') == 8 || $this->getRequestParameter('new_condition_operator') == 9){
	            $property_id = '_SMARTEST_ITEM_TAGGED';
	        }else{
	            $property_id = $this->getRequestParameter('new_condition_property_id');
	        }
	        
	        $c = new SmartestDynamicDataSetCondition;
	        $c->setSetId($set_id);
	        $c->setItempropertyId($property_id);
	        $c->setOperator($this->getRequestParameter('new_condition_operator'));
	        $c->setValue($this->getRequestParameter('new_condition_value'));
	        $c->save();
	        
	    } */
	    
	    $this->addUserMessageToNextRequest("Your set has been updated.", SmartestUserMessage::SUCCESS);
	    
	    if($this->getRequestParameter('_submit_action') == "continue" && $success){
	        $this->redirect('/sets/editSet?set_id='.$set->getId());
	    }else{
	        $this->formForward();
	    }
	    
	}
	
	public function updateDynamicSetConditions(){
	    
	    if($this->requestParameterIsSet('set_id')){
	        
	        $set_id = $this->getRequestParameter('set_id');
	        
	        $set = new SmartestCmsItemSet;
	        
	        if($set->find($set_id)){
	    
        	    if(is_array($this->getRequestParameter('conditions'))){
	        
        	        foreach($this->getRequestParameter('conditions') as $c_id => $c_post_data){
	            
        	            if($c_post_data['operator'] == 8 || $c_post_data['operator'] == 9){
            	            $property_id = '_SMARTEST_ITEM_TAGGED';
            	        }else{
            	            $property_id = $c_post_data['property_id'];
            	        }
	            
        	            $c = new SmartestDynamicDataSetCondition;
	            
        	            if($c->hydrate($c_id)){
        	                $c->setItempropertyId($property_id);
        	                $c->setOperator($c_post_data['operator']);
        	                $c->setValue($c_post_data['value']);
        	                $c->save();
        	            }
        	        }
        	    } 
	    
        	    if($this->getRequestParameter('new_condition_property_id') != 'IGNORE'){
	        
        	        if($this->getRequestParameter('new_condition_operator') == 8 || $this->getRequestParameter('new_condition_operator') == 9){
        	            $property_id = '_SMARTEST_ITEM_TAGGED';
        	        }else{
        	            $property_id = $this->getRequestParameter('new_condition_property_id');
        	        }
	        
        	        $c = new SmartestDynamicDataSetCondition;
        	        $c->setSetId($set_id);
        	        $c->setItempropertyId($property_id);
        	        $c->setOperator($this->getRequestParameter('new_condition_operator'));
        	        $c->setValue($this->getRequestParameter('new_condition_value'));
        	        
        	        $c->save();
	        
        	    }
        	    
        	    if($this->getRequestParameter('_submit_action') == "continue"){
        	        $this->redirect('/sets/editDynamicSetConditions?set_id='.$set->getId());
        	    }else{
        	        $this->formForward();
        	    }
	    
            }else{
                
                // Set not found
                
            }
            
        }else{
            
            // Set ID not provided
            
        }
	    
	}
	
	public function removeConditionFromSet($get){
	    
	    $c = new SmartestDynamicDataSetCondition;
	    
	    if($c->find($this->getRequestParameter('condition_id'))){
	        $c->delete();
	        $this->redirect('/sets/editSet?set_id='.$c->getSetId());
	    }else{
	        $this->formForward();
	    }
	    
	}
	
	public function transferItem($get, $post){
		
		$set_id = (int) $this->getRequestParameter('set_id');
		$set = new SmartestCmsItemSet;
		
		if($set->find($set_id)){
		    
		    if($set->getType() == "STATIC"){
		    
		        if($this->getRequestParameter('transferAction') == 'add'){
    			    $item_ids = ($this->requestParameterIsSet('available_items') && is_array($this->getRequestParameter('available_items'))) ? $this->getRequestParameter('available_items') : array();
    			    $set->addItems($item_ids);
    			    $set->fixOrderIndices();
    		    }else{
    			    $item_ids = ($this->requestParameterIsSet('used_items') && is_array($this->getRequestParameter('used_items'))) ? $this->getRequestParameter('used_items') : array();
    			    $set->removeItems($item_ids);
    			    $set->fixOrderIndices();
    		    }
		    
	        }else{
	            
	            $this->addUserMessageToNextRequest("The set is the wrong type to have items transferred into it.", SmartestUserMessage::WARNING);
	            
	        }
	        
	        $url = '/sets/editSet?set_id='.$set_id;
	        
	        if($this->getRequestParameter('from')){
	            $url .= '&from='.$this->getRequestParameter('from');
	        }
	        
	        if($this->getRequestParameter('item_id')){
	            $url .= '&item_id='.$this->getRequestParameter('item_id');
	        }
	        
		    $this->redirect($url);
		    exit;
		    
	    }else{
	        
	        $this->addUserMessageToNextRequest("The set ID was not recognized.", SmartestUserMessage::ERROR);
	        
	    }
		
		$this->formForward();
		
	}
	
	public function transferSingleItem($get, $post){
	    
	    $set_id = $this->getRequestParameter('set_id');
	    
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find($set_id)){
	        
	        // $item_id = (int) $request['item_id'];
	        $item_id = $this->getRequestParameter('item_id');
	        $item = new SmartestItem;
	        
	        if($item->find($item_id)){
	            // TODO: Check that the asset is the right type for this group
	            if($this->getRequestParameter('transferAction') == 'add'){
	                $set->addItems(array($item_id));
                }else{
                    $set->removeItems(array($item_id));
                }
                
                $set->fixOrderIndices();
                
	        }
	        
	    }else{
	        $this->addUserMessageToNextRequest("The set ID was not recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    if($this->hasFormReturnVar('item_id') || $this->getRequestParameter('returnTo') == 'editItem'){
            $this->redirect('/datamanager/editItem?item_id='.$item_id);
        }else{
            $this->formForward();
        }
	    
	}
	
	public function editStaticSetOrder($get){
	    
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find((int) $this->getRequestParameter('set_id'))){
	        
	        if($set->getType() == 'DYNAMIC'){
	            $this->addUserMessageToNextRequest("The set you are trying to change the order of is a dynamic saved query. Try changing its conditions instead.", SM_USER_MESSAGE_WARNING);
	            $this->redirect('/sets/editSet?set_id='.$set->getId());
	        }
	        
	        $this->send($set->getModel(), 'model');
	        $this->send($set, 'set');
	        $this->send($set->getMembers(SM_QUERY_ALL_DRAFT, null, $this->getSIte()->getId()), 'items');
	        $this->send($this->getApplicationPreference('reorder_static_set_num_cols'), 'num_cols');
	        
	    }else{
	        $this->addUserMessageToNextRequest("The set ID was not recognised.", SM_USER_MESSAGE_ERROR);
	        $this->redirect('/smartest/sets');
	    }
	    
	}
	
	public function moveItemInStaticSet($get){
	    
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find((int) $this->getRequestParameter('set_id'))){
	        
	        if($set->getType() == 'DYNAMIC'){
	            $this->addUserMessageToNextRequest("The set you are trying to change the order of is a dynamic saved query. Try changing its conditions instead.", SM_USER_MESSAGE_WARNING);
	            $this->redirect('/sets/editSet?set_id='.$set->getId());
	        }
	        
	        $set->fixOrderIndices();
	        
	        $last_order_index = max((count($set->getSimpleMembers(SM_QUERY_ALL_DRAFT)) - 1), 0);
	        
	        $lookup = new SmartestSetItemLookup;
	        
	        if($lookup->loadForOrderChange($set->getId(), $this->getRequestParameter('item_id'))){
	            
	            $current_position = $lookup->getOrder();
	            
	            if($this->getRequestParameter('direction') == 'up'){
	                
	                if($current_position > 0){
	                    
	                    $previous_lookup = new SmartestSetItemLookup;
	                    
	                    if($previous_lookup->loadForOrderChangeByPosition($set->getId(), ($current_position - 1))){
	                        $lookup->setOrder($previous_lookup->getOrder());
	                        $lookup->save();
	                        $previous_lookup->setOrder($current_position);
	                        $previous_lookup->save();
	                    }
	                }
	                
	            }else if($this->getRequestParameter('direction') == 'down'){
	                
	                if($current_position < $last_order_index){
	                    
	                    $next_lookup = new SmartestSetItemLookup;
	                    
	                    if($next_lookup->loadForOrderChangeByPosition($set->getId(), ($current_position + 1))){
	                        $lookup->setOrder($next_lookup->getOrder());
	                        $lookup->save();
	                        $next_lookup->setOrder($current_position);
	                        $next_lookup->save();
	                    }
	                    
	                }
	                
	            }
	            
	        }
	        
	        // print_r(SmartestPersistentObject::get('db:main')->getDebugInfo());
	        $this->redirect('/sets/editStaticSetOrder?set_id='.$set->getId());
	        
	    }else{
	        $this->addUserMessageToNextRequest("The set ID was not recognised.", SM_USER_MESSAGE_ERROR);
	        $this->redirect('/smartest/sets');
	    }
	    
	}
	
	public function previewSet($get){     
	    
	    $set_id = $this->getRequestParameter('set_id');
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find($set_id)){
	        
	        if(strlen($this->getRequestParameter('mode'))){
    	        if($set->getType() == 'DYNAMIC'){
    	            $this->setApplicationPreference('preview_dynamic_set_mode', $this->getRequestParameter('mode'));
  	            }else{
  	                $this->setApplicationPreference('preview_static_set_mode', $this->getRequestParameter('mode'));
  	            }
  	            $mode = (int) $this->getRequestParameter('mode');
    	    }else{
    	        if($set->getType() == 'DYNAMIC'){
    	            $mode = $this->getApplicationPreference('preview_dynamic_set_mode', SM_QUERY_ALL_DRAFT_CURRENT);
  	            }else{
  	                $mode = $this->getApplicationPreference('preview_static_set_mode', SM_QUERY_ALL_DRAFT_CURRENT);
  	            }
    	    }
    	    
    	    $this->send($mode, 'mode');
    	    
    	    $items = $set->getMembers($mode, null, $this->getSite()->getId());
	    
    	    $this->send($items, 'items');
    	    $this->send(count($items), 'count');
    	    $this->send($set, 'set');
    	    
    	    $model = new SmartestModel;
    	    
    	    if($model->find($set->getItemclassId())){
    	        $this->send($model, 'model');
    	        $this->setFormReturnUri();
        	    $this->setFormReturnDescription('data set');
    	    }
    	    
    	    $this->send($this->getApplicationPreference('item_list_style', 'grid'), 'list_view');
	    
        }else{
            
            $this->addUserMessageToNextRequest("The set ID was not recognized.", SM_USER_MESSAGE_ERROR);
            $this->redirect('/smartest/sets');
            
        }
	    
	}
	
	public function copySet($get){
		
		$id=$this->getRequestParameter('set_id');	
		$set = $this->manager->getSet($id);
		$set_type = $set['set_type'];
		$name=mysql_real_escape_string($set['set_name']);		
		$model_id=mysql_real_escape_string($set['set_itemclass_id']);	
		$set_name=$this->manager->getUniqueSetName($name);

		$set_id=$this->manager->insertSet($set_name,$model_id,$set_type);
		
		if($set_type=='DYNAMIC'){
			
			$rules = $this->manager->getSetRules($id);
			
			foreach($rules as $key=>$val){
				$property_id=$val['setrule_itemproperty_id'];
				$condition=$val['setrule_rule'];
				$content_value=$val['setrule_value'];
				$this->manager->addSetRule($set_id, "", $property_id, strtoupper($condition),$content_value);
			}
		}
		
		if($set_type=='STATIC'){
			$items = $this->manager->getStaticSetItems($id);
			foreach($items as $key=>$val){
				$setlookup_item_id=$val['setlookup_item_id'];
				$setlookup_order=$val['setlookup_order'];
				$this->manager->addStaticSetItems($set_id, $setlookup_item_id, $setlookup_order);
			}
		}
	}
	
	public function setExternalFeedAggregator(){
	    
	    $set_id = $this->getRequestParameter('set_id');
	    $set = new SmartestCmsItemSet;
	    
	    if($set->find($set_id)){
	        
	        /* $feeds = $set->getFeeds();
	        
	        $feed = new SimplePie();
            $feed->set_cache_location(SM_ROOT_DIR.'System/Cache/SimplePie/');
            $feed->set_feed_url($feeds[4]);
            $feed->set_input_encoding('UTF-8');
            $feed->set_item_class('SmartestExternalFeedItem');
            $feed->handle_content_type();
            $feed->init();
            $items = $feed->get_items();
            
            foreach($items as $item){
                $item->bf = $item->get_feed();
                /* $t = $item->get_title();
                $p = $item->get_permalink(); 
            }
            
            $this->send($items, 'items');*/
            
             $this->send($set->getFeedItems(), 'items');
	        
	    }
	    
	    // print_r($feeds);
	    
	}

/* 	public function removeRule($get){
		
	}
	
	
	//database queries
	public function insertRuleSet($get, $post){
	}

	public function deleteRule($get, $post){
		$setrule_id=$this->getRequestParameter('setrule_id');
		$this->manager->deleteSetRule($setrule_id);
	}

	public function insertRule($get, $post){
		$set_id=$this->getRequestParameter('set_id');
		$itemproperty_id=$this->getRequestParameter('itemproperty_id');
		$condition=$this->getRequestParameter('condition');
		$value=$this->getRequestParameter('value');
		$this->manager->addSetRule($set_id, "", $itemproperty_id, strtoupper($condition),$value);	 
	}
	
	public function deleteRuleSet($get, $post){
		
	}
	
// 	function updateSet($get, $post){
// 		$set_id=$this->getRequestParameter('set_id');	
//     		$items=$this->getRequestParameter('cmbRole');
//     		$this->manager->updateSet($set_id,$items);   
// 	}

	public function getSet($get="", $post=""){
		
		//this is from feed.class.php
		/* $const['EQUALS'] = 0;
		$const['NOT_EQUAL'] = 1;
		$const['CONTAINS'] = 2;
		$const['STARTS_WITH'] = 4;
		$const['ENDS_WITH'] = 5;
	
		$sets = $this->manager->getSets();
		$id = $get['id'];
		$model_id = null;
		
		foreach($sets as $set){
			if($set['set_id'] == $id){
				$model_id = $set['set_itemclass_id'];
			}
		}
		
		$rules = $this->manager->getSetRules($id);
		$dataquery = new DataQuery($model_id);
		
		foreach($rules as $rule){
			$dataquery->addCondition($rule['setrule_itemproperty_id'], $const[$rule['setrule_rule']], $rule['setrule_value']);		
		}
		
		$data = $dataquery->selectToArray();
		var_dump($data);

		
		//this is from item.class.php
// 		$this->schemasManager = new SchemasManager();
		$search_string = $get['search'];
		$items = $this->itemsManager->getItemsInClass($get["class_id"]);
		$itemBaseValues = $this->itemsManager->getItemClassBaseValues($get["class_id"]);    
		$itemClassMembers = $this->itemsManager->countItemClassMembers($get["class_id"]); //2
		$itemClassPropertyCount = $this->itemsManager->countItemClassProperties($get["class_id"]); //4
		
		return array('items'=>$items, 'properties'=>$info, 'itemClassMembers'=>$itemClassMembers, 'itemBaseValues'=>$itemBaseValues, 'itemClassPropertyCount'=>$itemClassPropertyCount, 'itemClassMemberCount'=>count($items));   
		
		
	}

	/* public function chooseSchemaForExport($get){
		
		$set_id=$this->getRequestParameter('set_id');			
		$set = $this->manager->getSet($set_id);		
		$schemas=$this->itemsManager->getSchemas();
		return(array("set"=>$set,"schemas"=>$schemas));
		
	}

	public function exportDataOptions($get){
		
		$set_id=$this->getRequestParameter('set_id');
		$schema_id=$this->getRequestParameter('schema_id');
		$schema_name=$this->SchemasManager->getSchemaName($schema_id);
		$set = $this->manager->getSet($set_id);	
		$pairing=$this->manager->getparing($set_id,$schema_id);
		$count=count($pairing);
		
		if($count==1){
			$name=$pairing[0]['dataexport_name'];
		}
		
		return(array("set"=>$set,"schema_id"=>$schema_id,"count"=>$count,"schema_name"=>$schema_name,"name"=>$name));
	}

	public function exportData($get){
		
		$set_id=$this->getRequestParameter('set_id');	
		$schema_id=$this->getRequestParameter('schema_id');	

		$msg=$this->getRequestParameter('msg');	
		$set = $this->manager->getSet($set_id);
		$model_id = $set['itemclass_id'];
		$definition = $this->itemsManager->getItemClassProperties($model_id);
		
		if($schema_id){
			$repeatingDefinition=$this->manager->getRepeatingDefinition($schema_id);
			$array=$this->manager->getschemasettings($schema_id);
		}

		return(array("set"=>$set,"Properties"=>$definition,"schemas"=>$this->itemsManager->getSchemas(),"schema_id"=>$schema_id,"schemsDefinition"=>$repeatingDefinition,"name"=>$name,"msg"=>$msg,"Settings"=>$array));
	}	

  	public function exportDataInsert($get,$post){
		
		$class_id=$post["class_id"];	
		$set_id=$post["set_id"];
		$name=$post["paring_name"];$varname = $this->_string->toVarName($name);
		$schema=$post["schema"];
		$settings_edit=$post["settings_edit"];//print_r($settings_edit);
		$pairing_count = $this->manager->checkExportName($name,$set_id);

		if($pairing_count > 0){
			header("Location:".$this->domain.$this->module."/exportData?set_id=$set_id&schema_id=$schema&msg=1");
		}else{
	
			$schema_varname=$this->SchemasManager->getSchemaVarName($schema);
			$paring_id=$this->manager->insertParing($schema,$class_id);
			$export=$this->manager->insertDataExport($name,$set_id,$paring_id,$varname);
			$definition = $this->itemsManager->getItemClassProperties($class_id);
		
			foreach($definition as $prop){
				$property_id=$prop['itemproperty_id'];
				$vocabulary_id=$post[$property_id];
				$this->manager->insertParingDetail($paring_id,$property_id,$vocabulary_id);
			}

			if($settings_edit){

				$settings = $this->manager->getschemasettings($schema);

				foreach($settings as $setting){
					$vocabulary_id=$setting['vocabulary_id'];
					$vocabulary_value=$post[$vocabulary_id];//print_r($vocabulary_value);
					$this->manager->insertSettingDetail($paring_id,$vocabulary_id,$vocabulary_value);
				}
			}

			$this->redirect("exportSuccess?set_id=$set_id&schema=$schema_varname&dataexport=$varname");
		}
	}	

	public function editExportData($get){
		
		$set_id=$this->getRequestParameter('set_id');	
		$schema_id=$this->getRequestParameter('schema_id');
		$pair_id=$this->getRequestParameter('pair_id');
	// 	$name=$this->getRequestParameter('name');
		$msg=$this->getRequestParameter('msg');	
		$set = $this->manager->getSet($set_id);
		$model_id = $set['itemclass_id'];
		$definition = $this->itemsManager->getItemClassProperties($model_id);//print_r($definition);
	
		if(!$pair_id){
			$pairing=$this->manager->getparing($set_id,$schema_id);
			$pair_id=$pairing[0]['paring_id'];	
			$name=$pairing[0]['dataexport_name'];
		}else{
			$name=$this->database->specificQuery("dataexport_name", "dataexport_pairing_id", $pair_id, "DataExports");
		}
	
		foreach($definition as $key=>$prop){
			$property_id=$prop['itemproperty_id'];
			$vocabulary_id=$this->manager->getSinglePairingVocabulary($pair_id,$property_id);
			$definition[$key]['pairingvocabulary']=$vocabulary_id;
		}	
	
		if($schema_id){
			$repeatingDefinition=$this->manager->getRepeatingDefinition($schema_id);
			$settings=$this->manager->getschemasettings($schema_id);
			
			foreach($settings as $key=>$setting){
				$setting_id = $setting['vocabulary_id'];
				$value = $this->manager->getSingleParingSetting($pair_id,$setting_id);
				$settings[$key]['pairingvocabulary']=$value;
			}
		}
		
		return(array("set"=>$set,"Properties"=>$definition,"schema_id"=>$schema_id,"pairing_id"=>$pair_id,"schemsDefinition"=>$repeatingDefinition,"name"=>$name,"msg"=>$msg,"Settings"=>$settings));
	}

	public function updateExportData($get,$post){
		
		$pairing_id=$post["pairing_id"];
		$class_id=$post["class_id"];
		$set_id=$post["set_id"];
		$schema=$post["schema"];
		$name=$post["paring_name"];$varname = $this->_string->toVarName($name);
		$export=$this->manager->updateDataExport($name,$pairing_id);
		$definition = $this->itemsManager->getItemClassProperties($class_id);

		foreach($definition as $prop){
			$property_id=$prop['itemproperty_id'];
			$vocabulary=$post[$property_id];
			$this->manager->updateParingDetail($pairing_id,$property_id,$vocabulary_id);
		}
	
		$settings=$this->manager->getschemasettings($schema);
		
		foreach($settings as $setting){
			$vocabulary_id=$setting['vocabulary_id'];
			$vocabulary_value=$post[$vocabulary_id];
			$this->manager->updateSettingDetail($pairing_id,$vocabulary_id,$vocabulary_value);
		}
		
		$schema_varname=$this->SchemasManager->getSchemaVarName($schema);
		$this->redirect($this->domain.$this->module."exportSuccess?set_id=$set_id&schema=$schema_varname&dataexport=$varname");
	}

	public function choosePairingForExport($get){
		$set_id=$get["set_id"];	$schema_id=$get["schema_id"];	
		$set = $this->manager->getSet($set_id);	
		$pairing=$this->manager->getparing($set_id,$schema_id);
		$schema_varname=$this->SchemasManager->getSchemaVarName($schema_id);
		return(array("set"=>$set,"pairing"=>$pairing,"schema"=>$schema_varname,"schema_id"=>$schema_id));
	}
	
	public function exportSuccess($get){
		$set_id=$get["set_id"];$set = $this->manager->getSet($set_id);
		$schema_varname=$get["schema"];
		$varname=$get["dataexport"];
		return(array("set"=>$set,"pairing"=>$pairing,"schema_varname"=>$schema_varname,"dataexport_varname"=>$varname));
	}
	
	public function getDataExports($get){
		$pairing=$this->manager->getDataExports();
		$count=count($pairing);
		return(array("pairing"=>$pairing,"count"=>$count));
	}
	
	public function editDataExportFeed($get){
		$export_id=$get["export_id"];
		$name=$get["pairing_name"];
		$sets=$this->manager->chooseSetForDataExport($export_id);
		$pairing=$this->manager->choosePairingForDataExport($export_id);
		//$pairing=$this->manager->getDataExports();
		return(array("export_id"=>$export_id,"pairing_name"=>$name,"sets"=>$sets,"pairing"=>$pairing));
	}
	
	public function updateDataExportFeed($get,$post){
		$export_id=$post["export_id"];
		$set=$post["set"];$pair=$post["pair"];
		$this->manager->updateDataExportFeed($export_id,$set,$pair);
	} */
}
