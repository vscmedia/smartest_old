<?php

function smarty_modifier_slug($string){
    
    return SmartestStringHelper::toSlug($string);
    
}