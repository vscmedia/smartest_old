<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_link($params, &$smartest_engine){
    
	if(isset($params['to']) && strlen($params['to'])){
	    
	    $ph = new SmartestParameterHolder('Raw Link Params: '.$params['to']);
	    $ph->loadArray($params);
	    
		$link = SmartestCmsLinkHelper::createLink($params['to'], $ph);
		
		if($GLOBALS['CURRENT_PAGE']){
		    $link->setHostPage($GLOBALS['CURRENT_PAGE']);
		}
		
		return $link->render($smartest_engine->getDraftMode());
		
	}else{
		return $smartest_engine->raiseError('Link could not be built. "to" field not properly defined.');
	}
	
}