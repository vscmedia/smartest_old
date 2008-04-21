<?php

// include_once 'System/Helpers/ItemsHelper.class.php';
include_once "Managers/SchemasManager.class.php";

class Sets extends SmartestSystemApplication{

	var $itemsManager;
	
	function __moduleConstruct(){
		// $this->itemsManager = new ItemsHelper();
		$this->SchemasManager = new SchemasManager();	
	}
	
	function startPage($get){
		// $sets = $this->manager->getSets($this->getSite()->getId());   
		
		$du = new SmartestDataUtility;
		$sets = $du->getDataSetsAsArrays(false, $this->getSite()->getId());
		$this->setTitle("Data Sets");
		$this->send($sets, 'sets');
		
		// return array("sets"=> $sets);
	}
	
	function deleteSet($get){
		$set_id = mysql_real_escape_string($get['set_id']);
		return $this->manager->deleteSet($set_id);		
	}
	
	function addSet($get){		

		$models = $this->itemsManager->getItemClasses($models); 		
		$set_name = $get['set_name'];
		$properties = null;		
		
		$this->setTitle('Create a new item set');
		
		if(is_numeric($get['model_id'])){
			$properties = $this->itemsManager->getItemClassProperties($get['model_id']); 		
		}
		
		return array("type"=>$get['type'] ,"model_id"=>$get['model_id'] ,"set_name"=>$set_name ,"models"=> $models, "properties"=>$properties);		
	}
	
	function insertSet($get, $post){
	    
	    $new_set_name = SmartestStringHelper::toVarName($post['set_name']);
	    
	    $set = new SmartestCmsItemSet;
	    
	    if(strlen($new_set_name) && !$set->hydrateBy('name', $new_set_name)){
	        
	        $set->setName($new_set_name);
	        $set->setLabel($post['set_name']);
	        $set->setType($post['set_type']);
	        $set->setItemclassId($post['set_model_id']);
	        $set->setSiteId($this->getSite()->getId());
	        $shared = @$post['set_shared'] ? 1 : 0;
	        $set->setShared($shared);
	        $set->save();
	        
	        if($set->getType() == 'DYNAMIC'){
	            $this->addUserMessageToNextRequest("Your set has been successfully created. Now you probably want to give it some rules.");
            }else{
                $this->addUserMessageToNextRequest("Your set has been successfully created. Now you probably want to decide what goes in there.");
            }
            
	        $this->redirect('/sets/editSet?set_id='.$set->getId());
	        
	    }else{
	        $this->addUserMessageToNextRequest("Error: Your set could not be created.");
	        $this->redirect('/smartest/sets');
	    }
	    
	    
	}

	function editSet($get, $post){  
	
		$set_id = $get['set_id'];
	    
	    $set = new SmartestCmsItemSet;
	    
	    if($set->hydrate($set_id)){
	        
	        if($set->getType() == "DYNAMIC"){
	            
	            $du = new SmartestDataUtility;
	            $sites = $du->getSitesAsArrays();
	            $conditions = $set->getConditionsAsArrays();
	            $this->send($sites, 'sites');
	            $this->send($conditions, 'conditions');
	            $this->send($set->__toArray(), 'set');
	            $this->send(true, 'show_form');
	            $this->send($set->getModel()->getPropertiesAsArrays(), 'properties');
	            $this->setTitle('Edit Dynamic Set');
	            
	            $formTemplateInclude = "editSet.dynamic.tpl";
	        
	        }else{
	            
	            // fetch all item ids
	            $all_items = $set->getModel()->getSimpleItemsAsArrays($this->getSite()->getId());
	            
	            // fetch set member item ids (and create objects for form)
	            $set_member_ids = $set->getMemberIds();
	            
	            // do the math
	            $set_member_arrays = array();
	            
	            foreach($all_items as $key=>$item){
	                
	                // if the item is in the set
	                if(in_array($item['id'], $set_member_ids)){
	                    // copy the item to the set members array
	                    $set_member_arrays[] = $item;
	                    // unset it in the original list
	                    unset($all_items[$key]);
	                }
	                
	            }
	            
	            $this->send($set->__toArray(), 'set');
	            $this->send($set_member_arrays, 'members');
	            $this->send($all_items, 'non_members');
	            $this->setTitle('Edit Static Set');
	            
	            $formTemplateInclude = "editSet.static.tpl";
	            
	        }
	        
	        $this->send($set->getModel()->__toArray(), 'model');
	        $this->send($formTemplateInclude, 'formTemplateInclude');
	        
	    }else{
	        $this->send(false, 'show_form');
	        $this->addUserMessage('The Set ID was not recognised');
	    }
	    
	    $this->setFormReturnUri();
	    
	}
	
	public function updateDynamicSet($get, $post){
	    
	    if(isset($post['set_id'])){
	        
	        $set_id = $post['set_id'];
	        
	        $set = new SmartestCmsItemSet;
	        
	        if($set->hydrate($set_id)){
	            
	            $set->setLabel($post['set_name']);
	            $set->setName(SmartestStringHelper::toVarName($post['set_name']));
	            $set->setSortField($post['set_sort_field']);
	            $set->setDataSourceSiteId($post['set_data_source_site_id']);
	            
	            print_r($set);
	            
	            $direction = ($post['set_sort_direction'] == 'DESC') ? 'DESC' : 'ASC';
	            
	            $set->setSortDirection($direction);
	            $set->save();
	        }
	    }
	    
	    if(is_array($post['conditions'])){
	        
	        foreach($post['conditions'] as $c_id => $c_post_data){
	            
	            $c = new SmartestDynamicDataSetCondition;
	            
	            if($c->hydrate($c_id)){
	                $c->setItempropertyId($c_post_data['property_id']);
	                $c->setOperator($c_post_data['operator']);
	                $c->setValue($c_post_data['value']);
	                $c->save();
	            }
	        }
	    }
	    
	    if(@$post['add_new_condition']){
	        
	        $c = new SmartestDynamicDataSetCondition;
	        $c->setSetId($set_id);
	        $c->setItempropertyId($post['new_condition_property_id']);
	        $c->setOperator($post['new_condition_operator']);
	        $c->setValue($post['new_condition_value']);
	        $c->save();
	        
	    }
	    
	    $this->addUserMessageToNextRequest("Your set has been updated.");
	    $this->formForward();
	    
	}
	
	function removeConditionFromSet($get){
	    
	    $c = new SmartestDynamicDataSetCondition;
	    
	    if($c->hydrate($get['condition_id'])){
	        $c->delete();
	    }else{
	        
	    }
	    
	    // print_r($c);
	    
	    $this->formForward();
	    
	}
	
	function transferItem($get, $post){
		
		$set_id = $post['set_id'];
		$set = new SmartestCmsItemSet;
		
		if($set->hydrate($set_id) && $set->getType() == "STATIC"){
		
		    if($post['transferAction'] == 'add'){
			    // $this->manager->addItemToStaticSet($post['available_items'], $post['set_id']);
			    $item_ids = (isset($post['available_items']) && is_array($post['available_items'])) ? $post['available_items'] : array();
			    $set->addItems($item_ids);
		    }else{
			    // $this->manager->removeItemFromStaticSet($post['used_items'], $post['set_id']);
			    $item_ids = (isset($post['available_items']) && is_array($post['available_items'])) ? $post['available_items'] : array();
			    $set->removeItems($item_ids);
		    }
		
	    }
		
		$this->formForward();
	}
	
	function previewSet($get){     
	    
	    $set_id = $get['set_id'];
	    $set = new SmartestCmsItemSet;
	    $set->hydrate($set_id);
	    
	    $items = $set->getMembersAsArrays();
	    
	    $this->send($items, 'items');
	    $this->send(count($items), 'count');
	    $this->send($set->__toArray(), 'set');
	    
	}
	
	function copySet($get){
		
		$id=$get['set_id'];	
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

	function removeRule($get){
		
	}
	
	
	//database queries
	function insertRuleSet($get, $post){
	}

	function deleteRule($get, $post){
		$setrule_id=$post['setrule_id'];
		$this->manager->deleteSetRule($setrule_id);
	}

	function insertRule($get, $post){
		$set_id=$post['set_id'];
		$itemproperty_id=$post['itemproperty_id'];
		$condition=$post['condition'];
		$value=$post['value'];
		$this->manager->addSetRule($set_id, "", $itemproperty_id, strtoupper($condition),$value);	 
	}
	
	function deleteRuleSet($get, $post){
		
	}
	
// 	function updateSet($get, $post){
// 		$set_id=$post['set_id'];	
//     		$items=$post['cmbRole'];
//     		$this->manager->updateSet($set_id,$items);   
// 	}

	function getSet($get="", $post=""){
		
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
		
		*/
	}

	function chooseSchemaForExport($get){
		
		$set_id=$get["set_id"];			
		$set = $this->manager->getSet($set_id);		
		$schemas=$this->itemsManager->getSchemas();
		return(array("set"=>$set,"schemas"=>$schemas));
		
	}

	function exportDataOptions($get){
		
		$set_id=$get["set_id"];
		$schema_id=$get["schema_id"];
		$schema_name=$this->SchemasManager->getSchemaName($schema_id);
		$set = $this->manager->getSet($set_id);	
		$pairing=$this->manager->getparing($set_id,$schema_id);
		$count=count($pairing);
		
		if($count==1){
			$name=$pairing[0]['dataexport_name'];
		}
		
		return(array("set"=>$set,"schema_id"=>$schema_id,"count"=>$count,"schema_name"=>$schema_name,"name"=>$name));
	}

	function exportData($get){
		
		$set_id=$get["set_id"];	
		$schema_id=$get["schema_id"];	

		$msg=$get["msg"];	
		$set = $this->manager->getSet($set_id);
		$model_id = $set['itemclass_id'];
		$definition = $this->itemsManager->getItemClassProperties($model_id);
		
		if($schema_id){
			$repeatingDefinition=$this->manager->getRepeatingDefinition($schema_id);
			$array=$this->manager->getschemasettings($schema_id);
		}

		return(array("set"=>$set,"Properties"=>$definition,"schemas"=>$this->itemsManager->getSchemas(),"schema_id"=>$schema_id,"schemsDefinition"=>$repeatingDefinition,"name"=>$name,"msg"=>$msg,"Settings"=>$array));
	}	

  	function exportDataInsert($get,$post){
		
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

	function editExportData($get){
		
		$set_id=$get["set_id"];	
		$schema_id=$get["schema_id"];
		$pair_id=$get["pair_id"];
	// 	$name=$get["name"];
		$msg=$get["msg"];	
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

	function updateExportData($get,$post){
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

	function choosePairingForExport($get){
		$set_id=$get["set_id"];	$schema_id=$get["schema_id"];	
		$set = $this->manager->getSet($set_id);	
		$pairing=$this->manager->getparing($set_id,$schema_id);
		$schema_varname=$this->SchemasManager->getSchemaVarName($schema_id);
		return(array("set"=>$set,"pairing"=>$pairing,"schema"=>$schema_varname,"schema_id"=>$schema_id));
	}
	
	function exportSuccess($get){
		$set_id=$get["set_id"];$set = $this->manager->getSet($set_id);
		$schema_varname=$get["schema"];
		$varname=$get["dataexport"];
		return(array("set"=>$set,"pairing"=>$pairing,"schema_varname"=>$schema_varname,"dataexport_varname"=>$varname));
	}
	
	function getDataExports($get){
		$pairing=$this->manager->getDataExports();
		$count=count($pairing);
		return(array("pairing"=>$pairing,"count"=>$count));
	}
	
	function editDataExportFeed($get){
		$export_id=$get["export_id"];
		$name=$get["pairing_name"];
		$sets=$this->manager->chooseSetForDataExport($export_id);
		$pairing=$this->manager->choosePairingForDataExport($export_id);
		//$pairing=$this->manager->getDataExports();
		return(array("export_id"=>$export_id,"pairing_name"=>$name,"sets"=>$sets,"pairing"=>$pairing));
	}
	
	function updateDataExportFeed($get,$post){
		$export_id=$post["export_id"];
		$set=$post["set"];$pair=$post["pair"];
		$this->manager->updateDataExportFeed($export_id,$set,$pair);
	}
}

?>
