<?php

function smarty_function_asset_group($params, $smartest_engine){
    
    if(isset($params['name'])){
        
        $site_id = $smartest_engine->getPage()->getSiteId();
        $group_name = SmartestStringHelper::toVarName($params['name']);
        
        $database = SmartestDatabase::getInstance('SMARTEST');
        $sql = "SELECT * FROM Sets WHERE set_name='".$group_name."' AND (set_site_id='".$site_id."' OR set_shared='1') AND (set_type='SM_SET_ASSETGALLERY' OR set_type='SM_SET_ASSETGROUP')";
        $result = $database->queryToArray($sql);
        
        if(count($result)){
            
            $group = new SmartestAssetGroup;
            $group->hydrate($result[0]);
            
            if(isset($params['assign'])){
                
                $var_name = SmartestStringHelper::toVarName($params['assign']);
                
                if(in_array($var_name, array('this', 'now', 'smarty'))){
                    // Restricted var name
                }else{
                    $smartest_engine->assign($var_name, $group);
                }
                
            }
            
        }else{
            // Group wasn't found
        }
        
    }
    
}