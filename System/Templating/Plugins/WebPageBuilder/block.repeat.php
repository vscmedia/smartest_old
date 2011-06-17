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
    }else{
	    $item_name = (isset($params['item']) && strlen($params['item'])) ? SmartestStringHelper::toVarName($params['item']) : "repeated_item";
    }
	
	if ($repeat) {
	
		$items = $smartest_engine->getRepeatBlockData($params);
		$index = 0;
		
		if($params['limit'] > 0){
			$items = array_slice($items, 0, $params['limit']);
		}

	}else{
		
		$items = array_pop($smartest_engine->_set_items_res);
    	$index = array_pop($smartest_engine->_set_items_index)+1;
    	
	}
	
	$item = $items[$index];
	$check_length=($length == 0 or $index < $length);
	$repeat = !empty($item);

	if($item){
		
		$smartest_engine->_set_items_res[] = &$items;
		$smartest_engine->_set_items_index[] = &$index;
		
	}
	
	echo $content;
	
	if($item){
	    
	    // these instructions are executed right before the item is displayed.
	    $smartest_engine->assign($item_name, $item);
	    $smartest_engine->assign("repeated_item_object", $item); // legacy support
	    $smartest_engine->assign("key", $index);
	    
	    if(isset($items[$index+1])){
	        $smartest_engine->assign("next_key", $index+1);
	        $smartest_engine->assign("is_last", false);
        }else{
            $smartest_engine->assign("next_key", false);
            $smartest_engine->assign("is_last", true);
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