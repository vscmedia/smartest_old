<?php
/**
 * Implements the statitics class
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

require "System/Helpers/ItemsHelper.class.php";


class QueryBuilderManager{

	var $database;
	var $displayPages;
	var $displayPagesIndex;
	var $assetsManager;
	
	function PagesManager(){
		$this->database = $_SESSION['database'];
	}
	
}	
?>
?>
