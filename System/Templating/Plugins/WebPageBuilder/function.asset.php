<?php

function smarty_function_asset($params, &$smarty){
    
    if(isset($params['id']) && is_numeric($params['id'])){
        $asset_id = $params['id'];
    }else if(isset($params['name']) && strlen($params['name'])){
        $asset_id = $params['name'];
    }
    
    if(isset($params['path'])){
        $path = (!in_array($params['path'], array('file', 'full'))) ? 'none' : $params['path'];
    }else{
        $path = 'none';
    }
    
    return $smarty->renderAssetById($asset_id, $params, $path);
    
}