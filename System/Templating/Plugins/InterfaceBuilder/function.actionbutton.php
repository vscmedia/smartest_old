<?php

function smarty_function_actionbutton($params, &$smarty){
	
	$html = "<a class=\"action-button\" ";
	
	if($params['type'] && $params['text']){
		
		switch($params['type']){
			
			case "submit":
			$html .= "href=\"javascript:void(0);\" onclick=\"$('".$params['object']."').submit();\" title=\"submit form\"";
			$default_text = "Submit Form";
			break;
			
			default:
			case "url":
			$html .= "href=\"javascript:void(0);\" onclick=\"window.location='".$params['object']."';\"";
			$default_text = "Go";
			break;
			
		}
		
		$html .= "><span>";
		
		if(isset($params['icon']) && is_file(SM_ROOT_DIR.'Public/Resources/Icons/'.$params['icon'])){
			$html .= "<img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/".$params['icon']."\" alt=\"[button icon]\" />";
		}
		
		if($params['text']){
			$html .= $params['text'];
		}else{
			$html .= $default_text;
		}
		
		$html .= "</span></a>";
		
		return $html;
	}else{
		return "Button failed: required parameters missing.";
	}
	
}