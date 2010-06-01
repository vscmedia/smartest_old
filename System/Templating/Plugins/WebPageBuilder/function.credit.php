<?php

function smarty_function_credit($params, &$smartest_engine){
    
    return $smartest_engine->renderSmartestCreditButton();
    
}