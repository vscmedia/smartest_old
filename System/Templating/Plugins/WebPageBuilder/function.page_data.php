<?php

function smarty_function_page_data($params, $smartest_engine){
    
    if(isset($params['name'])){
        
        $page_name = SmartestStringHelper::toSlug($params['name']);
        
        if($smartest_engine->hasOtherPage($page_name)){
            
            $page = $smartest_engine->getOtherPage($page_name);
            
            if(isset($params['assign'])){
                
                $var_name = SmartestStringHelper::toVarName($params['assign']);
                
                if(in_array($var_name, array('this', 'now', 'smarty'))){
                    // Restricted var name
                }else{
                    $smartest_engine->assign($var_name, $page);
                }
                
            }
            
            return;
            
        }
        
        $site_id = $smartest_engine->getPage()->getSiteId();
        
        $page_name = SmartestStringHelper::toSlug($params['name']);
        
        $database = SmartestDatabase::getInstance('SMARTEST');
        $sql = "SELECT * FROM Pages WHERE page_name='".$page_name."' AND page_site_id='".$site_id."' AND page_deleted='FALSE'";
        $result = $database->queryToArray($sql);
        
        if(count($result)){
            
            $page = new SmartestPage;
            $page->hydrate($result[0]);
            $page->setDraftMode($smartest_engine->getDraftMode());
            
            if(isset($params['assign'])){
                
                $var_name = SmartestStringHelper::toVarName($params['assign']);
                
                if(in_array($var_name, array('this', 'now', 'smarty'))){
                    // Restricted var name
                }else{
                    $smartest_engine->assign($var_name, $page);
                }
                
            }
            
        }else{
            // Page wasn't found
        }
        
    }else{
        // Page wasn't specified
    }
    
}