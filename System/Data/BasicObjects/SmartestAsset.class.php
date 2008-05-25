<?php

class SmartestAsset extends SmartestDataObject{
    
    protected $_allowed_types = array();
    protected $_draft_mode = false;
    protected $_text_fragment;
    protected $_type_info = null;
    protected $_site;
    protected $_image;
    
    protected function __objectConstruct(){
		
		$this->setTablePrefix('asset_');
		$this->setTableName('Assets');
		
	}
	
	public function __toArray($include_object=false, $include_owner=false){
	    $data = parent::__toArray();
	    
	    $data['text_content'] = $this->getContent();
	    $data['type_info'] = $this->getTypeInfo();
	    $data['default_parameters'] = $this->getDefaultParams();
	    
	    if($data['type_info']['storage']['type'] == 'database'){
	        $data['full_path'] = $this->_properties['url'];
	    }else{
	        $data['full_path'] = $this->getFullPathOnDisk();
	        
        }
        
        $data['size'] = $this->getSize();
	    
	    if($include_owner){
	        $o = new SmartestUser;
	        if($o->hydrate($this->_properties['user_id'])){
	            $data['owner'] = $o->__toArray();
            }else{
                $data['owner'] = array();
            }
        }
	    
	    if($this->isImage()){
            $data['width'] = $this->getWidth();
            $data['height'] = $this->getHeight();
        }
        
        if($include_object){
            $data['_object'] = $this;
        }
        
	    return $data;
	}
	
	public function __toString(){
	    
	    return $this->_properties['stringid'].' ('.$this->_properties['url'].')';
	    
	}
	
	public function getSite(){
	    
	    if($this->_site){
	        return $this->_site;
	    }else{
	        $site = new SmartestSite;
	        
	        if($site->hydrate($this->getSiteId())){
	            $this->_site = $site;
	            return $this->_site;
	        }else{
	            return null;
	        }
	    }
	    
	}
	
	public function getWidth(){
	    
	    if($this->isImage()){
	        if(!$this->_image){
		        $this->_image = new SmartestImage;
	            $this->_image->loadFile($this->getFullPathOnDisk());
	        }
		    return $this->_image->getWidth();
		}
	    
	}
	
	public function getHeight(){
	    
	    if($this->isImage()){
	        if(!$this->_image){
		        $this->_image = new SmartestImage;
	            $this->_image->loadFile($this->getFullPathOnDisk());
	        }
		    return $this->_image->getHeight();
		}
	    
	}
	
	public function getTypeInfo(){
	    
	    if(!$this->_type_info){
	        
	        $asset_types = SmartestDataUtility::getAssetTypes();
	        
	        if(array_key_exists($this->getType(), $asset_types)){
	            $this->_type_info = $asset_types[$this->getType()];
	        }else{
	            // some sort of error? unsupported type
	        }
	        
	    }
	    
	    return $this->_type_info;
	    
	}
	
	public function usesTextFragment(){
	    
	    $info = $this->getTypeInfo();
	    
	    // print_r($info);
	    
	    if($info['storage']['type'] == 'database'){
	        return true;
	    }else{
	        // this type of asset doesn't use table TextFragments
	        return false;
	    }
	    
	}
	
	public function usesLocalFile(){
	    $info = $this->getTypeInfo();
	    return (isset($info['storage']) && $info['storage']['type'] == 'file');
	}
	
	public function getTextFragment(){
	    
	    // var_dump($this->_text_fragment);
	    
	    // no text fragment has been created
	    if(!$this->_text_fragment){
	        
	        
	        if($this->usesTextFragment()){
    	        
    	        $tf = new SmartestTextFragment;
    	        
    	        if($this->getFragmentId()){
	                
	                if(!$tf->hydrate($this->getFragmentId())){
	                    
	                    // whoops, this asset doesn't have a text fragment - create one, but log that this was what happened
                        $tf->setAssetId($this->getId());
                        $tf->setCreated(time());
                        $tf->save();
                        $this->setFragmentId($tf->getId());
                    }
                    
                    $this->_text_fragment = $tf;
                    
    	        }else{
    	            // whoops, this asset doesn't have a text fragment - create one, but log that this was what happened
    	            if($this->getId()){
	                    $tf->setAssetId($this->getId());
                        $tf->setCreated(time());
	                    $tf->save();
                        $this->setFragmentId($tf->getId());
                        $this->_text_fragment = $tf;
                    }else{
                        $this->_text_fragment = $tf;
                    }
    	        }
    	        
    	    }else{
    	        return null;
    	    }
	    
        }
        
        return $this->_text_fragment;
	    
	}
	
	public function getContent(){
	    if($this->getTextFragment()){
	        if($this->isParsable()){
    	        return stripslashes($this->getTextFragment()->getContent());
            }else{
                return htmlspecialchars(stripslashes($this->getTextFragment()->getContent()), ENT_COMPAT, 'UTF-8');
            }
	    }else if($this->isEditable() && is_file($this->getFullPathOnDisk())){
	        return SmartestFileSystemHelper::load($this->getFullPathOnDisk(), true);
	    }else{
    	    return null;
    	}
	}
	
	public function setContent($raw_content, $escapeslashes=true){
	    
	    $info = $this->getTypeInfo();
	    
	    $content = $raw_content;
	    
	    if($this->getTextFragment()){
	        // save the text fragment in the database
	        $this->getTextFragment()->setContent($content);
	    }else if($this->usesLocalFile() && $this->isEditable()){
	        // save the file to its desired location
	        SmartestFileSystemHelper::save($this->getFullPathOnDisk(), $content, true);
	    }else{
	        // what happens here?
	        // probably nothing as it's just not the right type of asset
	        // log: SmartestAsset::setContent() called on a non-editable asset (asset_id)
	    }
	    
	}
	
	public function isEditable(){
	    $info = $this->getTypeInfo();
	    return (isset($info['editable']) && SmartestStringHelper::toRealBool($info['editable']));
	}
	
	public function isParsable(){
	    $info = $this->getTypeInfo();
	    return (isset($info['parsable']) && SmartestStringHelper::toRealBool($info['parsable']));
	}
	
	public function getFullPathOnDisk(){
	    
	    $info = $this->getTypeInfo();
	    
	    if($this->usesLocalFile()){
	        return SM_ROOT_DIR.$info['storage']['location'].$this->getUrl();
	    }else{
	        return null;
	    }
	    
	}
	
	public function getFullWebPath(){
	    
	    $info = $this->getTypeInfo();
	    
	    if($this->usesLocalFile() && substr($info['storage']['location'], 0, strlen('Public/')) == 'Public/'){
	        return SM_CONTROLLER_DOMAIN.substr($info['storage']['location'], strlen('Public/')).$this->getUrl();
	    }else{
	        return null;
	    }
	    
	}
	
	public function isImage(){
	    return in_array($this->getType(), array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'));
	}
	
	
	
	public function getDefaultParams(){
	    
	    $info = $this->getTypeInfo();
	    
	    $params = array();
	    
	    if(isset($info['param'])){
	        
	        // echo 'param';
	        
	        $raw_xml_params = $info['param'];
	        
	        foreach($raw_xml_params as $rxp){
	            if(isset($rxp['default'])){
	                $params[$rxp['name']] = $rxp['default'];
                }else{
                    $params[$rxp['name']] = '';
                }
	        }
	        
	        // print_r($xml_params);
	        
	        $default_serialized_data = $this->getParameterDefaults();
	        
	        // echo $default_serialized_data;
	        
	        if($asset_params = @unserialize($default_serialized_data)){
	            
	            if(is_array($asset_params)){
	            
	                // data found. loop through params from xml, replacing values with those from asset
    	            foreach($asset_params as $key => $value){
    	                if($params[$key] !== null){
    	                    $params[$key] = $value;
                        }
    	            }
	            
                }
	            
	        } // data not found, or not unserializable. just use defaults from 
	        
	        
	        
	    }
	    
	    // print_r($params);
	    
	    return $params;
	    
	}
	
	public function getDownloadableFilename(){
	    
	    if($this->usesLocalFile()){
	        return $this->getUrl();
	    }else{
	        $info = $this->getTypeInfo();
	        // print_r($info);
	        if(count($info['suffix'])){
	            $dot_suffix = $info['suffix'][0]['_content'];
	        }else{
	            // no suffix found - use txt and log this
	            $dot_suffix = 'txt';
	        }
	        
	        $file_name = strlen($this->getStringid()) ? $this->getStringid() : 'asset';
	        $file_name .= '.'.$dot_suffix;
	        
	        // echo $file_name;
	        
	        return $file_name;
	        
	    }
	}
	
	public function save(){
	    
	    parent::save();
	    
	    if($this->getTextFragment()){
	        
	        if(!$this->getFragmentId() || !$this->getTextFragment()->getId()){
	            // fragment has not been saved
	            $this->getTextFragment()->setAssetId($this->getId());
	            $this->getTextFragment()->save();
	            $this->setFragmentId($this->getTextFragment()->getId());
	            parent::save();
	        }else{
	            $this->getTextFragment()->save();
	        }
	    }
	    
	}
	
	protected function addAllowedType($type){
	    if(!isset($this->_allowed_types[$type])){
	        $this->_allowed_types[] = $type;
	        return true;
	    }else{
	        return false;
	    }
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
	    $info['type'] = 'asset';
	    $level++;
	    return array('info'=>$info, 'level'=>$level);
	}
	
	public function getLiveInstances(){
	    
	    $sql = "SELECT DISTINCT Pages.*, Sites.*, AssetIdentifiers.*, AssetClasses.* FROM Pages, Sites, AssetIdentifiers, Assets, AssetClasses WHERE asset_id='".$this->getId()."' AND assetidentifier_live_asset_id=asset_id AND assetidentifier_page_id=page_id AND page_site_id=site_id AND assetidentifier_assetclass_id=assetclass_id AND page_is_published='TRUE'";
	    
	    if($this->getSite()){
	        $sql .= " AND asset_site_id=site_id AND site_id='".$this->getSite()->getId()."'";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $instances = array();
	    
	    foreach($result as $ri){
	        
	        $instance = array();
	        $page = new SmartestPage;
	        $page->hydrate($ri);
	        $instance['page'] = $page->__toArray();
	        
	        $site = new SmartestSite;
	        $site->hydrate($ri);
	        $instance['site'] = $site->__toArray();
	        
	        if($this->getType() == 'SM_ASSETTYPE_CONTAINER_TEMPLATE'){
	            $assetclass = new SmartestContainer;
            }else{
                $assetclass = new SmartestPlaceholder;
            }
            
            $assetclass->hydrate($ri);
            $instance['assetclass'] = $assetclass->__toArray();
            
            $instances[] = $instance;
            
	    }
	    
	    // print_r($instances);
	    return $instances;
	}
	
	public function getDraftInstances(){
	    
	    $sql = "SELECT DISTINCT Pages.*, Sites.*, AssetIdentifiers.*, AssetClasses.* FROM Pages, Sites, AssetIdentifiers, Assets, AssetClasses WHERE asset_id='".$this->getId()."' AND assetidentifier_draft_asset_id=asset_id AND assetidentifier_page_id=page_id AND page_site_id=site_id AND assetidentifier_assetclass_id=assetclass_id AND page_is_published='TRUE'";
	    
	    if($this->getSite()){
	        $sql .= " AND asset_site_id=site_id AND site_id='".$this->getSite()->getId()."'";
	    }
	    
	    $result = $this->database->queryToArray($sql);
	    
	    $instances = array();
	    
	    foreach($result as $ri){
	        
	        $instance = array();
	        $page = new SmartestPage;
	        $page->hydrate($ri);
	        $instance['page'] = $page->__toArray();
	        
	        $site = new SmartestSite;
	        $site->hydrate($ri);
	        $instance['site'] = $site->__toArray();
	        
	        if($this->getType() == 'SM_ASSETTYPE_CONTAINER_TEMPLATE'){
	            $assetclass = new SmartestContainer;
            }else{
                $assetclass = new SmartestPlaceholder;
            }
            
            $assetclass->hydrate($ri);
            $instance['assetclass'] = $assetclass->__toArray();
            
            $instances[] = $instance;
            
	    }
	    
	    // print_r($instances);
	    return $instances;
	}
	
	public function delete(){
	    
	    $this->setDeleted(1);
	    
	    if($this->usesLocalFile()){
	        // move the file to 
	        $deleted_path = SmartestFileSystemHelper::getUniqueFileName(SM_ROOT_DIR.'Documents/Deleted/'.$this->getUrl());
	        $deleted_filename = basename($deleted_path);
	        $this->setDeletedFilename($deleted_filename);
	        SmartestFileSystemHelper::move($this->getFullPathOnDisk(), $deleted_path);
	    }
	    
	    parent::save();
	    
	}
	
	public function getSize($raw=false){
	    
	    $type_info = $this->getTypeInfo();
	    
	    if($type_info['storage']['type'] == 'database'){
	        
	        $size = mb_strlen($this->getContent());
	        
	        if(!$raw){
	            // size is in bytes
    	        if($size >= 1024){
    	            // convert to kilobytes
    	            $new_size = $size/1024;

    	            if($new_size >= 1024){
    	                // convert to megabytes
    	                $new_size = $new_size/1024;

    	                if($new_size >= 1024){
        	                // convert to gigabytes
        	                $new_size = $new_size/1024;

                            if($new_size >= 1024){
            	                // convert to terrabytes
            	                $new_size = $new_size/1024;
                                $size = number_format($new_size, 3, '.', ',').' TB';
                            }else{
                                $size = number_format($new_size, 2, '.', ',').' GB';
                            }

                        }else{
                            $size = number_format($new_size, 1, '.', ',').' MB';
                        }

                    }else{
                        $size = number_format($new_size, 1, '.', ',').' KB';
                    }

    	        }else{
    	            $size = $size.' Bytes';
    	        }
	        }
	    }else{
	        if($raw){
	            $size = SmartestFileSystemHelper::getFileSize($this->getFullPathOnDisk());
	        }else{
	            $size = SmartestFileSystemHelper::getFileSizeFormatted($this->getFullPathOnDisk());
            }
        }
        
        return $size;
	    
	}
	
	public function setIsDraft($draft){
	    $this->_draft_mode = $draft ? true : false;
	}
	
	public function getIsDraft(){
	    return $this->_draft_mode;
	}
	
	public function getDefaultParameterValues(){
	    if($data = @unserialize($this->getParameterDefaults())){
	        return $data;
	    }else{
	        return $this->getParameterDefaults();
	    }
	}

}