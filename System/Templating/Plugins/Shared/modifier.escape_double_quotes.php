<?php

function smarty_modifier_escape_double_quotes($string){
    
    return str_replace('"', '&quot;', $string);
    
}