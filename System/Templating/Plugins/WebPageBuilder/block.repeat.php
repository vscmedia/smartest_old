<?php

/**
 * Smarty plugin
 * @package Smartest CMS Smarty Plugins
 * @subpackage page manager
 */

function smarty_block_repeat($params, $content, &$smartest_engine, &$repeat){
	
	$dah = new SmartestDataAppearanceHelper;
	
	if($params['from'] == '_authors'){
        $item_name = (isset($params['item']) && strlen($params['item'])) ? SmartestStringHelper::toVarName($params['item']) : "author";
    }else if($params['from'] instanceof SmartestPageGroup || substr($params['from'], 0, 6) == 'pagegr' || substr($params['from'], 0, 6) == 'page_g'){
        $item_name = (isset($params['item']) && strlen($params['item'])) ? SmartestStringHelper::toVarName($params['item']) : "repeated_page";
    }else{
	    $item_name = (isset($params['item']) && strlen($params['item'])) ? SmartestStringHelper::toVarName($params['item']) : "repeated_item";
    }
	
	$limit = (isset($params['limit']) && is_numeric($params['limit'])) ? $params['limit'] : 0;
	$char_limit = (isset($params['char_limit']) && is_numeric($params['char_limit'])) ? $params['char_limit'] : 0;
	if(!isset($length)){$length = 0;}
	
	// echo $smartest_engine->_repeat_char_length_aggr;
	
	if ($repeat) {
	
		$items = $smartest_engine->getRepeatBlockData($params);
		
		if($items instanceof SmartestArray){
		    $items = $items->getValue();
		}
		
		$smartest_engine->assign("first", &$items[0]);
		$smartest_engine->assign("last", &$items[count($items)-1]);
		
		$index = 0;
		
		if($limit > 0){
			$items = array_slice($items, 0, $limit);
		}

	}else{
		
		$items = array_pop($smartest_engine->_set_items_res);
    	$index = array_pop($smartest_engine->_set_items_index)+1;
    	
	}
	
	$item = isset($items[$index]) ? $items[$index] : null;
	$check_length=($length == 0 or $index < $length);
	
	if(is_string($item)){
	    $char_length_incr = strlen($item);
	}else if($item instanceof SmartestCmsItem){
	    $char_length_incr = strlen($item->getName());
	}else if($item instanceof SmartestUser){
	    $char_length_incr = strlen($item->getFullName());
	}else{
	    $char_length_incr = 0;
	}
	
	$smartest_engine->_repeat_char_length_aggr+=$char_length_incr;
	
	$repeat = !empty($item) && (!$char_limit || ($char_limit && $smartest_engine->_repeat_char_length_aggr < $char_limit));

	if($item){
		
		$smartest_engine->_set_items_res[] = &$items;
		$smartest_engine->_set_items_index[] = &$index;
		
	}
	
	echo $content;
	
	if($item){
	    
	    // these instructions are executed right before the item is displayed.
	    $smartest_engine->assign($item_name, $item);
	    $smartest_engine->assign("repeated_item_object", &$item); // legacy support
	    $smartest_engine->assign("key", $index);
	    $smartest_engine->assign("iteration", $index+1);
	    
	    if(!isset($items[$index+1]) || ($limit && $limit == $index+1)){
	        $smartest_engine->assign("next_key", false);
            $smartest_engine->assign("is_last", true);
        }else{
            $smartest_engine->assign("next_key", $index+1);
	        $smartest_engine->assign("is_last", false);
        }
        
        if(isset($items[$index-1])){
	        $smartest_engine->assign("previous_key", $index-1);
	        $smartest_engine->assign("is_first", false);
        }else{
            $smartest_engine->assign("previous_key", false);
            $smartest_engine->assign("is_first", true);
        }
	    
	    // if($smartest_engine->getDraftMode()){
	        $dah->setItemAppearsOnPage($item->getId(), $smartest_engine->getPage()->getId());
        // }
	}
	
}