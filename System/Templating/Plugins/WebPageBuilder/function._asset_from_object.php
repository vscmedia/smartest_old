<?php

function smarty_function__asset_from_object($params, &$smartest_engine){
    
    if(isset($params['object']) && ($params['object'] instanceof SmartestAsset)){
    
        $smartest_engine->_renderAssetObject($params['object'], $params);
        
    }
    
}