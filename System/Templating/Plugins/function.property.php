<?php

function smarty_function_property($params, &$smarty){
    
    return $smarty->renderItemPropertyValue($params);
    
}