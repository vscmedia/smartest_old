<?php

function smarty_block_url_for($params, $content, &$smartest_engine, &$repeat){
    
    return $smartest_engine->getUrlFor($content);
    
}

