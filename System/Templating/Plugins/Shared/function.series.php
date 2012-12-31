<?php

function smarty_function_series($params, &$smartest_engine){
    
    $series_name = (isset($params['name']) && strlen($params['name'])) ? $params['name'] : 'default';
    
    switch($params['do']){
        
        case "insert":
        $s = $smartest_engine->initNumberSeriesByName($series_name);
        $s->insertValue($params['number']);
        break;
        
        case "sum":
        
        $s = $smartest_engine->initNumberSeriesByName($series_name);
        $sum = $s->getSum();
        
        if(isset($params['assign'])){
            $smartest_engine->assign($params['assign'], $sum);
        }else{
            return $sum;
        }
        
        break;
        
        case "average":
        
        $s = $smartest_engine->initNumberSeriesByName($series_name);
        $avg = $s->getAverage();
        
        if(isset($params['assign'])){
            $smartest_engine->assign($params['assign'], $avg);
        }else{
            return $avg;
        }
        
        break;
        
        case "iterate":
        
        break;
        
    }
    
}