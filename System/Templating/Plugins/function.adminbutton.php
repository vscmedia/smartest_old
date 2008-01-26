<?php

function smarty_function_adminbutton($params, &$smarty){
	
	$html = "<a class=\"button\" ";
	
	if($params['type'] && $params['object']){
		switch($params['type']){
			
			case "submit":
			$html .= "href=\"javascript:void(0);\" onclick=\"document.getElementById('".$params['object']."').submit();\" title=\"submit form\"";
			$default_text = "Submit Form";
			break;
			
			case "url":
			$html .= "href=\"javascript:void(0);\" onclick=\"window.location='".$params['object']."';\"";
			$default_text = "Go";
			break;
			
		}
		
		if($params['style']){
			$html .= " style=\"".$params['style']."\"";
		}
		
		$html .= ">";
		
		$html .= "<img src=\"".SM_CONTROLLER_DOMAIN."Resources/Images/grey_button_left.gif\" alt=\"\" />";
		
		if($params['icon']){
			$html .= "<img src=\"".SM_CONTROLLER_DOMAIN."Resources/Icons/".$params['icon']."\" alt=\"[button icon]\" />";
		}
		
		if($params['text']){
			$html .= "<span>".$params['text'];
		}else{
			$html .= "<span>".$default_text;
		}
		
		$html .= "</span><img src=\"".SM_CONTROLLER_DOMAIN."Resources/Images/grey_button_right.gif\" alt=\"\" /></a>";
		
		return $html;
	}else{
		return "Button failed: required parameters missing.";
	}
}

?>