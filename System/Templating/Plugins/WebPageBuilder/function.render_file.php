<?php

function smarty_function_render_file($params, &$smartest_engine){
    
    if(isset($params['asset']) && ($params['asset'] instanceof SmartestRenderableAsset)){
    
        // $smartest_engine->_renderAssetObject($params['object'], $params);
        $asset = $params['asset'];
        
        foreach($params as $key=>$value){
            if($key != 'id' && $key != 'asset'){
                $asset->setSingleAdditionalRenderDataParameter($key, $value);
            }
        }
        
        return $asset->render($smartest_engine->getDraftMode());
    
    }elseif(isset($params['id']) && is_numeric($params['id'])){
        
        $asset = new SmartestRenderableAsset;
        
        if($asset->find($params['id'])){
            
            foreach($params as $key=>$value){
                if($key != 'id' && $key != 'asset'){
                    $asset->setSingleAdditionalRenderDataParameter($key, $value);
                }
            }
            
            return $asset->render($smartest_engine->getDraftMode());
            
        }else{
            return $smartest_engine->raiseError('&lt;?sm:render_file:?&gt; must be provided with a SmartestRenderableAsset object or valid asset ID. Unknown asset ID given.');
        }
    
    }else{
        
        $type = gettype($params['object']);
        
        if($type == 'object'){
            $type = get_class($params['object']).' Object';
        }
        
        return $smartest_engine->raiseError('&lt;?sm:render_file:?&gt; must be provided with a SmartestRenderableAsset object. Object of class '.$type.' given.');
      
    }
    
}