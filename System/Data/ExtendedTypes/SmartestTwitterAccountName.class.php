<?php

class SmartestTwitterAccountName extends SmartestString{
    
    public function setValue($v){
        
        if($v{0} == '@'){
            $this->_string = substr($v, 1);
        }else{
            $this->_string = (string) $v;
        }
        
    }
    
    public function getUrl($secure=true){
        
        $p = $secure ? 'https' : 'http';
        return new SmartestExternalUrl($p.'://twitter.com/'.$this->_string);
        
    }
    
    public function offsetGet($offset){
        
        switch($offset){
            case "url":
            case "secure_url":
            return $this->getUrl();
            case "link":
            $p = new SmartestParameterHolder('Twitter account link parameters: @'.$this->_string);
            $p->setParameter('with', '@'.$this->_string);
            return SmartestCmsLinkHelper::createLink($this->getUrl(), $p)->render();
            case "secure_link":
            $p = new SmartestParameterHolder('Twitter account secure link parameters: @'.$this->_string);
            $p->setParameter('with', '@'.$this->_string);
            return SmartestCmsLinkHelper::createLink($this->getUrl(true), $p)->render();
            case "empty":
            return !strlen($this->_string);
            case "tweets_json_decoded":
            return $this->getTweetsJson();
        }
        
        return parent::offsetGet($offset);
        
    }
    
    public function __toString(){
        return (string) $this->_string;
    }
    
    public function getTweetsJson(){
        
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name='.$this->_string;
        $result = SmartestHttpRequestHelper::rawCurlRequest($url);
        
        // var_dump($result);
        
        if($json = @json_decode($result)){
            return $json;
        }else{
            return false;
        }
        
    }
  
}