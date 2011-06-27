<?php

class SmartestTypedDataFormHelper extends SmartestHelper{
    
    public static function render($type, $name, $value='', $id=''){
        
        $input = new SmartestTypedDataFormInput;
        $input->setType($type);
        $input->setName($name);
        $input->setValue($value);
        $input->setId($id);
        return $input->render();
        
    }

}