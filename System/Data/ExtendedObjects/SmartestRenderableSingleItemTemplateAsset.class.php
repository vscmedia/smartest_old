<?php 

class SmartestRenderableSingleItemTemplateAsset extends SmartestAsset{
    
    protected $_template_file;
    protected $_base_dir = '';
    protected $_item;
    
    public function __toString(){
        
        return ''.$this->render();
        
    }
    
    public function render(){
        
        if($this->_item){
            
            $sm = new SmartyManager('SingleItemTemplateRenderer');
            $r = $sm->initialize($this->getStringId());
            $r->assign('item', $this->_item);
            $r->assignTemplate($this->getFullPathOnDisk());
            $r->setDraftMode($draft_mode);
    	    $content = $r->renderTemplate();
    	    
    	    return $content;
            
        }
        
    }
    
    public function getFullPathOnDisk(){
        
        $this->_base_dir = SM_ROOT_DIR.$this->getStorageLocation();
        return $this->_base_dir.$this->getUrl();
        
    }
    
    public function getFile(){
        
        
        
        if(!$this->_template_file){
            
            $this->_template_file = new SmartestFile();
            
            if($this->_template_file->loadFile($this->getFullPathOnDisk())){
                    
            }else{
                // file doesn't exist or isn't readable
            }
            
        }
        
        return $this->_template_file;
        
    }
    
    public function find($id){
        
        if(parent::find($id)){
            $this->getFile();
            return true;
        }else{
            return false;
        }
        
    }
    
    public function findBy($field, $value, $site_id=''){
	    
	    $sql = $this->getRetrievalSqlQuery($value, $field, $site_id);
	    $h = new SmartestTemplatesLibraryHelper;
	    $sql .= " AND asset_type='SM_ASSETTYPE_SINGLE_ITEM_TEMPLATE'";
	    
	    $result = $this->database->queryToArray($sql);
	    $this->_last_query = $sql;
	    
	    if(count($result)){
	
		    foreach($result[0] as $name => $value){
			    if (substr($name, 0, strlen($this->_table_prefix)) == $this->_table_prefix) {
				    $this->_properties[substr($name, strlen($this->_table_prefix))] = $value;
			    }else if(isset($this->_no_prefix[$name])){
				    $this->_properties[$name] = $value;
			    }
		    }
	
		    $this->_came_from_database = true;
		    
		    return true;
	    }else{
		    return false;
	    }
	    
	}
	
	public function setItem(SmartestCmsItem $item){
	    $this->_item = $item;
	}
    
    public function offsetGet($offset){
        
        switch($offset){
            
            case "status":
            return "imported";
            
            case "action_url":
            return $this->getRequest()->getDomain()."templates/editTemplate?template=".$this->getId();
            
            case "label":
            return $this->getUrl();
            
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function getArrayForElementsTree($level){
	    
	    $info = array();
	    $info['asset_id'] = $this->getId();
	    $info['asset_webid'] = $this->getWebid();
	    $info['asset_type'] = $this->getType();
	    $info['assetclass_name'] = $this->getStringid();
	    $info['assetclass_id'] = 'asset_'.$this->getId();
	    $info['defined'] = 'PUBLISHED';
	    $info['exists'] = 'true';
	    $info['filename'] = $this->getUrl();
	    $info['type'] = 'template';
	    $level++;
	    return array('info'=>$info, 'level'=>$level);
	}
	
	public function getContent(){
	    
	    $file = $this->getFullPathOnDisk();
	    
	    if(is_file($file)){
		    $contents = SmartestFileSystemHelper::load($file, true);
		    return $contents;
	    }
	    
	}
    
}