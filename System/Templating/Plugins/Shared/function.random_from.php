<?php

function smarty_function_random_from($params, &$smartest_engine){
    
    if(isset($params['values'])){
        
        $values = explode('|', $params['values']);
        return($values[rand(0, (count($values)-1))]);
        
    }
    
}