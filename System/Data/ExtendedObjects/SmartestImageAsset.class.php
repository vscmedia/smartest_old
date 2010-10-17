<?php

class SmartestImageAsset extends SmartestAsset{
    
    protected $_image = null;
    
    public function getThumbnail($max_width, $max_height, $round_corners=false){
        return $this->_image->getThumbnail($max_width, $max_height, $round_corners=false);
    }
    
    /* public function __toString(){
        return $this->getImage()->__toString();
    } */
    
    public function renderAsMarkup(){

        switch($this->getType()){

            case "SM_ASSETTYPE_IMAGE":
            case "SM_ASSETTYPE_JPEG_IMAGE":
            case "SM_ASSETTYPE_GIF_IMAGE":
            case "SM_ASSETTYPE_PNG_IMAGE":
            // $markup = htmlentities(stripslashes($this->getTextFragment()->getContent()));
            
            if($this->getImage()){
                $markup = '<img src="'.$this->_request_data->g('domain').'Resources/Images/'.$this->getImage()->getFileName().'" alt="" />';
            }else{
                $markup = 'image file missing: '.$this->getUrl();
            }
            
            break;

        }

        return $markup;
    }
    
    public function getImage(){
        
        if(!$this->_image){
        
            if(is_file(SM_ROOT_DIR.'Public/Resources/Images/'.$this->getUrl())){
                $image = new SmartestImage();
                $image->loadFile(SM_ROOT_DIR.'Public/Resources/Images/'.$this->getUrl());
                $this->_image = $image;
            }
        
        }
        
        return $this->_image;
        
    }
    
}