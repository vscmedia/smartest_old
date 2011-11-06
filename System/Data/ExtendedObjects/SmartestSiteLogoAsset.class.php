<?php

class SmartestSiteLogoAsset extends SmartestAsset{

    public function getStartPageGlassyLogo(){
        if($this->getImage()){
            return $this->getImage()->getSquareVersion(130)->overlayWith(SM_ROOT_DIR.'Public/Resources/System/Images/site_icon_overlay.png');
        }
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "startpage_glossy":
            return $this->getStartPageGlassyLogo();
        }
        
        return parent::offsetGet($offset);
        
    }

}