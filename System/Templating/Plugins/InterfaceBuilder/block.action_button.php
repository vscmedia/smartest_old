<?php

function smarty_block_action_button($params, $content, &$smarty, &$repeat){
	
	$html = "<a class=\"action-button\" ";
	
	if($content){
		
		switch($params['type']){
			
			case "submit":
			$html .= "href=\"javascript:void(0);\" onclick=\"$('".$content."').submit();\" title=\"submit form\"";
			$default_text = "Submit Form";
			break;
			
			default:
			case "url":
			$html .= "href=\"javascript:void(0);\" onclick=\"window.location='".$content."';\"";
			$default_text = "Go";
			break;
			
		}
		
		$html .= "><span>";
		
		if(isset($params['icon']) && is_file(SM_ROOT_DIR.'Public/Resources/Icons/'.$params['icon'])){
			$html .= "<img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/".$params['icon']."\" alt=\"[button icon]\" />";
		}
		
		if(isset($params["text"]) && strlen($params["text"])){
			$html .= $params["text"];
		}else{
			$html .= $default_text;
		}
		
		$html .= "</span></a>";
		
		return $html;
	}else{
		return "Button failed: no destination.";
	}
	
}