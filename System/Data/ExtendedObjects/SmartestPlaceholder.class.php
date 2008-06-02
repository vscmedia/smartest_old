<?php

class SmartestPlaceholder extends SmartestAssetClass{
    
    protected $_asset_type;
    
	protected function __objectConstruct(){
		
		$this->addPropertyAlias('TypeId', 'assettype_id');
		$this->_table_prefix = 'assetclass_';
		$this->_table_name = 'AssetClasses';
		
	}
	
	public function getAssetType(){
	    
	    if(!is_object($this->_asset_type)){
	        $type = new SmartestPlaceHolder;
	        $type->hydrate($this->getAssetTypeId());
	        $this->_asset_type = $type;
        }
	    
	    return $this->_asset_type;
	    
	}
	
	public function getPossibleAssets(){
	    
	    // print_r($this->getSite());
	    
	    $type = $this->getTypeInfo();
	    
	    // print_r($type);
	    
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
	
	public function isLinkable(){
	    
	    $type = $this->getTypeInfo();
	    return (isset($type['linkable']) && $type['linkable'] && strtolower($type['linkable']) != 'false') ? true : false;
	    
	}
	
	public function getAssetsByType(){
	    
	    $site_id = $this->getSite()->getId();
	    
	    $args = func_get_args();
	    
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
            
            // var_dump($types);
            
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
            
            // echo $sql;
            
            $result = $this->database->queryToArray($sql);
            $official_types = SmartestDataUtility::getAssetTypes();
            $assets = array();
            
            // echo $sql;
            
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