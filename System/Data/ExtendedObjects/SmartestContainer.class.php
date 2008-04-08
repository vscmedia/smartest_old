<?php

class SmartestContainer extends SmartestAssetClass{

	protected function __objectConstruct(){
		
		$this->_table_prefix = 'assetclass_';
		$this->_table_name = 'AssetClasses';
		
	}
	
	public function getPossibleAssets(){
	    
	    // print_r($this->getSite());
	    
	    $type = $this->getTypeInfo();
	    
	    /*if($this->getType() == 'SM_ASSETTYPE_IMAGE'){
            $types = array('SM_ASSETTYPE_IMAGE', 'SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE');
        }else{
            $types = array($this->getType());
        } */
        
        return $this->getAssetsByType($type['accept']);
        
	}
	
	public function getPossibleAssetsAsArrays(){
	    
	    $objects = $this->getPossibleAssets();
	    $arrays = array();
	    
	    foreach($objects as $asset){
	        $arrays[] = $asset->__toArray();
	    }
	    
	    return $arrays;
	    
	}
	
	public function hydrateBy($field, $value){
	    $sql = "SELECT * FROM AssetClasses WHERE ".$this->_table_prefix.$field."='".$value."' AND ".$this->_table_prefix."type='SM_ASSETCLASS_CONTAINER' AND (".$this->_table_prefix."site_id='".$this->getSite()->getId()."' OR ".$this->_table_prefix."shared='1')";
	    $result = $this->database->queryToArray($sql);
	    
	    if(count($result)){
	        parent::hydrate($result[0]);
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public function getAssetsByType(){
	    
	    $site_id = $this->getSite()->getId();
	    
	    $args = func_get_args();
	    
	    // var_dump($args);
	    
	    if(count($args)){
	        
	        // detect whether we are being passed an array of types, or a list of arguments
	        if(is_array($args[0][0])){
	            $types = $args[0][0];
	        }else if(is_array($args[0])){
	            // print_r($args);
	            $types = $args[0];
            }else{
                $types = $args;
            }
        }else{
            // no types were selected
            $types = array();
        }
        
        if(count($types)){
            
            $sql = "SELECT * FROM Assets WHERE asset_type IN (";
            
            foreach($types as $key => $t){
                
                if($key > 0){
                    $sql .= ', ';
                }
                
                $sql .= "'".$t."'";
                
            }
            
            $sql .= ')';
            
            if(is_numeric($site_id)){
                $sql .= " AND (asset_site_id='".$site_id."' OR asset_shared='1')";
            }
            
            $sql .= ' ORDER BY asset_stringid';
            
            $result = $this->database->queryToArray($sql);
            $official_types = SmartestDataUtility::getAssetTypes();
            $assets = array();
            
            foreach($result as $raw_asset){
                
                if(in_array($raw_asset['asset_type'], array_keys($official_types))){
                    
                    $try_class = $official_types[$raw_asset['asset_type']]['class'];
                    
                    if(class_exists($try_class)){
                        $class = $try_class;
                    }else{
                        $class = 'SmartestAsset';
                    }
                    
                }else{
                    $class = 'SmartestAsset';
                }
                
                $asset = new $class;
                $asset->hydrate($raw_asset);
                $assets[] = $asset;
                
            }
            
            return $assets;
            
        }else{
            
            return array();
            
        }
        
	}
	
	public function getAssetsByTypeAsArrays(){
	    
	    $types = func_get_args();
	    $objects = $this->getAssetsByType($types);
	    $array = array();
	    
	    foreach($objects as $asset){
	        $array[] = $asset->__toArray();
	    }
	    
	    return $array;
	    
	}

}