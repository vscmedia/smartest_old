<?php

class SmartestTextFragmentAttachment extends SmartestManyToManyLookup{
    
    protected $_defined = false;
    protected $_asset;
    protected $_textFragment;
    
    public function getTextFragmentId(){
        return $this->getEntityForeignKeyValue(1);
    }
    
    public function setTextFragmentId($id){
        $this->setEntityForeignKeyValue(1, (int) $id);
    }
    
    public function getAttachedAssetId(){
        return $this->getEntityForeignKeyValue(2);
    }
    
    public function setAttachedAssetId($id){
        $this->setEntityForeignKeyValue(2, (int) $id);
    }
    
    public function hydrate($id){
        
        parent::hydrate($id);
        
        if(is_array($id) && isset($id['asset_id'])){
            $a = new SmartestAsset;
            $a->hydrate($id);
            $this->_asset = $a;
        }
        
        if(is_array($id) && isset($id['textfragment_id'])){
            $tf = new SmartestTextFragment;
            $tf->hydrate($id);
            $this->_textFragment = $tf;
        }
        
    }
    
    public function __toArray(){
        
        $data = parent::__toArray();
        $data['status'] = $this->hasAsset() ? 'DEFINED' : 'UNDEFINED';
        
        if($this->hasAsset()){
            $data['caption'] = $this->getCaption();
            $data['alignment'] = $this->getAlignment();
            $data['caption_alignment'] = $this->getCaptionAlignment();
            $data['float'] = $this->getFloat();
            $data['border'] = $this->getBorder();
            $data['asset'] = $this->_asset->__toArray();
        }
        
        return $data;
        
    }
    
    public function setAttachmentName($name){
        $this->setInstanceName($name);
    }
    
    public function getAttachmentName(){
        return $this->getInstanceName($name);
    }
    
    public function getCaption(){
        return $this->getContextDataField('caption');
    }
    
    public function setCaption($caption){
        $this->setContextDataField('caption', mysql_real_escape_string($caption));
    }
    
    public function getAlignment(){
        return $this->getContextDataField('align');
    }
    
    public function setAlignment($align){
        
        $align = strtolower($align);
        
        if(!in_array($align, array('left', 'center', 'right'))){
            $align = 'left';
        }
        
        $this->setContextDataField('align', $align);
    }
    
    public function getCaptionAlignment(){
        return $this->getContextDataField('caption_align');
    }
    
    public function setCaptionAlignment($align){
        
        $align = strtolower($align);
        
        if(!in_array($align, array('left', 'center', 'right'))){
            $align = 'left';
        }
        
        $this->setContextDataField('caption_align', $align);
    }
    
    public function getFloat(){
        return $this->getContextDataField('float');
    }
    
    public function setFloat($float){
        $this->setContextDataField('float', SmartestStringHelper::toRealBool($float));
    }
    
    public function getBorder(){
        return $this->getContextDataField('border');
    }
    
    public function setBorder($border){
        $this->setContextDataField('border', SmartestStringHelper::toRealBool($border));
    }
    
    public function hasAsset(){
        return (is_object($this->_asset) && $this->_asset->getUrl());
    }
    
    public function getAsset(){
        return $this->_asset;
    }
    
    public function hasTextFragment(){
        return (is_object($this->_textFragment) && $this->_textFragment->getId());
    }
    
    public function getTextFragment(){
        return $this->_textFragment;
    }
    
    public function save(){// The only team in North London.
        
        if(!$this->getType()){
            $this->setType('SM_MTMLOOKUP_TEXTFRAGMENT_ATTACHMENTS');
        }
        
        parent::save();
        
    }
    
}