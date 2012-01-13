<?php

function smartest_filter_preview($html, $filter){
    
    if($filter->getDraftMode()){
        
        $request_data = SmartestPersistentObject::get('request_data');
        
        $preview_url = '/website/renderEditableDraftPage?page_id='.$request_data->getParameter('request_parameters')->getParameter('page_id').'&amp;liberate_link=false';
        if($request_data->getParameter('request_parameters')->hasParameter('item_id')) $preview_url .= '&amp;item_id='.$request_data->getParameter('request_parameters')->getParameter('item_id');
        if($request_data->getParameter('request_parameters')->hasParameter('search_query')) $preview_url .= '&amp;q='.$request_data->getParameter('request_parameters')->getParameter('search_query');
        if($request_data->getParameter('request_parameters')->hasParameter('author_id')) $preview_url .= '&amp;author_id='.$request_data->getParameter('request_parameters')->getParameter('author_id');
        if($request_data->getParameter('request_parameters')->hasParameter('tag')) $preview_url .= '&amp;tag='.$request_data->getParameter('request_parameters')->getParameter('tag');
        
        $phtml = SmartestFileSystemHelper::load($filter->getDirectory().'previewbar.html.txt');
        $phtml = str_replace('%PREVIEWURL%', $preview_url, $phtml);
        $phtml = str_replace('%OVERHEAD%', SmartestPersistentObject::get('timing_data')->getParameter('overhead_time_taken'), $phtml);
        $phtml = str_replace('%BUILDTIME%', SmartestPersistentObject::get('timing_data')->getParameter('smarty_time_taken'), $phtml);
        $phtml = str_replace('%TOTAL%', SmartestPersistentObject::get('timing_data')->getParameter('full_time_taken'), $phtml);
        
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