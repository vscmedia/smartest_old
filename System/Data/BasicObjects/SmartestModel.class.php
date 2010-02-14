<?php

class SmartestModel extends SmartestBaseModel{

	protected $_model_properties = array();

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'itemclass_';
		$this->_table_name = 'ItemClasses';
		
	}
	
	public function find($id){
	    $bool = parent::find($id);
	    $this->buildPropertyMap();
	    return $bool;
	}
	
	protected function buildPropertyMap(){
		
		if(!count($this->_model_properties)){
	    
	        if(SmartestCache::hasData('model_properties_'.$this->_properties['id'], true)){
    		    $result = SmartestCache::load('model_properties_'.$this->_properties['id'], true);
    	    }else{
    		    $sql = "SELECT * FROM ItemProperties WHERE itemproperty_itemclass_id='".$this->_properties['id']."' ORDER BY itemproperty_id ASC";
    		    $result = $this->database->queryToArray($sql);
    		    SmartestCache::save('model_properties_'.$this->_properties['id'], $result, -1, true);
    	    }
		
    		foreach($result as $db_property){
    			$property = new SmartestItemProperty;
    			$property->hydrate($db_property);
    			$this->_model_properties[] = $property;
    		}
		
	    }
		
	}
	
	public function getProperties(){
	    
	    $this->buildPropertyMap();
	    return $this->_model_properties;
	    
	}
	
	public function refresh(){
	    SmartestCache::clear('model_properties_'.$this->_properties['id'], true);
        SmartestObjectModelHelper::buildAutoClassFile($this->_properties['id'], SmartestStringHelper::toCamelCase($this->getName()));
	}
	
	public function delete($remove=false){
	    
	    if($remove){
	    
	        // Delete properties and property values
    	    foreach($this->getProperties() as $p){
    	        $p->delete(false);
    	    }
	    
    	    // Delete items, now bereft of any property values
    	    $sql = "DELETE FROM Items WHERE item_itemclass_id='".$this->getId()."'";
    	    $this->database->rawQuery($sql);
	    
    	    parent::delete();
	    
        }
	    
	}
	
    public function offsetGet($offset){
	    
	    switch($offset){
	        
	        case "sets":
	        case "datasets":
	        return $this->getDataSets();
	        
	        case "default_metapage_id":
	        return $this->getDefaultMetaPageId($this->getCurrentSiteId());
	        
	    }
	    
	    return parent::offsetGet($offset);
	    
	}
	
	public function __toArray(){
	    $data = parent::__toArray();
	    $data['default_metapage_id'] = $this->getDefaultMetaPageId($this->getCurrentSiteId());
	    return $data;
	}
	
    public function __toArrayLikeCmsItemArray(){
	    
	    $array = array();
	    $array['_model'] = $this->__toArray();
	    $array['_properties'] = array();
	    
	    foreach($this->_model_properties as $p){
	        
	        if(is_object($p)){
	            $array['_properties'][$p->getId()] = $p->__toArray();
	            $array['_properties'][$p->getId()]['_options'] = $p->getPossibleValuesAsArrays();
	            $array[$p->getId()] = '';
	        }
	    }
	    
	    return $array;
	    
	}
	
	public function getDataSets($site_id=''){
	    
	    $sql = "SELECT * FROM Sets WHERE set_itemclass_id='".$this->getId()."'";
	    
	    if(is_numeric($site_id)){
	        $sql .= " AND (set_site_id='".$site_id."' OR set_shared='1')";
	    }
	    
	    $sql .= " ORDER BY set_label";
	    
	    $result = $this->database->queryToArray($sql);
	    $sets = array();
	    
	    foreach($result as $r){
	        $s = new SmartestCmsItemSet;
	        $s->hydrate($r);
	        $sets[] = $s;
	    }
	    
	    return $sets;
	    
	}
	
	/* public function getPropertiesAsArrays(){
		
		$propertyarrays = array();
		
		foreach($this->getProperties() as $property){
		    $propertyarrays[] = $property->__toArray();
		}
		
		return $propertyarrays;
	} */
	
	public function getPropertyIds(){
		
		$ids = array();
		
		foreach($this->_model_properties as $mp){
			$ids[] = $mp->getId();
		}
		
		return $ids;
	}
	
	public function getPropertyNames(){
		
		$propertynames = array();
		
		foreach($this->_model_properties as $mp){
			$propertynames[] = $mp->getName();
		}
		
		return $propertynames;
	}
	
	public function getPropertyVarNames(){
		
		$propertynames = array();
		
		foreach($this->_model_properties as $mp){
			$propertynames[] = $mp->getVarName();
		}
		
		return $propertynames;
	}
	
	public function getPropertyVarNamesLookup(){
	    
	    $properties = array();
		
		foreach($this->_model_properties as $mp){
			$properties[$mp->getVarName()] = $mp->getId();
		}
		
		return $properties;
	}
    
    public function getClassName(){
	    return SmartestStringHelper::toCamelCase($this->getName());
    }
    
    public function getAutoClassName(){
        return 'auto'.$this->getClassName();
    }
    
    public function getSimpleItems($site_id='', $mode=0, $query='', $exclude=''){
        
        $mode = (int) $mode;
        
        $sql = "SELECT * FROM Items WHERE item_itemclass_id='".$this->_properties['id']."' AND item_deleted != 1";
        
        if(is_numeric($site_id)){
            $sql .= " AND item_site_id='".$site_id."'";
        }
        
        if($mode > 0){
            
            if(in_array($mode, array(4,5,6))){
                $sql .= " AND item_public='TRUE'";
            }else if(in_array($mode, array(1,2,3))){
                $sql .= " AND item_public='FALSE'";
            }
            
            if($mode == 8){
                $sql .= " AND item_is_archived='1'";
            }else{
                $sql .= " AND item_is_archived='0'";
            }
            
            if(in_array($mode, array(2,3,5,6))){
                $sql .= " AND item_modified > item_last_published";
            }
            
            if(in_array($mode, array(3,6))){
                $sql .= " AND item_changes_approved='1'";
            }else if(in_array($mode, array(2,5))){
                $sql .= " AND item_changes_approved='0'";
            }
            
        }
        
        if(strlen($query)){
            
            $words = explode(' ', $query);
            
            $sql .= ' AND (';
            $conditions = array();
            
            foreach($words as $w){
                $conditions[] = "(item_name LIKE '%".$w."%' OR item_search_field LIKE '%".$w."%')";
            }
            
            $sql .= implode(' AND ', $conditions).')';
            
        }
        
        if(is_array($exclude)){
            $sql .= " AND item_id NOT IN ('".implode("', '", $exclude)."')";
        }
        
        $sql .= " ORDER BY item_name";
        
        $result = $this->database->queryToArray($sql);
        $items = array();
        
        foreach($result as $db_array){
            $item = new SmartestItem;
            $item->hydrate($db_array);
            $items[] = $item;
        }
        
        return $items;
    }
    
    public function getSimpleItemsAsArrays($site_id='', $mode=0, $query=''){
        
        $items = $this->getSimpleItems($site_id, $mode, $query);
        $arrays = array();
        
        foreach($items as $item){
            $arrays[] = $item->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getItemIds($site_id=''){
        
        $sql = "SELECT item_id FROM Items WHERE item_itemclass_id='".$this->_properties['id']."' AND item_deleted != 1";
        
        if(is_numeric($site_id)){
            $sql .= " AND item_site_id='".$site_id."'";
        }
        
        $sql .= " ORDER BY item_name";
        
        $result = $this->database->queryToArray($sql);
        $items = array();
        
        foreach($result as $db_array){
            $items[] = $db_array['item_id'];
        }
        
        return $items;
        
    }
    
    public function getMetaPages(){
        
        $sql = "SELECT * FROM Pages WHERE page_type='ITEMCLASS' AND page_dataset_id='".$this->_properties['id']."' and page_deleted != 1";
        
        if(is_object($this->getSite())){
            $sql .= " AND page_site_id='".$this->getSite()->getId()."'";
        }
        
        $result = $this->database->queryToArray($sql);
        $pages = array();
        
        foreach($result as $record){
            $p = new SmartestPage;
            $p->hydrate($record);
            $pages[] = $p;
        }
        
        return $pages;
        
    }
    
    public function getMetaPagesAsArrays(){
        
        $pages = $this->getMetaPages();
        $arrays = array();
        
        foreach($pages as $p){
            $arrays[] = $p->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function hasDefaultDescriptionPropertyId(){
        return is_numeric($this->getDefaultDescriptionPropertyId());
    }
    
    public function getAvailableDescriptionProperties(){
        
        $properties = $this->getProperties();
        $ok_properties = array();
        $asset_types = SmartestDataUtility::getAssetTypes();
        
        $text_asset_types = array();
        
        foreach($asset_types as $a){
            if($a['storage']['type'] == "database"){
                $text_asset_types[] = $a['id'];
            }
        }
        
        foreach($properties as $p){
            
            $info = $p->getTypeInfo();
            
            if((isset($info['long']) && strtolower($info['long']) != 'false') || ($p->getDatatype() == 'SM_DATATYPE_ASSET' && in_array($p->getForeignKeyFilter(), $text_asset_types))){
                $ok_properties[] = $p;
            }
        }
        
        // print_r($ok_properties);
        
        return $ok_properties;
    }
    
    public function getDefaultMetaPageId($site_id){
        // TODO: this functionality should be stored in the database
        return SmartestSystemSettingHelper::load('model_'.$this->_properties['id'].'_default_metapage_site_'.$site_id);
    }
    
    public function setDefaultMetaPageId($site_id, $id){
        // TODO: this functionality should be stored in the database
        return SmartestSystemSettingHelper::save('model_'.$this->_properties['id'].'_default_metapage_site_'.$site_id, $id);
    }
    
    public function clearDefaultMetaPageId($site_id){
        // TODO: this functionality should be stored in the database
        return SmartestSystemSettingHelper::clear('model_'.$this->_properties['id'].'_default_metapage_site_'.$site_id);
    }
    
    public function getAvailableDescriptionPropertiesAsArrays(){
        
        $properties = $this->getAvailableDescriptionProperties();
        $arrays = array();
        
        foreach($properties as $p){
            $arrays[] = $p->__toArray();
        }
        
        return $arrays;
        
    }
    
    public function getForeignKeyProperties($item_id=''){
        
        if(is_numeric($item_id)){
            
            $class = $this->getClassName();
            $item = new $class;
            
            if($item->hydrate($item_id)){
                $properties = $item->getPropertyValueHolders();
            }else{
                $properties = $this->getProperties();
            }
            
        }else{
            $properties = $this->getProperties();
        }
        
        $fk_properties = array();
        
        foreach($properties as $p){
            if($p->getDatatype() == 'SM_DATATYPE_CMS_ITEM'){
                $fk_properties[] = $p;
            }
        }
        
        return $fk_properties;
        
    }
    
    public function hasForeignKeyProperties(){
        
        return (bool) count($this->getForeignKeyProperties());
        
    }
    
    public function getForeignKeyPropertiesForModelId($model_id, $item_id=''){
        
        $model_id = (int) $model_id;
        
        $fk_properties = $this->getForeignKeyProperties($item_id);
        $properties = array();
        
        foreach($fk_properties as $p){
            if($p->getForeignKeyFilter() == $model_id){
                $properties[] = $p;
            }
        }
        
        return $properties;
        
    }
    
    public function hasForeignKeyPropertiesForModelId($model_id){
        
        return (bool) count($this->getForeignKeyPropertiesForModelId($model_id));
        
    }
    
    public function getMainSite(){
        
        $s = new SmartestSite;
        
        if($this->getSiteId() == '0'){
            
            $sql = "SELECT site_id FROM Sites ORDER BY site_id ASC";
            $result = $this->database->queryToArray($sql);
            $first_site_id = $result[0]['site_id'];
            
            // This code will be uncommented in future
            // It ensures the model is always attached to a site
            /* $copy = $this->copy();
            $copy->setSiteId($first_site_id);
            $copy->save(); */
            
            SmartestLog::getInstance('system')->log("\SmartestModel->getMainSite() was called on a model (".$this->getName().") that does not yet have a site attached to it.");
            $site_id = $first_site_id;
            
        }else{
            $site_id = $this->getSiteId();
        }
        
        if(!$s->find($site_id)){
            SmartestLog::getInstance('system')->log("Model ".$this->getName()." is attached to a site that no longer exists and must be reassigned.");
        }
        
        return $s;
    }
    
    public function getSitesWhereUsed(){
        
        $sql = "SELECT DISTINCT Sites.* FROM Sites, Items, ItemClasses, Pages WHERE (Items. item_itemclass_id=ItemClasses.itemclass_id AND Items.item_site_id=Sites.site_id AND ItemClasses.itemclass_id='".$this->getId()."') OR (Pages.page_type='ITEMCLASS' AND Pages.page_dataset_id='".$this->getId()."' AND Pages.page_site_id=Sites.site_id)";
        
        $result = $this->database->queryToArray($sql);
        
        $sites = array();
        
        foreach($result as $r){
            $s = new SmartestSite;
            $s->hydrate($r);
            $sites[] = $s;
        }
        
        return $sites;
    }
    
    public function getNumberOfSitesWhereUsed(){
        return count($this->getSitesWhereUsed());
    }
    
    public function isUsedOnMultipleSites(){
        return $this->getNumberOfSitesWhereUsed() > 1;
    }
    
    public function getOtherModelsWithSameName(){
        
        $sql = "SELECT * FROM ItemClasses WHERE itemclass_type='SM_ITEMCLASS_MODEL' AND itemclass_name='".$this->getName()."' AND itemclass_id != '".$this->getId()."'";
        $result = $this->database->queryToArray($sql);
        
        $model_objects = array();
		
		foreach($result as $model){
			$m = new SmartestModel;
			$m->hydrate($model);
			$model_objects[] = $m;
		}
		
		return $model_objects;
        
    }
    
    public function hasSameNameAsModelOnOtherSite(){
        
        return (bool) count($this->getOtherModelsWithSameName());
        
    }
    
    public function setShared($new_shared_status){
        
        $currently_shared = $this->getShared();
        
        // If the shared status is being changed
        if($currently_shared != $new_shared_status){
            if($new_shared_status == '0'){
                // Model is being made site-specific, so move class file to Sites/.../Library/ObjectModel/
                if(SmartestFileSystemHelper::move($this->getClassFilePath(1), $this->getClassFilePath(0))){
                    // echo 'moved to sites folder';
                    parent::setShared('0');
                    return true;
                }else{
                    // class file couldn't be moved
                    return false;
                }
            }else if($new_shared_status == '1'){
                // Model is being shared, so move class file to Library/ObjectModel/
                if(SmartestFileSystemHelper::move($this->getClassFilePath(0), $this->getClassFilePath(1))){
                    // echo 'moved to library folder';
                    parent::setShared('1');
                    return true;
                }else{
                    // class file couldn't be moved
                    return false;
                }
            }
        }else{
            return true;
        }
    }
    
    public function getFilesThatMustBeWrtableForSharingToggle(){
        
        return array(
            SmartestFileSystemHelper::dirName($this->getClassFilePath(1)),
            SmartestFileSystemHelper::dirName($this->getClassFilePath(0)),
            $this->getClassFilePath()
        );
        
    }
    
    public function getFilesThatMustBeWrtableForSharingToggleButAreNot(){
        
        $bad_files = array();
        
        foreach($this->getFilesThatMustBeWrtableForSharingToggle() as $file){
            if(!is_writable($file)){
                $bad_files[] = $file;
            }
        }
        
        return $bad_files;
        
    }
    
    public function isMovable(){
        
        return count($this->getFilesThatMustBeWrtableForSharingToggleButAreNot()) ? false : true;
        
    }
    
    public function save(){
        
        $r = parent::save();
        SmartestCache::clear('models_query_site_'.$this->getSiteId(), true);
        SmartestCache::clear('models_query'.$this->getSiteId(), true);
        return $r;
        
    }
    
    ///////////////////////////// Code for building and including model classes /////////////////////////////////
    
    public function init(){
        
        // Do constants first
	    $constant_name = SmartestStringHelper::toCamelCase($this->getName());
		
	    if(!defined($constant_name)){
			define($constant_name, $item_class["itemclass_id"], true);
		}
		
		// if(is_file(SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php')){
		if(class_exists($this->getAutoClassName())){ // $this->getAutoClassName()
		    
		}else{
        	if(is_file($this->getAutoClassFilePath())){
        		// include SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php';
        		include $this->getAutoClassFilePath();
        	}else{
        		// build auto class
        		if($this->buildAutoClassFile()){
        			// include SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$class_name.'.class.php';
        			include $this->getAutoClassFilePath();
        		}else{
        			throw new SmartestException('Could not auto-generate model class: '.$this->getName(), SM_ERROR_MODEL);
        		}
        	}
	    }
		
		if(class_exists($this->getClassName())){
		    
		}else{
		    if(is_file($this->getClassFilePath())){
        		// include SM_ROOT_DIR.'Library/ObjectModel/'.$class_name.'.class.php';
        		include $this->getClassFilePath();
        	}else{
        		// build extensible class
        		if($this->buildClassFile()){
        			// include SM_ROOT_DIR.'Library/ObjectModel/'.$class_name.'.class.php';
        			include $this->getClassFilePath();
        		}else{
        			throw new SmartestException('Could not auto-generate model class: '.$this->getName(), SM_ERROR_MODEL);
        		}
        	}
	    }
        
    }
    
    public function getAutoClassFilePath(){
        
        // if($this->getShared()){
            return SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$this->getClassName().$this->getId().'.class.php';
        // }else{
        //    return SM_ROOT_DIR.'System/Cache/ObjectModel/Models/auto'.$this->getClassName().'.class.php';
        //}
        
    }
    
    public function getComments($status, $site_id=''){
        
        $sql = "SELECT * FROM Items, Comments WHERE Comments.comment_object_id=Items.item_id AND Comments.comment_type='SM_COMMENTTYPE_ITEM_PUBLIC' AND Comments.comment_status='".$status."' AND Items.item_itemclass_id='".$this->getId()."'";
        $result = $this->database->queryToArray($sql);
        
        $comments = array();
        
        foreach($result as $r){
            $c = new SmartestItemPublicComment;
            $c->hydrateWithSimpleItem($r);
            $comments[] = $c;
        }
        
        return $comments;
        
    }
    
    // returs a binary, rather that getShared() which returns a raw value from the database
    public function isShared(){
        return ($this->_properties['shared'] == '1');
    }
    
    public function getClassFilePath($shared_status=-1){
        
        if($shared_status == -1){
            $shared = $this->isShared();
            $specified = false;
        }else{
            $shared = $shared_status;
            $specified = true;
        }
        
        if($shared){
            return SM_ROOT_DIR.'Library/ObjectModel/'.$this->getClassName().'.class.php';
        }else{
            if(!is_dir(SM_ROOT_DIR.'Sites/'.$this->getMainSite()->getDirectoryName().'/Library/ObjectModel/') && !@mkdir(SM_ROOT_DIR.'Sites/'.$this->getMainSite()->getDirectoryName().'/Library/ObjectModel/')){
                SmartestLog::getInstance('system')->log('Site-specific model class could not be created because '.SM_ROOT_DIR.'Sites/'.$this->getMainSite()->getDirectoryName().'/Library/ObjectModel/ is not writable.', SmartestLog::WARNING);
                if(!$specified){
                    return SM_ROOT_DIR.'Library/ObjectModel/'.$this->getClassName().'.class.php';
                }
            }
            return SM_ROOT_DIR.'Sites/'.$this->getMainSite()->getDirectoryName().'/Library/ObjectModel/'.$this->getClassName().'.class.php';
        }
        
    }
    
    public function buildClassFile(){
		
		$className = $this->getClassName();
		    
		if($file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/object_template.txt')){
	
			$file = str_replace('__THISCLASSNAME__', $className, $file);
			$file = str_replace('__AUTOCLASSNAME__', 'auto'.$className, $file);
			$file = str_replace('__TIME__', date("Y-m-d H:i:s"), $file);
			
			// var_dump($this->isShared());
			// echo 'Built: '.$this->getClassFilePath();
			return file_put_contents($this->getClassFilePath(), $file);
		
		}else{
			return false;
		}
		
	}
    
    public function buildAutoClassFile(){
		
		$className = $this->getClassName();
	    $properties = $this->getProperties();
	    $constants = '';
	
		foreach($properties as $property){
		
			$constant_name  = SmartestStringHelper::toConstantName($property->getName());
			$constant_value = $property->getId();
		
			if(is_numeric($constant_name{0})){
				$constant_name = '_'.$constant_name;
			}
		
			$new_constant = '    const '.$constant_name.' = '.$constant_value.";\n";
		    $constants .= $new_constant;
		
		}
	
		if($file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/autoobject_template.txt')){
		
			$functions = $this->buildAutoClassFunctionCode();
			$varnames_lookup = $this->buildAutoClassVarnameLookupCode();
		
			$file = str_replace('__THISCLASSNAME__', $this->getAutoClassName(), $file);
			$file = str_replace('__THECONSTANTS__', $constants, $file);
			$file = str_replace('__THEFUNCTIONS__', $functions, $file);
			$file = str_replace('__THEVARNAMELOOKUPS__', $varnames_lookup, $file);
			$file = str_replace('__MODEL_ID__', $this->getId(), $file);
			$file = str_replace('__TIME__', date("Y-m-d h:i:s"), $file);
	
			file_put_contents($this->getAutoClassFilePath(), $file);
			return true;
		
		}else{
			return false;
		}
		
	}
	
	public function buildAutoClassVarnameLookupCode(){
	    
	    $varnames_lookup = '    protected $_varnames_lookup = array('."\n";
		$i = 1;
		
		$properties = $this->getProperties();
		
		foreach($properties as $property){
			
			$new_constant = "        '".$property->getVarname()."' => ".$property->getId();
			
			if($i < count($properties)){
			    $new_constant .= ',';
			}
			
			$new_constant .= "\n";
			
			$varnames_lookup .= $new_constant;
			$i++;
			
		}
		
		$varnames_lookup .= '    );'."\n\n";
		
		return $varnames_lookup;
	    
	}
	
	public function buildAutoClassFunctionCode(){
	    
	    $file = file_get_contents(SM_ROOT_DIR.'System/Data/ObjectModelTemplates/autoobject_datafunctions.txt');
	    $code = '';
	    
	    foreach($this->getProperties() as $property){
			
			$constant_name  = SmartestStringHelper::toCamelCase($property->getName());
			$constant_value = $property->getId();
			$bool_typecast = $property->getDatatype() == '' ? '(bool) ' : '';
			
			$f = str_replace('__PROPNAME__', $constant_name, $file);
			$f = str_replace('__PROPID__', $constant_value, $f);
			$f = str_replace('__BOOLTYPECAST__', $bool_typecast, $f);
			
			$code .= $f;
			
		}
		
		return $code;
	    
	}
    
}
