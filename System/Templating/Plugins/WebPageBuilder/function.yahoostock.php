<?php

function smarty_function_yahoostock($params, &$smartest_engine){
    
    if(isset($params['symbol']) && strlen($params['symbol'])){
        
        $sh = new SmartestYahooDataDownloadHelper;
        
        if(isset($params['flags']) && strlen($params['flags'])){
            $data = $sh->getData($params['symbol'], $params['flags']);
        }else{
            $data = $sh->getData($params['symbol']);
        }
        
        if(isset($params['assign']) && strlen($params['assign'])){
            $smartest_engine->assign(SmartestStringHelper::toVarName($params['assign']), $data);
        }
    }
    
}