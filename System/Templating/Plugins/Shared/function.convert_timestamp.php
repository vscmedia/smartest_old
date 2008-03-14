<?php

function smarty_function_convert_timestamp($params, &$smarty){
	
	if(isset($params['format'])){
		$format = $params['format'];
	}else{
		$format = SM_OPTIONS_DATE_FORMAT;
	}
	
	if(isset($params['time'])){
		$time = $params['time'];
	}else{
		$time = $params['time'];
	}
	
	$date = date($format, $time);
	
	return $date;
	
}

?>