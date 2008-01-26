<?php

// global $sm_errorStack;
// print_r($sm_errorStack);
// $errorStack = $GLOBALS["sm_errorStack"];

function error($message, $type){
	// global $errorStack;
	$errorStack = $GLOBALS["sm_errorStack"];
	print_r($errorStack);
	// $errorStack->recordError($message, $type);
}

?>