<?php

function smarty_modifier_varname($string){
    
    return SmartestStringHelper::toVarName($string);
    
}