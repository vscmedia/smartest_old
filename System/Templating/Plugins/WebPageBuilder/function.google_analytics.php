<?php

function smarty_function_google_analytics($params, &$smartest_engine){

	return $smartest_engine->renderGoogleAnalyticsTags($params);

}