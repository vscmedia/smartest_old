<?php

function smarty_modifier_camelcase($string){
    return SmartestStringHelper::toCamelCase($string);
}