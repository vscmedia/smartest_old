<?php

class SmartestContainerDefinition extends SmartestAssetIdentifier{

	protected $_container;
	protected $_depth = null;
	protected $_template = null;
	
	protected function __objectConstruct(){
		
		$this->addPropertyAlias('ContainerId', 'assetclass_id');
		$this->_table_prefix = 'assetidentifier_';
		$this->_table_name = 'AssetIdentifiers';
		
	}
	
    public function load($name, $page, $draft=false){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            
            $container = new SmartestContainer;
            
            $sql = "SELECT * FROM AssetClasses WHERE assetclass_type = 'SM_ASSETCLASS_CONTAINER' AND assetclass_name='".$name."' AND (assetclass_site_id='".$page->getSiteId()."' OR assetclass_shared=1)";
            $result = $this->database->queryToArray($sql);
            
            if(count($result)){
            // if($container->hydrateBy('name', $name)){
                
                $container->hydrate($result[0]);
                
                $this->_asset_class = $container;
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_asset_class->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    
                    if($container->getType() != "SM_ASSETCLASS_CONTAINER"){
                        
                        // raise a warning?
                        
                    }
                    
                    $template = new SmartestContainerTemplateAsset;
                    
                    // print_r($this);
                    
                    if($draft){
                        $template_id = $this->getDraftAssetId();
                    }else{
                        $template_id = $this->getLiveAssetId();
                    }
                    
                    if($template->hydrate($template_id)){
                        
                        $this->_template = $template;
                        $this->_template->setIsDraft($draft);
                        // print_r($this->_template);
                        $this->_loaded = true;
                        return true;
                        
                    }else{
                        // Template doesn't exist
                        // echo "Template doesn't exist<br />";
                        $this->_loaded = false;
                        return false;
                    }
                }else{
                    // Container not defined
                    // echo "Container not defined<br />";
                    $this->_loaded = false;
                    return false;
                }
                
            }else{
                // Container by that name doesn't exist
                // echo "Container by that name doesn't exist<br />";
                $this->_loaded = false;
                return false;
            }
        }
    }
    
    public function loadForUpdate($name, $page, $draft=false){
        
        if(strlen($name) && is_object($page)){
            
            $this->_page = $page;
            
            $container = new SmartestContainer;
            
            if($container->hydrateBy('name', $name)){
                
                // echo 'loaded';
                
                $this->_asset_class = $container;
                $sql = "SELECT * FROM AssetIdentifiers WHERE assetidentifier_assetclass_id='".$this->_asset_class->getId()."' AND assetidentifier_page_id='".$this->_page->getId()."'";
                $result = $this->database->queryToArray($sql);
                
                // var_dump($result);
                
                if(count($result)){
                    
                    $this->hydrate($result[0]);
                    
                    // var_dump($container->getType());
                    
                    if($container->getType() == "SM_ASSETCLASS_CONTAINER"){
                        
                        $template = new SmartestContainerTemplateAsset;
                        $this->_template = $template;
                        $this->_template->setIsDraft($draft);
                        $this->_loaded = true;
                        return true;
                        
                    }else{
                        
                        // asset class being filled is not a container
                        $this->_loaded = false;
                        return false;
                        
                    }
                    
                    
                }else{
                    
                    // Container not defined
                    $this->_loaded = false;
                    return false;
                }
                
            }else{
                // Container by that name doesn't exist
                $this->_loaded = false;
                return false;
            }
        }
    }
    
    public function getTemplateFilePath(){
        
        if($this->_template->getFile()->exists()){
            // var_dump($this->_template->getFile()->getPath());
            return $this->_template->getFile()->getPath();
        }else{
            return null;
        }
    }
    
    public function getTemplate(){
        /* if($this->_template->getFile()->exists()){
            // var_dump($this->_template->getFile()->getPath());
            return $this->_template->getFile()->getPath();
        }else{
            return null;
        } */
        
        return $this->_template;
    }
    
    public function hydrateFromGiantArray($array){
        
        $this->hydrate($array);
        
        $container = new SmartestContainer;
        $container->hydrate($array);
        $this->_asset_class = $container;
        
        $template = new SmartestContainerTemplateAsset;
        $template->hydrate($array);
        $this->_template = $template;
        
    }
    
    public function getContainer(){
	    
	    if(!is_object($this->_asset_class)){
	    
	        $c = new SmartestContainer;
	        $c->hydrate($this->getAssetClassId());
	        $this->_asset_class = $c;
	    
        }
	    
	    return $this->_asset_class;
	}

}