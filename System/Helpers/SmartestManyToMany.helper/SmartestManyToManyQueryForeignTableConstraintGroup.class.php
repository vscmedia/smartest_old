<?php

class SmartestManyToManyQueryForeignTableConstraintGroup{
    
    protected $_constraints = array();
    protected $_operator = 'OR';
    
    public function addConstraint($full_field, $value, $operator=0){
        $c = new SmartestManyToManyQueryForeignTableConstraint($full_field, $value, $operator);
        $this->_constraints[] = $c;
    }
    
    public function setOperator($operator='OR'){
        $this->_operator = (strtoupper($operator) == 'AND') ? 'AND' : 'OR';
    }
    
    public function getSql(){
        
        $i = 0;
        $sql = '(';
        
        foreach($this->_constraints as $c){
            
            if($i > 0){
                $sql .= ' '.$this->_operator.' ';
            }
            
            $sql .= $c->getSql();
        }
        
        $sql .= ')';
        
        return $sql;
        
    }
}