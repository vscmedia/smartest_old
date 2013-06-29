<?php

function smartest_filter_modal($html, $filter){
    
    if(SmartestPersistentObject::get('request_data')->getParameter('namespace') == "modal"){
        
        // If Tidy is installed, use it
        /* if(function_exists('tidy_get_output')){
            // Specify configuration
            /* $config = array(
                'indent'         => true,
                'output-xhtml'   => true,
                'wrap'           => 200
            );

            // Tidy
            $tidy = new tidy;
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();

            // Output
            $html = (string) $tidy; */
            
            //$html;
            
        // }
        
        // if($element = simplexml_load_string(html_entity_decode('<div>'.$html.'</div>', ENT_COMPAT, 'UTF-8'))){
        if($element = simplexml_load_string('<div>'.$html.'</div>')){
            // echo "Valid HTML";
            $work_area_element = $element->xpath("/div/div[1]");
            $work_area_element[0]['id'] = 'modal-work-area';
            return $work_area_element[0]->asXML();
        }else{
            // echo "invalid HTML";
            $html = str_replace('id="work-area"', 'id="modal-work-area"', $html);
            $html = str_replace('id="actions-area"', 'id="modal-actions-area"', $html);
            return $html;
        }
        
        // return $html;
        
    }else{
        return $html;
    }
    
}