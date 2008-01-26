<?php

interface SmartestDataAccessClass{
	
	public function queryToArray($query, $file='', $line='');
	public function rawQuery($query, $file='', $line='');
	public function howMany($query, $file='', $line='');
	public function specificQuery($wantedField, $qualifyingField, $qualifyingValue, $table);
	public function getDebugInfo();
	
}