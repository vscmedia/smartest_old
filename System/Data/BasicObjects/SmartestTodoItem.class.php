<?php

class SmartestTodoItem extends SmartestDataObject{

	const PERSONAL = 'SM_TODOITEMTYPE_PERSONAL';
	const DUTY = 'SM_TODOITEMTYPE_DUTY';
	const ASSIGNMENT = 'SM_TODOITEMTYPE_ASSIGNMENT';
	
	protected function __objectConstruct(){
		
		$this->_table_prefix = 'todoitem_';
		$this->_table_name = 'TodoItems';
		
	}
	
}