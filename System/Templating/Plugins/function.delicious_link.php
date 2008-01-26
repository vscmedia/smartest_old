<?php

function smarty_function_delicious_link($params, &$smarty){
	
	if($params['text']){
		$text = " ".$params['text'];
	}else{
		$text = " Add this page to del.icio.us";
	}
	
	if($params['class']){
		$class = " class=\"".$params['class']."\"";
	}else{
		$class = "";
	}
	
	return "<a href=\"http://del.icio.us/post?url=http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\" title=\"Add to your del.icio.us bookmarks\"$class><img src=\"/Resources/Icons/delicious.png\" width=\"16\" height=\"16\" alt=\"del.icio.us\" border=\"0\" />$text</a>";
}

?>