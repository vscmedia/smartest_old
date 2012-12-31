<?php

function smarty_function_display_date_range($params, &$smartest_engine){
    
    $acceptable_start_date = (isset($params['from']) && $params['from'] instanceof SmartestDateTime);
    $acceptable_end_date   = (isset($params['to']) && $params['to'] instanceof SmartestDateTime);
    
    if($acceptable_start_date && $acceptable_end_date){
        
        $range = new SmartestCalendarEvent;
        $range->loadFromTwoDates($params['from'], $params['to'], true);
        
        if(isset($params['date_format'])){
            $range->setDateFormat($params['date_format']);
        }
        
        if(isset($params['time_format'])){
            $range->setTimeFormat($params['time_format']);
        }
        
        return $range->__toString();
    }
    
}