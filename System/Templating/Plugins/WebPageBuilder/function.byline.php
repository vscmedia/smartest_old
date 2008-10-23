<?php

function smarty_function_byline($params, &$smartest_engine){
    
    $authors = $smartest_engine->_tpl_vars['this']['authors'];
    
    // print_r(array_keys($authors));
    
    $num_authors = count($authors);
    $byline = '';
    
    if($num_authors){
        for($i=0;$i<$num_authors;$i++){
            
            $byline .= $authors[$i]['full_name'];
            
            if(isset($authors[$i+2])){
                $byline .= ', ';
            }else if(isset($authors[$i+1])){
                $byline .= ' and ';
            }
            
        }
        
        return $byline;
    }
    
}