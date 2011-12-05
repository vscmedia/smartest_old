<?php

// require_once SM_ROOT_DIR.'Managers/SchemasManager.class.php';
// require_once SM_ROOT_DIR.'System/Applications/Assets/AssetsManager.class.php';

class Items extends SmartestSystemApplication{

	// private $SchemasManager;
  
	protected function __smartestApplicationInit(){
	    $this->database = SmartestPersistentObject::get('db:main'); /* usage of the $this->database variable should be phased out in main classes */
		// $this->SchemasManager = new SchemasManager();
		// $this->AssetsManager = new AssetsManager();
	}
	
	public function startPage($get){	
	    
		$this->setTitle("Items");
		
	}
		
	public function getItemClasses(){
	    
		$this->setFormReturnUri();
		
		$du = new SmartestDataUtility;
		$models = $du->getModels(false, $this->getSite()->getId(), true);
		$this->send($models, 'models');
		
		$this->send($this->getUser()->hasToken('create_models'), 'allow_create_models');
		$this->send($this->getUser()->hasToken('delete_models'), 'allow_delete_models');
		
		$this->setTitle("Items");
		$this->setFormReturnDescription('models');
		
		$recent = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId());
        $this->send($recent, 'recent_items');
		
	}
	
	public function getItemClassSets($get){
	    
	    // $this->redirect('/sets/getItemClassSets?class_id='.$this->getRequestParameter('class_id'));
	    $this->redirect('@sets:model_sets?class_id='.$this->getRequestParameter('class_id'));
	    
	}

	public function getItemClassProperties($get){
		
		$this->send($this->getUser()->hasToken('edit_model'), 'can_edit_model');
	    $this->send($this->getUser()->hasToken('create_remove_properties'), 'can_edit_properties');
		
		if($this->getUser()->hasToken('create_remove_properties')){
		
    		$this->setFormReturnUri();
		
    		$itemclassid = (int) $this->getRequestParameter('class_id');
		
    		$model = new SmartestModel;
    		$model->find($itemclassid);
		
    		$this->setTitle("Model Properties | ".$model->getName());
		
    		$definition = $model->getProperties();
		
    		$this->send($model, 'model');
    		$this->send($definition, 'definition');
    		$create_remove_properties = $this->getUser()->hasToken('create_remove_properties');
		
    		// At some point these will be separated
    		$this->send($create_remove_properties, 'can_add_properties');
    		$this->send($create_remove_properties, 'can_delete_properties');
		
    		// Retrieve recently edited
            $recent = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $itemclassid);
            $this->send($recent, 'recent_items');
        
        }else{
            
            $this->addUserMessageToNextRequest('You don\'t have permission to edit model properties', SmartestUserMessage::ACCESS_DENIED);
            $this->formForward();
            
        }
		 
	}
	
	public function editItemClassPropertyOrder(){
	    
	    $model = new SmartestModel;
	    
	    if($model->find($this->getRequestParameter('class_id'))){
	        $this->send($model, 'model');
	        $this->send($this->getUser()->hasToken('create_remove_properties'), 'can_edit_properties');
	        $this->send($model->getProperties(), 'properties');
	    }
	    
	}

	/* public function itemClassSettings($get){

		$itemclassid = $this->getRequestParameter('class_id');
		$itemclass = $this->manager->getItemClass($itemclassid);		
		$itemClassProperties = $this->manager->getSettingItemsInClass($itemclassid); 
		return (array("settings"=>$itemClassProperties, "itemclass"=>$itemclass));

	}
	
	function insertItemClassSettings($get,$post){
		foreach($post as $key=>$val){
		    $this->manager->updateItemSettings($key,$val); 
		}
	} */
	
	public function getItemClassMembers($get, $post){
  	    
  	    $this->send($this->getApplicationPreference('item_list_style', 'grid'), 'list_view');
  	    $this->send($this->getUser()->hasToken('create_remove_properties'), 'can_edit_properties');
  	    
  	    $this->setFormReturnUri();
  	    
  	    if(is_numeric($this->getRequestParameter('mode'))){
  	        $mode = (int) $this->getRequestParameter('mode');
  	    }else{
  	        $mode = SM_STATUS_CURRENT;
  	    }
  	    
  	    $model = new SmartestModel;
  	    
  	    if($this->getRequestParameter('use_plural_name')){
  	        $found_model = $model->findBy('varname', $this->getRequestParameter('plural_name'));
  	    }else{
  	        $model_id = $this->getRequestParameter('class_id');
  	        $found_model = $model->find($model_id);
        }
  	    
  	    $query = $this->getRequestParameter('q') ? $this->getRequestParameter('q') : '';
  	    
  	    if($found_model){
  	        
  	        $this->send((bool) count($model->getMetaPages()), 'has_metapages');
  	        
  	        if(is_file($model->getClassFilePath())){
  	            
  	            if(class_exists($model->getClassName())){
  	                $class_exists = true;
  	            }else{
  	                
  	                $du = new SmartestDataUtility;
      	            $du->flushModelsCache();
      	            
  	                if($model->buildClassFile()){
      	                $class_exists = true;
      	            }else{
      	                $class_exists = false;
      	                $this->addUserMessage("The class file for this model could not be built", SmartestUserMessage::WARNING);
                    }
  	            }
  	            
  	        }else{
  	            
  	            $du = new SmartestDataUtility;
  	            $du->flushModelsCache();
  	            
  	            if($model->buildClassFile()){
  	                $class_exists = true;
  	            }else{
  	                $class_exists = false;
  	                $this->addUserMessage("The class file for this model could not be built", SmartestUserMessage::WARNING);
                }
                
  	        }
  	        
  	        $items = $model->getSimpleItems($this->getSite()->getId(), $mode, $query);
  	        $allow_create_new = ($this->getUser()->hasToken('add_items') && $class_exists);
  	        
  	        $this->setTitle($model->getPluralName());
  	        $this->send($allow_create_new, 'allow_create_new');
  	        $this->send($items, 'items');
  	        $this->send($mode, 'mode');
  	        $this->send(count($items), 'num_items');
  	        $this->send($model, 'model');
  	        $this->send($query, 'query');
  	        
  	        $this->setTitle($model->getPluralName());
  	        $this->setFormReturnDescription(strtolower($model->getPluralName()));
  	        
  	        // Retrieve recently edited
  	        $recent = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $model_id);
  	        $this->send($recent, 'recent_items');
  	        
  	    }else{
  	        $this->addUserMessageToNextRequest('The model ID was not recognized.', SmartestUserMessage::ERROR);
  	        $this->redirect('/smartest/models');
  	    }
	
	}
    
    public function releaseUserHeldItems($get){
        
        $model = new SmartestModel;
        
        if($model->hydrate($this->getRequestParameter('class_id'))){
            $num_held_items = $this->getUser()->getNumHeldItems($model->getId(), $this->getSite()->getId());
	        $this->getUser()->releaseItems($model->getId(), $this->getSite()->getId());
	        $this->addUserMessageToNextRequest($num_held_items.' '.$model->getPluralName()." were released.", SmartestUserMessage::SUCCESS);
        }else{
            $this->addUserMessageToNextRequest("The model ID was not recognized.");
        }
        
        $this->redirect('/datamanager/getItemClassMembers?class_id='.$this->getRequestParameter('class_id'));
    }
    
    public function getItemClassComments($get){
        
        $model = new SmartestModel;
        $model_id = (int) $this->getRequestParameter('class_id');
        $status = ($this->getRequestParameter('show') && in_array($this->getRequestParameter('show'), array('SM_COMMENTSTATUS_APPROVED', 'SM_COMMENTSTATUS_PENDING', 'SM_COMMENTSTATUS_REJECTED'))) ? $this->getRequestParameter('show') : 'SM_COMMENTSTATUS_APPROVED';
        
        if($model->find($model_id)){
            
            $comments = $model->getComments($status, $this->getSite()->getId());
            $this->send($comments, 'comments');
            $this->send(count($comments), 'num_comments');
            $this->send($model, 'model');
            $this->send($status, 'show');
            
        }else{
            $this->addUserMessageToNextRequest('The model ID was not recognized', SmartestUserMessage::ERROR);
            $this->redirect('/smartest/models');
        }
        
    }

	// public function getItemXml($get, $post){
	
	/* public function getItemClassXml($get){
    		$channel=null;
		$itemBaseValues = $this->manager->getItemClassBaseValues($get["class_id"]);
		$schemaDetails = $this->SchemasManager->getSchema($itemBaseValues['itemclass_schema_id']);
				//var_dump($schemaDetails);

		$itemClassProperties = $this->manager->getSettingItemsInClass($get["class_id"]); 
		if(is_array($itemClassProperties)){			
			foreach($itemClassProperties as $i=>$item_settings){
					if($item_settings['itemproperty_name']!=''){
						$channel[$item_settings['itemproperty_name']] = $item_settings['itemproperty_setting_value'];
					}
					else if($item_settings['itemproperty_varname']!=''){
						$channel[$item_settings['itemproperty_varname']] = $item_settings['itemproperty_setting_value'];
					}
			}
		}
// print_r($channel);
		$getItems = $this->manager->getItemsInClass($get["class_id"]);

		$resource = null;  
		for($i=0; $i<count($getItems); $i++){	
		$item_id=$getItems[$i]['item']["item_id"];
		$class=	$get["class_id"];
		$propertyValues = $this->manager->getItemPropertyValues($item_id,$class);
		if(is_array($propertyValues)){
			foreach($propertyValues as $key=>$value){
				if($value['itemproperty_name']){
					$resource[$value['itemproperty_name']] = ltrim($value['itempropertyvalue_content']);
				}
				else if($value['itemproperty_varname']){
					$resource[$value['itemproperty_varname']] = ltrim($value['itempropertyvalue_content']);
				}
			}
       			$channel[] = $resource;
		}	
		}
	
// print_r($channel);
		$serializer_options = array ( 
			'addDecl' => TRUE, 
			'encoding' => $schemaDetails['schema_encoding'], 
			'indent' => '  ', 
			'defaultTagName' => $schemaDetails['schema_default_tag'],
			'rootName' => $schemaDetails['schema_root_tag'], 
      			'attributesArray' => '_attributes',
			'rootAttributes' => array (
			'xmlns' => $schemaDetails['schema_namespace'],
				'lang' => $schemaDetails['schema_lang'],
				'xml:lang' => $schemaDetails['schema_lang']
			)
    		); 
    
		$serializer = &new XML_serializer($serializer_options); 
		$status = $serializer->serialize($channel);
		// var_dump($resource);
		if (PEAR::isError($status)) { 
			$this->_error($status->getMessage());
		}  
		header('Content-type: text/xml'); 
		die( $serializer->getSerializedData());	

	} */
	
	
	//// DELETE
	
	/*
	functions:
	removeProperty()
	deleteItem()
	deleteItemClass()
	*/
	
	/* function removeProperty($get, $post){
		$itemproperty_id = mysql_real_escape_string($this->getRequestParameter('itemproperty_id'));
    		return $this->manager->deleteItemClassProperty($itemproperty_id);
	} */
	
	public function deleteProperty($get){
	    
	    $property = new SmartestItemProperty;
	    
	    if($this->getUser()->hasToken('create_remove_properties')){
    	
    	    if($property->find($this->getRequestParameter('itemproperty_id'))){
    	    
        	    $model = new SmartestModel;
    	    
        	    if($model->find($property->getItemclassId()) && $model->getPrimaryPropertyId() == $property->getId()){
        	        $model->setPrimaryPropertyId('');
        	        $model->save();
        	    }
    	    
        	    $property->delete();
	        
    	        $this->addUserMessageToNextRequest("The property has been deleted.", SmartestUserMessage::SUCCESS);
    	        $this->formForward();
	        
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest("You don't have permission to delete properties.", SmartestUserMessage::ACCESS_DENIED);
	        $this->formForward();
            
        }
	}
	
	public function deleteItem($get){
		// $item_id = mysql_real_escape_string($this->getRequestParameter('item_id'));
		if(is_numeric($this->getRequestParameter('item_id'))){
		    
		    $item_id = $this->getRequestParameter('item_id');
		    
		    if($this->getUser()->hasToken('delete_items')){
	            
	            $item = SmartestCmsItem::retrieveByPk($item_id);
	            
	            if(is_object($item)){
	                $model_name = $item->getModel()->getName();
	                
	                if($item->delete()){
	                    $this->addUserMessageToNextRequest('The '.$model_name.' was moved to the trash.', SmartestUserMessage::SUCCESS);
                    }else{
                        $this->addUserMessageToNextRequest('The '.$model_name.' could not be deleted because it is in use on one or more pages. See today\'s log for more details.', SmartestUserMessage::WARNING);
                    }
                    
	            }else{
	                $this->addUserMessageToNextRequest('The item ID was not recognised.', SmartestUserMessage::ERROR);
	            }
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('You don\'t have permission to delete items.', SmartestUserMessage::ACCESS_DENIED);
	            
	        }
	    }else{
	        
	        $this->addUserMessageToNextRequest('The item ID was not recognised.', SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function deleteItemClass($get){
		
		if($this->getUser()->hasToken('delete_models')){
		    
		    $model = new SmartestModel;
		    
		    if($model->find($this->getRequestParameter('class_id'))){
		        $model->delete(true);
		    }
		    
		    if($shared){
		        $du = new SmartestDataUtility;
		        $du->flushModelsCache();
		    }
		    
		}else{
		    $this->addUserMessageToNextRequest("You do not have permission to delete models");
		}
		
		$this->formForward();
		
	}
	
	//// EDIT (pre-action interface/options) and UPDATE (the actual action)
	
    /* public function editItemProperty($get, $post){
		
		$property_id = $this->getRequestParameter('itemproperty_id'); //print_r($property_id);
		
		$property = new SmartestItemProperty;
		
		if($property->hydrate($property_id)){
		    
		    $model_id = $property->getItemclassId();
		    $model = new SmartestModel;
		    $model->hydrate($model_id);
		    
		    $this->addUserMessage('Editing existing properties will change how the data stored by that property is retrieved and displayed.', SmartestUserMessage::WARNING);
		    
		    $data_types = SmartestDataUtility::getDataTypes();
		    
		    $this->setTitle($model->getPluralName().' | Edit Property');
		    
		    $this->send($data_types, 'data_types');
		    $this->send($model->compile(), 'model');
		    $this->send($property->compile(), 'property');
		    
		    // print_r($data_types);
		    
		    // $this->send('model');
		    
    		// $itemClass = $this->manager->getItemClass($class_id);
    		// $property = $this->manager->getSingleItemProperty($property_id);
		
    		// if($property[0]['itemproperty_datatype']){
    		//     $date = explode('-',$property[0]['itemproperty_defaultvalue']);
    		// }
		
    	    // $propertyTypes = $this->manager->getItemPropertyTypes();
    		// $models = $this->manager->getItemClasses();
    		// $dropdownMenu = $this->manager->getDropdownMenu();
		
    		/* if($get["name"]){
    		    $name = $get["name"];
    		}else{
    		    $name = $property[0]['itemproperty_name'];
    		}
		
    		if($get["type"]){
    		    $type = $get["type"];
    		}else{
    		    $type = $property[0]['itemproperty_datatype'];
    		}
		
    		if($get["sel_id"]){
    		    $sel_id = $get["sel_id"];
    		}else{
    		    $sel_id = $property[0]['itemproperty_dropdown_id'];
    		}
	    
    	    if($get["model_id"]){
    	        $model_id = $get["model_id"];
    	    }else{
    	        $model_id = $property[0]['itemproperty_model_id'];
    	    }
	    
    		$dropdownValues = $this->manager->getDropdownMenuValues($sel_id);
		
    		if($model_id){
    		    $items = $this->manager->getItemsInClass($model_id);
    		}
		
	    }

		// return (array("details"=>$property[0], "itemclass"=>$itemClass, "Types"=>$propertyTypes,"models"=>$models,"dropdownMenu"=>$dropdownMenu,"dropdownValues"=>$dropdownValues,"name"=>$name,"type"=>$type,"sel_id"=>$sel_id,"model_id"=>$model_id,"sel_items"=>$items,"month"=>$date[0],"day"=>$date[1]));
	} */
	
	public function openItem($get){
	    
	    $item = new SmartestItem;
	    
	    if($item->find($this->getRequestParameter('item_id'))){
	        
	        if(($item->getCreatedbyUserid() != $this->getUser()->getId()) && !$this->getUser()->hasToken('modify_items')){
	            $this->addUserMessageToNextRequest('You didn\'t create this item and do not have permission to edit it.', SmartestUserMessage::ACCESS_DENIED);
	            SmartestLog::getInstance('site')->log('Suspicious activity: '.$this->getUser()->__toString().' tried to edit '.strtolower($item->getModel()->getName()).' \''.$item->getName().'\' via direct URL entry.');
    		    $this->formForward();
	        }
	        
	        if($item->getIsHeld() && $item->getHeldBy() != $this->getUser()->getId() && !$this->getUser()->hasToken('edit_held_items')){
    	        
    	        // item is being edited by somebody else
    	        
    	        $u = new SmartestUser;
        	    $u->hydrate($item->getHeldBy());
        	    $this->addUserMessageToNextRequest('The item is already being edited by '.$u->getFullName().'.', SmartestUserMessage::INFO);
		    
    		    if($this->getRequestParameter('from')=='todoList'){
        		    $this->redirect('/smartest/todo');
        		}else{
        	        $this->redirect('/'.$this->getRequest()->getModule().'/getItemClassMembers?class_id='.$item->getItemclassId());
        		}
    		
    	    }else{
    	        
    	        if($this->getUser()->hasToken('modify_items')){
                    
                    $item->clearRecentlyEditedInstances($this->getSite()->getId(), $this->getUser()->getId());
                    
                    if($item->getIsHeld() && $this->getUser()->hasToken('edit_held_items') && $item->getHeldBy() != $this->getUser()->getId()){
            		    $u = new SmartestUser;
                	    $u->hydrate($item->getHeldBy());
                	    $this->addUserMessageToNextRequest('Careful: this item is already being edited by '.$u->getFullName().'.', SmartestUserMessage::INFO);
            		}else{
            		    $item->setIsHeld(1);
                        $item->setHeldBy($this->getUser()->getId());
                        $item->save();
                
                        /* if(!$this->getUser()->hasTodo('SM_TODOITEMTYPE_RELEASE_ITEM', $item->getId())){
        	                $this->getUser()->assignTodo('SM_TODOITEMTYPE_RELEASE_ITEM', $item->getId(), 0);
                        } */
            		}
                
    		        $destination = '/smartest/item/edit/'.$item->getId();
		
    	    	    if($this->getRequestParameter('from')){
    	    	        if($this->getRequestParameter('page_webid')){
    	    	            $database = SmartestDatabase::getInstance('SMARTEST');
    	    	            $sql = "SELECT * FROM Pages WHERE page_type='ITEMCLASS' AND page_dataset_id='".$item->getItemclassId()."' AND page_webid='".$this->getRequestParameter('page_webid')."'";
    	    	            $result = $database->queryToArray($sql);
    	    	            if(count($result)){
    	    	                $destination .= '&page_id='.$this->getRequestParameter('page_webid');
    	    	            }
	    	            }else{
            		        $destination .= '&from='.$this->getRequestParameter('from');
        		        }
            		}
		    
                    $this->redirect($destination);
                
                }else{
                    $this->addUserMessageToNextRequest('You don\'t have permssion to edit items', SmartestUserMessage::ACCESS_DENIED);
                    $this->redirect('/'.$this->getRequest()->getModule().'/getItemClassMembers?class_id='.$item->getItemclassId());
                }
    	    }
        }
	    
	}
	
	public function releaseItem($get){
	    
	    $item = new SmartestItem;
	    $item->hydrate($this->getRequestParameter('item_id'));
	    
	    if($item->getIsHeld()){
	        // item is being edited by somebody else
	        if($item->getHeldBy() == $this->getUser()->getId()){
	            $item->setIsHeld(0);
                $item->setHeldBy(0);
                $item->save();
                $this->addUserMessageToNextRequest('The item has been released.', SmartestUserMessage::SUCCESS);
                
                if($todo = $this->getUser()->getTodo('SM_TODOITEMTYPE_RELEASE_ITEM', $item->getId())){
	                $todo->complete();
                }
                
                if($this->getRequestParameter('from') && $this->getRequestParameter('from')=='todoList'){
                    $this->redirect('/smartest/todo');
                }else{
                    $this->redirect('/datamanager/getItemClassMembers?class_id='.$item->getItemclassId());
                }
                // $this->formForward();
	        }else{
	            $this->addUserMessageToNextRequest('The item is already being edited by somebody else.', SmartestUserMessage::WARNING);
	            $this->formForward();
	        }
	        
	    }else{
	        
	        if($todo = $this->getUser()->getTodo('SM_TODOITEMTYPE_RELEASE_ITEM', $item->getId())){
                $todo->complete();
            }else{
                $this->addUserMessageToNextRequest('The item was not locked.', SmartestUserMessage::INFO);
            }
	        
	        $this->formForward();
	    }
	    
	    // $this->redirect('/'.SM_CONTROLLER_MODULE.'/getItemClassMembers?class_id='.$item->getItemclassId());
	    
	    
	}
	
	public function itemTags($get){
	    
	    if(!$this->getRequestParameter('from')){
	        $this->setFormReturnUri();
        }
	    
	    $item_id = $this->getRequestParameter('item_id');
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        $model = new SmartestModel;
	        $model->hydrate($item->getItemclassId());
	        $this->send($model, 'model');
	        
	        $this->setTitle($item->getName().' | Tags');
	        
	        $du  = new SmartestDataUtility;
	        $tags = $du->getTags();
	        
	        $item_tags = array();
	        $i = 0;
	        
	        foreach($tags as $t){
	            
	            $item_tags[$i] = $t->__toArray();
	            
	            if($t->hasItem($item->getId())){
	                $item_tags[$i]['attached'] = true;
	            }else{
	                $item_tags[$i]['attached'] = false;
	            }
	            
	            $i++;
	        }
	        
	        $this->send($item_tags, 'tags');
	        $this->send($item, 'item');
	        
	        if($this->getRequestParameter('page_id')){
		        
		        $page = new SmartestPage;
		        if($page->hydrate($this->getRequestParameter('page_id'))){
		            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
		        }else{
		            $this->send(false, 'page_is_editable');
		        }
		        
		    }else{
		        $this->send(false, 'page_is_editable');
		    }
	        
	    }else{
	        $this->addUserMessage('The item ID has not been recognized.', SmartestUserMessage::ERROR);
	    }
	    
	}
	
	public function updateItemTags($get, $post){
	    
	    $item = new SmartestItem;
	    
	    if($item->hydrate($this->getRequestParameter('item_id'))){
	    
	        $du  = new SmartestDataUtility;
            $tags = $du->getTags();
        
            if(is_array($this->getRequestParameter('tags'))){
                
                $item_new_tag_ids = array_keys($this->getRequestParameter('tags'));
                $item_current_tag_ids = $item->getTagIdsArray();
                
                foreach($tags as $t){
                    
                    if(in_array($t->getId(), $item_new_tag_ids) && !in_array($t->getId(), $item_current_tag_ids)){
                        $item->tag($t->getId());
                    }
                    
                    if(in_array($t->getId(), $item_current_tag_ids) && !in_array($t->getId(), $item_new_tag_ids)){
                        $item->untag($t->getId());
                    }
                    
                }
                
                $this->addUserMessageToNextRequest('The tags on this item were successfully updated.', SmartestUserMessage::SUCCESS);
                
            }else{
                // clear all item tags
                $item->clearTags();
                $this->addUserMessageToNextRequest('The tags on this item were successfully removed.', SmartestUserMessage::SUCCESS);
            }
        
        }else{
            
            // item ID wasn't recognised
            
        }
	    
	    $this->formForward();
	}
	
	public function relatedContent($get){
	    
	    $this->setFormReturnUri();
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    $item = SmartestCmsItem::retrieveByPk($item_id);
	    
	    if($item->isHydrated()){
	        
	        $this->send($item, 'item');
	        
	        $this->setTitle($item->getName()." | Related Content");
	        $model = $item->getModel();
	        
	        $this->send($model, 'model');
	        
	        $related_items_this_model = $item->getItem()->getRelatedItemsAsArrays(true);
	        $related_pages = $item->getItem()->getRelatedPagesAsArrays(true);
	        
	        $this->send($related_items_this_model, 'related_items_this_model');
	        $this->send($related_pages, 'related_pages');
	        
	        $du = new SmartestDataUtility;
	        $models = $du->getModels(false, $this->getSite()->getId());
	        $related_foreign_items = array();
	        
	        foreach($models as $key=>$m){
	            if($m['id'] == $model->getId()){
	                unset($models[$key]);
	            }else{
	                $related_foreign_items[$key] = $item->getItem()->getRelatedForeignItemsAsArrays(true, $m['id']);
                }
	        }
	        
	        $this->send($models, 'models');
	        $this->send($related_foreign_items, 'related_foreign_items');
	        
	        if($this->getRequestParameter('page_id')){
		        
		        $page = new SmartestPage;
		        if($page->hydrate($this->getRequestParameter('page_id'))){
		            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
		        }else{
		            $this->send(false, 'page_is_editable');
		        }
		        
		    }else{
		        $this->send(false, 'page_is_editable');
		    }
	        
	    }
	    
	}
	
	public function editRelatedContent($get){
	    
	    $item = new SmartestItem;
	    $item_id = $this->getRequestParameter('item_id');
	    
	    if($item->hydrate($item_id)){
	        
	        if($this->getRequestParameter('model_id')){
	            
	            $model_id = (int) $this->getRequestParameter('model_id');
	            $model = new SmartestModel;
	            
	            if($model->hydrate($model_id)){
	                $mode = 'items';
	            }else{
	                $mode = 'pages';
	            }
            }
	        
	        $this->send($mode, 'mode');
	        
	        if($mode == 'items'){
	            
	            $this->setTitle($item->getName()." | Related ".$model->getPluralName());
	            $this->send($item, 'item');
	            $this->send($model, 'model');
	            
	            if($model->getId() == $item->getModelId()){
	                $related_ids = $item->getRelatedItemIds(true, $model->getId());
                }else{
                    $related_ids = $item->getRelatedForeignItemIds(true, $model->getId());
                }
                
	            $all_items  = $model->getSimpleItems($this->getSite()->getId());
	            $this->send($all_items, 'items');
	            $this->send($related_ids, 'related_ids');
	            
            }else{
                
                $this->setTitle($item->getName()." | Related pages");
    	        $this->send($item, 'item');
    	        $related_ids = $item->getRelatedPageIds(true);
    	        $helper = new SmartestPageManagementHelper;
    	        $pages = $helper->getPagesList($this->getSite()->getId());
    	        $this->send($pages, 'pages');
    	        $this->send($related_ids, 'related_ids');
    	        
            }
	        
	        // $related_pages = $page->getRelatedPagesAsArrays(true);
    	    
	    }else{
	        $this->addUserMessageToNextRequest('The item ID was not recognized', SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/models');
	    }
	    
	}
	
	public function updateRelatedItemConnections($get, $post){
	    
	    $item = new SmartestItem;
	    $item_id = $this->getRequestParameter('item_id');
	    
	    if($item->hydrate($item_id)){
	        
	        $model = new SmartestModel;
    	    $model_id = $this->getRequestParameter('model_id');
    	    
    	    if($model->hydrate($model_id)){
	        
	            if($this->getRequestParameter('items')){
	            
	                if(is_array($this->getRequestParameter('items'))){
	            
        	            $new_related_ids = array_keys($this->getRequestParameter('items'));
	            
        	            if(count($new_related_ids)){
	                    
    	                    $items = $model->getSimpleItems();
	                    
    	                    if($model->getId() == $item->getModelId()){
        	                    $old_related_ids = $item->getRelatedItemIds(true);
        	                    $same_model = true;
    	                    }else{
    	                        $old_related_ids = $item->getRelatedForeignItemIds(true, $model->getId());
    	                        $same_model = false;
    	                    }
	                    
    	                    foreach($items as $i){
    	            
                	            if(in_array($i->getId(), $new_related_ids) && !in_array($i->getId(), $old_related_ids)){
                	                // add connection
                	                if($same_model){
                	                    $item->addRelatedItem($i->getId());
                	                }else{
                	                    $item->addRelatedForeignItem($i->getId());
                	                }
                	            }
    	            
                	            if(in_array($i->getId(), $old_related_ids) && !in_array($i->getId(), $new_related_ids)){
                	                // remove connection
                	                if($same_model){
                	                    $item->removeRelatedItem($i->getId());
                	                }else{
                	                    $item->removeRelatedForeignItem($i->getId());
                	                }
                	            }
                	        }
    	        
        	            }
    	        
                    }else{
                        $this->addUserMessageToNextRequest('Incorrect input format: Data should be array of items', SmartestUserMessage::ERROR);
                    }
                    
                }else{
                    $item->removeAllRelatedItems($model->getId());
                }
            
            }else{
                
                $this->addUserMessageToNextRequest('The model ID was not recognized', SmartestUserMessage::ERROR);
                
            }
            
        }else{
            $this->addUserMessageToNextRequest('The item ID was not recognized', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	public function updateRelatedPageConnections($get, $post){
	    
	    $item = new SmartestItem;
	    $item_id = $this->getRequestParameter('item_id');
	    
	    if($item->hydrate($item_id)){
	        
	        if($this->getRequestParameter('pages') && is_array($this->getRequestParameter('pages'))){
	            
	            $new_related_ids = array_keys($this->getRequestParameter('pages'));
	            
	            if(count($new_related_ids)){
	            
    	            $helper = new SmartestPageManagementHelper;
                    $pages = $helper->getPagesList($this->getSite()->getId());
                    $old_related_ids = $item->getRelatedPageIds(true);
            	        
            	    foreach($pages as $page){
    	            
            	        if(in_array($page['id'], $new_related_ids) && !in_array($page['id'], $old_related_ids)){
                            // add connection
                            $item->addRelatedPage($page['id']);
                        }
    	        
        	            if(in_array($page['id'], $old_related_ids) && !in_array($page['id'], $new_related_ids)){
        	                // remove connection
        	                $item->removeRelatedPage($page['id']);
        	            }
        	        }
    	        
                }
    	        
            }else{
                $item->removeAllRelatedPages();
                // $this->addUserMessageToNextRequest('Incorrect input format: Data should be array of pages', SmartestUserMessage::ERROR);
            }
        }else{
            $this->addUserMessageToNextRequest('The item ID was not recognized', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	public function authors($get){
	    
	    if(!$this->getRequestParameter('from')){
	        $this->setFormReturnUri();
	    }
	    
	    $item_id = $this->getRequestParameter('item_id');
	    
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        $uhelper = new SmartestUsersHelper;
	        $uhelper->distributeAuthorCreditTokenFromItem($item, $this->getSite()->getId());
	        $users = $uhelper->getCreditableUsersOnSite($this->getSite()->getId());
	        $this->send($users, 'users');
	        $author_ids = $item->getAuthorIds();
	        $this->send($author_ids, 'author_ids');
	        $this->send($item, 'item');
	        $this->send($this->getUser()->hasToken('modify_user_permissions'), 'provide_tokens_link');
	        
	        if($this->getRequestParameter('page_id')){
		        
		        $page = new SmartestPage;
		        if($page->hydrate($this->getRequestParameter('page_id'))){
		            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
		        }else{
		            $this->send(false, 'page_is_editable');
		        }
		        
		    }else{
		        $this->send(false, 'page_is_editable');
		    }
	        
	    }else{
            $this->addUserMessage('The item ID was not recognized', SmartestUserMessage::ERROR);
        }
	    
	}
	
	public function updateAuthors($get, $post){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        if($this->getRequestParameter('users') && count($this->getRequestParameter('users'))){
	        
	            $uhelper = new SmartestUsersHelper;
                $users = $uhelper->getCreditableUsersOnSite($this->getSite()->getId());
            
                $new_author_ids = array_keys($this->getRequestParameter('users'));
                $old_author_ids = $item->getAuthorIds();
            
                foreach($users as $u){
                    
                    if(in_array($u->getId(), $old_author_ids) && !in_array($u->getId(), $new_author_ids)){
                        // remove connection
                        $item->removeAuthorById($u->getId());
                        
                    }
                    
                    if(in_array($u->getId(), $new_author_ids) && !in_array($u->getId(), $old_author_ids)){
                        // add connection
                        $item->addAuthorById($u->getId());
                    }
                }
                
                $this->addUserMessageToNextRequest('The authors of this item were sucessfully updated.', SmartestUserMessage::SUCCESS);
            
            }else{
                
                $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_ITEM_AUTHORS');
        	    
        	    $q->setTargetEntityByIndex(1);
        	    $q->addQualifyingEntityByIndex(2, $item->getId());

        	    $q->addSortField('Users.user_lastname');

        	    $q->delete();
        	    
        	    $this->addUserMessageToNextRequest('The authors of this item were sucessfully updated.', SmartestUserMessage::SUCCESS);
                
            }
	        
	    }else{
            $this->addUserMessageToNextRequest('The item ID was not recognized', SmartestUserMessage::ERROR);
        }
	    
	    $this->formForward();
	    
	}
	
	public function itemComments($get){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    $item = new SmartestItem;
	    
	    if($item->find($item_id)){
	        
	        $this->send($item, 'item');
	        $show = ($this->getRequestParameter('show') && in_array($this->getRequestParameter('show'), array('SM_COMMENTSTATUS_APPROVED', 'SM_COMMENTSTATUS_PENDING', 'SM_COMMENTSTATUS_REJECTED'))) ? $this->getRequestParameter('show') : 'SM_COMMENTSTATUS_APPROVED';
	        $this->send($show, 'show');
	        
	        $comments = $item->getPublicComments($show);
	        $this->send($comments, 'comments');
	        $this->send(count($comments), 'num_comments');
	        
	        if($this->getRequestParameter('page_id')){
		        
		        $page = new SmartestPage;
		        if($page->hydrate($this->getRequestParameter('page_id'))){
		            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
		        }else{
		            $this->send(false, 'page_is_editable');
		        }
		        
		    }else{
		        $this->send(false, 'page_is_editable');
		    }
	        
	    }
	    
	}
	
	public function moderateComment($get){
	    
	    $from = ($this->getRequestParameter('from') && in_array($this->getRequestParameter('from'), array('item_list', 'model_list'))) ? $this->getRequestParameter('from') : 'model_list';
	    
	    $comment_id = (int) $this->getRequestParameter('comment_id');
	    $comment = new SmartestItemPublicComment;
	    
	    if($comment->find($comment_id)){
	        
	        if($this->getRequestParameter('item_id')){
	            $item_id = (int) $this->getRequestParameter('item_id');
            }else{
                $item_id = $comment->getItemId();
            }
            
    	    $item = new SmartestItem;
    	    
    	    if($item->find($item_id)){
	        
	            $action = ($this->getRequestParameter('action') && in_array($this->getRequestParameter('action'), array('APPROVE', 'MAKEPENDING', 'REJECT'))) ? $this->getRequestParameter('action') : 'REJECT';
	            
	            switch($action){
	                
	                case "APPROVE":
	                $comment->approve();
	                $message = "The comment has been approved";
	                break;
	                
	                case "MAKEPENDING":
	                $comment->makePending();
	                $message = "The comment status has been set to 'pending'";
	                break;
	                
	                case "REJECT":
	                $comment->reject();
	                $message = "The comment has been rejected";
	                break;
	                
	            }
	            
	            $this->addUserMessageToNextRequest($message, SmartestUserMessage::SUCCESS);
	            
	            if($from == 'item_list'){
	                $this->redirect('/datamanager/itemComments?item_id='.$item_id.'&show='.$this->getRequestParameter('fromStatus'));
                }else{
                    $this->redirect('/datamanager/getItemClassComments?class_id='.$item->getItemclassId().'&show='.$this->getRequestParameter('fromStatus'));
                }
	        
            }else{
                
                $this->addUserMessageToNextRequest("The item ID was not recognized", SmartestUserMessage::ERROR);
    	        // $this->redirect('/datamanager/itemComments?item_id='.$item_id);
                
                /* if($from == 'item_list'){
	                $this->redirect('/datamanager/itemComments?item_id='.$item_id.'&show='.$this->getRequestParameter('fromStatus'));
                }else{
                    $this->redirect('/datamanager/getItemClassComments?class_id='.$item->getItemclassId().'&show='.$this->getRequestParameter('fromStatus'));
                } */
                $this->redirect('/smartest/models');
                
            }
	    
	    }else{
	        
	        $this->addUserMessageToNextRequest("The comment ID was not recognized", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/models');
	        
	    }
	    
	}
	
	public function editModel($get){
	    
	    $model_id = (int) $this->getRequestParameter('class_id');
	    $model = new SmartestModel();
	    
	    $this->send($this->getUser()->hasToken('edit_model'), 'can_edit_model');
	    $this->send($this->getUser()->hasToken('create_remove_properties'), 'can_edit_properties');
	    
	    if($model->find($model_id)){
	        
	        $this->send($model, 'model');
	        $this->send($model->getMetaPages(), 'metapages');
	        
	        $num_items_on_site = count($model->getSimpleItems($this->getSite()->getId()));
	        $num_items_all_sites = count($model->getSimpleItems());
	        
	        $file_path = substr($model->getClassFilePath(), strlen(SM_ROOT_DIR));
	        $this->send($file_path, 'class_file');
	        
	        $this->send(($num_items_on_site > 0) ? number_format($num_items_on_site) : 'none', 'num_items_on_site');
	        $this->send(number_format($num_items_all_sites), 'num_items_all_sites');
	        $this->send($this->getUser()->hasToken('edit_model_plural_name'), 'allow_plural_name_edit');
	        $this->send($this->getUser()->hasToken('edit_model'), 'allow_infn_edit');
	        
	        $sites_where_used = $model->getSitesWhereUsed();
	        $multiple_sites = (count($sites_where_used) > 1);
	        
	        $site_ids = array();
	        foreach($sites_where_used as $s){
	            $site_ids[] = $s->getId();
	        }
	        
	        $shared = ($model->isShared() || $multiple_sites);
	        $this->send($shared, 'shared');
	        
	        $this->send(SmartestFileSystemHelper::getFileSizeFormatted($model->getClassFilePath()), 'class_file_size');
	        
	        $is_movable = $model->isMovable();
	        
	        if($shared){
	            $ast = (!$multiple_sites && $model->getSiteId() == $this->getSite()->getId() && $is_movable);
            }else{
                $ast = ($model->hasSameNameAsModelOnOtherSite() || !$is_movable) ? false : true;
            }
            
            $this->send($ast, 'allow_sharing_toggle');
            $this->send($is_movable, 'is_movable');
            
            if(!$is_movable){
                $this->send($model->getFilesThatMustBeWrtableForSharingToggleButAreNot(), 'unwritable_files');
            }
            
	        $this->send($this->getSite()->getId(), 'current_site_id');
	        
	        if($model->getSiteId() == '0'){
	            $this->send(true, 'allow_main_site_switch');
	            if($multiple_sites){
                    $this->send($this->getUser()->getAllowedSites($site_ids), 'sites');
                }else{
                    $this->send($this->getUser()->getAllowedSites(), 'sites');
                }
            }else{
                $this->send(false, 'allow_main_site_switch');
            }
	        
	        $this->send($model->getAvailableDescriptionProperties(), 'description_properties');
	        $this->send($model->getAvailableSortProperties(), 'sort_properties');
	        $this->send($model->getAvailableThumbnailProperties(), 'thumbnail_properties');
	        
	        $recent = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $model_id);
  	        $this->send($recent, 'recent_items');
  	        
  	        $allow_create_new = $this->getUser()->hasToken('add_items');
  	        $this->send($allow_create_new, 'allow_create_new');
  	        
  	        $this->send($model->getAvailablePrimaryProperties(), 'available_primary_properties');
	        
	    }else{
	        $this->addUserMessageToNextRequest("The model ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/models');
	    }
	    
	}
	
	public function updateModel($get, $post){
	    
	    if($this->getUser()->hasToken('edit_model')){
	    
    	    $model_id = $this->getRequestParameter('class_id');
    	    $model = new SmartestModel;
	    
    	    $error = false;
	    
    	    if($model->find($model_id)){
	        
    	        if($this->getRequestParameter('itemclass_default_metapage_id')){
    	            if(is_numeric($this->getRequestParameter('itemclass_default_metapage_id'))){
    	                $model->setDefaultMetaPageId($this->getSite()->getId(), (int) $this->getRequestParameter('itemclass_default_metapage_id'));
                    }else if($this->getRequestParameter('itemclass_default_metapage_id') == 'NONE'){
                        $model->clearDefaultMetaPageId($this->getSite()->getId());
                    }
                }
            
                if($this->getUser()->hasToken('edit_model_plural_name')){
                    if($this->getRequestParameter('itemclass_plural_name') && strlen($this->getRequestParameter('itemclass_plural_name'))){
                        $model->setPluralName($this->getRequestParameter('itemclass_plural_name'));
                    }else{
                        $this->addUserMessage("The plural name you entered was invalid.", SmartestUserMessage::WARNING);
                        $error = true;
                    }
                }
                
                if($this->getUser()->hasToken('edit_model')){
                    $model->setItemNameFieldName($this->getRequestParameter('itemclass_item_name_field_name'));
                }
            
                if(is_numeric($this->getRequestParameter('itemclass_default_description_property_id')) && $this->getRequestParameter('itemclass_default_description_property_id') > 1){
                    $model->setDefaultDescriptionPropertyId($this->getRequestParameter('itemclass_default_description_property_id'));
                }
                
                if(is_numeric($this->getRequestParameter('itemclass_default_sort_property_id'))){
                    $model->setDefaultSortPropertyId($this->getRequestParameter('itemclass_default_sort_property_id'));
                }
                
                if(is_numeric($this->getRequestParameter('itemclass_default_thumbnail_property_id'))){
                    $model->setDefaultThumbnailPropertyId($this->getRequestParameter('itemclass_default_thumbnail_property_id'));
                }
            
                if($this->getRequestParameter('itemclass_primary_property_id')){
                    if(is_numeric($this->getRequestParameter('itemclass_primary_property_id'))){
                        $model->setPrimaryPropertyId($this->getRequestParameter('itemclass_primary_property_id'));
                    }else if($this->getRequestParameter('itemclass_primary_property_id') == 'NONE'){
                        $model->setPrimaryPropertyId('');
                    }
                }
            
                $model->setColor($this->getRequestParameter('itemclass_color'));
            
                if($model->isUsedOnMultipleSites()){
                
                    $model->setShared('1');
                
                }else{
                
                    if($model->getSiteId() == $this->getSite()->getId()){
                    
                        $shared = $this->getRequestParameter('itemclass_shared') ? 1 : 0;
                    
                        if($model->setShared($shared)){
                        
                        }else{
                            $this->addUserMessage("The model's class file could not be moved.", SmartestUserMessage::WARNING);
                            $error = true;
                        }
                    
                    }
                }
            
                if($model->getSiteId() == '0'){
                
                    if($this->getRequestParameter('itemclass_site_id') && (int) $this->getRequestParameter('itemclass_site_id') > 0 && in_array($this->getRequestParameter('itemclass_site_id'), $this->getUser()->getAllowedSiteIds())){
                        $new_site_id = $this->getRequestParameter('itemclass_site_id');
                    }else{
                        $new_site_id = $this->getSite()->getId();
                    }
                
                    $model->setSiteId($new_site_id);
                
                }
            
                if($error){
                    $this->setRequestParameter('class_id', $model->getId());
                    $this->forward('datamanager', 'editModel');
                }else{
                    $this->addUserMessageToNextRequest("The model has been successfully updated.", SmartestUserMessage::SUCCESS);
                    $model->save();
                }
            
                $this->redirect("/datamanager/editModel?class_id=".$model->getId());
            
    	    }else{
    	        $this->addUserMessageToNextRequest("The model ID was not recognized.", SmartestUserMessage::ERROR);
    	        $this->redirect("/smartest/models");
    	    }
	    
        }else{
            
            $this->addUserMessageToNextRequest('You don\'t have permission to edit models', SmartestUserMessage::ACCESS_DENIED);
            $this->formForward();
            
        }
	    
	}
	
	public function itemInfo($get){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    
	    $item = SmartestCmsItem::retrieveByPk($item_id);
	    
	    if(is_object($item)){
	        
	        $this->setFormReturnUri();
	        
	        $this->send($item->getModel()->getMetaPages(), 'metapages');
		    
		    $authors = $item->getItem()->getAuthors();
		    
		    $num_authors = count($authors);
            $byline = '';

            if($num_authors){
                
                for($i=0;$i<$num_authors;$i++){

                    $byline .= $authors[$i]['full_name'];

                    if(isset($authors[$i+2])){
                        $byline .= ', ';
                    }else if(isset($authors[$i+1])){
                        $byline .= ' and ';
                    }

                }

                $this->send($byline, 'byline');
            }else{
                $this->send('No Authors', 'byline');
            }
		    
		    if($page = $item->getMetaPage()){
		        $this->send(true, 'has_page');
		        $this->send($page, 'page');
		    }
		    
		    $sets = $item->getItem()->getCurrentStaticSets();
		    $this->send($sets, 'sets');
		    
		    $possible_sets = $item->getItem()->getPossibleSets();
		    $this->send($possible_sets, 'possible_sets');
		    
		    $this->setTitle($item->getModel()->getName().' Information | '.$item->getName());
		    $this->send($item, 'item');
		    
		    $recent = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $item->getItem()->getItemclassId());
		    $this->send($recent, 'recent_items');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest("The item ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function toggleItemArchived($get){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        if($item->getIsArchived() === '0'){
	            $item->setIsArchived(1);
	            $item->save();
	            $this->addUserMessageToNextRequest('The item has been archived.', SmartestUserMessage::SUCCESS);
            }else if($item->getIsArchived() === '1'){
                $item->setIsArchived(0);
	            $item->save();
	            $this->addUserMessageToNextRequest('The item has been removed from the archive and made current.', SmartestUserMessage::SUCCESS);
            }
	    }else{
	        $this->addUserMessageToNextRequest("The item ID was not recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function editItem($get, $post){
		
		$item_id = $this->getRequestParameter('item_id');
		
		$item = SmartestCmsItem::retrieveByPk($item_id);
		
	    if(is_object($item)){
	        
	        if(($item->getItem()->getCreatedbyUserid() != $this->getUser()->getId()) && !$this->getUser()->hasToken('modify_items')){
	            $this->addUserMessageToNextRequest('You didn\'t create this item and do not have permission to edit it.', SmartestUserMessage::ACCESS_DENIED);
	            SmartestLog::getInstance('site')->log('Suspicious activity: '.$this->getUser()->__toString().' tried to edit '.strtolower($item->getModel()->getName()).' \''.$item->getName().'\' via direct URL entry.');
    		    $this->redirect('/'.$this->getRequest()->getModule().'/getItemClassMembers?class_id='.$item->getItem()->getItemclassId());
	        }
	        
	        $item->setDraftMode(true);
	        
	        if($item->getItem()->getIsHeld() && $item->getItem()->getHeldBy() != $this->getUser()->getId() && !$this->getUser()->hasToken('edit_held_items')){
	            $this->addUserMessageToNextRequest('The item is already being edited.', SmartestUserMessage::ACCESS_DENIED);
	            SmartestLog::getInstance('site')->log('Suspicious activity: '.$this->getUser()->__toString().' tried to edit '.strtolower($item->getModel()->getName()).' \''.$item->getName().'\' via direct URL entry.');
    		    $this->redirect('/'.$this->getRequest()->getModule().'/getItemClassMembers?class_id='.$item->getItem()->getItemclassId());
	        }
		    
		    $this->send($item->getModel()->getMetaPages(), 'metapages');
		    $this->send((bool) count($item->getModel()->getMetaPages()), 'has_metapages');
		    $this->send($this->getUser()->hasToken('create_remove_properties'), 'can_edit_properties');
		    $this->send($this->getUser()->hasToken('create_assets'), 'can_create_assets');
		    
		    $this->setTitle('Edit '.$item->getModel()->getName().' | '.$item->getName());
		    $this->send($item, 'item');
		    $this->send((bool) $this->getUser()->hasToken('edit_item_name'), 'allow_edit_item_slug');
		    
		    $sets = $item->getItem()->getCurrentStaticSets();
		    $this->send($sets, 'sets');
		    
		    $possible_sets = $item->getItem()->getPossibleSets();
		    $this->send($possible_sets, 'possible_sets');
		    
		    $this->getUser()->addRecentlyEditedItemById($item_id, $this->getSite()->getId());
		    
		    $metapage = new SmartestPage;
		    
		    if($metapage->find($item->getMetapageId())){
		        $this->send($metapage->getWebid(), 'default_metapage_id');
		    }else if($metapage->find($item->getModel()->getDefaultMetapageId($this->getSite()->getId()))){
		        $this->send($metapage->getWebid(), 'default_metapage_id');
		    }
		    
		    $recent = $this->getUser()->getRecentlyEditedItems($this->getSite()->getId(), $item->getItem()->getItemclassId());
		    $this->send($recent, 'recent_items');
		    
		    if($this->getRequestParameter('page_id')){
		        
		        $page = new SmartestPage;
		        if($page->hydrate($this->getRequestParameter('page_id'))){
		            $this->send($page->isEditableByUserId($this->getUser()->getId()), 'page_is_editable');
		        }else{
		            $this->send(false, 'page_is_editable');
		        }
		        
		    }else{
		        $this->send(false, 'page_is_editable');
		    }
		    
	    }
		
	}
	
	public function updateItem($get, $post){  
		
		if($this->getUser()->hasToken('modify_items')){
		
	    	$item_id = $this->getRequestParameter('item_id');
		
    		$item = SmartestCmsItem::retrieveByPk($item_id);
		
    		if(is_object($item)){
		        
		        // update name
    		    if (strlen($this->getRequestParameter('item_name'))){
			        $item->getItem()->setName(SmartestStringHelper::sanitize($this->getRequestParameter('item_name')));
		        }else{
		            $this->addUserMessage("You cannot leave the item's name empty. Changes were not saved.", SmartestUserMessage::WARNING);
		            $this->setRequestParameter('item_id', $item->getId());
		            $this->forward('datamanager', 'editItem');
		        }
		        
		        $allow_edit_item_slug = $this->getUser()->hasToken('edit_item_name');
		        
		        $item->getItem()->setLanguage(SmartestStringHelper::sanitize($this->getRequestParameter('item_language')));
		        $item->getItem()->setSearchField(SmartestStringHelper::sanitize($this->getRequestParameter('item_search_field')));
		        $item->getItem()->setMetapageId($this->getRequestParameter('item_metapage_id'));
        		$item->getItem()->setModified(time());
		        
		        if(strlen($this->getRequestParameter('item_slug'))){
		            if($allow_edit_item_slug){
		                $item->getItem()->setSlug(SmartestStringHelper::toSlug($this->getRequestParameter('item_slug')));
		            }
		        }else{
		            if($allow_edit_item_slug){
		                if(strlen($item->getItem()->getSlug())){
		                    // the 'slug' is being changed from something to nothing, which obviously we don't want.
		                    $this->addUserMessage("You cannot leave the item's short name empty. Changes were not saved.", SmartestUserMessage::WARNING);
		                    $this->setRequestParameter('item_id', $item->getId());
		                    $this->forward('datamanager', 'editItem');
	                    }else{
	                        // there was nothing there to begin with, so just quietly generate one
	                        $item->getItem()->setSlug(SmartestStringHelper::toSlug($item->getItem()->getName()), true);
	                    }
	                }
		        }
        		
        		$item->getItem()->save();
        		
        		// loop through properties
		
		        $new_values = $this->getRequestParameter('item');
    		    $properties = $item->getProperties(true);
    		    
    		    if(is_array($new_values)){
		    
		            foreach($new_values as $property_id=>$new_value){
		                $item->setPropertyValueByNumericKey($property_id, $new_value);
			        }
			        
			        $item->save();
			    
			    }
			    
			    $this->addUserMessageToNextRequest('The item was updated successfully.', SmartestUserMessage::SUCCESS);
		
	        }else{
	        
	            $this->addUserMessageToNextRequest('The item ID was not recognised.', SmartestUserMessage::ERROR);
	        
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('You don\'t have permssion to edit items', SmartestUserMessage::ACCESS_DENIED);
            
        }
	    
	    if($this->getRequestParameter('_submit_action') == "continue"){
	        if($this->getRequestParameter('page_id')){
	            $this->redirect("/datamanager/editItem?page_id=".$this->getRequestParameter('page_id')."&item_id=".$item->getItem()->getId());
            }else{
                $this->redirect("/smartest/item/edit/".$item->getItem()->getId());
            }
	    }else{
	        $this->formForward();
	    }
	
	}
	
	public function addTodoItem($get){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        $uhelper = new SmartestUsersHelper;
	        $users = $uhelper->getUsersOnSiteAsArrays($this->getSite()->getId());
	        
	        $this->send($users, 'users');
	        $this->send($item->__toArray(), 'item');
	        $this->send($item->getModel()->__toArray(), 'model');
	        $this->send($this->getUser()->__toArray(), 'user');
	        
	        $todo_types = SmartestTodoListHelper::getTypesByCategoryAsArrays('SM_TODOITEMCATEGORY_ITEMS', true);
	        $this->send($todo_types, 'todo_types');
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The item ID was not recognised.', SmartestUserMessage::ERROR);
	        $this->formForward();
	        
	    }
	    
	}
	
	public function insertTodoItem($get, $post){
	    
	    $item_id = (int) $this->getRequestParameter('item_id');
	    
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        $user = new SmartestUser;
	        $user_id = (int) $this->getRequestParameter('todoitem_receiving_user_id');
	        
	        if($user->hydrate($user_id)){
	            
	            // $user->assignTodo('SM_TODOITEMTYPE_EDIT_ITEM', $item_id, $this->getUser()->getId(), SmartestStringHelper::sanitize())
	            
		        $type_id = $this->getRequestParameter('todoitem_type');
	            $type = SmartestTodoListHelper::getType($type_id);
                
                $message = SmartestStringHelper::sanitize($this->getRequestParameter('todoitem_description'));
                
        	    if(isset($message{1})){
        	        $input_message = SmartestStringHelper::sanitize($message);
        	    }else{
        	        $input_message = $type->getDescription();
        	    }
        	    
        	    $priority = (int) $this->getRequestParameter('todoitem_priority');
        	    $size     = (int) $this->getRequestParameter('todoitem_size');
	            
	            $todo = new SmartestTodoItem;
	            $todo->setReceivingUserId($user->getId());
        	    $todo->setAssigningUserId($this->getUser()->getId());
        	    $todo->setForeignObjectId($item->getId());
        	    $todo->setTimeAssigned(time());
        	    $todo->setDescription($input_message);
        	    $todo->setType($type_id);
        	    $todo->setPriority($priority);
        	    $todo->setSize($size);
        	    $todo->save();
        	    
        	    if(!$todo->isSelfAssigned()){
        	        
        	        $message = 'Hi '.$user.",\n\n".$this->getUser()." has added a new task to your to-do list. Please visit ".$this->getRequest()->getDomain()."smartest/todo for more information.\n\nYours truly,\nThe Smartest Web Content Management Platform";
        	        $user->sendEmail('New To-do Assigned', $message);
        	        
        	    }
	            
	        }else{
	            
	            $this->addUserMessageToNextRequest('The user ID was not recognized.', SmartestUserMessage::ERROR);
	            
	        }
	        
	        
	    }else{
	        
	        $this->addUserMessageToNextRequest('The item ID was not recognized.', SmartestUserMessage::ERROR);
	        
	    }
	    
	    $this->formForward();
	    
	}
	
	public function approveItem($get){
	    
	    $item_id = $this->getRequestParameter('item_id');
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        if($this->getUser()->hasToken('approve_item_changes')){
	            
		        // user has permission. allow item to be approved.
	            $this->addUserMessageToNextRequest('The item has been approved.', SmartestUserMessage::SUCCESS);
	            $item->setChangesApproved(1);
		        $item->setIsHeld(0);
		        $item->setHeldBy(0);
	            $item->save();
		    
		        if($todo = $this->getUser()->getTodo('SM_TODOITEMTYPE_APPROVE_ITEM', $item->getId())){
			        $todo->complete();
		        }
		        
		        $uhelper = new SmartestUsersHelper;
		        $publishable_users = $uhelper->getUsersThatHaveToken(array('publish_all_items', 'publish_approved_items'), $this->getUser());
		    
		        foreach($publishable_users as $u){
		            if(!$u->hasTodo('SM_TODOITEMTYPE_PUBLISH_ITEM', $item->getId())){
			            $u->assignTodo('SM_TODOITEMTYPE_PUBLISH_ITEM', $item->getId(), 0);
		            }
		        }
		      
	        }else{
	            // user does not have permission
	            $this->addUserMessageToNextRequest('You do not have permission to approve item changes.', SmartestUserMessage::ACCESS_DENIED);
	        }
	        
	    }else{
	        $this->addUserMessageToNextRequest('The Item ID was not recognised.', SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	    
	}
	
	public function preview(){
	    
	    $item_id = $this->getRequestParameter('item_id');
        $item = SmartestCmsItem::retrieveByPk($item_id);
        
        if(is_object($item)){
            
            $metapages = $item->getModel()->getMetaPages();
            $num_metapages = count($metapages);
            
            if(!$num_metapages){
                
            }else if($num_metapages == 1){
                // forward to preview of only metapage
                $this->redirect('/websitemanager/preview?page_id='.$metapages[0]->getWebId().'&item_id='.$item_id);
            }else{
                // display choice
                $this->send($metapages, 'metapages');
                $this->send($item, 'item');
                
                // checkto see if a specific page has already been chosen - We'll use this as a default
                $metapage = new SmartestPage;

    		    if($metapage->find($item->getMetapageId())){
    		        $this->send($metapage, 'default_metapage');
    		    }else if($metapage->find($item->getModel()->getDefaultMetapageId($this->getSite()->getId()))){
    		        $this->send($metapage, 'default_metapage');
    		    }
                
            }
            
        }
	    
	}
	
	public function publishItem($get, $post){
	    
	    if(isset($_POST['item_id'])){
	    
	        // actually publish the item, or at least try
	        $item_id = $this->getRequestParameter('item_id');
            $item = SmartestCmsItem::retrieveByPk($item_id);
        
            if(is_object($item)){
	            
	            if(($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items')){
	                
	                if(!count($item->getModel()->getMetaPages())){
	                    if($this->getRequestParameter('hide_no_metapage_warning') == '1'){
                            $this->setApplicationPreference('hide_metapages_publish_warning', '1');
                        }
                    }
	                
	                // it is ok to publish the item
    	            $item->publish();
    	            
    	            $update_itemspaces = $this->getRequestParameter('update_itemspaces');
    	            
    	            if($update_itemspaces == 'IGNORE'){
    	                
    	            }else{
    	                
    	                $itemspace_defs = $item->getItemSpaceDefinitions(true);
    	                
    	                foreach($itemspace_defs as $def){
    	                    
    	                    $def->publish();
    	                    
    	                    if($update_itemspaces == 'PUBLISHPAGE'){
    	                        
    	                        if($page = $def->getPage()){
    	                            
    	                            if(($this->getUser()->hasToken('publish_approved_pages') && $page->isApproved()) || $this->getUser()->hasToken('publish_all_pages')){
    	                                $page->publish();
	                                }
    	                        }
    	                    }
    	                }
    	            }
    	            
    	            $update_pages = $this->getRequestParameter('update_pages');
    	            
    	            if($page = $item->getMetaPage()){
    	                
    	                if($update_pages == 'PERITEM'){
    	                
    	                    $page->publishAssetClasses($item->getId(), true);
    	                    
    	                    /* if($this->getRequestParameter('metapage_id') && is_numeric($this->getRequestParameter('metapage_id'))){
    	                
        	                    $page = new SmartestPage;
    	                
        	                    if($page->hydrate($this->getRequestParameter('metapage_id'))){
        	                        $page->publish();
        	                    }else{
        	                        $this->getRequestParameter('metapage_id');
        	                    }
    	                
        	                } */
    	            
    	                }elseif($update_pages == 'PUBLISH'){
    	                    
    	                    $page->publish($item->getId());
    	                    
    	                }
	                
                    }
	                
	                $this->addUserMessageToNextRequest('The item has been published.', SmartestUserMessage::SUCCESS);
	            
    	        }else{
	            
    	            // the user doesn't have permissions
    	            if(!$item->isApproved()){
    	                $this->addUserMessageToNextRequest('You don\'t have permission to publish items without them being approved first.', SmartestUserMessage::ACCESS_DENIED);
    	            }else{
    	                $this->addUserMessageToNextRequest('You don\'t have permission to publish items.', SmartestUserMessage::ACCESS_DENIED);
    	            }
	            
    	        }
	    
            }else{
            
                $this->addUserMessageToNextRequest('The Item ID was not recognised.', SmartestUserMessage::ERROR);
            
            }
            
            if($this->hasFormReturnVar('item_id') && !$this->getRequestParameter('from')){
                if($this->getRequestParameter('page_id')){
                    $this->redirect('/datamanager/editItem?page_id='.$this->getRequestParameter('page_id').'&item_id='.$item_id);
                }else{
                    $this->redirect('/datamanager/editItem?item_id='.$item_id);
                }
            }else{
                $this->formForward();
            }
        
        }else{
            
            // Display publish options/warnings before doing the deed
            $item_id = $this->getRequestParameter('item_id');
            $item = SmartestCmsItem::retrieveByPk($item_id);
            
            if(is_object($item)){
	            
	            $metapages = $item->getModel()->getMetaPages();
                $this->send($metapages, 'metapages');
                
                if(!count($metapages)){
                    $this->send(($this->getApplicationPreference('hide_metapages_publish_warning') == '1') ? false : true, 'metapages_publish_warning');
                }
	            
	            if($page = $item->getMetaPage()){
	                $this->send($page, 'meta_page');
	                $this->send($item->getItem()->getDefaultMetaPageHasBeenChanged(), 'meta_page_has_changed');
                    if($this->getUser()->hasToken('publish_all_pages') || $this->getUser()->hasToken('publish_approved_pages')){
                        $this->send(true, 'show_page_publish_option');
                    }else{
                        $this->send(false, 'show_page_publish_option');
                    }
                }else{
                    $this->send(false, 'show_page_publish_option');
                }
	            
    	        if(($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items')){
	            
    	            // user has permission - show options
    	            $this->send($item, 'item');
    	            $this->setTitle('Publish '.$item_data['_model']['name']);
	            
    	        }else{
	            
    	            // the user doesn't have permissions
    	            if(!$item->isApproved()){
    	                $this->addUserMessageToNextRequest('You don\'t have permission to publish items without them being approved first.', SmartestUserMessage::ACCESS_DENIED);
    	            }else{
    	                $this->addUserMessageToNextRequest('You don\'t have permission to publish items.', SmartestUserMessage::ACCESS_DENIED);
    	            }
    	            
    	            if($this->hasFormReturnVar('item_id')){
                      $this->redirect('/datamanager/editItem?item_id='.$item_id);
                  }else{
    	                $this->formForward();
    	            }
	            
    	        }
	    
            }else{
            
                $this->addUserMessageToNextRequest('The Item ID was not recognised.', SmartestUserMessage::ERROR);
                $this->formForward();
            
            }
            
        }
	    
	}
	
	public function unpublishItem($get){
	    
	    $item_id = $this->getRequestParameter('item_id');
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        // if(($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items')){
	            $item->setPublic('FALSE');
	            $item->save();
	            $this->addUserMessageToNextRequest('The item is no longer visible on the site.', SmartestUserMessage::SUCCESS);
	        /* }else{
	            $this->addUserMessageToNextRequest('You don\'t have permission to unpublish items.', SmartestUserMessage::ACCESS_DENIED);
	        } */
        }else{
            $this->addUserMessageToNextRequest('The Item ID was not recognised.', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	/* public function editProperties($get){  
		
		$formValues = $this->manager->getItemClassProperties($get["class_id"]);
		$selectMenus = array();
		
		if (is_array($formValues)){
		
			foreach ($formValues as $property){
				if ($property["itemproperty_datatype"] == "OTHERCLASS"){
					$classmembers = $this->getItemClassMembers($property["itemproperty_parent_class_id"]);
					
					for($i=0;$i<count($classmembers[0]);$i++){
						if(strlen($property["itemproperty_varname"]) > 0){
							$selectMenus[$property["itemproperty_varname"]][$i]["id"] = $classmembers[0][$i]["item"]["item_id"];
							$selectMenus[$property["itemproperty_varname"]][$i]["name"] = $classmembers[0][$i]["item"]["item_name"];
						}
						else{
							$selectMenus[$property["itemproperty_name"]][$i]["id"] = $classmembers[0][$i]["item"]["item_id"];
							$selectMenus[$property["itemproperty_name"]][$i]["name"] = $classmembers[0][$i]["item"]["item_name"];
						}
					}
				}
			}
		}
		
		$itemBaseValues = $this->manager->getItemClassBaseValues($get["class_id"]);
		$itemClass = $this->manager->getItemClass($get["class_id"]);		
		return array("itemClass"=>$itemClass[0], "itemProperties"=>$itemBaseValues, "formProperties"=>$formValues, "otherClassMenus"=>$selectMenus);
	} */
	
    public function updateItemClassProperty($get, $post){
		
		if($this->getUser()->hasToken('create_remove_properties')){
		
    		$itemproperty_id = (int) $this->getRequestParameter('itemproperty_id');
    		$property = new SmartestItemProperty;
		
    		if($property->find($itemproperty_id)){
		    
    		    $property->setRequired($this->getRequestParameter('itemproperty_required') ? 'TRUE' : 'FALSE');
    		    $property->setHint($this->getRequestParameter('itemproperty_hint'));
    		    
    		    if($this->getRequestParameter('itemproperty_default_value')){
    		        try{
    		            if($v = SmartestDataUtility::objectizeFromRawFormData($this->getRequestParameter('itemproperty_default_value'), $property->getDataType())){
    		                $property->setDefaultValue($v->getStorableFormat());
    		            }
    		        }catch(SmartestException $e){
    		            
    		        }
    		    }
		    
    		    if($property->getDataType() == 'SM_DATATYPE_ASSET' || $property->getDataType() == 'SM_DATATYPE_ASSET_DOWNLOAD'){
		    
        		    if($this->getRequestParameter('itemproperty_filter') == 'NONE'){
		        
        		        $property->setOptionSetType('SM_PROPERTY_FILTERTYPE_NONE');
        		        $property->setOptionSetId(0);
		        
        		    }else if($this->getRequestParameter('itemproperty_filter_type') && $this->getRequestParameter('itemproperty_filter_type') == 'ASSET_GROUP'){
		        
        		        $property->setOptionSetType('SM_PROPERTY_FILTERTYPE_ASSETGROUP');
        		        $property->setOptionSetId((int) $this->getRequestParameter('itemproperty_filter'));
		        
        		    }
		    
    	        }
	        
    	        if($property->getDataType() == 'SM_DATATYPE_CMS_ITEM' || $property->getDataType() == 'SM_DATATYPE_CMS_ITEM_SELECTION'){
                
                    if($this->getRequestParameter('itemproperty_filter') == 'NONE'){
		        
        		        $property->setOptionSetType('SM_PROPERTY_FILTERTYPE_NONE');
        		        $property->setOptionSetId(0);
		        
        		    }else{
    		        
        		        $property->setOptionSetType('SM_PROPERTY_FILTERTYPE_DATASET');
        		        $property->setOptionSetId((int) $this->getRequestParameter('itemproperty_filter'));
    		        
        		    }
                
                }
		    
    		    $property->save();
    		    $this->addUserMessageToNextRequest('The property was updated.', SmartestUserMessage::SUCCESS);
    		    SmartestCache::clear('model_properties_'.$property->getItemclassId(), true);
	    
            }else{
            
                $this->addUserMessageToNextRequest('The property ID was not recognized.', SmartestUserMessage::ERROR);
            
            }
        
        }else{
            
            $this->addUserMessageToNextRequest('You don\'t have permission to edit model properties', SmartestUserMessage::ACCESS_DENIED);
            
        }
		
		$this->formForward();
		
	}
	
	//// ADD (pre-action interface/options) and INSERT (the actual action)
	
	/*
	functions:
	addItem()
	insertItem()
	addItemClass()
	insertItemClass()
	*/
	
	public function addItem($get, $post){
        
        $this->send(date("Y"), 'default_year');
        $this->send(date("m"), 'default_month');
        $this->send(date("d"), 'default_day');
        
        if($this->getUser()->hasToken('add_items')){
            
            if($this->getRequestParameter('class_id')){
            
                $model_id = $this->getRequestParameter('class_id');
            
            }else if($this->getRequestParameter('for') == 'ipv' && $this->getRequestParameter('property_id')){
                
                $p = new SmartestItemProperty;
                
                if($p->find($this->getRequestParameter('property_id'))){
                    if($p->getDatatype() == 'SM_DATATYPE_CMS_ITEM'){
                        
                        $model_id = $p->getForeignKeyFilter();
                        $this->send($p, 'parent_property');
                        
                        if($parent_item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
                            $this->send($parent_item, 'parent_item');
                        }
                        
                    }else{
                        
                    }
                }else{
                    
                }
                
            }
            
            $model = new SmartestModel;
            
            if($model->find($model_id)){
                
                if($model->hasPrimaryProperty() && $model->getPrimaryProperty()->getDatatype() == 'SM_DATATYPE_ASSET'){
                    $this->redirect('/smartest/file/new?for=ipv&property_id='.$model->getPrimaryPropertyId());
                }
                
                $this->send($this->getUser()->hasToken('create_assets'), 'can_create_assets');
                $this->send($this->getUser()->hasToken('create_remove_properties'), 'can_edit_properties');
                $this->send($model->getProperties(), 'properties');
                $this->send($model, 'model');
            
            }else{
                
                $this->addUserMessageToNextRequest('The model id was not recognised.', SmartestUserMessage::ERROR);
                
            }
        
        }
        
	}
	
	public function insertItem($get, $post){
	    
	    // values for new item have been submitted, so process them
        $model = new SmartestModel;
        
		if($model->find((int) $this->getRequestParameter('class_id'))){
		
		    $class_name = $model->getClassname();
		    
		    if(!class_exists($class_name)){
		        $model->init();
		    }
        
            if(class_exists($class_name)){
        
    		    $item = new $class_name;
        		
        		// provided it has a name, save the item - incomplete or not. incomplete items can be created & saved, but not published.
        		$new_values = $this->getRequestParameter('item');
        		
                if($new_values['_name']){
                
                    $item->hydrateNewFromRequest($new_values);
                    $item->setSiteId($this->getSite()->getId());
                
                    if($success = $item->save()){
                        
                        if($this->getUser()->hasToken('author_credit')){
                            $item->addAuthorById($this->getUser()->getId());
                        }
                        
                        if($this->getRequestParameter('for') == 'ipv'){
                            
                            $parent_item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'));
                            $parent_item->setPropertyValueByNumericKey($this->getRequestParameter('property_id'), $item->getId());
                            $this->addUserMessageToNextRequest("Your new ".$model->getName()." has been created.", SmartestUserMessage::SUCCESS);
                            $this->redirect("/datamanager/openItem?item_id=".$parent_item->getId());
                            
                        }else{
                        
                            if($this->getRequestParameter('nextAction') == 'createAsset' && is_numeric($this->getRequestParameter('property_id'))){
                            
                                // redirect the user to the screen for creating an asset
                                $this->addUserMessageToNextRequest("Your new ".$model->getName()." has been created.", SmartestUserMessage::SUCCESS);
                                $this->redirect("/assets/startNewFileCreationForItemPropertyValue?property_id=".$this->getRequestParameter('property_id')."&item_id=".$item->getId());
                            
                            }else if($this->getRequestParameter('nextAction') == 'createTemplate' && is_numeric($this->getRequestParameter('property_id'))){
                            
                                // redirect the user to the screen for creating a single-item template
                                $this->addUserMessageToNextRequest("Your new ".$model->getName()." has been created.", SmartestUserMessage::SUCCESS);
                                $this->redirect("/templates/startNewTemplateCreationForItemPropertyValue?property_id=".$this->getRequestParameter('property_id')."&item_id=".$item->getId());
                            
                            }else if($this->getRequestParameter('nextAction') == 'createItem' && is_numeric($this->getRequestParameter('property_id'))){
                            
                                // redirect the user to the screen for creating another item
                                $this->addUserMessageToNextRequest("Your new ".$model->getName()." has been created.", SmartestUserMessage::SUCCESS);
                                $this->redirect("/datamanager/addItem?for=ipv&property_id=".$this->getRequestParameter('property_id')."&item_id=".$item->getId());
                            
                            }else{
                        
                                $this->addUserMessageToNextRequest("Your new ".$model->getName()." has been created.", SmartestUserMessage::SUCCESS);
                                $this->redirect("/datamanager/openItem?item_id=".$item->getId());
                        
                            }
                        
                        }
                        
                    }else{
                        $this->formForward();
                    }
                
                
                }else{
                    $this->addUserMessageToNextRequest("You cannot create ".$class_name." does not exist or is not properly defined.", SmartestUserMessage::ERROR);
                    $this->formForward();
                }
        
            }else{
            
                $this->addUserMessageToNextRequest("The model class '".$model->getClassName()."' does not exist.", SmartestUserMessage::WARNING);
                $this->formForward();
            
            }
        
        }
	    
	}

 /*   function insertSettings($get, $post){
  
		$itemclass_id = $this->getRequestParameter('itemclass_id');
		$item_name = $this->getRequestParameter('itemName');
		$item_slung=$this->_string->toSlug($item_name);
		$item_id = $this->manager->setItemname($this->_string->random(32),$item_slung,$itemclass_id,$item_name);
		
		foreach ($this->getRequestParameter('itemProperty') as $itemproperty_varname=>$itempropertyvalue_content){
			
			$itemproperty = $this->manager->getItemProperties($itemclass_id,$itemproperty_varname);
			$itemproperty_id = $itemproperty[0]["itemproperty_id"];
			$itemproperty_datatype = $itemproperty[0]["itemproperty_datatype"];
			
			if($itemproperty_datatype != 'FILE'){
			
				switch($itemproperty_datatype){					
					case "NUMERIC":
					case "BOOLEAN":
					case "STRING":
					case "OTHERCLASS":
					case "TEXT":
						$this->manager->setItemPropertyValues($item_id,$itemproperty_id,$itempropertyvalue_content);
					break;
					
					case "DATE":
						$date = $itempropertyvalue_content['Y']."-".$itempropertyvalue_content['M']."-".$itempropertyvalue_content['D'];
						$this->manager->setItemPropertyValues($item_id,$itemproperty_id,$date);		
					break;
				}
	
				$itemproperty = $this->manager->getItemproperty_datatype($itemclass_id,$itemproperty_varname);
				$itemproperty_id = $itemproperty[0]["itemproperty_id"];
				$itemproperty_datatype = $itemproperty[0]["itemproperty_datatype"];
				
				if($itemproperty_datatype != 'FILE'){
					
					switch($itemproperty_datatype){						
						case "NUMERIC":
						case "BOOLEAN":
						case "STRING":
						case "OTHERCLASS":
						case "TEXT":
						$this->manager->setItemPropertyValues($item_id,$itemproperty_id,$itempropertyvalue_content);
						break;
						
						case "DATE":
						$date = $itempropertyvalue_content['Y']."-".$itempropertyvalue_content['M']."-".$itempropertyvalue_content['D'];
						$this->manager->setItemPropertyValues($item_id,$itemproperty_id,$date);		
						break;
					}
				}
			}
		}
	} */
	
	public function addItemClass($get){
		
		if($this->getUser()->hasToken('create_models')){
		
		    // get possible parent pages for meta page
		    $pagesTree = $this->getSite()->getNormalPagesList(true);
		    $this->send($pagesTree, 'pages');
		    $this->send(($this->getRequestParameter('createmetapage') && $this->getRequestParameter('createmetapage') == 'true') ? true : false, 'cmp');
		    
		    // get page templates
		    $tlh = new SmartestTemplatesLibraryHelper;
            $templates = $tlh->getMasterTemplates($this->getSite()->getId());
            $this->send($templates, 'templates');
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't have permission to add models.");
	        $this->redirect('/smartest/models');
	        
	    }
	}
  
	public function insertItemClass($get, $post){
		
		if($this->getUser()->hasToken('create_models')){
		    
		    $du = new SmartestDataUtility;
		    
		    if(strlen($this->getRequestParameter('itemclass_name')) > 2 && $du->isValidModelName($this->getRequestParameter('itemclass_name'))){
		        
		        $shared = ($this->getRequestParameter('itemclass_shared') && $this->getRequestParameter('itemclass_shared'));
		        
		        if($du->modelNameIsAvailable($this->getRequestParameter('itemclass_name'), $this->getSite()->getId(), false)){
		        
        		    $model = new SmartestModel;
        		    $model->setName($this->getRequestParameter('itemclass_name'));
        		    $model->setPluralName($this->getRequestParameter('itemclass_plural_name'));
        		    $model->setVarname(SmartestStringHelper::toVarName($this->getRequestParameter('itemclass_plural_name')));
        		    $model->setWebid(SmartestStringHelper::random(16));
        		    $model->setType('SM_ITEMCLASS_MODEL');
        		    $model->setSiteId($this->getSite()->getId());
        		    
        		    // This feature needs to be thought through some more:
        		    // $model->setItemNameFieldVisible($this->getRequestParameter('itemclass_name_field_visible') ? 1 : 0);
        		    $model->setItemNameFieldVisible(1);
        		    
        		    if($model->hasSameNameAsModelOnOtherSite()){
        		        $model->setShared(0);
        		        $this->addUserMessageToNextRequest("The model could not be shared because it has the same name as a model used on another site.");
        		    }else{
        		        $model->setShared((int) $shared);
    		        }
    		        
        		    $model->save();
    		    
        		    if($this->getRequestParameter('create_meta_page') && $this->getRequestParameter('create_meta_page') == 1){
        		        $p = new SmartestPage;
        		        $p->setTitle($this->getRequestParameter('itemclass_name'));
        		        $p->setWebId(SmartestStringHelper::random(32));
        		        $p->setName(SmartestStringHelper::toSlug($this->getRequestParameter('itemclass_name')));
        		        $p->setSiteId($this->getSite()->getId());
        		        $p->addUrl(SmartestStringHelper::toSlug($this->getRequestParameter('itemclass_plural_name')).'/:name.html'); 
        		        $p->setParent($this->getRequestParameter('meta_page_parent'));
        		        $p->setDraftTemplate($this->getRequestParameter('meta_page_template'));
        		        $p->setCreated(time());
        		        $p->setCreatedbyUserid(0);
        		        $p->setIsPublished('FALSE');
        		        $p->setType('ITEMCLASS');
        		        $p->setDataSetId($model->getId());
        		        $p->save();
        		        SmartestCache::clear('site_pages_tree_'.$this->getSite()->getId(), true);
        		        $model->setDefaultMetaPageId($this->getSite()->getId(), $p->getId());
        		        $model->save();
        		    }
    		    
        		    $this->addUserMessageToNextRequest("The new model has been saved. Now add some properties.", SmartestUserMessage::SUCCESS);
        		    // SmartestCache::clear('model_class_names', true);
        		    // SmartestCache::clear('model_id_name_lookup', true);
        		    if($shared){
        		        $du->flushModelsCache();
        		    }
        		    
        		    $model->init();
        		    
        		    $this->redirect("/".$this->getRequest()->getModule()."/addPropertyToClass?class_id=".$model->getId());
    		    
		        }
		    
    	    }else{
	        
    	        $this->addUserMessageToNextRequest("The model name '".$this->getRequestParameter('itemclass_name')."' is not valid.", SmartestUserMessage::WARNING);
    	        $this->forward('datamanager', 'addItemClass');
	        
    	    }
	    
        }else{
            $this->addUserMessageToNextRequest("You don't have permission to add models.");
	        $this->formForward();
        }
		
	}
  
	public function insertItemClassProperty($get, $post){
		
		$model_id = $this->getRequestParameter('class_id');
		
		if($this->getUser()->hasToken('create_remove_properties')){
		
    		$new_property_name = $this->getRequestParameter('itemproperty_name');
		
    		$model = new SmartestModel;
    		$model->find($model_id);
		
    		if(SmartestDataUtility::isValidPropertyName($new_property_name)){
		    
    		    $new_get_method = 'get'.SmartestStringHelper::toCamelCase($this->getRequestParameter('itemproperty_name'));
		    
    		    if(in_array($new_get_method, get_class_methods($model->getClassName()))){
		        
    		        $this->addUserMessage('A property with that name already exists for this model.', SmartestUserMessage::WARNING);
    		        $this->forward('datamanager', 'addPropertyToClass');
		        
    		    }else{
		    
    		        $property = new SmartestItemProperty;
		
            		$property->setName($this->getRequestParameter('itemproperty_name'));
            		$property->setVarname(SmartestStringHelper::toVarName($property->getName()));
            		$property->setDatatype($this->getRequestParameter('itemproperty_datatype'));
            		$property->setRequired($this->getRequestParameter('itemproperty_required') ? 'TRUE' : 'FALSE');
            		$property->setItemClassId($model->getId());
            		$property->setWebid(SmartestStringHelper::random(16));
            		$property->setOrderIndex($model->getNextPropertyOrderIndex());
		
            		if($this->getRequestParameter('foreign_key_filter')){
            		    $property->setForeignKeyFilter($this->getRequestParameter('foreign_key_filter'));
            		}
            		
            		if($this->getRequestParameter('create_group')){
            		    
            		    if($this->getRequestParameter('itemproperty_datatype') == 'SM_DATATYPE_ASSET'){
            		    
            		        $set = new SmartestAssetGroup;
            		    
                		    $label = $property->getName().' files for '.$model->getPluralName();
                	    
                    	    $set->setLabel($label);
                    	    $set->setName(SmartestStringHelper::toVarName($label));

                	        switch(substr($this->getRequestParameter('foreign_key_filter'), 8, 1)){
                	            case 'T':
                	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETTYPE');
                	            break;
                	            case 'C':
                	            $set->setFilterType('SM_SET_FILTERTYPE_ASSETCLASS');
                	            break;
                	        }

                    	    $set->setFilterValue($this->getRequestParameter('foreign_key_filter'));
                    	    $set->setSiteId($this->getSite()->getId());
                    	    $set->setShared(0);
                    	    $set->save();
                	    
                    	    $property->setOptionSetType('SM_PROPERTY_FILTERTYPE_ASSETGROUP');
                    	    $property->setOptionSetId($set->getId());
                	    
            	        }else if($this->getRequestParameter('itemproperty_datatype') == 'SM_DATATYPE_TEMPLATE'){
            		        
            		        $set = new SmartestTemplateGroup;
            		        $label = $property->getName().' templates for '.$model->getPluralName();
            		        $set->setLabel($label);
                    	    $set->setName(SmartestStringHelper::toVarName($label));
                    	    $set->setFilterType('SM_SET_FILTERTYPE_TEMPLATETYPE');
                    	    $set->setFilterValue('SM_ASSETTYPE_SINGLE_ITEM_TEMPLATE');
                    	    
                    	    $set->setSiteId($this->getSite()->getId());
                    	    $shared = ($model->isShared()) ? 1 : 0;
                    	    $set->setShared($shared);
                    	    $set->save();
                	    
                    	    $property->setOptionSetType('SM_PROPERTY_FILTERTYPE_TEMPLATEGROUP');
                    	    $property->setOptionSetId($set->getId());
            		        
            		    }
                	    
            		}
		
            		$property->save();
	    
            	    SmartestCache::clear('model_properties_'.$model->getId(), true);
            	    SmartestObjectModelHelper::buildAutoClassFile($model->getId(), $model->getName());
    	    
            	    SmartestLog::getInstance('site')->log($this->getUser()->__toString()." added a property called $new_property_name to model ".$model->getName().".", SmartestLog::USER_ACTION);
	    
            	    $this->addUserMessageToNextRequest("Your new property has been added.", SmartestUserMessage::SUCCESS);
	    
        	        if($this->getRequestParameter('continue') == 'NEW_PROPERTY'){
        	            $this->redirect('/datamanager/addPropertyToClass?class_id='.$model->getId().'&continue=NEW_PROPERTY');
        	        }else{
        	            $this->redirect('/datamanager/getItemClassProperties?class_id='.$model->getId());
        	        }
	        
                }
	    
            }else{
                $this->addUserMessageToNextRequest("You must enter a valid property name.", SmartestUserMessage::WARNING);
                SmartestLog::getInstance('site')->log("{$this->getUser()} tried to add a property called '$new_property_name' to model {$model->getName()}.", SmartestLog::WARNING);
                SmartestLog::getInstance('system')->log("{$this->getUser()} tried to add a property called '$new_property_name' to model {$model->getName()}.", SmartestLog::ERROR);
                $this->forward('datamanager', 'addPropertyToClass');
            }
        
        }else{
            
            $this->addUserMessageToNextRequest("You do not have permission to save properties.", SmartestUserMessage::WARNING);
            $this->redirect('/datamanager/getItemClassProperties?class_id='.$model_id);
            
        }

	}
	
	public function addPropertyToClass($get){
		
		if($this->getUser()->hasToken('create_remove_properties')){
		
		    $name=$get["name"];
    		$sel_id=$get["sel_id"];
    		$type=$get["type"];
    		$model_id=$get["class_id"];
		
    		$model = new SmartestModel;
    		
    		if($model->hydrate($model_id)){
    		    
    		    $data_types = SmartestDataUtility::getDataTypes();
    		    
    		    $this->send($data_types, 'data_types');
    		    $this->send($model, 'model');
    		    $this->setTitle('Add a Property to Model | '.$model->getPluralName());
    		    $this->send($this->getRequestParameter('continue') ? $this->getRequestParameter('continue') : 'PROPERTIES', 'continue');
    		    
    		    if($this->getRequestParameter('itemproperty_datatype')){
    		        
    		        $data_type_code = $this->getRequestParameter('itemproperty_datatype');
    		        
    		        if(isset($data_types[$data_type_code])){
    		            
    		            $property = new SmartestItemProperty;
    		            $property->setDataType($data_type_code);
    		            
    		            $this->send($data_types[$data_type_code]['description'], 'type_description');
    		            
    		            $data_type = $data_types[$data_type_code];
    		            
    		            if(($data_type['valuetype'] == 'foreignkey' || $data_type['valuetype'] == 'manytomany') && isset($data_type['filter']['typesource'])){
    		                
    		                if(is_file($data_type['filter']['typesource']['template'])){
    		                    $this->send(SmartestDataUtility::getForeignKeyFilterOptions($data_type_code), 'foreign_key_filter_options');
    		                    $this->send(SM_ROOT_DIR.$data_type['filter']['typesource']['template'], 'filter_select_template');
    		                }else{
    		                    $this->send($data_type['filter']['typesource']['template'], 'intended_file');
    		                    $this->send(SM_ROOT_DIR.'System/Applications/Items/Presentation/FKFilterSelectors/filtertype.unknown.tpl', 'filter_select_template');
    		                }
    		                
    		                $this->send(true, 'foreign_key_filter_select');
    		                
    		            }else if($data_type['valuetype'] == 'auto'){
    		                
    		                if(is_file($data_type['filter']['typesource']['template'])){
    		                    $this->send(new SmartestArray($model->getReferringProperties()), 'foreign_key_filter_options');
    		                    $this->send(SM_ROOT_DIR.$data_type['filter']['typesource']['template'], 'filter_select_template');
    		                }else{
    		                    $this->send($data_type['filter']['typesource']['template'], 'intended_file');
    		                    $this->send(SM_ROOT_DIR.'System/Applications/Items/Presentation/FKFilterSelectors/filtertype.unknown.tpl', 'filter_select_template');
    		                }
    		                
    		                $this->send(true, 'foreign_key_filter_select');
    		                
    		            }
    		            
    		            $this->send(true, 'show_full_form');
    		            $this->send($property, 'property');
    		            
		            }else{
		                $this->send(false, 'show_full_form');
		            }
    		    
		        }else{
		            
		            $this->send(false, 'show_full_form');
		            
		        }
    		
		    }else{
		        
		        $this->addUserMessageToNextRequest("The model ID was not recognized.", SmartestUserMessage::WARNING);
    	        $this->redirect('/smartest/models');
		        
		    }
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't have permission to add model properties.", SmartestUserMessage::ACCESSDENIED);
	        $this->redirect('/smartest/models');
	        
	    }
		
	}
	
	public function editItemClassProperty($get){
	    
	    $property_id = $this->getRequestParameter('itemproperty_id');
	    $property = new SmartestItemProperty;
		
		if($this->getUser()->hasToken('create_remove_properties')){
		
    		if($property->find($property_id)){
		    
    		    $model_id = $property->getItemclassId();
    		    $model = new SmartestModel;
    		    $model->find($model_id);
		    
    		    if(!strlen($property->getWebid())){
    		        $property->setWebid(SmartestStringHelper::random(16));
    		        $property->save();
    		    }
		    
    		    if($this->getRequestParameter('from') == 'item_edit' && is_numeric($this->getRequestParameter('item_id'))){
    		    
        		    $ruri = '/datamanager/editItem?item_id='.$this->getRequestParameter('item_id');
    		    
        		    if($this->getRequestParameter('page_id')){
        		        $ruri .= '&page_id='.$this->getRequestParameter('page_id');
        		    }
		    
        		    $this->setTemporaryFormReturnUri($ruri);
		    
        		    if($item = SmartestCmsItem::retrieveByPk($this->getRequestParameter('item_id'))){
        	            $this->setTemporaryFormReturnDescription(strtolower($item->getModel()->getName()));
        	        }
        		}
		    
    		    $this->setTitle($model->getPluralName().' | Edit Property');
		    
    		    $data_types = SmartestDataUtility::getDataTypes();
		    
    		    if($property->getDataType() == 'SM_DATATYPE_ASSET' || $property->getDataType() == 'SM_DATATYPE_ASSET_DOWNLOAD'){
		        
    		        $possible_groups = $property->getPossibleFileGroups($this->getSite()->getId());
    		        $this->send($possible_groups, 'possible_groups');
    		        $this->send($this->getUser()->hasToken('create_assets'), 'can_create_assets');
    		        
    		        $fkf = $property->getForeignKeyFilter();
    		        
    		        if($fkf{8} == 'C'){
    		            
    		            $h = new SmartestAssetClassesHelper;
    		            $types = $h->getAssetTypesFromAssetClassType($fkf);
    		            $type_names = new SmartestArray;
    		            
    		            foreach($types as $t){
    		                $type_names->push($t['label']);
    		            }
    		            
    		            $this->send($type_names->__toString().' files', 'file_type');
    		            
    		        }else{
    		            
    		            $h = new SmartestAssetsLibraryHelper;
    		            $types = $h->getTypes();
    		            
    		            if(isset($types[$fkf])){
    		                $this->send($types[$fkf]['label'].' files', 'file_type');
    		            }else{
    		                $this->send('Unknown file type', 'file_type');
    		            }
    		            
    		        }
		        
    		    }
		    
    		    if($property->getDataType() == 'SM_DATATYPE_TEMPLATE'){
		        
    		        $possible_groups = $property->getPossibleTemplateGroups($this->getSite()->getId());
    		        $this->send($possible_groups, 'possible_groups');
		        
    		    }
		    
    		    if($property->getDataType() == 'SM_DATATYPE_CMS_ITEM' || $property->getDataType() == 'SM_DATATYPE_CMS_ITEM_SELECTION'){
		        
    		        $possible_sets = $property->getPossibleDataSets($this->getSite()->getId());
    		        $this->send($possible_sets, 'possible_sets');
		        
    		    }
		    
    		    $this->send($data_types, 'data_types');
    		    $this->send($model->compile(), 'model');
    		    $this->send($property, 'property');
		    
    		}else{
    		    
    		    $this->addUserMessageToNextRequest("The property ID was not found", SmartestUserMessage::ERROR);
    		    $this->formForward();
    		    
    		}
		
	    }else{
	        
	        $this->addUserMessageToNextRequest("You don't have permission to edit item properties.", SmartestUserMessage::ACCESS_DENIED);
		    $this->formForward();
	        
	    }
	    
	}
	
	public function startItemClassPropertyRegularization(){
	    
	    $property = new SmartestItemProperty;
	    
	    if($property->find($this->getRequestParameter('itemproperty_id'))){
	        
	        $sd = SmartestYamlHelper::fastLoad(SM_ROOT_DIR."System/Core/Info/system.yml");
	        
	        if(in_array($property->getDatatype(), $sd['system']['regularizable_types'])){
	        
    	        if(!strlen($property->getWebid())){
    		        $property->setWebid(SmartestStringHelper::random(16));
    		        $property->save();
    		    }
	        
    	        $this->send($property, 'property');
    	        $this->send(count($property->getStoredValues($this->getSite()->getId())), 'num_values');
    	        $this->send(true, 'allow');
	        
            }else{
                
                $this->send($property, 'property');
                $this->send(false, 'allow');
                
            }
	        
	    }
	    
	}
	
	public function addNewItemClassAction($get, $post){
    
		if(strlen($this->getRequestParameter('item_class_name'))>0){
			
			$status = $this->manager->addNewItemClass($this->getRequestParameter('item_class_name'));        
			
			if($status){
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
	
	/* public function getXmlTest($get){
		$this->schemasManager = new SchemasManager();
		$search_string = $this->getRequestParameter('search');
		$items = $this->manager->getItemsInClass($get["class_id"]);
		$itemBaseValues = $this->manager->getItemClassBaseValues($get["class_id"]);    
		$itemClassMembers = $this->manager->countItemClassMembers($get["class_id"]); 
		$itemClassPropertyCount = $this->manager->countItemClassProperties($get["class_id"]); 		
		$item_id  = $this->getRequestParameter('item_id');      
		$itemsclass = $this->manager->getItemValues($item_id);
		$itemsclass_id = $itemsclass[0]['item_itemclass_id'];

		// get properties and their values
		$itemspropertyvalues = $this->manager->getItemPropertyValues($item_id,$itemsclass_id);

		// populate popup menus, if any
		$selectMenus = array();
		$dates = array();

		if (is_array($itemspropertyvalues)){
			foreach ($itemspropertyvalues as $property){
				if ($property["itemproperty_datatype"] == "OTHERCLASS"){
			
				}
      
				if($property["itemproperty_datatype"] == "DATE"){
		
					$date_parts = explode("-", $property["itempropertyvalue_content"]);
					
					if( isset($property["itemproperty_varname"]) ){
						$dates[$property["itemproperty_varname"]]["Y"] = $date_parts[0];
						$dates[$property["itemproperty_varname"]]["M"] = $date_parts[1];
						$dates[$property["itemproperty_varname"]]["D"] = $date_parts[2];
					}
					else{
						$dates[$property["itemproperty_name"]]["Y"] = $date_parts[0];
						$dates[$property["itemproperty_name"]]["M"] = $date_parts[1];
						$dates[$property["itemproperty_name"]]["D"] = $date_parts[2];
					}
				}
			}
		}
  
		$itemBaseValues = $this->manager->getItemClass($itemsclass_id);
		$itemBaseValues["item_id"] = $item_id;
		$itemBaseValues["item_name"] = $this->database->specificQuery("item_name", "item_id", $item_id, "Items");
		$itemBaseValues["item_public"] = $this->database->specificQuery("item_public", "item_id", $item_id, "Items");



		$schemaDetails = $this->schemasManager->getSchema($itemClass['itemclass_schema_id']);
		
		$serializer_options = array ( 
			'addDecl' => TRUE, 
			'encoding' => $schemaDetails['schema_encoding'], 
			'indent' => '  ', 
			'defaultTagName' => $schemaDetails['schema_default_tag'],
			'rootName' => $schemaDetails['schema_root_tag'], 
      "attributesArray" => "_attributes",
			'rootAttributes' => array (
			'xmlns' => $schemaDetails['schema_namespace'],
				'lang' => $schemaDetails['schema_lang'],
				'xml:lang' => $schemaDetails['schema_lang']
				), 
    	); 
    
		$serializer = &new XML_serializer($serializer_options); 
		$status = $serializer->serialize($resource); 

		if (PEAR::isError($status)) { 
			$this->_error($status->getMessage());
		} 
  
		header('Content-type: text/plain'); 
		die( $serializer->getSerializedData());		
	} */
	
	/* public function importData($get){
	    $class_id=$get["class_id"];
	    $itemClass = $this->manager->getItemClass($get["class_id"]);
	    return(array("itemClass"=>$itemClass));
	} */

	/* public function importDataAction($get,$post){
	    
	    $class_id=$post["class_id"];
	    $indicator=$post["indicator"];
	    $file_name=$_FILES['file']['name'];
	    $file=$_FILES['file']['tmp_name'];
	    $fcontents = file($file); 
	    $properties_csv=explode(",",$fcontents[0]);
	    move_uploaded_file($_FILES['file']['tmp_name'], 'System/Temporary/'.$file_name);
	    $itemClass = $this->manager->getItemClass($class_id);
	    $properties = $this->manager->getItemClassProperties($class_id);

		foreach($properties as $p){

			if($p['itemproperty_setting'] == 1 || $p['itemproperty_datatype'] == 'NODE'){
			    
			}else{
				$type_id=$p['itemproperty_datatype'];
				$property_type_name=$this->manager->getItemClassPropertyTypeName($type_id);
				$p['itemproperty_datatype_name']=$property_type_name;
				$formValues[] = $p;			
			}
		}
// print_r($fcontents[0]);
 	    return(array("properties_csv"=>$properties_csv,"properties"=>$formValues,"itemClass"=>$itemClass,"check"=>$indicator,"file"=>$file_name));
	} */
	
	/* public function insertImportData($get,$post){
	    
	    $checkbox_true = array("TRUE","true","ON","On","on","1");
    	$class_id=$this->getRequestParameter('class_id');
    	$check=$this->getRequestParameter('check_on_off');
    	$file_name=$this->getRequestParameter('file_name');
    	$item_idex=$this->getRequestParameter('item_name');//print_r($post);
    	$formValues=null;
    	$fcontents = file('System/Temporary/'.$file_name);
    	$p=sizeof($fcontents);
    	
    	if($check){
    	    $start=1;
    	}else{
    	    $start=0;
    	}
    	
		for($k=$start; $k<$p; $k++){
			$line = trim($fcontents[$k]);    
			$line=str_replace("'","",$line);
			$line=str_replace('"','',$line);
			$datas=explode(',',$line);

			$item_name = $datas[$item_idex];	
			$item_slung=$this->_string->toSlug($item_name);	

			$item_id = $this->manager->setItemname($this->_string->random(32),$item_slung,$class_id,$item_name,'TRUE');

			$properties = $this->manager->getItemClassProperties($class_id);
			foreach($properties as $prop){
				if($prop['itemproperty_setting'] == 1 || $prop['itemproperty_datatype'] == 'NODE' ){
				}			
				else{							
				$property_id= $prop['itemproperty_id'];
				$varname=$prop['itemproperty_varname'];
				$csv_index=$post[$varname];
				$value=$datas[$csv_index];$value=addslashes($value);
				if($csv_index=="blank"){$value='';}
				if($prop['itemproperty_datatype'] == 3){
				if(in_array($value,$checkbox_true)){$value='TRUE';}else{$value='FALSE';}	
				}
				if($prop['itemproperty_datatype'] == 6){
				$timestamp=strtotime($value);$value=date('Y-m-d', $timestamp);	
				}
//print_r($value);echo $varname.'<br>';
 				$this->manager->setItemPropertyValues($item_id,$property_id,$value);
				}
			}
		}
		
	    @unlink('System/Temporary/'.$file_name);
	} */
	
	public function duplicateItem($get){
		
		$id = mysql_real_escape_string($this->getRequestParameter('item_id'));
		$class_id = mysql_real_escape_string($this->getRequestParameter('class_id'));
		$item_details= $this->manager->getItemValues($id);
		// get properties and their values
		$itemspropertyvalues = $this->manager->getItemPropertyValues($id,$class_id);

		$name=mysql_real_escape_string($item_details[0]['item_name']);	
		$item_public = mysql_real_escape_string($item_details[0]['item_public']);	
		
		$item_name=$this->manager->getUniqueItemName($name);	
		$item_slung=$this->_string->toSlug($item_name);		
		$item_id = $this->manager->setItemname($this->_string->random(32),$item_slung,$class_id,$item_name,$item_public);

		foreach($itemspropertyvalues as $p){
		    $property_id=mysql_real_escape_string($p['itemproperty_id']);	
		    $content =mysql_real_escape_string($p['itempropertyvalue_content']);	
		    $this->manager->setItemPropertyValues($item_id,$property_id,$content);
		}

	}
	
	/* public function exportData($get){
	    $class_id=$get["class_id"];
    	$schema_id=$get["schema_id"];
    	$itemClass = $this->manager->getItemClass($class_id);
    	$definition = $this->manager->getItemClassProperties($class_id);
    	if($schema_id){
    	$schemsDefinition = $this->manager->getSchemaDefinitions($schema_id);
    	}
    	return(array("itemClass"=>$itemClass,"itemClassProperties"=>$definition,"schemas"=>$this->manager->getSchemas(),"schema_id"=>$schema_id,"schemsDefinition"=>$schemsDefinition));
	}
	
	function exportDataXml($get,$post){
	    
	    $class_id=$post["class_id"];
	    $schema_id=$post["schema"];
	    $properties = $this->manager->getItemClassProperties($class_id);
	    
	    foreach($properties as $prop){
		    $property_id=$prop['itemproperty_id'];
		    $vocabulary_id=$post[$property_id];
		    $this->manager->setDataExport($schema_id,$class_id,$property_id,$vocabulary_id);
	    }
	    
	    $channel=null;  $resource = null;
	    $schemaDetails = $this->SchemasManager->getSchema($schema_id);
	    $getItems = $this->manager->getItemsInClass($class_id);
	
		for($i=0; $i<count($getItems); $i++){	
		    $item_id=$getItems[$i]['item']["item_id"];
		    $propertyValues = $this->manager->getItemPropertyValues($item_id,$class_id);
            
		    if(is_array($propertyValues)){
			    foreach($propertyValues as $key=>$value){
				    $itemproperty_id=$value['itemproperty_id'];
				    $shemavocabulary_name=$this->manager->getItemSchemaVocabularyName($itemproperty_id,$class_id,$schema_id);
				    if($shemavocabulary_name){
					    $resource[$shemavocabulary_name] = ltrim($value['itempropertyvalue_content']);
				    }				
			    }
			    
       			$channel[] = $resource;
		    }	
		}
        
        $serializer_options = array ( 
			'addDecl' => TRUE, 
			'encoding' => $schemaDetails['schema_encoding'], 
			'indent' => '  ', 
			'defaultTagName' => $schemaDetails['schema_default_tag'],
			'rootName' => $schemaDetails['schema_root_tag'], 
      			'attributesArray' => '_attributes',
			'rootAttributes' => array (
			    'xmlns' => $schemaDetails['schema_namespace'],
				'lang' => $schemaDetails['schema_lang'],
				'xml:lang' => $schemaDetails['schema_lang']
			)
		); 
    
		$serializer = &new XML_serializer($serializer_options); 
		$status = $serializer->serialize($channel);
		
		if (PEAR::isError($status)) { 
			$this->_error($status->getMessage());
		}
		
		header('Content-type: text/xml'); 
		die( $serializer->getSerializedData());	
	} */
	
	public function addSet($get){
		$this->redirect("/sets/addSet?class_id=".$this->getRequestParameter('class_id'));
	}
	
}
