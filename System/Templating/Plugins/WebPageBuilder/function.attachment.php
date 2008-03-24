<?php

function smarty_function_attachment($params, &$smarty){
    
    return $smarty->renderAttachment($params['name']);
    
}