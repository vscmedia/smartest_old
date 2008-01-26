<?php

class Condition{

	// the id of the property
	var $property;
	
	// the matching value
	var $value;
	
	// the relationship between the first two, like equals
	var $operator;
	
	// is this an inclusive or exclusive condition
	var $type;
	
	function Condition($property,$operator,$value){
		$this->property = $property;
		$this->operator = $operator;
		$this->value = $value;
		$this->type = 'inclusive';
	}

}

?>