<?php

function smartest_filter_preview($html, $filter){
    
    if($filter->getDraftMode()){
        
        $request_data = SmartestPersistentObject::get('request_data');
        
        $preview_url = '/website/renderEditableDraftPage?page_id='.$request_data->getParameter('request_parameters')->getParameter('page_id').'&amp;hide_newwin_link=true';
        if($request_data->getParameter('request_parameters')->hasParameter('item_id')) $preview_url .= '&amp;item_id='.$request_data->getParameter('request_parameters')->getParameter('item_id');
        if($request_data->getParameter('request_parameters')->hasParameter('search_query')) $preview_url .= '&amp;q='.$request_data->getParameter('request_parameters')->getParameter('search_query');
        if($request_data->getParameter('request_parameters')->hasParameter('author_id')) $preview_url .= '&amp;author_id='.$request_data->getParameter('request_parameters')->getParameter('author_id');
        if($request_data->getParameter('request_parameters')->hasParameter('tag_name')) $preview_url .= '&amp;tag_name='.$request_data->getParameter('request_parameters')->getParameter('tag_name');
        
        $smartest_preview_url = '/websitemanager/preview?page_id='.$request_data->getParameter('request_parameters')->getParameter('page_id');
        if($request_data->getParameter('request_parameters')->hasParameter('item_id')) $smartest_preview_url .= '&amp;item_id='.$request_data->getParameter('request_parameters')->getParameter('item_id');
        if($request_data->getParameter('request_parameters')->hasParameter('search_query')) $smartest_preview_url .= '&amp;q='.$request_data->getParameter('request_parameters')->getParameter('search_query');
        if($request_data->getParameter('request_parameters')->hasParameter('author_id')) $smartest_preview_url .= '&amp;author_id='.$request_data->getParameter('request_parameters')->getParameter('author_id');
        if($request_data->getParameter('request_parameters')->hasParameter('tag_name')) $smartest_preview_url .= '&amp;tag='.$request_data->getParameter('request_parameters')->getParameter('tag_name');
        
        $sm = new SmartyManager('BasicRenderer');
        $r = $sm->initialize('preview_bar_html');
        $r->setDraftMode(true);
        
        $r->assign('overhead_time', SmartestPersistentObject::get('timing_data')->getParameter('overhead_time_taken'));
        $r->assign('build_time', SmartestPersistentObject::get('timing_data')->getParameter('smarty_time_taken'));
        $r->assign('total_time', SmartestPersistentObject::get('timing_data')->getParameter('full_time_taken'));
        $r->assign('liberate_link_url', $preview_url);
        $r->assign('preview_link_url', $smartest_preview_url);
        $r->assign('page_webid', $request_data->getParameter('request_parameters')->getParameter('page_id'));
        $r->assign('hide_liberate_link', SmartestStringHelper::toRealBool(SmartestPersistentObject::get('request_data')->getParameter('request_parameters')->getParameter('hide_newwin_link')));
        
        if($request_data->getParameter('request_parameters')->hasParameter('item_id')){
            $item_id = (int) $request_data->getParameter('request_parameters')->getParameter('item_id');
            $item = new SmartestItem;
            if($item->find($item_id)){
                $r->assign('item_id', $item_id);
                $r->assign('model_name', $item->getModel()->getName());
                $r->assign('show_item_edit_link', true);
            }else{
                $r->assign('show_item_edit_link', false);
            }
        }else{
            $r->assign('show_item_edit_link', false);
        }
        
        $phtml = $r->fetch($filter->getDirectory().'previewbar.tpl');
        
        preg_match('/<body[^>]*?'.'>/i', $html, $match);
		
		if(!empty($match[0])){
			$body_tag = $match[0];
		}else{
			$body_tag = '';
		}
		
		$pcss = SmartestFileSystemHelper::load($filter->getDirectory().'previewbar.stylehtml.txt');
		$pcss = str_replace('%DOMAIN%', $filter->getRequestData()->g('domain'), $pcss);
		
		$html = str_replace('</head>', $pcss.'</head>', $html);
		$html = str_replace($body_tag, $body_tag."\n".$phtml, $html);
        $html = str_replace('</body>', "<script type=\"text/javascript\">if(parent.showPreview){parent.showPreview();}</script>\n<!--Page was built in: ".SmartestPersistentObject::get('timing_data')->getParameter('full_time_taken')."ms -->\n</body>", $html);
        
    }else{
        
        $heartbeat_id = SmartestPersistentObject::get('request_data')->getParameter('request_parameters')->getParameter('heartbeat_id');
        
        if($heartbeat_id == SM_CMS_PAGE_SITE_UNIQUE_ID){
            $html = str_replace('</head>', "<meta name=\"smartest:siteid\" content=\"".$heartbeat_id."\" />\n</head>", $html);
            $html = str_replace('</body>', "<!--SMARTEST HEARTBEAT-->\n</body>", $html);
        }
      
        /* 
        if(defined('SM_CMS_PAGE_SITE_UNIQUE_ID')){
            $sid = "<!--SMARTEST HEARTBEAT-->\n<!--SITEID: ".SM_CMS_PAGE_SITE_UNIQUE_ID."-->\n";
        }else{
            $sid = "<!--SMARTEST HEARTBEAT-->\n";
        }
        
        $creator = "\n<!--Powered by Smartest v".constant('SM_INFO_VERSION_NUMBER')." -->\n".$sid;
        $html = str_replace('</body>', $creator."<!--Page was returned in: ".SmartestPersistentObject::get('timing_data')->getParameter('full_time_taken')."ms -->\n</body>", $html);
        */
        
    }
    
    return $html;
    
}