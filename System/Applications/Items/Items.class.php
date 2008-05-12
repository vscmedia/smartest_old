<?php

require_once SM_ROOT_DIR.'Managers/SchemasManager.class.php';
// require_once SM_ROOT_DIR.'System/Applications/Assets/AssetsManager.class.php';

class Items extends SmartestSystemApplication{

	private $SchemasManager;
  
	public function __moduleConstruct(){
	    $this->database = SmartestPersistentObject::get('db:main'); /* usage of the $this->database variable should be phased out in main classes */
		$this->SchemasManager = new SchemasManager();
		// $this->AssetsManager = new AssetsManager();
	}
	
	public function startPage($get){	
		
	}
		
	public function getItemClasses(){
	    
		$this->setFormReturnUri();
		
		$du = new SmartestDataUtility;
		$models = $du->getModelsAsArrays();
		$this->send($models, 'models');
		
	}

	public function getItemClassProperties($get){
		
		$this->setFormReturnUri();
		
		$itemclassid = $get['class_id'];
		$model = new SmartestModel;
		$model->hydrate($itemclassid);
		
		$modelarray = $model->__toArray();
		
		$this->setTitle("Properties of model: ".$model->getName());
		
		$definition = $model->getPropertiesAsArrays();
		
		$this->send($modelarray, 'itemclass');
		$this->send($definition, 'definition');
		 
	}

	public function itemClassSettings($get){

		$itemclassid = $get['class_id'];
		$itemclass = $this->manager->getItemClass($itemclassid);		
		$itemClassProperties = $this->manager->getSettingItemsInClass($itemclassid); 
		return (array("settings"=>$itemClassProperties, "itemclass"=>$itemclass));

	}
	
	function insertItemClassSettings($get,$post){
		foreach($post as $key=>$val){
		    $this->manager->updateItemSettings($key,$val); 
		}
	}
	
	function getItemClassMembers($get="", $post=""){
  	    
  	    $this->setFormReturnUri();
  	    
  	    $model = new SmartestModel;
  	    
  	    $model_id = $get['class_id'];
  	    
  	    if($model->hydrate($model_id)){
  	        
  	        // echo $this->getSite()->getId();
  	        $items = $model->getSimpleItemsAsArrays($this->getSite()->getId());
  	        $this->send($items, 'items');
  	        $this->send(count($items), 'num_items');
  	        $this->send($model->__toArray(), 'model');
  	        
  	    }else{
  	        $this->addUserMessageToNextRequest('The model ID was not recognized.', SmartestUserMessage::ERROR);
  	        $this->redirect('/smartest/models');
  	    }
	
	}
    
    public function releaseUserHeldItems($get){
        
        $model = new SmartestModel;
        
        if($model->hydrate($get['class_id'])){
            $num_held_items = $this->getUser()->getNumHeldItems($model->getId(), $this->getSite()->getId());
	        $this->getUser()->releaseItems($model->getId(), $this->getSite()->getId());
	        $this->addUserMessageToNextRequest($num_held_items.' '.$model->getPluralName()." were released.", SmartestUserMessage::SUCCESS);
        }else{
            $this->addUserMessageToNextRequest("The model ID was not recognized.");
        }
        
        $this->redirect('/datamanager/getItemClassMembers?class_id='.$get['class_id']);
    }

	function getItemXml($get, $post){
		$items = null;			
		$info = null;
		$propertyValues = $this->manager->getItemPropertyValues($get["item_id"],$get["class_id"]);
		$itemClass = $this->manager->getItemClass($get["class_id"]);
		$content = null;        
		
		foreach($propertyValues as $key=>$value){
			$info[$key] = $value;
		}
		
		$items[0]['item'] = $getItems[$i];
		$items[0]['item']['itemclass_id'] = $get["class_id"];
		$items[0]['properties'] = $info;
    
    
		$vocabulary = $this->SchemasManager->getVocabularyList();    
		$resource = null;
		
		if(is_array($items)){			
			foreach($items as $i=>$item){
				foreach($item['properties'] as $j=>$value){
					if(in_array($value['itemproperty_name'], $vocabulary)){
						$resource[$i][$value['itemproperty_name']] = ltrim($value['itempropertyvalue_content']);
					}
					else if(in_array($value['itemproperty_varname'], $vocabulary)){
						$resource[$i][$value['itemproperty_varname']] = ltrim($value['itempropertyvalue_content']);
					}
				}
			}
		}
    	
		$itemBaseValues = $this->manager->getItemClassBaseValues($get["class_id"]);


		$schemaDetails = $this->SchemasManager->getSchema($itemClass['itemclass_schema_id']);
		
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
			// die($status->getMessage()); 
			$this->_error($status->getMessage());
		} 
  
		header('Content-type: text/xml'); 
		die( $serializer->getSerializedData());	
	}
	

	
	function getItemClassXml($get){
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

	}
	
	
	//// DELETE
	
	/*
	functions:
	removeProperty()
	deleteItem()
	deleteItemClass()
	*/
	
	function removeProperty($get, $post){
		$itemproperty_id = mysql_real_escape_string($post['itemproperty_id']);
    		return $this->manager->deleteItemClassProperty($itemproperty_id);
	}
	
	function deleteProperty($get){
	    // $itemproperty_id = mysql_real_escape_string($get['itemproperty_id']);
    	// return $this->manager->deleteItemClassProperty($itemproperty_id);
    	$property = new SmartestItemProperty;
    	
    	if($property->hydrate($get['itemproperty_id'])){
    	    
    	    $model = new SmartestModel;
    	    
    	    
    	    if($model->hydrate($property->getModelId())){
    	        // delete property - this should done before any cache or code files are regenerated
    	        $property->delete();
    	        // clear the cache and rebuild auto object model file
    	        SmartestCache::clear('model_properties_'.$model->getId(), true);
    	        SmartestObjectModelHelper::buildAutoClassFile($model->getId(), $model->getName());
            }else{
                // just delete the property
                $property->delete();
            }
	        
	        $this->addUserMessageToNextRequest("The property has been deleted.", SmartestUserMessage::SUCCESS);
	        $this->formForward();
	        
	    }
	}
	
	function deleteItem($get){
		// $item_id = mysql_real_escape_string($get['item_id']);
		if(is_numeric($get['item_id'])){
		    
		    $item_id = $get['item_id'];
		    
		    if($this->getUser()->hasToken('delete_items')){
	            
	            $item = SmartestCmsItem::retrieveByPk($item_id);
	            
	            if(is_object($item)){
	                $model_name = $item->getModel()->getName();
	                $item->delete();
	                $this->addUserMessageToNextRequest('The '.$model_name.' was moved to the trash.', SmartestUserMessage::SUCCESS);
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
	
	function deleteItemClass($get){
		$class_id = mysql_real_escape_string($get['class_id']);
		return $this->manager->deleteItemClass($class_id);
	}
	
	//// EDIT (pre-action interface/options) and UPDATE (the actual action)
	
	function editItemProperty($get, $post){  		
		
		$property_id = $get['itemproperty_id']; //print_r($property_id);
		
		$property = new SmartestItemProperty;
		
		if($property->hydrate($property_id)){
		    
		    $model_id = $property->getItemclassId();
		    $model = new SmartestModel;
		    $model->hydrate($model_id);
		    
		    $this->addUserMessage('Editing existing properties will change how the data referred to by that property is stored and accessed.', SmartestUserMessage::WARNING);
		    
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
		
    		/* $propertyTypes = $this->manager->getItemPropertyTypes();
    		$models = $this->manager->getItemClasses();
    		$dropdownMenu = $this->manager->getDropdownMenu();
		
    		if($get["name"]){
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
    		} */
		
	    }

		// return (array("details"=>$property[0], "itemclass"=>$itemClass, "Types"=>$propertyTypes,"models"=>$models,"dropdownMenu"=>$dropdownMenu,"dropdownValues"=>$dropdownValues,"name"=>$name,"type"=>$type,"sel_id"=>$sel_id,"model_id"=>$model_id,"sel_items"=>$items,"month"=>$date[0],"day"=>$date[1]));
	}
	
	public function openItem($get){
	    
	    $item = new SmartestItem;
	    $item->hydrate($get['item_id']);
	    
	    if($item->getIsHeld() && $item->getHeldBy() != $this->getUser()->getId()){
	        // item is being edited by somebody else
	        $u = new SmartestUser;
	        $u->hydrate($item->getHeldBy());
	        $this->addUserMessageToNextRequest('The item is already being edited by '.$u->getUsername().'.', SmartestUserMessage::INFO);
	        $this->redirect('/'.SM_CONTROLLER_MODULE.'/getItemClassMembers?class_id='.$item->getItemclassId());
	    }else{
	        if($this->getUser()->hasToken('modify_items')){
                $item->setIsHeld(1);
                $item->setHeldBy($this->getUser()->getId());
                $item->save();
                $this->redirect('/'.SM_CONTROLLER_MODULE.'/editItem?item_id='.$item->getId());
            }else{
                $this->addUserMessageToNextRequest('You don\'t have permssion to edit items', SmartestUserMessage::ACCESS_DENIED);
                $this->redirect('/'.SM_CONTROLLER_MODULE.'/getItemClassMembers?class_id='.$item->getItemclassId());
            }
	    }
	}
	
	public function releaseItem($get){
	    
	    $item = new SmartestItem;
	    $item->hydrate($get['item_id']);
	    
	    if($item->getIsHeld()){
	        // item is being edited by somebody else
	        if($item->getHeldBy() == $this->getUser()->getId()){
	            $item->setIsHeld(0);
                $item->setHeldBy(0);
                $item->save();
                $this->addUserMessageToNextRequest('The item has been released.', SmartestUserMessage::SUCCESS);
                if(isset($get['from']) && $get['from']=='todoList'){
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
	        $this->addUserMessageToNextRequest('The item was not locked.', SmartestUserMessage::INFO);
	        $this->formForward();
	    }
	    
	    // $this->redirect('/'.SM_CONTROLLER_MODULE.'/getItemClassMembers?class_id='.$item->getItemclassId());
	    
	    
	}
	
	public function itemTags($get){
	    
	    $this->setFormReturnUri();
	    
	    $item_id = $get['item_id'];
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        $model = new SmartestModel;
	        $model->hydrate($item->getItemclassId());
	        $this->send($model->__toArray(), 'model');
	        
	        $this->setTitle($item->getName().' | Tags');
	        
	        // $page_tag_ids = $page->getTagsAsIds();
	        $du  = new SmartestDataUtility;
	        $tags = $du->getTags();
	        // print_r($tags);
	        
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
	        
	        // print_r($t);
	        
	        $this->send($item_tags, 'tags');
	        $this->send($item->__toArray(), 'item');
	        
	    }else{
	        $this->addUserMessage('The item ID has not been recognized.', SmartestUserMessage::ERROR);
	    }
	    
	}
	
	public function updateItemTags($get, $post){
	    
	    $item = new SmartestItem;
	    
	    if($item->hydrate($post['item_id'])){
	    
	        $du  = new SmartestDataUtility;
            $tags = $du->getTags();
        
            if(is_array($post['tags'])){
                
                $item_new_tag_ids = array_keys($post['tags']);
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
	    
	    $item_id = (int) $get['item_id'];
	    $item = SmartestCmsItem::retrieveByPk($item_id);
	    
	    if($item->isHydrated()){
	        
	        $this->send($item->__toArray(), 'item');
	        
	        $this->setTitle($item->getName()." | Related Content");
	        $model = $item->getModel();
	        
	        $this->send($model->__toArray(), 'model');
	        
	        $related_items_this_model = $item->getItem()->getRelatedItemsAsArrays(true);
	        $related_pages = $item->getItem()->getRelatedPagesAsArrays(true);
	        
	        $this->send($related_items_this_model, 'related_items_this_model');
	        $this->send($related_pages, 'related_pages');
	        
	        $du = new SmartestDataUtility;
	        $models = $du->getModelsAsArrays();
        
	        foreach($models as $key=>$m){
	            if($m['id'] == $model->getId()){
	                unset($models[$key]);
	            }else{
	                $models[$key]['related_items'] = $item->getItem()->getRelatedForeignItemsAsArrays(true, $m['id']);
                }
	        }
	        
	        $this->send($models, 'models');
	        
	    }
	    
	}
	
	public function editRelatedContent($get){
	    
	    $item = new SmartestItem;
	    $item_id = $get['item_id'];
	    
	    if($item->hydrate($item_id)){
	        
	        if(isset($get['model_id'])){
	            
	            $model_id = (int) $get['model_id'];
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
	            $this->send($item->__toArray(), 'item');
	            $this->send($model->__toArray(), 'model');
	            
	            if($model->getId() == $item->getModelId()){
	                $related_ids = $item->getRelatedItemIds(true, $model->getId());
                }else{
                    $related_ids = $item->getRelatedForeignItemIds(true, $model->getId());
                }
                
	            $all_items  = $model->getSimpleItemsAsArrays($this->getSite()->getId());
	            $this->send($all_items, 'items');
	            $this->send($related_ids, 'related_ids');
	            
            }else{
                
                $this->setTitle($item->getName()." | Related pages");
    	        $this->send($item->__toArray(), 'item');
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
	    $item_id = $post['item_id'];
	    
	    if($item->hydrate($item_id)){
	        
	        $model = new SmartestModel;
    	    $model_id = $post['model_id'];
    	    
    	    if($model->hydrate($model_id)){
	        
	            if(isset($post['items']) && is_array($post['items'])){
	            
    	            $new_related_ids = array_keys($post['items']);
	            
    	            if(count($new_related_ids)){
	                    
	                    $items = $model->getSimpleItems();
	                    
	                    if($model->getId() == $item->getModelId()){
    	                    $old_related_ids = $item->getRelatedItemIds(true);
    	                    $same_model = true;
	                    }else{
	                        $old_related_ids = $item->getRelatedForeignItemIds(true, $model->getId());
	                        $same_model = false;
	                    }
	                    
	                    // print_r($old_related_ids);
	                    // print_r($new_related_ids);
	                    
            	        foreach($items as $i){
    	            
            	            if(in_array($i->getId(), $new_related_ids) && !in_array($i->getId(), $old_related_ids)){
            	                // add connection
            	                // echo 'Add item '.$i->getName().'<br />';
            	                if($same_model){
            	                    $item->addRelatedItem($i->getId());
            	                }else{
            	                    $item->addRelatedForeignItem($i->getId());
            	                }
            	            }
    	            
            	            if(in_array($i->getId(), $old_related_ids) && !in_array($i->getId(), $new_related_ids)){
            	                // remove connection
            	                // echo 'Remove item '.$i->getName().'<br />';
            	                if($same_model){
            	                    $item->removeRelatedItem($i->getId());
            	                }else{
            	                    $item->removeRelatedForeignItem($i->getId());
            	                }
            	            }
            	        }
    	        
    	            }else{
	                
    	                $item->removeAllRelatedItems($model->getId());
	                
    	            }
	            
    	        
                }else{
                    $this->addUserMessageToNextRequest('Incorrect input format: Data should be array of items', SmartestUserMessage::ERROR);
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
	    $item_id = $post['item_id'];
	    
	    if($item->hydrate($item_id)){
	        
	        if(isset($post['pages']) && is_array($post['pages'])){
	            
	            $new_related_ids = array_keys($post['pages']);
	            
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
    	        
                }else{
	                
                    $item->removeAllRelatedPages();
	                
    	        }
    	        
            }else{
                $this->addUserMessageToNextRequest('Incorrect input format: Data should be array of pages', SmartestUserMessage::ERROR);
            }
        }else{
            $this->addUserMessageToNextRequest('The item ID was not recognized', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	public function editModel($get){
	    
	    $model_id = $get['class_id'];
	    $model = new SmartestModel();
	    
	    if($model->hydrate($model_id)){
	        
	        // print_r($model->getDefaultMetaPageId($this->getSite()->getId()));
	        
	        $this->send($model->__toArray(), 'model');
	        $this->send($model->getMetaPagesAsArrays(), 'metapages');
	        // $props = $model->getAvailableDescriptionProperties();
	        $this->send($model->getAvailableDescriptionPropertiesAsArrays(), 'description_properties');
	    }else{
	        $this->addUserMessageToNextRequest("The model ID was not recognized.", SmartestUserMessage::ERROR);
	        $this->redirect('/smartest/models');
	    }
	    
	}
	
	public function updateModel($get, $post){
	    
	    $model_id = $post['class_id'];
	    
	    $model = new SmartestModel;
	    
	    if($model->hydrate($model_id)){
	        
	        if(isset($post['itemclass_default_metapage_id']) && is_numeric($post['itemclass_default_metapage_id'])){
	            $model->setDefaultMetaPageId($this->getSite()->getId(), (int) $post['itemclass_default_metapage_id']);
            }
            
            if(isset($post['itemclass_plural_name']) && strlen($post['itemclass_plural_name'])){
                $model->setPluralName($post['itemclass_plural_name']);
            }else{
                $this->addUserMessageToNextRequest("The plural name you entered was invalid.");
            }
            
            if(isset($post['itemclass_default_description_property_id']) && is_numeric($post['itemclass_default_description_property_id'])){
                $model->setDefaultDescriptionPropertyId($post['itemclass_default_description_property_id']);
            }else{
                $this->addUserMessageToNextRequest("The plural name you entered was invalid.", SmartestUserMessage::WARNING);
            }
            
            $model->save();
            
	    }else{
	        $this->addUserMessageToNextRequest("The model ID was not recognized.", SmartestUserMessage::ERROR);
	    }
	    
	    $this->formForward();
	}
	
	public function editItem($get, $post){
		
		if(!isset($get['from'])){
		    $this->setFormReturnUri();
		}
		
		$item_id = $get['item_id'];
		
		$item = SmartestCmsItem::retrieveByPk($item_id);
		
		if(is_object($item)){
		    $item_array = $item->__toArray(true, true, true); // draft mode, use numeric keys, and $get_all_fk_property_options in that order
		    $this->send($item->getModel()->getMetaPagesAsArrays(), 'metapages');
		    $this->setTitle('Edit '.$item->getModel()->getName().' | '.$item->getName());
		    $this->send($item_array, 'item');
		    
		    $page = new SmartestPage;
		    
		    if($page->hydrate($item->getModel()->getDefaultMetapageId($this->getSite()->getId()))){
		        $this->send($page->getWebid(), 'default_metapage_id');
		    }
		    
	    }
		
	}
	
	public function updateItem($get, $post){  
		
		if($this->getUser()->hasToken('modify_items')){
		
	    	$item_id = $post['item_id'];
		
    		$item = SmartestCmsItem::retrieveByPk($item_id);
		
    		if(is_object($item)){
		
    		    // update name
    		    if (strlen($post['item_name'])){
    		        $item->getItem()->setName($post['item_name']);
		        }
		        
		        $item->getItem()->setSearchField($post['item_search_field']);
		        
        		// update is public
        		// $public = ($post['item_is_public'] == 'TRUE') ? 'TRUE' : 'FALSE';
        		
        		// $item->getItem()->setPublic($public);
        		$item->getItem()->setMetapageId($post['item_metapage_id']);
        		$item->getItem()->setModified(time());
        		
        		$item->getItem()->save();
		
        		// loop through properties
		
    		    $new_values = $post['item'];
    		    $properties = $item->getProperties(true);
		        
		        // print_r($new_values);
		        
    		    foreach($new_values as $property_id=>$new_value){
    		        $item->setPropertyValueByNumericKey($property_id, $new_value);
    		    }
		    
		        $this->addUserMessageToNextRequest('The item was updated successfully.', SmartestUserMessage::SUCCESS);
		
	        }else{
	        
	            $this->addUserMessageToNextRequest('The item ID was not recognised.', SmartestUserMessage::SUCCESS);
	        
	        }
	    
        }else{
            
            $this->addUserMessageToNextRequest('You don\'t have permssion to edit items', SmartestUserMessage::ACCESS_DENIED);
            
        }
	    
	    $this->formForward();
	
	}
	
	public function approveItem($get){
	    
	    $item_id = $get['item_id'];
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        
	        if($this->getUser()->hasToken('approve_item_changes')){
	            if((bool) $item->getChangesApproved()){
	                // user has permission. allow item to be approved.
	                $this->addUserMessageToNextRequest('The item has been approved.', SmartestUserMessage::SUCCESS);
	                $item->setChangesApproved(1);
	                $item->save();
                }else{
                    $this->addUserMessageToNextRequest('The item had not been changed.', SmartestUserMessage::INFO);
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
	
	public function publishItem($get){
	    
	    $item_id = $get['item_id'];
        $item = SmartestCmsItem::retrieveByPk($item_id);
        
        // print_r($item);
	    
	    if(is_object($item)){
	    
	        if(($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items')){
	            
	            // it is ok to publish the item
	            $item->publish();
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
        
        $this->formForward();
	    
	}
	
	function unpublishItem($get){
	    
	    $item_id = $get['item_id'];
	    $item = new SmartestItem;
	    
	    if($item->hydrate($item_id)){
	        if(($this->getUser()->hasToken('publish_approved_items') && $item->isApproved()) || $this->getUser()->hasToken('publish_all_items')){
	            $item->setPublic('FALSE');
	            $item->save();
	            $this->addUserMessageToNextRequest('The item is no longer visible on the site.', SmartestUserMessage::SUCCESS);
	        }else{
	            $this->addUserMessageToNextRequest('You don\'t have permission to unpublish items.', SmartestUserMessage::ACCESS_DENIED);
	        }
        }else{
            $this->addUserMessageToNextRequest('The Item ID was not recognised.', SmartestUserMessage::ERROR);
        }
        
        $this->formForward();
	    
	}
	
	function editProperties($get){  
		
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
	}
	
    function updateItemClassProperty($get, $post){
		
		$itemproperty_id = $post['itemproperty_id'];
		$property = new SmartestItemProperty;
		$property->hydrate($itemproperty_id);
		
		$old_name = $property->getName();
		
		$property->setName($post['itemproperty_name']);
		$property->setVarname(SmartestStringHelper::toVarName($property->getName()));
		$property->setDatatype($post['itemproperty_datatype']);
		$property->setRequired($post['itemproperty_required'] ? 'TRUE' : 'FALSE');
		
		if($property->getId()){
		    
		    $property->save();
		    
		    if($old_name == $property->getName()){
    		    $this->addUserMessageToNextRequest('The property was updated.', SmartestUserMessage::SUCCESS);
    		}else{
    		    $this->addUserMessageToNextRequest('The property was updated and permanently renamed to "'.$property->getName().'".', SmartestUserMessage::SUCCESS);
    		}
	    }else{
	        $this->addUserMessageToNextRequest('There was an error updating the property.', SmartestUserMessage::ERROR);
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
	
	function addItem($get, $post){
        
        $this->send(date("Y"), 'default_year');
        $this->send(date("m"), 'default_month');
        $this->send(date("d"), 'default_day');
        
        if($this->getUser()->hasToken('add_items')){
        
            if(isset($post['save_item'])){
            
                // values for new item have been submitted, so process them
                $model = new SmartestModel;
        		$model->hydrate($post['class_id']);

        		$item = new SmartestCmsItem;
        		$item->setModelId($model->getId());
        		
        		// provided it has a name, save the item - incomplete or not. incomplete items can be created & saved, but not published.
                if($post['item']['_name']){
                    $new_values = $post['item'];
                    $item->hydrateNewFromRequest($new_values);
                    $item->setSiteId($this->getSite()->getId());
                    $success = $item->save();
                    // $this->redirect("/datamanager/editItem?item_id=".$item->getId());
                    $this->addUserMessageToNextRequest("Your new ".$model->getName()." has been created.", SmartestUserMessage::SUCCESS);
                    $this->redirect("/datamanager/getItemClassMembers?class_id=".$model->getId());
                }else{
                    $this->addUserMessageToNextRequest("You cannot create ".$model->getPluralName()." without entering a name.", SmartestUserMessage::WARNINR);
                    $this->redirect("/datamanager/getItemClassMembers?class_id=".$model->getId());
                }
        
            }else if(isset($get['class_id'])){
        
                $model_id = $get['class_id'];
                $model = new SmartestModel;
                
                if($model->hydrate($model_id)){
            
                    $model_array = $model->__toArrayLikeCmsItemArray();
                    
                    $this->send($model_array, 'item');
                
                }else{
                    
                    $this->addUserMessageToNextRequest('The model id was not recognised.', SmartestUserMessage::ERROR);
                    
                }
            
            }
        
        }
        
        /*if(!isset($get['from'])){
		    $this->setFormReturnUri();
		}
		
		$item_id = $get['item_id'];
		
		$item = SmartestCmsItem::retrieveByPk($item_id);
		
		$this->setTitle('Edit Item');
		
		$item_array = $item->__toArray(true, true);
		
		$this->send($item_array, 'item'); */
        
		/*$properties = $this->manager->getItemClassProperties($get["class_id"]);
		$user_id = $_SESSION['user']['user_id'];
		
		foreach($properties as $p){

			if($p['itemproperty_setting'] == 1 || $p['itemproperty_datatype'] == 'NODE'){
			}			
			else{
			if($p['itemproperty_dropdown_id']){
			$dropdn_id=$p['itemproperty_dropdown_id'];
			$dropdn=$this->manager->getDropdownMenuValues($dropdn_id);
			$p['dropdown']=$dropdn;
			}
			if($p['itemproperty_model_id']){
			$model_id=$p['itemproperty_model_id'];
			$modeldn=$this->manager->getItemsInClass($model_id);
			$p['modeldropdown']=$modeldn;
			}		
				$formValues[] = $p;			
			}
		}
		
		$itemBaseValues = $this->manager->getItemClassBaseValues($get["class_id"]);		
		$itemClass = $this->manager->getItemClass($get["class_id"]);
		
		$this->setTitle("Data Manager | ".$itemBaseValues["itemclass_plural_name"]." | Create a New ".$itemBaseValues["itemclass_name"]);
 
		return array("itemClass"=>$itemClass[0], "itemProperties"=>$itemBaseValues, "formProperties"=>$formValues, "user_id"=>$user_id);*/
	}

 /*   function insertSettings($get, $post){
  
		$itemclass_id = $post['itemclass_id'];
		$item_name = $post['itemName'];
		$item_slung=$this->_string->toSlug($item_name);
		$item_id = $this->manager->setItemname($this->_string->random(32),$item_slung,$itemclass_id,$item_name);
		
		foreach ($post['itemProperty'] as $itemproperty_varname=>$itempropertyvalue_content){
			
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
	
	function addItemClass(){
		
		// return array('schemas'=>$this->manager->getSchemas(), "user_id"=>$_SESSION['user']['user_id']);
	}
  
	function insertItemClass($get, $post){
		
		if(strlen($post['itemclass_name']) > 2 && SmartestDataUtility::isValidModelName($post['itemclass_name'])){
		    
		    $model = new SmartestModel;
		    $model->setName($post['itemclass_name']);
		    $model->setPluralName($post['itemclass_plural_name']);
		    $model->setVarname(SmartestStringHelper::toVarName($post['itemclass_plural_name']));
		    $model->setWebid(SmartestStringHelper::random(16));
		    $model->save();
		    $this->addUserMessageToNextRequest("The new model has been saved. Now add some properties.", SmartestUserMessage::SUCCESS);
		    SmartestCache::clear('model_class_names', true);
		    SmartestCache::clear('model_id_name_lookup', true);
		    $this->redirect("/".SM_CONTROLLER_MODULE."/addPropertyToClass?class_id=".$model->getId());
		    
	    }else{
	        
	        $this->addUserMessageToNextRequest("The model name \'".$post['itemclass_name']."\' is not valid.", SmartestUserMessage::WARNING);
	        $this->formForward();
	        
	    }
		
	}
  
	function insertItemClassProperty($get, $post){
		
		$new_property_name = $post['itemproperty_name'];
		
		$model_id = $post['class_id'];
		
		$model = new SmartestModel;
		$model->hydrate($model_id);
		
		$property = new SmartestItemProperty;
		
		$property->setName($post['itemproperty_name']);
		$property->setVarname(SmartestStringHelper::toVarName($property->getName()));
		$property->setDatatype($post['itemproperty_datatype']);
		$property->setRequired($post['itemproperty_required'] ? 'TRUE' : 'FALSE');
		$property->setItemclassId($model->getId());
		
		$property->save();
	    
	    SmartestCache::clear('model_properties_'.$model->getId(), true);
	    SmartestObjectModelHelper::buildAutoClassFile($model->getId(), $model->getName());
	    
	    $this->addUserMessageToNextRequest("Your new property has been added.", SmartestUserMessage::SUCCESS);
	    
	    if($post['continue'] == 'NEW_PROPERTY'){
	        $this->redirect('/datamanager/addPropertyToClass?class_id='.$model->getId());
	    }else{
	        $this->redirect('/datamanager/getItemClassProperties?class_id='.$model->getId());
	    }

	}
	
	function addPropertyToClass($get){
		
		$name=$get["name"];
		$sel_id=$get["sel_id"];
		$type=$get["type"];
		$model_id=$get["class_id"];
		
		$model = new SmartestModel;
		$model->hydrate($model_id);
		
		$this->setTitle('Add a Property to Model | '.$model->getPluralName());
		
		// $itemClasses = $this->manager->getItemClasses($get["class_id"]);
		$data_types = SmartestDataUtility::getDataTypes();
		
		$this->send($model->compile(), 'model');
		$this->send($data_types, 'data_types');
		
		// print_r($data_types);
		
	}
	
	function addNewItemClassAction($get, $post){
    
		if(strlen($post['item_class_name'])>0){
			
			$status = $this->manager->addNewItemClass($post['item_class_name']);        
			
			if($status){
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
	
	function getXmlTest($get){
		$this->schemasManager = new SchemasManager();
		$search_string = $get['search'];
		$items = $this->manager->getItemsInClass($get["class_id"]);
		$itemBaseValues = $this->manager->getItemClassBaseValues($get["class_id"]);    
		$itemClassMembers = $this->manager->countItemClassMembers($get["class_id"]); 
		$itemClassPropertyCount = $this->manager->countItemClassProperties($get["class_id"]); 		
		$item_id  = $get['item_id'];      
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
	}
	
	function importData($get){
	$class_id=$get["class_id"];
	$itemClass = $this->manager->getItemClass($get["class_id"]);
	return(array("itemClass"=>$itemClass));
	}

	function importDataAction($get,$post){
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
			}			
			else{
				$type_id=$p['itemproperty_datatype'];
				$property_type_name=$this->manager->getItemClassPropertyTypeName($type_id);
				$p['itemproperty_datatype_name']=$property_type_name;
				$formValues[] = $p;			
			}
		}
// print_r($fcontents[0]);
 	return(array("properties_csv"=>$properties_csv,"properties"=>$formValues,"itemClass"=>$itemClass,"check"=>$indicator,"file"=>$file_name));
	}
	function insertImportData($get,$post){
	$checkbox_true = array("TRUE","true","ON","On","on","1");
	$class_id=$post['class_id'];
	$check=$post['check_on_off'];
	$file_name=$post['file_name'];
	$item_idex=$post['item_name'];/*print_r($post);*/
	$formValues=null;
	$fcontents = file('System/Temporary/'.$file_name);
	$p=sizeof($fcontents);
	if($check){$start=1;}else{$start=0;}
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
	}
	function duplicateItem($get){
		$id = mysql_real_escape_string($get['item_id']);
		$class_id = mysql_real_escape_string($get['class_id']);
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
	function exportData($get){
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
	}
	function addSet($get){
		$this->redirect("/sets/addSet?model_id=".$get['class_id']);
	}
}