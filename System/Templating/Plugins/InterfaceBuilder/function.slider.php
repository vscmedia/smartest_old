<?php

function smarty_function_slider($params, &$smartest_engine){
    
    if(isset($params['name'])){
        
        $slider = new SmartestParameterHolder('Slider Parameters: '.$params['name']);
        $slider->setParameter('name', $params['name']);
        $slider->setParameter('id', SmartestStringHelper::toSlug($params['name']));
        
        if(isset($params['value'])){
            $slider->setParameter('value', $params['value']);
        }
        
        if(isset($params['value_unit'])){
            $slider->setParameter('value_unit', $params['value_unit']);
        }
        
        if(isset($params['min']) && isset($params['max'])){
            
            if(is_numeric($params['min']) && is_numeric($params['max'])){
                
                if($params['min'] < $params['max']){
                    $min = $params['min'];
                    $max = $params['max'];
                    $slider->setParameter('show', true);
                }else{
                    echo 'Slider Error: $min must be less than $max.';
                    $slider->setParameter('show', false);
                }
                
            }else{
                echo 'Slider Error: $min and $max must both be numbers.';
                $slider->setParameter('show', false);
            }
            
        }else{
            $min = 0;
            $max = 1;
            $slider->setParameter('show', true);
        }
        
        if($slider->getParameter('show')){
            
            $slider->setParameter('minimum', $min);
            $slider->setParameter('maximum', $max);
            
            if(isset($params['value'])){
                $js_value = ($params['value'] - $min) / ($max - $min);
            }else{
                $js_value = 0;
            }
            
            $slider->setParameter('js_value', $js_value);
        
            if($smartest_engine->getScriptIncluded('scriptaculous/slider')){
                $smartest_engine->assign('_include_slider_js', false);
            }else{
                $smartest_engine->assign('_include_slider_js', true);
                $smartest_engine->setScriptIncluded('scriptaculous/slider');
            }
            
            $smartest_engine->assign('_slider_input_data', $slider);
            $smartest_engine->run(SM_ROOT_DIR.'System/Presentation/InterfaceBuilder/Inputs/slider.tpl', array());
        
        }
        
    }
    
}