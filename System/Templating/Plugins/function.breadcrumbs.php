<?php

function smarty_function_breadcrumbs($params, &$smarty){
	
	if($smarty->_tpl_vars['this']['navigation']['breadcrumbs']){
		
		$breadcrumbs = $smarty->_tpl_vars['this']['navigation']['breadcrumbs'];
		$separator = (isset($params['separator'])) ? $params['separator'] : "&gt;&gt;";
		$string = "";
		
		foreach($breadcrumbs as $key => $page){
			
			/* if($page['id'] == $smarty->getPage()->getId() || $page['is_published'] != "TRUE"){
				$text = $page['title'];
			}else{
				
				if(SM_CONTROLLER_METHOD == "renderEditableDraftPage"){
					$text = "<a href=\"".SM_CONTROLLER_DOMAIN."websitemanager/preview?page_id=".$page['page_webid']."\" target=\"_top\"";
				}else{
					$text = "<a href=\"".SM_CONTROLLER_DOMAIN.$page['url']."\"";
				}
				
				if(isset($params['linkclass'])){
					$text .= " class=\"".$params['linkclass']."\"";
				}
				$text .= ">".$page['title']."</a>";
			} */
			
			// print_r($page);
			
			if($page['type'] == 'ITEMCLASS'){
			    
			    if(is_object($smarty->getPage()->getPrincipalItem())){
			        $id = $smarty->getPage()->getPrincipalItem()->getId();
			        $to = 'metapage:webid='.$page['webid'].':id='.$id;
			    }else{
			        $to = 'page:webid='.$page['webid'];
			    }
			    
			    // $to = 'metapage:webid='.$page['webid'].':';
			    
			// }else if($page['is_tag_page']){
			
			//    $to = SM_CONTROLLER_DOMAIN.'tags/';
			
		    }else{
		        $to = 'page:webid='.$page['webid'];
		    }
			
			$text = $smarty->renderLink($to, array('goCold' => 'true'));
			
			if($key > 0){
				$string .= " $separator ";
			}
			
			$string .= $text;
		}
		
		return $string;
	}else{
		return "Smartest Error: automatic breadcrumbing failed";
	}
}