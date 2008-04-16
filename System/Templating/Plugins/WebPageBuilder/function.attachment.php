<?php

function smarty_function_attachment($params, &$smartest_engine){
    
    return $smartest_engine->renderAttachment($params['name']);
    
}