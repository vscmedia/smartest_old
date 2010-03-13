<?php

function smarty_modifier_paragraphs($string){
    
    return SmartestStringHelper::toParagraphs($string);
    
}