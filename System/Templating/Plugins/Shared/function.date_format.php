<?php

function smarty_function_date_format($params, &$smartest_engine){
    
    if(isset($params['date'])){
        
        if($params['date'] instanceof SmartestDateTime){
            $raw = $params['date']->getUnixFormat();
        }else if(is_numeric($params['date'])){
            $raw = $params['date'];
        }else{
            $sdt = new SmartestDateTime;
            if($sdt->setValue($params['date'])){
                $raw = $sdt->getUnixFormat();
            }else{
                $raw = 0;
            }
        }
        
        $format = isset($params['format']) ? $params['format'] : 'l jS F, Y';
        
        return date($format, $raw);
        
    }else{
        return $smartest_engine->raiseError("date_format: date='' parameter must be set");
    }
    
}