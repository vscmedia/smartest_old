<?php

function smarty_block_help($params, $content, &$smarty, &$repeat){
    
    $id = 'helplink-'.SmartestStringHelper::random(6);
    
    if(isset($params['buttonize']) && !SmartestStringHelper::toRealBool($params['buttonize'])){
        $html = '<a href="#'.SmartestStringHelper::toSlug('help-'.$params['id']).'" class="sm-help-link" id="'.$id.'">'.$content.'</a><script type="text/javascript">$(\''.$id.'\').observe(\'click\', function(e){HELP.load(\''.$params['id'].'\');Event.stop(e);})</script>';
    }else{
        $html = '<a href="#'.SmartestStringHelper::toSlug('help-'.$params['id']).'" class="sm-help-link button" id="'.$id.'"><span>'.$content.'</span></a><script type="text/javascript">$(\''.$id.'\').observe(\'click\', function(e){HELP.load(\''.$params['id'].'\');Event.stop(e);})</script>';
    }
    
    return $html;
    
}