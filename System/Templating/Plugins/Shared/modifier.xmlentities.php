<?php

function smarty_modifier_xmlentities($string){
    return SmartestStringHelper::toXmlEntities($string);
}