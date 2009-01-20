<?php

function smartest_filter_preview($html, &$filter){
    
    if($filter->getDraftMode()){
        
        $phtml = SmartestFileSystemHelper::load($filter->getDirectory().'previewbar.html.txt');
        $phtml = str_replace('%OVERHEAD%', SM_OVERHEAD_TIME, $phtml);
        $phtml = str_replace('%BUILDTIME%', SM_SMARTY_TIME, $phtml);
        $phtml = str_replace('%TOTAL%', SM_TOTAL_TIME, $phtml);
        
        preg_match('/<body[^>]*?'.'>/i', $html, $match);
		
		if(!empty($match[0])){
			$body_tag = $match[0];
		}else{
			$body_tag = '';
		}
		
		$pcss = SmartestFileSystemHelper::load($filter->getDirectory().'previewbar.stylehtml.txt');
		$pcss = str_replace('%DOMAIN%', SM_CONTROLLER_DOMAIN, $pcss);
		
		$html = str_replace('</head>', $pcss.'</head>', $html);
		$html = str_replace($body_tag, $body_tag."\n".$phtml, $html);
        $html = str_replace('</body>', "<script language=\"javascript\">parent.showPreview();</script>\n<!--Page was built in: ".SM_TOTAL_TIME."ms -->\n</body>", $html);
        
    }else{
        
        $creator = "\n<!--Powered by Smartest(TM) Web Platform-->\n";
        $html = str_replace('</body>', $creator."<!--Page was returned in: ".SM_TOTAL_TIME."ms -->\n</body>", $html);
        
    }
    
    return $html;
    
}