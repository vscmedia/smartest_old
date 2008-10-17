<?php

SmartestHelper::register('HttpRequest');

class SmartestHttpRequestHelper extends SmartestHelper{
	
	static function getContent($address, $correctResources=true, $type='GET', $variables=''){
		
		if(substr($address, 0, 7) != 'http://' && substr($address, 0, 8) != 'https://'){
			$address = 'http://'.$address;
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $address);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Smartest PageGrab [HTTP Request Helper], (Version'.SM_SYSTEM_VERSION.')');
		
		if($type == 'POST'){
			curl_setopt($ch, CURLOPT_POST, 1);
		}
		
		$page = curl_exec($ch);
		curl_close($ch);
		
		
		if($correctResources){
		
			$res = self::getExternalResources($page);
			$protocol = (self::isSecure($address)) ? 'https://' : 'http://';
			$protocol_length = strlen($protocol);
			$hostname = self::getHostName($address);
			
			$urls = self::getLinkUrls($page);
			
			$already_processed = array();
			
			if(is_array($urls)){
				foreach($urls as $resource_url){
				    if(!in_array($resource_url, $already_processed)){
					    if($resource_url{0} == '/'){
					        
					        $regexp = SmartestStringHelper::toRegularExpression($resource_url);
    						$regexp = '/href=[\'"]?'.$regexp.'[\'"]/';
    						
    						$page = preg_replace($regexp, 'href="'.$protocol.$hostname.$resource_url."\\1".'"', $page);
    						$already_processed[] = $resource_url;
    						
    					}else{
    						if(substr($resource_url, 0, $protocol_length) != $protocol && strlen($resource_url) > 1){
    							$regexp = SmartestStringHelper::toRegularExpression($resource_url);
        						$page = preg_replace('/href=[\'"]?'.$regexp.'[\'"]/', 'href="'.$protocol.$hostname.$resource_url."\\1".'"', $page);
        						$already_processed[] = $resource_url;
    						}
    					}
				    }
				}
			}
			
			$already_processed = array();
		
			if(is_array($res)){
				foreach($res as $resource_url){
				    if(!in_array($resource_url, $already_processed)){
					    if($resource_url{0} == '/'){
    						$page = str_replace($resource_url, $protocol.$hostname.$resource_url, $page);
    						$already_processed[] = $resource_url;
    					}else{
    						if(substr($resource_url, 0, $protocol_length) != $protocol){
    							$page = str_replace($resource_url, $protocol.$hostname.'/'.$resource_url, $page);
    							$already_processed[] = $resource_url;
    						}
    					}
				    }
				}
			}
		
		}
		
		return $page;
		
	}
	
	static function getHostName($address){
		
		preg_match('/^https?:\/\/([\w\.]{7,})\//', $address, $matches);
		return $matches[1];
	}
	
	static function getHostAddress($address){
		
		// $host = self::getHostName($address);
		// return `host $host`;
	}
	
	static function getExternalResources($page){
		
		if(substr($page, 0, 7) == 'http://' || substr($page, 0, 8) == 'https://'){
			$html = self::getContent($page, false);
		}else{
			$html = $page;
		}
		
		preg_match_all('/<(link|script|img)[^>]+(href|src)=[\'"](\.{0,2}\/?[^\'"]+)/', $html, $matches);
		preg_match_all('/<style[^>]*>[\s\n]*@import\s*((url\()?[\'"]?([\w\.\/-]+)[\'"]?\)?);/', $html, $matches_2);
		
		// print_r($matches_2);
		
		$res = $matches[3];
		$css_imports = $matches_2[3];
		
		foreach($css_imports as $css){
			if(!in_array($css, $res)){
				$res[] = $css;
			}
		}
		
		return $res;
	}
	
	static function getLinkUrls($page){
	    
	    if(substr($page, 0, 7) == 'http://' || substr($page, 0, 8) == 'https://'){
			$html = self::getContent($page, false);
		}else{
			$html = $page;
		}
		
		preg_match_all('/<a[^>]*\shref=[\'"]?([^\'"]+)[\'"]([^>]*)>([^(<\/)]*)<\/a>/', $html, $matches);
		preg_match_all('/(window\.location|document\.location\.href)=[\'"](\.{0,2}\/?[^\'"]+)/', $html, $matches_2);
		
		$urls = $matches[1];
		$css_imports = $matches_2[2];
		
		foreach($css_imports as $css){
			if(!in_array($css, $urls)){
				$urls[] = $css;
			}
		}
		
		return $urls;
		
	}
	
	static function getTitle($page){
		
		if(substr($page, 0, 7) == 'http://' || substr($page, 0, 8) == 'https://'){
			$html = self::getContent($page, false);
		}else{
			$html = $page;
		}
		
		preg_match('/<title>(.+)<\/title>/', $html, $matches);
		
		return $matches[1];
		
	}
	
	static function getMetas($page){
		
		if(substr($page, 0, 7) == 'http://' || substr($page, 0, 8) == 'https://'){
			$html = self::getContent($page, false);
		}else{
			$html = $page;
		}
		
		preg_match_all('/<meta (name|http-equiv)=[\'"]?([^"\']+)[\'"]? content=[\'"]?([^"\']*)[\'"]?/', $html, $matches);
		
		$metas = array();
		
		foreach($matches[0] as $key=>$meta){
			$metas[$key]['name'] = $matches[2][$key];
			$metas[$key]['value'] = $matches[3][$key];
			$metas[$key]['type'] = ($matches[1][$key] == 'http-equiv') ? 'http-equiv' : 'normal';
		}
		
		return $metas;
		
	}
	
	static function isSecure($address){
		return (substr($address, 0, 8) == 'https://');
	}
	
}