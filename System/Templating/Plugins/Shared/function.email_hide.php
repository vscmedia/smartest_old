<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_function_email_hide($params, &$smartest_engine){
    
	if(isset($params['address']) && strlen($params['address'])){
	    
	    /* $ph = new SmartestParameterHolder('Raw Link Params: '.$params['to']);
	    $ph->loadArray($params);
	    
		$link = SmartestCmsLinkHelper::createLink($params['to'], $ph);
		
		if($GLOBALS['CURRENT_PAGE']){
		    $link->setHostPage($GLOBALS['CURRENT_PAGE']);
		}
		
		if($link->hasError()){
		    return $smartest_engine->raiseError($link->getErrorMessage());
		}
		
		return $link->render($smartest_engine->getDraftMode()); */
		
		$s = new SmartestString($params['address']);
		return $s->toHtmlEncoded();
		
	}else{
		return $smartest_engine->raiseError('Email address could not be obfuscated. "address" field not properly defined.');
	}
	
}