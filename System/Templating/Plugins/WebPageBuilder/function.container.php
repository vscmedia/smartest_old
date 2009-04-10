<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_container($params, &$smartest_engine){
    // print_r($params);
	if(isset($params['name'])){
	    return $smartest_engine->renderContainer($params['name'], $params, $smartest_engine->getPage());
	}else{
		return null;
	}
}
