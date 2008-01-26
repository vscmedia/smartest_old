<?php

function smarty_modifier_summary($string, $length=300){
    return SmartestStringHelper::toSummary($string, $length);
}