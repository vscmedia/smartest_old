<?php

function smarty_function_cms_page($params, &$smartest_engine){
    if(isset($params['page']) && is_object($params['page'])){
        
        if(isset($params['draft']) && $params['draft'] && strtolower($params['draft']) != 'false'){
            $draft_mode = true;
        }else{
            $draft_mode = false;
        }
        
        return $smartest_engine->renderPage($params['page'], $draft_mode);
    }
}