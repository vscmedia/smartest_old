<?php

function smarty_function_cms_page($params, &$smarty){
    if(isset($params['page']) && is_object($params['page'])){
        
        if(isset($params['draft']) && $params['draft'] && strtolower($params['draft']) != 'false'){
            $draft_mode = true;
        }else{
            $draft_mode = false;
        }
        
        // print_r($draft_mode);
        
        return $smarty->renderPage($params['page'], $draft_mode);
    }
}