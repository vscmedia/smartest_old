<?php

function smarty_function_subrequest($params, &$smartest){
    
    if(isset($params['url'])){
        
        $url = $params['url'];
        
        if(isset($params['iframe']) && SmartestStringHelper::toRealBool($params['iframe'])){
            
            return $smartest->renderIframe($params);
            
        }else{
            
            $result = SmartestHttpRequestHelper::getContent($url);
            return $result;
        
        }
    }
}