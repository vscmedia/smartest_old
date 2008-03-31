<?php

function smarty_function__asset_from_object($params, &$smarty){
    
    if(isset($params['object']) && ($params['object'] instanceof SmartestAsset)){
    
        $smarty->_renderAssetObject($params['object'], $params);
        
    }
    
}