<?php

function smarty_function_breadcrumbs($params, &$smarty){
	
	return $smarty->renderBreadcrumbs($params);
	
	/* if($smarty->_tpl_vars['this']['navigation']['breadcrumbs']){
		
		$breadcrumbs = $smarty->_tpl_vars['this']['navigation']['breadcrumbs'];
		$separator = (isset($params['separator'])) ? $params['separator'] : "&gt;&gt;";
		$string = "";
		
		$link_params = array();
		
		if(isset($params['linkclass'])){
		    $link_params['class'] = $params['linkclass'];
		}
		
		$link_params['goCold'] = 'true';
		
		foreach($breadcrumbs as $key => $page){
			
			if($page['type'] == 'ITEMCLASS'){
			    
			    if(is_object($smarty->getPage()->getPrincipalItem())){
			        $id = $smarty->getPage()->getPrincipalItem()->getId();
			        $to = 'metapage:webid='.$page['webid'].':id='.$id;
			    }else{
			        $to = 'page:webid='.$page['webid'];
			    }
			    
			}else{
		        $to = 'page:webid='.$page['webid'];
		    }
			
			$text = $smarty->renderLink($to, $link_params);
			
			if($key > 0){
				$string .= " $separator ";
			}
			
			$string .= $text;
		}
		
		return $string;
	}else{
		return $smarty->raiseError("Automatic breadcrumbing failed.");
	} */
}