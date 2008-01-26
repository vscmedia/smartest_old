<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {humandate} plugin
 *
 * Type:     function<br>
 * Name:     humandate<br>
 * Purpose:  turns mysql style yyyy-mm-dd dates into presentable human-friendly ones
 * @link http://smarty.php.net/manual/en/language.function.fetch.php {fetch}
 *       (Smarty online manual)
 * @param array
 * @param Smarty
 * @return string
 */

function smarty_function_humantime($params, &$smarty){

	if (empty($params['inputtime'])) {
        $smarty->_trigger_fatal_error("[plugin] parameter 'file' cannot be empty");
        return;
    }else{
    	if (preg_match("/^([0-2][0-9]):([0-6][0-9]):([0-6][0-9])$/", $params['inputtime'])) {
    		$time_elements = explode(":", $params['inputtime']);
    		$hour = $time_elements[0];
    		if($hour > 12){
    			$hour -= 12;
    			$ap = PM;
    		}else{
    			$ap = AM;
    		}
    		$humantime = $hour.":".$time_elements[1]." ".$ap;
    		return $humantime;
		} else {
    		return $params['inputtime'];
		}
    }

}

?>