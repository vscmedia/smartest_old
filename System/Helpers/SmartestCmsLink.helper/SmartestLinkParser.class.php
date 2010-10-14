<?php

class SmartestLinkParser{
    
    const LINK_TARGET_TITLE = 'SM_LINK_GET_TARGET_TITLE';
    
    public static function replaceAll($string){
        
        $oc = self::parseEasyLinks($string);
        
    }
    
    public static function parseEasyLinks($string){
        
        $pattern = '/\[(\[(([\w_-]+):)?([^\]\|]+)(\|([^\]]+))?\]|(\+)?(https?:\/\/[^\s\]]+)(\s+([^\]]+))?)\]/i';
        preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);
        
        $links = array();
        
        if(is_array($matches)){
            
            foreach($matches as $m){
                
                if(isset($m[8])){ // this means link started with 'http', so it is external
                    
                    $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[7]);
                    
                    $l->setParameter('original', $m[0]);
                    
                    $l->setParameter('scope', SM_LINK_SCOPE_EXTERNAL);
                    $l->setParameter('destination', $m[8]);
                    
                    if(strlen($m[7])){
                        $l->setParameter('newwin', true);
                    }
                
                    if($m[10]){
                        $l->setParameter('text', $m[10]);
                    }else{
                        $l->setParameter('text', $m[8]);
                    }
                    
                    $l->setParameter('format', SM_LINK_FORMAT_URL);
                
                }else{
                    
                    $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[2].SmartestStringHelper::toSlug($m[4]));
                    
                    $l->setParameter('scope', SM_LINK_SCOPE_INTERNAL);
                    $l->setParameter('namespace', $m[3]);
                    
                    if(in_array($m[3], array('image', 'asset', 'download'))){
                        $l->setParameter('destination', $m[2].$m[4]);
                        $l->setParameter('filename', $m[4]);
                    }else if($m[3] == 'mailto'){
                        $l->setParameter('destination', $m[4]);
                    }else{
                        $l->setParameter('destination', $m[2].SmartestStringHelper::toSlug($m[4]));
                    }
                    
                    $l->setParameter('original', $m[0]);
                    
                    if(strpos($l->getParameter('destination'), '=') !== false){

                        // if(preg_match('/(name|id|webid)=([\w_-]+)(:(name|id|webid)=([\w_-]+))?/i', $l->getParameter('destination'), $dm)){
                        if(preg_match('/(meta)?page:((name|id|webid)=)?([\w_-]+)(:((name|id|webid)=)?([\w_-]+))?/i', $l->getParameter('destination'), $dm)){
                            
                            $l->setParameter('format', SM_LINK_FORMAT_AUTO);
                            
                            if(strlen($dm[2])){
                                $l->setParameter('page_ref_field_name', strtolower($dm[3]));
                            }else{
                                $l->setParameter('page_ref_field_name', 'name');
                            }

                            if($dm[3] == 'webid'){
                                $l->setParameter('page_ref_field_value', $dm[4]);
                            }else{
                                $l->setParameter('page_ref_field_value', trim(SmartestStringHelper::toSlug($dm[4])));
                            }

                            if(isset($dm[5]) && strlen($dm[5])){
                                if(strlen($dm[6])){
                                    if($dm[7] == 'name'){
                                        $l->setParameter('item_ref_field_name', 'slug');
                                    }else{
                                        $l->setParameter('item_ref_field_name', $dm[7]);
                                    }
                                }else{
                                    $l->setParameter('item_ref_field_name', 'slug');
                                }
                                $l->setParameter('item_ref_field_value', trim(SmartestStringHelper::toSlug($dm[8])));
                            }

                        }else{
                            
                        }

                    }else{
                        
                        if(strtolower($l->getParameter('namespace')) == 'page'){
                            $l->setParameter('page_ref_field_name', 'name');
                            $l->setParameter('page_ref_field_value', SmartestStringHelper::toSlug($m[4]));
                        }else{
                            // echo $l->getParameter('namespace');
                            if(!in_array($l->getParameter('namespace'), array('image', 'download', 'tag', 'asset', 'mailto'))){
                                $l->setParameter('destination', $m[2].SmartestStringHelper::toSlug($m[4]));
                                $l->setParameter('item_ref_field_name', 'slug');
                                $l->setParameter('item_ref_field_value', SmartestStringHelper::toSlug($m[4]));
                                $l->setParameter('format', SM_LINK_FORMAT_USER);
                            }
                        }
                    }
                
                    if(isset($m[6])){
                        $l->setParameter('text', $m[6]);
                    }else{
                        $l->setParameter('text', self::LINK_TARGET_TITLE);
                    }
                }
                
                $links[] = $l;
            
            }
        }
        
        return $links;
    }
    
    public static function parseSingle($string){
        
        if($string == '#'){
            
            $l = new SmartestParameterHolder("Empty Link Parameters");
            $l->setParameter('scope', SM_LINK_SCOPE_NONE);
            $l->setParameter('destination', '#');
        
        }else if(preg_match('/^(https?:\/\/[^\s]+)(\s+([^\]]+))?$/i', $string, $m)){
            
            $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[0]);
            $l->setParameter('destination', $m[1]);
            $l->setParameter('scope', SM_LINK_SCOPE_EXTERNAL);
            $l->setParameter('text', $m[2]);
            $l->setParameter('format', SM_LINK_FORMAT_URL);
            
        }else{
        
            $pattern = '/^(([\w_-]+):)?([^\|]+[^:])(\|([^\]]+))?$/i';
            preg_match($pattern, $string, $m);
            
            $l = new SmartestParameterHolder("Parsed Link Destination Properties: ".$m[0]);
            $l->setParameter('scope', SM_LINK_SCOPE_INTERNAL);
            $l->setParameter('destination', $m[1].$m[3]);
            $l->setParameter('namespace', $m[2]);
            $l->setParameter('format', SM_LINK_FORMAT_USER);
            
            if($l->getParameter('namespace') == 'mailto'){
                $l->setParameter('destination', $m[3]);
            }
                
            if(in_array($m[2], array('image', 'asset', 'download'))){
                $l->setParameter('filename', $m[3]);
            }
        
            if(isset($m[5])){
                $l->setParameter('text', $m[5]);
            }else{
                $l->setParameter('text', self::LINK_TARGET_TITLE);
            }
            
            if($m[2]){
                $l->setParameter('page_ref_field_name', 'name');
                $l->setParameter('page_ref_field_value', SmartestStringHelper::toSlug($m[3]));
            }else{
                $l->setParameter('scope', SM_LINK_SCOPE_NONE);
                return $l;
            }
            
            if(strpos($l->getParameter('destination'), '=')){
            
                if(preg_match('/(meta)?page:((name|id|webid)=)?([\w_-]+)(:((name|id|webid)=)?([\w_-]+))?/i', $l->getParameter('destination'), $m)){
                    
                    if(strlen($m[2])){
                        $l->setParameter('page_ref_field_name', $m[3]);
                    }else{
                        $l->setParameter('page_ref_field_name', 'name');
                    }
                    
                    if($m[3] == 'webid'){
                        $l->setParameter('page_ref_field_value', $m[4]);
                    }else{
                        $l->setParameter('page_ref_field_value', trim(SmartestStringHelper::toSlug($m[4])));
                    }
                    
                    $l->setParameter('format', SM_LINK_FORMAT_AUTO);
                    
                    if(isset($m[5]) && strlen($m[5])){
                        if(strlen($m[6])){
                            if($m[7] == 'name'){
                                $l->setParameter('item_ref_field_name', 'slug');
                            }else{
                                $l->setParameter('item_ref_field_name', $m[7]);
                            }
                        }else{
                            $l->setParameter('item_ref_field_name', 'slug');
                        }
                        $l->setParameter('item_ref_field_value', SmartestStringHelper::toSlug($m[8]));
                    }
                
                }
            
            }
        
        }
        
        return $l;
        
    }
    
    public function processRegexMatch($match){
        
    }
    
}