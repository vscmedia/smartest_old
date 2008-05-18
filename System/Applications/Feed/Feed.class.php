<?php
/**
 *
 * PHP versions 4/5
 *
 * @category   WebApplication
 * @package    PHP-Controller
 * @author     Eddie Tejeda <eddie@visudo.com>
 */


require_once 'System/Helpers/ItemsHelper.class.php';
require_once 'Managers/SmartSetManager.class.php';

class Feed extends SmartestApplication{
  

	var $itemsManager;
	var $smartSetManager;
	
	function __moduleConstruct(){
		$this->itemsManager = new ItemsHelper();
		$this->smartSetManager = new SmartSetManager();
	}

  function search($get){
  
  } 

  function set($get){
		$const['EQUALS'] = 0;
		$const['NOT_EQUAL'] = 1;
		$const['CONTAINS'] = 2;
		$const['STARTS_WITH'] = 4;
		$const['ENDS_WITH'] = 5;
	
	
		$sets = $this->smartSetManager->getSets();
		$id = $get['id'];
		$model_id = null;
		foreach($sets as $set){
			if($set['set_id'] == $id){
				$model_id = $set['set_itemclass_id'];
			}
		}
		$rules = $this->smartSetManager->getSetRules($id);

		
		$dataquery = new DataQuery($model_id);
		
		foreach($rules as $rule){
			$dataquery->addCondition($rule['setrule_itemproperty_id'], $const[$rule['setrule_rule']], $rule['setrule_value']);		
		}
		
		$data = $dataquery->selectToArray();
		var_dump($data);
		die();
  } 
  
}


?>
