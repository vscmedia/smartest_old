<?php

function smarty_function_breadcrumbs($params, &$smartest_engine){
	
	  return $smartest_engine->renderBreadcrumbs($params);
	
}