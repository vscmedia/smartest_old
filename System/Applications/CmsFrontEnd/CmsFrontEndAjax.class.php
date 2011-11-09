<?php

class CmsFrontEndAjax extends SmartestSystemApplication{

    public function userAgent(){
        
        // Official Mime Type for JSON is 'application/json' See http://www.ietf.org/rfc/rfc4627.txt
        header('Content-type: application/json');
        echo $this->getUserAgent()->getSimpleClientSideObjectAsJson();
        exit;
        
    }

}