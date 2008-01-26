<?php

function smarty_function_dud_link($params, &$smarty){
    
    if($smarty->getUserAgent()->isSafari()){
        return 'javascript:nothing()';
    }else{
        return '#';
    }
    
}