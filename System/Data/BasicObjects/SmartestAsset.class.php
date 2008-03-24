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
	
	public function __toArray(){
	    $data = parent::__toArray();
	    $data['text_content'] = $this->getContent();
	    $data['type_info'] = $this->getTypeInfo();
	    $data['default_parameters'] = $this->getDefaultParams();
	    $data['full_path'] = $this->getFullPathOnDisk();
	    
	    if($this->isImage()){
            $data['width'] = $this->getWidth();
            $data['height'] = $this->getHeight();
        }
        
	    return $data;
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
	        return stripslashes($this->getTextFragment()->getContent());
	    }else if($this->isEditable() && is_file($this->getFullPathOnDisk())){
	        return SmartestFileSystemHelper::load($this->getFullPathOnDisk(), true);
	    }else{
    	    return null;
    	}
	}
	
	public function setContent($raw_content, $escapeslashes=true){
	    
	    $info = $this->getTypeInfo();
	    
	    if($escapeslashes && !$this->usesLocalFile()){
	        $content = addslashes($raw_content);
	    }else{
	        $content = $raw_content;
	    }
	    
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
	
	/* public function getDisplayParameters(){
	    
	    $info = $this->getTypeInfo();
	    // return 
	    // print_r($info);
	    
	}
	
	public function getParsableFilePath($draft_mode=false){
	    if($draft_mode){
	        $file_path = SM_ROOT_DIR.'System/Cache/TextFragments/Previews/tfpreview_'.SmartestStringHelper::toHash($this->getTextFragment()->getId(), 8, 'SHA1').'.tmp.tpl';
	    }else{
	        $file_path = SM_ROOT_DIR.'System/Cache/TextFragments/Live/tflive_'.SmartestStringHelper::toHash($this->getTextFragment()->getId(), 8, 'SHA1').'.tpl';
	    }
	    
	    return $file_path;
	}
	
	public function publish(){
	    if($this->isParsable() && $this->usesTextFragment()){
	        return SmartestFileSystemHelper::save($this->getParsableFilePath(), $this->getTextFragment()->getContent(), true);
	    }
	}
	
	public function isPublished(){
	    return file_exists($this->getParsableFilePath());
	}
	
	public function createPreviewFile(){
	    if($this->isParsable() && $this->usesTextFragment()){
	        $result = SmartestFileSystemHelper::save($this->getParsableFilePath(true), $this->getTextFragment()->getContent(), true);
	        // print_r($this->getTextFragment()->getContent());
	        // $result = file_put_contents($this->getParsableFilePath(true), $this->getTextFragment()->getContent());
	        var_dump($result);
	        return $result;
	        // return true;
	    }else{
	        return false;
	    }
	}
	
	public function ensurePreviewFileExists(){
	    if(!file_exists($this->getParsableFilePath(true))){
	        return $this->createPreviewFile();
	    }else{
	        return true;
	    }
	} */
	
	/* public function renderMarkupInTextFragment($draft_mode=false){
	    if($draft_mode){
	        $file_path = SM_ROOT_DIR.'System/Cache/TextFragments/Previews/tfpreview_'.$this->getTextFragment()->getId().'.tmp.tpl';
	    }else{
	        $file_path = SM_ROOT_DIR.'System/Cache/TextFragments/Live/tfpreview_'.$this->getTextFragment()->getId().'.tmp.tpl';
	    }
	} */
	
	/* public function renderAsMarkup($draft_mode=false, $params=''){
	    
	    // TODO: 3rd-party media types
	    
	    switch($this->getType()){
	        
	        case "SM_ASSETTYPE_RICH_TEXT":
            $markup = stripslashes($this->getTextFragment()->getContent());
            break;
            
            case "SM_ASSETTYPE_PLAIN_TEXT":
            case "SM_ASSETTYPE_SL_TEXT":
            $markup = htmlentities(stripslashes($this->getTextFragment()->getContent()), ENT_COMPAT, 'UTF-8');
            break;
	        
	        case "SM_ASSETTYPE_JAVASCRIPT":
	        $markup = '<script language="javascript" type="text/javascript" src="'.SM_CONTROLLER_DOMAIN.'Resources/Javascript/'.$this->getUrl().'"></script>';
	        break;
	        
	        case "SM_ASSETTYPE_STYLESHEET":
	        $markup = '<link rel="stylesheet" href="'.SM_CONTROLLER_DOMAIN.'Resources/Stylesheets/'.$this->getUrl().'" />';
	        break;
	        
	        case "SM_ASSETTYPE_QUICKTIME_MOVIE":
	        
	        $full_path = SM_CONTROLLER_DOMAIN.'Resources/Assets/'.$this->getUrl();
	        $autostart = $this->getAutoPlay() ? "true" : "false";
	        
	        $markup = "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" width=\"{$this->getWidth()}\" height=\"{$this->getHeight()}\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\">
            <param name=\"src\" value=\"{$full_path}\" />
            <param name=\"controller\" value=\"true\" />
            <param name=\"target\" value=\"myself\" />
            <param name=\"autoplay\" value=\"true\" />
            <param name=\"qtsrc\" value=\"{$full_path}\" />
            <param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\" />
            <embed controller=\"true\" width=\"{$this->getWidth()}\" height=\"{$this->getHeight()}\" target=\"myself\" 
            qtsrc=\"{$full_path}\" 
            src=\"{$full_path}\" 
            bgcolor=\"#FFFFFF\" border=\"0\" autoplay=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/indext.html\"></embed>		
            </object>";
            
	        break;
	        
	        case "SM_ASSETTYPE_MPEG_MOVIE":
	        
	        break;
	        
	        case "SM_ASSETTYPE_SHOCKWAVE_FLASH":
	        
	        break;
	        
	        case "SM_ASSETTYPE_WMV":
	        
	        $full_path = SM_CONTROLLER_DOMAIN.'Resources/Assets/'.$this->getUrl();
	        $autostart = $this->getAutoPlay() ? "True" : "False";
	        
	        $markup = "<object width=\"{$this->getWidth()}\" height=\"{$this->getHeight()}\" classid=\"CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95\" id=\"mediaplayer1\">
            <param name=\"Filename\" value=\"{$full_path}\">
            <param name=\"AutoStart\" value=\"{$autostart}\">
            <param name=\"ShowControls\" value=\"True\">
            <param name=\"ShowStatusBar\" value=\"False\">
            <param name=\"ShowDisplay\" value=\"False\">
            <param name=\"AutoRewind\" value=\"False\">
            <embed type=\"application/x-mplayer2\" pluginspage=\"http://www.microsoft.com/Windows/Downloads/Contents/MediaPlayer/\" width=\"{$this->getWidth()}\" height=\"{$this->getHeight()}\" src=\"{$full_path}\" filename=\"{$full_path}\" autostart=\"True\" showcontrols=\"True\" showstatusbar=\"False\" showdisplay=\"False\" autorewind=\"True\"></embed> 
            </object>";
	        
	        break;
	    }
	    
	    return $markup;
	} */
	
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
	
	public function setIsDraft($draft){
	    $this->_draft_mode = $draft ? true : false;
	}
	
	public function getIsDraft(){
	    return $this->_draft_mode;
	}

}