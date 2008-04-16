<?php

function smarty_function_sitemap($params, &$smartest_engine){
    
    return $smartest_engine->renderSiteMap();
    
}