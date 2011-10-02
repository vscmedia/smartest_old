<?php

function smarty_block_help($params, $content, &$smarty, &$repeat){
    
    $id = 'helplink-'.SmartestStringHelper::random(8);
    $html = '<a href="#" id="'.$id.'">'.$content.'</a><script type="text/javascript">$(\''.$id.'\').observe(\'click\', function(){HELP.load(\''.$params['id'].'\')})</script>';
    return $html;
    
}