<?php

function smarty_modifier_end($string){
    
    $parts = explode(':', $string);
    return end($parts);
    
}