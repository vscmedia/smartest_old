<?php

function smarty_function_google_analytics($params, &$smarty){

	if($params['id']){
	
	$account_id = $params['id'];
	
	$string = "<script src=\"http://www.google-analytics.com/urchin.js\" type=\"text/javascript\">
</script>
<script type=\"text/javascript\">
_uacct = \"$account_id\";
urchinTracker();
</script>";

		return $string;
		
	}else{
		
		return;
		
	}

}

?>