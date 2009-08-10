<?php

class SmartestAsset extends SmartestBaseAsset implements SmartestSystemUiObject{
    
    protected $_allowed_types = array();
    protected $_draft_mode = false;
    protected $_text_fragment;
    protected $_type_info = null;
    protected $_site;
    protected $_image;
    
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
            
            case "text_content":
            return $this->getContent();
            
            case "type_info":
            return $this->getTypeInfo();
            
            case "default_parameters":
            return $this->getDefaultParams();
            
            case "full_path":
            $type_info = $this->getTypeInfo();
            if($type_info['storage']['type'] == 'database'){
    	        return $this->_properties['url'];
    	    }else{
    	        return $this->getFullPathOnDisk();
            }
            
            case "web_path":
            if($this->usesLocalFile()){
                return $this->getFullWebPath();
            }
            
            case "size":
            return $this->getSize();
            
            case "owner":
            $o = new SmartestUser;
	        if($o->hydrate($this->_properties['user_id'])){
	            return $o->__toArray();
            }else{
                return array();
            }
            
            case "is_image":
            return $this->isImage();
            
            case "image":
            return $this->isImage() ? $this->getImage() : null;
            
            case "width":
            return $this->isImage() ? $this->getWidth() : null;
            
            case "height":
            return $this->isImage() ? $this->getHeight() : null;
            
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
            
        }
        
        return parent::offsetGet($offset);
        
    }
	
	public function __toString(){
	    
	    if($this->_properties['id']){
	        return $this->_properties['stringid'].' ('.$this->_properties['url'].')';
        }else{
            return '';
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
	    
	    // no text fragment has been retrieved
	    if(!$this->_text_fragment){
	        
	        if($this->usesTextFragment()){
    	        
    	        $tf = new SmartestTextFragment;
    	        
    	        // echo $this->getFragmentId();
    	        
    	        if($this->getFragmentId()){
	                
	                if(!$tf->hydrate($this->getFragmentId())){
	                    
	                    // echo "No Fragment";
	                    
	                    // whoops, this asset doesn't have a text fragment - create one, but log that this was what happened
                        $tf->setAssetId($this->getId());
                        $tf->setCreated(time());
                        // $tf->save();
                        $this->setField('fragment_id', $tf->getId());
                    }
                    
                    $this->_text_fragment = $tf;
                    
    	        }else{
    	            
    	            // whoops, this asset doesn't have a text fragment - create one, but log that this was what happened
    	            if($this->getId()){
	                    $tf->setAssetId($this->getId());
                        $tf->setCreated(time());
	                    // $tf->save();
                        // $this->setField('FragmentId', $tf->getId());
                        // $this->save();
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
	    
	    $info = $this->getTypeInfo();
	    
	    if($this->usesLocalFile()){
	        if($this->getDeleted()){
	            return SM_ROOT_DIR.'Documents/Deleted/'.$this->getUrl();
	        }else{
	            return SM_ROOT_DIR.$info['storage']['location'].$this->getUrl();
            }
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
	
	public function getImage(){
	    if($this->isImage()){
	        if(!$this->_image){
		        $this->_image = new SmartestImage;
	            $this->_image->loadFile($this->getFullPathOnDisk());
	        }
		    return $this->_image;
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
	    
	    if($this->usesTextFragment()){
	    
	        $tf = $this->getTextFragment();
	        
	        if($tf->getId()){
	            // the textfragment already exists in the database
	            $this->setFragmentId($this->getTextFragment()->getId());
	            $tf->save();
	        }else{
	            // the textfragment is a new object
	            $tf->setAssetId($this->getId());
	            $tf->save();
	            $this->setFragmentId($tf->getId());
	            parent::save();
	        }
	        
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
	        $this->setUrl($deleted_filename);
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
	    $groups = $alh->getAssetGroupsThatAcceptType($this->getType());
	    
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
	
	// System UI calls
	
	public function getSmallIcon(){
	    
	    $info = $this->getTypeInfo();
	    
	    if(isset($info['icon']) && is_file(SM_ROOT_DIR.'Public/Resources/Icons/'.$info['icon'])){
	        return SM_CONTROLLER_DOMAIN.'Resources/Icons/'.$info['icon'];
	    }else{
	        return SM_CONTROLLER_DOMAIN.'Resources/Icons/page_white.png';
	    }
	    
	}
	
	public function getLargeIcon(){
	    
	    
	    
	}
	
	public function getLabel(){
	    
	    return $this->getUrl();
	    
	}
	
	public function getActionUrl(){
	    
	    return SM_CONTROLLER_DOMAIN.'assets/editAsset?asset_id='.$this->getId();
	    
	}

}