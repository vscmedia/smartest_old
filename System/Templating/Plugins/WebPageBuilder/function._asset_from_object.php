<?php

function smarty_function__asset_from_object($params, &$smartest_engine){
    
    if(isset($params['object']) && ($params['object'] instanceof SmartestRenderableAsset)){
    
        // $smartest_engine->_renderAssetObject($params['object'], $params);
        return $params['object']->render($smartest_engine->getDraftMode());
        
    }else{
        
        $type = gettype($params['object']);
        
        if($type == 'object'){
            $type = get_class($params['object']).' Object';
        }
        
        return $smartest_engine->raiseError('&lt;?sm:_asset_from_object:?&gt; must be provided with a SmartestRenderableAsset object. '.$type.' given.');
      
    }
    
}