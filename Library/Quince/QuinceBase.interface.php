<?php

interface QuinceBase{

	function send($data, $name="");
	function redirect($destination="");
	function _error($message, $type='');
	
}

?>