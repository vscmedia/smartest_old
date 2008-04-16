<?php

function smarty_function_property($params, &$smartest_engine){
    
    return $smartest_engine->renderItemPropertyValue($params);
    
}