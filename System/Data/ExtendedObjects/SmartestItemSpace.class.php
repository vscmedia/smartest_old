<?php

class SmartestItemSpace extends SmartestAssetClass{
    
    protected $_definition;
    protected $_data_set;
    
    protected function __objectConstruct(){
		
		$this->_table_prefix = 'assetclass_';
		$this->_table_name = 'AssetClasses';
		
	}
	
	public function exists($name, $site_id){
	    
	    $sql = "SELECT * FROM AssetClasses WHERE assetclass_name='".$name."' AND assetclass_type='SM_ASSETCLASS_ITEM_SPACE' AND assetclass_site_id='".$site_id."'";
	    $query_result = $this->database->queryToArray($sql);
	    
	    if(count($query_result) > 0){
	        $this->hydrate($query_result[0]);
	        return true;
	    }else{
	        return false;
        }
	}
	
	public function getDataSet(){
	    
	    if(!$this->_data_set){
	        
	        $set_id = $this->getDataSetId();
	        $set = new SmartestCmsItemSet;
	        
	        if($set->hydrate($set_id)){
	            $this->_data_set = $set;
	        }
        }
        
        return $this->_data_set;
	    
	}
	
	public function getOptions(){
	    
	    return $this->getDataSet()->getMembers(SM_QUERY_ALL_LIVE_CURRENT);
	    
	}
	
	public function getOptionsAsArrays(){
	    
	    return $this->getDataSet()->getSimpleMembersAsArrays(SM_QUERY_ALL_LIVE_CURRENT);
	    
	}
	
	public function getDataSetId(){
	    return $this->getInfoField('dataset_id');
	}
	
	public function setDataSetId($id){
	    $id = (int) $id;
	    $this->setInfoField('dataset_id', $id);
	}
	
	public function getUsesTemplate(){
	    return $this->getInfoField('uses_template');
	}
	
	public function usesTemplate(){
	    return $this->getInfoField('uses_template');
	}
	
	public function setUsesTemplate($bool){
	    $bool = (bool) $bool;
	    $this->setInfoField('uses_template', $bool);
	}
	
	public function getTemplateAssetId(){
	    return $this->getInfoField('template_asset_id');
	}
	
	public function setTemplateAssetId($id){
	    $id = (int) $id;
	    $this->setInfoField('template_asset_id', $id);
	}
	
	public function save(){
	    
	    if($this->_properties['type'] != 'SM_ASSETCLASS_ITEM_SPACE'){
	        $this->setField('Type', 'SM_ASSETCLASS_ITEM_SPACE');
	    }
	    
	    parent::save();
	    
	}
    
}
