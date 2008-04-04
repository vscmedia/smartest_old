<?php

function smarty_function_google_analytics($params, &$smarty){

	return $smarty->renderGoogleAnalyticsTags($params);

}