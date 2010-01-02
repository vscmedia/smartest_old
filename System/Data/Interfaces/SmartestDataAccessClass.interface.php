<?php

interface SmartestDataAccessClass{
	
	public function queryToArray($query);
	public function rawQuery($query);
	public function howMany($query);
	public function specificQuery($wantedField, $qualifyingField, $qualifyingValue, $table);
	public function getDebugInfo();
	
}