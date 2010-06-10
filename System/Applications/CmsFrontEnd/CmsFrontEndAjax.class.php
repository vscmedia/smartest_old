<?php

class CmsFrontEndAjax extends SmartestSystemApplication{

    public function userAgent(){
        
        echo $this->getUserAgent()->getSimpleClientSideObjectAsJson();
        
    }

}