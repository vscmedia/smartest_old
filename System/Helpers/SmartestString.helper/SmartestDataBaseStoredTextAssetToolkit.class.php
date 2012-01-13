<?php

// for ref: http://hobix.com/textile/

class SmartestDataBaseStoredTextAssetToolkit{
    
    protected $_renderer;
    
    public function __construct($renderer){
        $this->_renderer = $renderer;
    }
    
    public function parseTextileTextAsset($content, $asset, $draft_mode=false){
        
        if(stripos($content, 'NewColumn') !== false){
            $content = SmartestStringHelper::parseTextileIntoColumns($content);
        }else{
            $content = SmartestStringHelper::parseTextile($content);
        }
        
        $links = SmartestLinkParser::parseEasyLinks($content);
        
        foreach($links as $l){
            
            $link = new SmartestCmsLink($l, array());
            
            if($link->hasError()){
                $content = str_replace($l->getParameter('original'), $this->_renderer->raiseError($link->getErrorMessage(), $draft_mode), $content);
            }else{
                $content = str_replace($l->getParameter('original'), $link->render($this->_renderer->getDraftMode()), $content);
            }
        }
        
        return $content;
        
    }
    
    public function storeTextileTextAsset($raw_contents){
        
        
        
    }
    
    public function parseRichTextAsset($raw_contents){
        
        $content = $raw_contents;
        
        $links = SmartestLinkParser::parseEasyLinks($content);
        
        foreach($links as $l){
            
            $link = new SmartestCmsLink($l, array());
            
            if($link->hasError()){
                $content = str_replace($l->getParameter('original'), $this->_renderer->raiseError($link->getErrorMessage(), $draft_mode), $content);
            }else{
                $content = str_replace($l->getParameter('original'), $link->render($this->_renderer->getDraftMode()), $content);
            }
        }
        
        if(stripos($content, 'NewColumn') !== false){
            $content = SmartestStringHelper::separateIntoColumns($content);
        }
        
        return $content;
        
    }
    
    public function storeRichTextAsset($raw_contents){
        
        
        
    }
    
    public function parsePlainTextAsset($raw_contents, $asset, $draft_mode=false){
        
       $rd = $asset->getRenderData();
       
       if($rd['parse_urls']){
           $content = preg_replace('/(https?:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/', '<a href="${1}">${1}</a>', $raw_contents);
       }else{
           $content = $raw_contents;
       }
       
       $content = str_replace('<3', '♥', $content);
       
       if($rd['convert_double_line_breaks']){
           $content = preg_replace("/[\r\n]{2,}/", '<br /><br />', $content);
       }
       
       return $content;
        
    }
    
    public function storePlainTextAsset($raw_contents){
        
        
        
    }
    
}