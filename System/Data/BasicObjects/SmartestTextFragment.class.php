<?php

class SmartestTextFragment extends SmartestBaseTextFragment{
    
    protected function __objectConstruct(){
        
        $this->_table_prefix = 'textfragment_';
		$this->_table_name = 'TextFragments';
        
    }
    
    public function getAttachments(){
        
        $attachment_names = $this->parseAttachmentNames();
        $attachments = array();
        
        // print_r($attachment_names);
        
        foreach($attachment_names as $a){
            $attachments[$a] = '';
        }
        
        // look up any defined attachments
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
        
        foreach($attachment_names as $a){
            $q->addAllowedInstanceName($a);
        }
        
        $q->addForeignTableConstraint('Assets.asset_deleted', 1, SmartestQuery::NOT_EQUAL);
        $results = $q->retrieve();
        
        foreach($attachment_names as $a){
          
            if(array_key_exists($a, $results)){
                $attachments[$a] = $results[$a];
            }else{
                $attachments[$a] = new SmartestTextFragmentAttachment;
                $attachments[$a]->setInstanceName($a);
            }
        }
        
        return $attachments;
        
    }
    
    public function getAttachmentsAsArrays($include_objects=false){
        
        $attachments = $this->getAttachments();
        $arrays = array();
        
        foreach($attachments as $name => $object){
            
            $arrays[$name] = $object->__toArray();
            $arrays[$name]['_name'] = $name;
            
        }
        
        return $arrays;
        
    }
    
    public function getAttachmentsForElementsTree($level, $version){
        if($version == 'draft'){
            
            $attachments = $this->getAttachmentsAsArrays(true);
            $children = array();
            
            foreach($attachments as $key=>$a){
                
                $child = array();
                $child['info']['asset_id'] = $a['asset']['id'];
                $child['info']['asset_webid'] = $a['asset']['webid'];
                $child['info']['asset_type'] = $a['asset']['type'];
                $child['info']['assetclass_name'] = $key;
                $child['info']['assetclass_id'] = $key;
                $child['info']['defined'] = 'PUBLISHED';
                $child['info']['exists'] = 'true';
                $child['info']['filename'] = '';
                $child['info']['type'] = 'attachment';
                
                $child['asset_object'] = $a['asset_object'];
                $children[] = $child;
            }
            
            return $children;
            
        }else{
            return array();
        }
    }
    
    public function parseAttachmentNames(){
        $regexp = preg_match_all('/<\?sm:attachment.+?name="([\w_-]+)"/', $this->_properties['content'], $matches);
        $attachment_names = array();
        
        foreach($matches[1] as $an){
            $n = SmartestStringHelper::toVarName($an);
            if(!in_array($n, $attachment_names)){
                $attachment_names[] = $n;
            }
        }
        
        return $attachment_names;
    }
    
    public function getAttachmentCurrentDefinition($attachment_name){
        
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
        $q->addAllowedInstanceName($attachment_name);
        $q->addForeignTableConstraint('Assets.asset_deleted', 1, SmartestQuery::NOT_EQUAL);
        
        $results = array_values($q->retrieve());
        
        if(count($results)){
            $def = $results[0];
            if($def instanceof SmartestTextFragmentAttachment){
                return $def;
            }else{
                return new SmartestTextFragmentAttachment;
            }
        }else{
            return new SmartestTextFragmentAttachment;
        }
    }
    
    public function attachmentIsDefined($attachment_name){
        $q = new SmartestManyToManyQuery('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
        $q->setTargetEntityByIndex(2);
        $q->addQualifyingEntityByIndex(1, $this->getId());
        $q->addAllowedInstanceName($attachment_name);
    }
    
    public function getDisplayParameters(){
	    
	    $info = $this->getTypeInfo();
	    
	}
	
	public function getParsableFilePath($draft_mode=false){
	    
	    if($draft_mode){
	        $file_path = SM_ROOT_DIR.'System/Cache/TextFragments/Previews/tfpreview_'.SmartestStringHelper::toHash($this->getId(), 8, 'SHA1').'.tmp.tpl';
	    }else{
	        $file_path = SM_ROOT_DIR.'System/Cache/TextFragments/Live/tflive_'.SmartestStringHelper::toHash($this->getId(), 8, 'SHA1').'.tpl';
	    }
	    
	    return $file_path;
	}
	
	public function publish(){
	    return SmartestFileSystemHelper::save($this->getParsableFilePath(), $this->getContent(), true);
	}
	
	public function isPublished(){
	    return file_exists($this->getParsableFilePath());
	}
	
	public function createPreviewFile(){
	    $result = SmartestFileSystemHelper::save($this->getParsableFilePath(true), stripslashes($this->getContent()), true);
	    return $result;
	}
	
	public function ensurePreviewFileExists(){
	    if(!file_exists($this->getParsableFilePath(true))){
	        return $this->createPreviewFile();
	    }else{
	        return true;
	    }
	}
	
	public function getContent(){
	    return $this->_getContent();
	}
	
	public function setContent($content){
	    return $this->_setContent($content);
	}
	
	protected function _getContent(){
	    return $this->_properties['content'];
	}
	
	protected function _setContent($content){
	    return $this->setField('content', $content);
	}
	
	public function save(){
	    
	    $this->setModified(time());
	    $this->createPreviewFile();
	    
	    parent::save();
	    
	}
    
}