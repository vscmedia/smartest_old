<?php

class SmartestTwoDimArray{
	
	static function sort($data, $field_name, $order='asc'){
		
		$new_copy = array();
		
		if(is_array($data)){
			
			foreach($data as $record){
				if(array_key_exists($record, $field_name)){
					if(!$new_copy[$record[$field_name]]){
						$new_copy[$record[$field_name]] = $record;
					}else{
						// the value is a repeated value, just add an extra bit onto the end
					}
				}else{
					// error: requested sort field doesn't exist
				}
			}
			
			if($order == 'desc'){
				krsort($new_copy);
			}else{
				ksort($new_copy);
			}
			
			// function will always return numeric keys starting at 0
			return array_values($new_copy);
			
		}else{
			// error: data wasn't array
		}
	}
	
	static function query($data, $query){
		
	}
	
	static function sqlQuery($data, $field_name, $value){
		// /'SELECT (*|([\w_]+(, )?)+)( FROM [\w_]+) WHERE (([\w_]+\s?=\s?[\w_]+(, )?)+)( LIMIT (\d))?/i
	}
}