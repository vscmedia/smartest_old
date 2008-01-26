<?php

class SmartestPlaceholderDefinition extends SmartestAssetIdentifier{
    
    protected $_placeholder;
    protected $_asset;
    protected $_is_linkable = false;
    
	protected function __objectConstruct(){
		
		$this->addPropertyAlias('PlaceholderId', 'assetclass_id');
		$this->_table_prefix = 'assetidentifier_';
		$this->_table_name = 'AssetIdentifiers';
		
	}
	
	public function load($name, $page, $draft=false){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            
            $placeholder = new SmartestPlaceholder;
            
            $sql = "SELECT * FROM AssetClasses WHERE assetclass_type != 'SM_ASSETCLASS_CONTAINER' AND assetclass_name='".$name."' AND (assetclass_site_id='".$page->getSiteId()."' OR assetclass_shared=1)";
            $result = $this->database->queryToArray($sql);
            
            if(count($result)){
            // if($placeholder->hydrateBy('name', $name)){
                
                $placeholder->hydrate($result[0]);
                
                $this->_placeholder = $placeholder;
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_placeholder->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    
                    $asset_types = SmartestDataUtility::getAssetTypes();
                    $assetclass_types = SmartestDataUtility::getAssetClassTypes();
                    
                    if($draft){
                        $asset_id = $this->getDraftAssetId();
                    }else{
                        $asset_id = $this->getLiveAssetId();
                    }
                    
                    $asset = new SmartestAsset;
                    $asset->hydrate($asset_id);
                    
                    // print_r($asset_types);
                    
                    // if(array_key_exists($placeholder->getType(), $asset_types) && isset($asset_types[$placeholder->getType()]['class'])){
                        // $class_name = $asset_types[$placeholder->getType()]['class'];
                        // echo $class_name;
                        // $asset = new $class_name;
                        // $asset = new SmartestAsset;
                    // }else{
                        // $asset = new SmartestAsset;
                    // }
                    
                    if(in_array($placeholder->getType(), array('SM_ASSETTYPE_IMAGE', 'SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_SL_TEXT'))){
                        $this->_is_linkable = true;
                    }else{
                        $this->_is_linkable = false;
                    }
                    
                    
                    
                    return $asset_id;
                    
                    /* if($asset->hydrate($asset_id)){
                        
                        $this->_asset = $asset;
                        $this->_asset->setIsDraft($draft);
                        // print_r($asset->getType());
                        // print_r($this->database->getDebugInfo());
                        $this->_loaded = true;
                        return true;
                        
                    }else{
                        // Asset doesn't exist
                        $this->_loaded = false;
                        return false;
                    } */
                    
                }else{
                    // Placeholder not defined
                    $this->_loaded = false;
                    return false;
                }
                
            }else{
                // Placeholder by that name doesn't exist
                $this->_loaded = false;
                return false;
            }
        }
    }
    
    public function loadForUpdate($name, $page, $draft=false){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            
            $placeholder = new SmartestPlaceholder;
            
            if($placeholder->hydrateBy('name', $name)){
                
                // echo 'loaded';
                
                $this->_placeholder = $placeholder;
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_placeholder->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                // var_dump($result);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    
                    if($placeholder->getType() != "SM_ASSETTYPE_CONTAINER_TEMPLATE"){
                        
                        $asset = new SmartestAsset;
                        $this->_asset = $asset;
                        $this->_asset->setIsDraft($draft);
                        $this->_loaded = true;
                        return true;
                        
                    }else{
                        
                        // asset class being filled is a container
                        $this->_loaded = false;
                        return false;
                        
                    }
                    
                    
                }else{
                    
                    // placeholder not defined
                    $this->_loaded = false;
                    return false;
                }
                
            }else{
                // Placeholder by that name doesn't exist
                $this->_loaded = false;
                return false;
            }
        }
    }
    
    public function getAsset($draft=false){
        
        if(!$this->_asset){
            if($draft){
                $asset_id = $this->getDraftAssetId();
            }else{
                $asset_id = $this->getLiveAssetId();
            }
            
            $a = new SmartestAsset;
            
            if($a->hydrate($asset_id)){
                $this->_asset = $a;
            }else{
                // no asset defined
            }
            
        }
        
        return $this->_asset;
        
    }
    
    public function getDefaultAssetRenderData($draft=false){
        
        $asset = $this->getAsset($draft);
        
        if(is_object($asset)){
            return @unserialize($asset->getParameterDefaults());
        }
        
    }
    
    public function getMarkup(){
        
        if($this->_asset){
            
            $asset_markup = $this->_asset->renderAsMarkup();
            
            if(!in_array($this->_asset->getType(), array('SM_ASSETTYPE_JAVASCRIPT', 'SM_ASSETTYPE_STYLESHEET'))){
            
                if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
				    $edit_link = "<a title=\"Click to edit definition for placeholder: ".$this->getPlaceholder()->getLabel()." (".$this->getPlaceholder()->getType().")\" href=\"".SM_CONTROLLER_DOMAIN."websitemanager/definePlaceholder?assetclass_id=".$this->getPlaceholder()->getName()."&amp;page_id=".$this->_page->getWebid()."\" style=\"text-decoration:none;font-size:11px\" target=\"_top\"><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/arrow_refresh_small.png\" alt=\"edit\" style=\"display:inline;border:0px;\" /><!-- Swap this asset--></a>";
			    }else{
				    $edit_link = "<!--edit link-->";
			    }
            
                $asset_markup .= $edit_link;
            
            }
        
            if($this->_is_linkable && strlen($this->getLinkUrl())){
                $string = '<a href="'.$this->getLinkUrl().'">'.$asset_markup.'</a>';
            }else{
                $string = $asset_markup;
            }
            
            return $string;
        }
    }
	
	public function getPlaceholder(){
	    
	    if(!is_object($this->_placeholder)){
	        $ph = new SmartestPlaceHolder;
	        $ph->hydrate($this->getAssetClassId());
	        $this->_placeholder = $ph;
        }
	    
	    return $this->_placeholder;
	}
	
	public function getType(){
	    return $this->getPlaceholder()->getType();
	}
	
	/* public function getTypeCode(){
	    return $this->getPlaceholder()->getAssetType()->getCode();
	} 
	
	public function getAsset($draft=false){
	    
	    if(!is_object($this->_asset)){
	        
	        switch($this->getTypeCode()){
	            /* case "HTML":
	            case "TEXT":
	            case "LINE":
	            $class = 'SmartestTextAsset'; 
	            break; 
	            case "JPEG":
	            case "GIF":
	            case "PNG":
	            case "GIMG":
	            $class = 'SmartestImageAsset';
	            break;
	            default:
	            $class = 'SmartestAsset';
	            break;
	        }
	        
	        $asset = new $class;
	        $hydrate = $draft ? $asset->hydrate($this->getDraftAssetId()) : $asset->hydrate($this->getLiveAssetId());
	        $this->_asset = $asset;
        }
	    
	    return $this->_asset;
	}*/

}