<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_url($params, &$smartest_engine){
	
	/* if(@$params['to']){
		return $smarty->renderUrl($params['to'], $params);
	}else{
		return null;
	} */
	
	if(isset($params['to']) && strlen($params['to'])){
	    
	    $ph = new SmartestParameterHolder('Raw Link Params: '.$params['to']);
	    $ph->loadArray($params);
	    
		$link = SmartestCmsLinkHelper::createLink($params['to'], $ph);
		
		if($GLOBALS['CURRENT_PAGE']){
		    $link->setHostPage($GLOBALS['CURRENT_PAGE']);
		}
		
		if($link->hasError()){
		    return $smartest_engine->raiseError($link->getErrorMessage());
		}
		
		
		return $link->getUrl($smartest_engine->getDraftMode());
		
	}else{
	    return $smartest_engine->raiseError('URL could not be built. "to" field not properly defined.');
	}
	
}