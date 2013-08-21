<?php

class SmartestAsset extends SmartestBaseAsset implements SmartestSystemUiObject, SmartestStorableValue, SmartestSubmittableValue{
    
    protected $_allowed_types = array();
    protected $_draft_mode = false;
    protected $_text_fragment;
    protected $_type_info = null;
    protected $_site;
    protected $_image;
    protected $_save_textfragment_on_save = false;
    protected $_set_textfragment_asset_id_on_save = false;
    protected $_absolute_uri_object;
    
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
	
	public function offsetGet($offset){
        
        switch($offset){
            
            case "text":
            case "text_content":
            return $this->getContent();
            
            case "description":
            case "caption":
            return $this->getDescription();
            
            case "text_fragment":
            return $this->getTextFragment();
            
            case "type_info":
            return $this->getTypeInfo();
            
            case "type":
            return $this->_properties['type'];
            
            case "label":
            return new SmartestString($this->getLabel());
            
            case "default_parameters":
            return $this->getDefaultParams();
            
            case "full_path":
            $type_info = $this->getTypeInfo();
            if($type_info['storage']['type'] == 'database'){
    	        return $this->_properties['url'];
    	    }else{
    	        return $this->getFullPathOnDisk();
            }
            
            case "absolute_uri":
            case "absolute_url":
            return $this->getAbsoluteUri();
            
            case "storage_location":
            return $this->getStorageLocation();
            
            case "is_external":
            return $this->isExternal();
            
            case "web_path":
            if($this->usesLocalFile()){
                return $this->getFullWebPath();
            }
            break;
            
            case "size":
            return $this->getSize();
            
            case "owner":
            $o = new SmartestSystemUser;
	        if($o->find($this->_properties['user_id'])){
	            return $o;
            }else{
                return array();
            }
            
            case "is_image":
            return $this->isImage();
            
            case "is_web_accessible":
            return ($this->isExternal() || $this->isWebAccessible());
            
            case "image":
            return $this->isImage() ? $this->getImage() : null;
            
            case "width":
            return $this->isImage() ? $this->getWidth() : null;
            
            case "height":
            return $this->isImage() ? $this->getHeight() : null;
            
            case "dimensions":
            return $this->isImage() ? $this->getWidth().' x '.$this->getHeight() : null;
            
            case "word_count":
            case "wordcount":
            return $this->getWordCount();
            
            case "text_length":
            return $this->getTextLength();
            
            case "credit":
            return $this->isImage() ? $data['default_parameters']['credit'] : null;
            
            case "groups":
            return $this->getGroups();
            
            case "small_icon":
            return $this->getSmallIcon();
            
            case "large_icon":
            return $this->getLargeIcon();
            
            case "label":
            return $this->getLabel();
            
            case "action_url":
            return $this->getActionUrl();
            
            case "site":
            return $this->getSite();
            
            case "download_link_contents":
            return 'download:'.$this->getUrl();
            
            case "download_uri":
            case "download_url":
            return $this->getAbsoluteDownloadUri();
            
            case "secure_download_uri":
            case "secure_download_url":
            return $this->getAbsoluteDownloadUri(true);
            
            case "link_contents":
            if($this->isImage()){
                return 'image:'.$this->getUrl();
            }else{
                return 'download:'.$this->getUrl();
            }
            break;
            
            case "file_size":
            return $this->getSize();
            
            case "raw_file_size":
            return $this->getSize(true);
            
            case "empty":
            return !is_numeric($this->getId()) || !strlen($this->getId());
            
            case "is_too_large":
            return $this->isTooLarge();
            
        }
        
        return parent::offsetGet($offset);
        
    }
	
	public function __toString(){
	    
	    if($this->_properties['id']){
	        return $this->_properties['label'].' ('.$this->_properties['url'].')';
        }else{
            return '';
        }
        
	}
	
	// The next two methods are for the SmartestStorableValue interface
    public function getStorableFormat(){
        return $this->_properties['id'];
    }
    
    public function hydrateFromStorableFormat($v){
        if(is_numeric($v)){
            return $this->find($v);
        }
    }
    
    // and two from SmartestSubmittableValue
    
    public function renderInput($params){
        
    }
    
    public function hydrateFromFormData($v){
        if(is_numeric($v)){
            return $this->find($v);
        }
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
	
	public function assignSiteFromObject(SmartestSite $site){
	    if($this->getSiteId() == $site->getId()){
	        $this->_site = $site;
	        return true;
	    }else{
	        return false;
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
	
	public function getWordCount(){
	    
	    if($this->usesTextFragment()){
	        return $this->getTextFragment()->getWordCount();
	    }else{
	        return 0;
	    }
	    
	}
	
	public function getTextLength(){
	    
	    if($this->usesTextFragment()){
	        return $this->getTextFragment()->getLength();
	    }else{
	        return 0;
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
	
	public function getMimeType(){
	    
	    $info = $this->getTypeInfo();
	    
	    if(!isset($info['suffix'])){
	        // the file type doesn't have any suffixes, so is probably externally hosted or not a real file type
	        return null;
	    }
	    
	    $suffixes = $info['suffix'];
	    $mysuffix = $this->getDotSuffix();
	    
	    if(count($suffixes) == 1){
	        return $info['suffix'][0]['mime'];
	    }else{
	        foreach($info['suffix'] as $suffix){
	            if($suffix['_content'] == $mysuffix){
	                return $suffix['mime'];
	            }
	        }
	        // if the file's suffix doesn't match any of those listed against its type, there is a problem, but never mind
	        return $info['suffix'][0]['mime'];
	    }
	    
	}
	
	public function usesTextFragment(){
	    
	    $info = $this->getTypeInfo();
	    
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
	    
	    // no text fragment has been retrieved
	    if(!$this->_text_fragment){
	        
	        if($this->usesTextFragment()){
    	        
    	        $tf = new SmartestTextFragment;
    	        
    	        if($this->getFragmentId()){
	                
	                if(!$tf->find($this->getFragmentId())){
	                    
	                    // whoops, this asset doesn't have a text fragment - create one, but log that this was what happened
                        $tf->setAssetId($this->getId());
                        $tf->setCreated(time());
                        $this->setField('fragment_id', $tf->getId());
                        $this->_save_textfragment_on_save = true;
                        SmartestLog::getInstance('system')->log("Text asset '".$this->getLabel()."' with ID ".$this->getId()." did not have an associated TextFragment. A new one was created.");
                    }
                    
                    $this->_text_fragment = $tf;
                    
    	        }else{
    	            
    	            // whoops, this asset doesn't have a text fragment - create one, but log that this was what happened
    	            if($this->getId()){
	                    $tf->setAssetId($this->getId());
                        $tf->setCreated(time());
                        $this->_save_textfragment_on_save = true;
	                    $this->_text_fragment = $tf;
	                    SmartestLog::getInstance('system')->log("Text asset '".$this->getLabel()."' with ID ".$this->getId()." did not have an associated TextFragment. A new one was created.");
                    }else{
                        // this is a new text asset, so it doesn't have an id yet.
                        $this->_text_fragment = $tf;
                        $this->_set_textfragment_asset_id_on_save = true;
                    }
    	        }
    	        
    	        $this->_text_fragment->setAsset($this);
    	        
    	    }else{
    	        return null;
    	    }
	    
        }
        
        return $this->_text_fragment;
	    
	}
	
	public function getContent(){
	    
	    if($this->getTextFragment()){
	        if($this->isParsable()){
	            $string = $this->getTextFragment()->getContent();
            }else{
                $string = htmlspecialchars($this->getTextFragment()->getContent(), ENT_COMPAT, 'UTF-8');
            }
	    }else if($this->isEditable() && is_file($this->getFullPathOnDisk())){
	        $string = SmartestFileSystemHelper::load($this->getFullPathOnDisk(), true);
	    }else{
    	    $string = null;
    	}
    	
    	$s = new SmartestString($string);
    	
    	return $s;
    	
	}
	
	public function setContent($raw_content, $escapeslashes=true){
	    
	    $info = $this->getTypeInfo();
	    
	    $content = $raw_content;
	    
	    if($this->getTextFragment()){
	        // save the text fragment in the database
	        $this->getTextFragment()->setContent($content);
	        $this->_save_textfragment_on_save = true;
	    }else if($this->usesLocalFile() && $this->isEditable()){
	        // save the file to its desired location
	        SmartestFileSystemHelper::save($this->getFullPathOnDisk(), $content, true);
	    }else{
	        // what happens here?
	        // probably nothing as it's just not the right type of asset. Just log and move on
	        SmartestLog::getInstance('system')->log('SmartestAsset::setContent() called on a non-editable asset ('.$this->getId().')');
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
	    
	    if($this->usesLocalFile()){
	        return SM_ROOT_DIR.$this->getStorageLocation().$this->getUrl();
	    }else{
	        return null;
	    }
	    
	}
	
	public function getStorageLocation($include_smartest_root=false){
	    
	    $root = $include_smartest_root ? SM_ROOT_DIR : null;
	    
	    if($this->usesLocalFile()){
	        if($this->getDeleted()){
	            return $root.'Documents/Deleted/';
	        }else{
	            $info = $this->getTypeInfo();
	            return $root.$info['storage']['location'];
            }
	    }else{
	        return null;
	    }
	    
	}
	
	public function isWebAccessible(){
	    $info = $this->getTypeInfo();
	    return $this->usesLocalFile() && substr($info['storage']['location'], 0, strlen('Public/')) == 'Public/';
	}
	
	public function getFullWebPath(){
	    
	    $info = $this->getTypeInfo();
	    
	    if($this->isWebAccessible()){
	        return $this->_request->getDomain().substr($info['storage']['location'], strlen('Public/')).$this->getUrl();
	    }else{
	        return null;
	    }
	    
	}
	
	public function isExternal(){
	    $info = $this->getTypeInfo();
	    return ($info['storage']['type'] == 'external_translated');
	}
	
	public function getAbsoluteUri(){
	  
	    if($this->isExternal()){
	        return new SmartestExternalUrl($this->getUrl());
	    }else{
	        if($this->isWebAccessible()){
	            return new SmartestExternalUrl('http://'.$this->getSite()->getDomain().$this->getFullWebPath());
	        }
	    }
	    
	}
	
	public function isImage(){
	    return in_array($this->getType(), array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'));
	}
	
	public function isTemplate(){
	    $alh = new SmartestAssetsLibraryHelper;
	    return in_array($this->getType(), $alh->getTypeIdsInCategories('templates'));
	}
	
	public function isTooLarge(){
	    if($this->isImage()){
	        return $this->getImage()->isTooLarge();
	    }
	}
	
	public function getImage(){
	    if($this->isImage()){
	        if(!$this->_image){
		        $this->_image = new SmartestImage;
	            $this->_image->loadFile($this->getFullPathOnDisk());
	        }
		    return $this->_image;
		}
	}
	
	public function getStoreMethodName(){
	    return 'store'.SmartestStringHelper::toCamelCase(substr($this->getType(), 13)).'Asset';
	}
	
	public function getParseMethodName(){
	    return 'parse'.SmartestStringHelper::toCamelCase(substr($this->getType(), 13)).'Asset';
	}
	
	public function getConvertMethodName(){
	    $info = $this->getTypeInfo();
	    
	    if($info['convert_to_smarty']){
	        return 'convert'.SmartestStringHelper::toCamelCase(substr($this->getType(), 13)).'AssetToSmartyFile';
        }else{
            return 'convert'.SmartestStringHelper::toCamelCase(substr($this->getType(), 13)).'Asset';
        }
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
	        
	        $default_serialized_data = $this->getParameterDefaults();
	        
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
	    
	    return $params;
	    
	}
	
	public function getDescription(){
	    return $this->getField('search_field');
	}
	
	public function setDescription($description){
	    return $this->setField('search_field', $description);
	}
	
	public function getDownloadableFilename(){
	    
	    if($this->usesLocalFile()){
	        return $this->getUrl();
	    }else{
	        $info = $this->getTypeInfo();
	        
	        if(count($info['suffix'])){
	            $dot_suffix = $info['suffix'][0]['_content'];
	        }else{
	            // no suffix found - use txt and log this
	            $dot_suffix = 'txt';
	        }
	        
	        $file_name = strlen($this->getStringid()) ? $this->getStringid() : 'asset';
	        $file_name .= '.'.$dot_suffix;
	        
	        return $file_name;
	        
	    }
	}
	
	public function getDotSuffix(){
	    $alh = new SmartestAssetsLibraryHelper;
	    preg_match($alh->getSuffixTestRegex($this->getType()), $this->getUrl(), $matches);
	    return substr($matches[1], 1);
	}
	
	public function getDownloadUrl(){
	    return $this->_request->getDomain().'download/'.$this->getUrl().'?key='.$this->getWebid();
	}
	
	public function getAbsoluteDownloadUri($secure=false){
	    if(!$this->_absolute_uri_object){
	        $protocol = $secure ? 'https://' : 'http://';
	        if($this->isExternal()){
    	        $this->_absolute_uri_object = new SmartestExternalUrl($this->getUrl());
    	    }else{
	            $this->_absolute_uri_object = new SmartestExternalUrl($protocol.$this->getSite()->getDomain().$this->getDownloadUrl());
            }
        }
        return $this->_absolute_uri_object;
	}
	
	public function save(){
	    
	    parent::save();
	    
	    if($this->usesTextFragment()){
	    
	        $tf = $this->getTextFragment();
	        
	        if($this->_set_textfragment_asset_id_on_save){
	            $tf->setAssetId($this->getId());
	        }
	        
	        if($this->_set_textfragment_asset_id_on_save || $this->_save_textfragment_on_save || !$tf->getId()){
	            $tf->save();
	            if($this->getFragmentId() != $tf->getId()){
	                $this->setFragmentId($tf->getId());
	            }
	        }
	        
	        /* if($tf->getId()){
	            // the textfragment already exists in the database
	            $this->setFragmentId($this->getTextFragment()->getId());
	            $tf->save();
	        }else{
	            // the textfragment is a new object
	            $tf->setAssetId($this->getId());
	            $tf->save();
	            $this->setFragmentId($tf->getId());
	            parent::save();
	        } */
	        
    	    /* if(!$this->getFragmentId()){
	            // this asset isn't linked
	            $this->getTextFragment()->setAssetId($this->getId());
	            $this->getTextFragment()->save();
	            
	            parent::save();
	        }else{
	            $this->getTextFragment()->save();
	        } */
    	    
	    
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
	        $instance['page'] = $page;
	        
	        $site = new SmartestSite;
	        $site->hydrate($ri);
	        $instance['site'] = $site;
	        
	        if($this->getType() == 'SM_ASSETTYPE_CONTAINER_TEMPLATE'){
	            $assetclass = new SmartestContainer;
            }else{
                $assetclass = new SmartestPlaceholder;
            }
            
            $assetclass->hydrate($ri);
            $instance['assetclass'] = $assetclass;
            
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
	        $instance['page'] = $page;
	        
	        $site = new SmartestSite;
	        $site->hydrate($ri);
	        $instance['site'] = $site;
	        
	        if($this->getType() == 'SM_ASSETTYPE_CONTAINER_TEMPLATE'){
	            $assetclass = new SmartestContainer;
            }else{
                $assetclass = new SmartestPlaceholder;
            }
            
            $assetclass->hydrate($ri);
            $instance['assetclass'] = $assetclass;
            
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
	        $this->setUrl($deleted_filename);
	        SmartestFileSystemHelper::move($this->getFullPathOnDisk(), $deleted_path);
	    }
	    
	    parent::save();
	    
	}
	
	public function duplicate($name, $site_id=null, $pointer_only=false){
	    
	    $dup = $this->duplicateWithoutSaving();
	    
	    // Check the name is unique
	    if(is_numeric($site_id)){
	        $sql = "SELECT asset_stringid, asset_label from Assets WHERE asset_site_id='".$site_id."' OR asset_shared='1'";
	    }else{
	        $sql = "SELECT asset_stringid, asset_label from Assets WHERE asset_site_id='".$this->getSiteId()."' OR asset_shared='1'";
	    }
	    
	    $dup->setWebId(SmartestStringHelper::random(32));
	    $existing_names_labels = $this->database->queryFieldsToArrays(array('asset_label', 'asset_stringid'), $sql);
	    
	    $stringid = SmartestStringHelper::guaranteeUnique(SmartestStringHelper::toVarName($name), $existing_names_labels['asset_stringid'], '_');
	    $label    = SmartestStringHelper::guaranteeUnique($name, $existing_names_labels['asset_label'], ' ');
	    
	    $dup->setStringId($stringid);
	    $dup->setLabel($label);
	    
	    $info = $this->getTypeInfo();
	    
	    if($info['storage']['type'] == 'file'){
	        // if the storage is a file on the disk, copy that file and get the new file's name on disk
	        if($pointer_only){
	            
	        }else{
	            $new_filename_full = SmartestFileSystemHelper::getUniqueFileName($this->getFullPathOnDisk());
	            
	            if(SmartestFileSystemHelper::copy($this->getFullPathOnDisk(), $new_filename_full)){
	                $new_filename = basename($new_filename_full);
	                $dup->setUrl($new_filename);
	            }else{
	                return false;
	            }
	            
	        }
	        
	    }else if($info['storage']['type'] == 'database'){
	        
	        // otherwise if the file is stored as a text fragment, copy that and get the new text fragment's ID
	        $textfragment = $this->getTextFragment()->duplicate();
	        
	        // Set the new textfragment ID
	        $this->setFragmentId($textfragment->getId());
	        $dup->setUrl($dup->getStringId().'.'.$info['suffix'][0]['_content']);
	        
	    }
	    
	    if(is_numeric($site_id) && $site_id != $this->getSiteid()){
	        $dup->setSiteId($site_id);
	    }
	    
	    // copy many-to-many data, such as authors
	    
	    // save the duplicate
	    $dup->save();
	    
	    if($info['storage']['type'] == 'database'){
	        $textfragment->setAssetId($dup->getId());
	        $textfragment->save();
        }
	    
	    // print_r($this->getGroupMemberships());
	    foreach($this->getGroupMemberships() as $membership){
	        
	        if($group = $membership->getGroup()){
	            if($group->getSiteId() != $site_id){
	                $group->setShared(1);
	            }
	        }
	        
	        $nm = $membership->duplicateWithoutSaving();
	        $nm->setAssetId($dup->getId());
	        $nm->save();
	        
	    }
	    
	    return $dup;
	    
	}
	
	public function getOtherPointers(){
	    
	    $info = $this->getTypeInfo();
	    
	    if($info['storage']['type'] == 'file'){
	        $sql = "SELECT Assets.*, Sites.* FROM Assets, Sites WHERE Sites.site_id=Assets.asset_site_id AND Assets.asset_id != '".$this->getId()."' AND Assets.asset_url = '".addslashes($this->getUrl())."' AND Assets.asset_type = '".$this->getType()."' AND Assets.asset_deleted != '1' AND Assets.asset_is_hidden != '1'";
	        $result = $this->database->queryToArray($sql);
	        
	        $pointers = array();
	        
	        foreach($result as $r){
	            $a = new SmartestAsset;
	            $s = new SmartestSite;
	            $a->hydrate($r);
	            $s->hydrate($r);
	            $a->assignSiteFromObject($s);
	            $pointers[] = $a;
	        }
	        
	        return $pointers;
	        
	    }else{
	        return array();
	    }
	    
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
	
	public function getPossibleOwners(){
	    
	    return $this->getSite()->getUsersThatHaveAccess();
	    
	}
	
	public function getGroupMemberships($refresh=false, $mode=1, $approved_only=false){
        
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_ASSET_GROUP_MEMBERSHIP');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
	    
	    $q->addSortField(SM_MTM_SORT_GROUP_ORDER);
	    
	    $result = $q->retrieve(true);
        
        return $result;
        
    }
    
    public function getGroupIds(){
        
        $memberships = $this->getGroupMemberships();
        $gids = array();
        
        foreach($memberships as $m){
	        $gids[] = $m->getGroupId();
	    }
        
        return $gids;
        
    }
	
	public function getGroups(){
	    
	    $memberships = $this->getGroupMemberships();
	    $groups = array();
	    
	    foreach($memberships as $m){
	        $groups[] = $m->getGroup();
	    }
	    
	    return $groups;
	    
	}
	
	public function getPossibleGroups(){
	    
	    $alh = new SmartestAssetsLibraryHelper;
	    $groups = $alh->getAssetGroupsThatAcceptType($this->getType(), $this->getSiteId());
	    
	    $existing_group_ids = $this->getGroupIds();
	    
	    foreach($groups as $k => $g){
	        if(in_array($g->getId(), $existing_group_ids)){
	            unset($groups[$k]);
	        }
	    }
	    
	    return $groups;
	    
	}
	
	public function addToGroupById($gid, $force=false){
        
        if($force || !in_array($gid, $this->getGroupIds())){
            
            $m = new SmartestAssetGroupMembership;
            $m->setGroupId($gid);
            $m->setAssetId($this->getId());
            $m->save();
            
        }
        
    }
	
	public function getComments(){
	    
	    $sql = "SELECT * FROM Comments, Users WHERE comment_type='SM_COMMENTTYPE_ASSET_PRIVATE' AND comment_object_id='".$this->getId()."' AND comment_author_user_id=user_id ORDER BY comment_posted_at";
	    $result = $this->database->queryToArray($sql);
	    
	    $comments = array();
	    
	    foreach($result as $r){
	        $c = new SmartestAssetComment;
	        $c->hydrate($r);
	        $comments[] = $c;
	    }
	    
	    return $comments;
	    
	}
	
    public function addComment($content, $user_id){
        
        $comment = new SmartestAssetComment;
        $comment->setAuthorUserId($user_id);
        $comment->setContent($content);
        $comment->setAssetId($this->getId());
        $comment->setPostedAt(time());
        
        $comment->save();
        
    }
    
    public function clearRecentlyEditedInstances($site_id, $user_id=''){
	    
	    $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_RECENTLY_EDITED_ASSETS');
	    
	    $q->setTargetEntityByIndex(1);
	    
        $q->addQualifyingEntityByIndex(1, $this->getId());
        $q->addQualifyingEntityByIndex(3, (int) $site_id);
        
        if(is_numeric($user_id)){
            $q->addQualifyingEntityByIndex(2, $user_id);
        }
        
        $q->delete();
	    
	}
	
	public function setStringId($stringid, $site_id=''){
	    
	    if($this->_properties['id']){
	        $sql = "SELECT asset_stringid FROM Assets WHERE (asset_site_id='".$this->getSiteId()."' OR asset_shared='1') AND asset_id != '".$this->getId()."'"; 
	    }else{
	        if($site_id){
	            $sql = "SELECT asset_stringid FROM Assets WHERE (asset_site_id='".$site_id."' OR asset_shared='1')"; 
	        }else{
	            $sql = "SELECT asset_stringid FROM Assets"; 
	        }
	    }
	    
	    $fields = $this->database->queryFieldsToArrays(array('asset_stringid'), $sql);
        $stringid = SmartestStringHelper::guaranteeUnique($stringid, $fields['asset_stringid'], '_');
        
        return parent::setStringId($stringid);
	    
	}
	
	// System UI calls
	
	public function getSmallIcon(){
	    
	    $info = $this->getTypeInfo();
	    
	    if(isset($info['icon']) && is_file(SM_ROOT_DIR.'Public/Resources/Icons/'.$info['icon'])){
	        return $this->_request->getDomain().'Resources/Icons/'.$info['icon'];
	    }else{
	        return $this->_request->getDomain().'Resources/Icons/page_white.png';
	    }
	    
	}
	
	public function getLargeIcon(){
	    
	}
	
	public function getLabel(){
	    
	    return parent::getLabel() ? $this->_properties['label'] : $this->getStringId();
	    
	}
	
	public function getActionUrl(){
	    
	    // return $this->_request->getDomain().'assets/editAsset?asset_id='.$this->getId();
	    return $this->_request->getDomain().'smartest/file/edit/'.$this->getId();
	    
	}
	
	public function tag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->find($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->findBy('name', $tag_name)){
                // create tag
    	        $tag->setLabel($tag_identifier);
    	        $tag->setName($tag_name);
    	        $tag->save();
    	    }
	    }
	    
	    $sql = "INSERT INTO TagsObjectsLookup (taglookup_tag_id, taglookup_object_id, taglookup_type) VALUES ('".$tag->getId()."', '".$this->_properties['id']."', 'SM_ASSET_TAG_LINK')";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}
	
	public function untag($tag_identifier){
	    
	    if(is_numeric($tag_identifier)){
	        
	        $tag = new SmartestTag;
	        
	        if(!$tag->hydrate($tag_identifier)){
	            // kill it off if they are supplying a numeric ID which doesn't match a tag
	            return false;
	        }
	        
	    }else{
	        
	        $tag_name = SmartestStringHelper::toSlug($tag_identifier);
	        
	        $tag = new SmartestTag;

    	    if(!$tag->hydrateBy('name', $tag_name)){
                return false;
    	    }
	    }
	    
	    $sql = "DELETE FROM TagsObjectsLookup WHERE taglookup_object_id='".$this->_properties['id']."' AND taglookup_tag_id='".$tag->getId()."' AND taglookup_type='SM_ASSET_TAG_LINK'";
	    $this->database->rawQuery($sql);
	    return true;
	    
	}

}