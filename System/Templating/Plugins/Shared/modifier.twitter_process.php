<?php

function smarty_modifier_twitter_process($string){
    
    // convert ordinary links
    $string = preg_replace('/http:\/\/([\w\/\._-]+)/i', "<a href=\"http://$1\">http://$1</a>", $string);
    
    // parse out @usernames
    $string = preg_replace('/@([\w_]+)/i', "@<a href=\"http://twitter.com/$1\">$1</a>", $string);
    
    // parse out #hashtags
    $string = preg_replace('/#([\w_-]+)/i', "<a href=\"http://twitter.com/search?q=%23$1\">#$1</a>", $string);
    
    // make initial usernames bold
    $string = preg_replace('/^([\w_]+):/i', "<strong><a href=\"http://twitter.com/$1\">$1</a></strong>:", $string);
    
    return $string;
    
}