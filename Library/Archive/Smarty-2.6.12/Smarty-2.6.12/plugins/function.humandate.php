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

function smarty_function_humandate($params, &$smarty){

	if (empty($params['inputdate'])) {
        $smarty->_trigger_fatal_error("[plugin] parameter 'file' cannot be empty");
        return;
    }else{
    	if (preg_match("/^([12][0-9]{3})-([01][0-2])-([0-3][0-9])$/", $params['inputdate'])) {
    		$date_elements = explode("-", $params['inputdate']);
    		$humandate = date("l jS F, Y", mktime("01", "00", "00", $date_elements[1], $date_elements[2], $date_elements[0]));
    		return $humandate;
		} else {
    		return $params['inputdate'];
		}
    }

}

?>