<?php

SmartestHelper::register('UserAgent');

class SmartestUserAgentHelper extends SmartestHelper{
	
	protected $browser = array();
	protected $simpleObject;
	protected $userAgent;
	
	function __construct(){
    	
    	$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
    	
    	//language
		if($languages = getenv('HTTP_ACCEPT_LANGUAGE')){
			$languages = preg_replace('/(;q=[0-9]+.[0-9]+)/i','',$languages);
    	}else{
			$languages = 'en-us';
    	}
    	
        $this->browser['language'] = $languages; 
	}
	
	public function getPlatform(){
	    
	    if(!isset($this->browser['platform'])){
	    
	        // detect platform	    
    	    if(preg_match('/Win(dows|98|95|32|16|NT|XP)/i', $this->userAgent)) {
    	  	    $this->browser['platform'] = 'Windows';
   		    }else if(stripos($this->userAgent, 'Mac')) {
    	  	    $this->browser['platform'] = 'Macintosh';
    	    }else if(stripos($this->userAgent, 'linux')) {
    	  	    $this->browser['platform'] = 'GNU/Linux';
            }else if(stripos($this->userAgent, 'unix')) {
                $this->browser['platform'] = 'Unix';
    	    }else{
    	 	    $this->browser['platform'] = 'Unknown';
    	    }
    	}
    	
    	return $this->browser['platform'];
    	
	}
    
    public function getAppName(){
	    
	    if(!isset($this->browser['appName'])){
	    
	        // detect browser program
		    if (stripos($this->userAgent, 'MSIE')){
                $this->browser['appName'] = 'Explorer';
    	    }else if (stripos($this->userAgent, 'Safari')) {
                $this->browser['appName'] = 'Safari';
    	    }else if (stripos($this->userAgent, 'Firefox')) {
                $this->browser['appName'] = 'Firefox';
    	    }else if (stripos($this->userAgent, 'Opera')) {
                $this->browser['appName'] = 'Opera';
            }else if (stripos($this->userAgent, 'OmniWeb')) {
                $this->browser['appName'] = 'OmniWeb';
            }else if (stripos($this->userAgent, 'Netscape')) {
                $this->browser['appName'] = 'Netscape';
            }else if (stripos($this->userAgent, 'Camino')) {
                $this->browser['appName'] = 'Camino';
            }else{
    		    $this->browser['appName'] = 'Unknown';
    	    }
    	}
    	
	    return $this->browser['appName'];
	}
	
	public function getAppVersion(){
	    
	    if(!isset($this->browser['appVersion'])){
	        
    	    if($this->isExplorer()){
    	        // look for number after 'MSIE'
    	        preg_match('/MSIE\s?(\d+\.\d+)/i', $this->userAgent, $matches);
    	        $this->browser['appVersion'] = $matches[1];
    	        $this->browser['appVersionInteger'] = (int) floor($matches[1]);
    	    }else if($this->isSafari()){
    	        preg_match('/Safari\/((\d+)(\.[\d]+)+)/i', $this->userAgent, $matches);
    	        $build = $matches[2];
    	        // echo $build;
    	        switch($build){
    	            case 85:
    	            $this->browser['appVersion'] = '1.0';
    	            $this->browser['appVersionInteger'] = 1;
    	            break;
    	            case 100:
    	            $this->browser['appVersion'] = '1.1';
    	            $this->browser['appVersionInteger'] = 1;
    	            break;
    	            case 125:
    	            $this->browser['appVersion'] = '1.2';
    	            $this->browser['appVersionInteger'] = 1;
    	            break;
    	            case 312:
    	            $this->browser['appVersion'] = '1.3';
    	            $this->browser['appVersionInteger'] = 1;
    	            break;
    	            case 412:
    	            $this->browser['appVersion'] = '2.0';
    	            $this->browser['appVersionInteger'] = 2;
    	            break;
    	            case 416:
    	            $this->browser['appVersion'] = '2.0.2';
    	            $this->browser['appVersionInteger'] = 2;
    	            break;
    	            case 417:
    	            $this->browser['appVersion'] = '2.0.3';
    	            $this->browser['appVersionInteger'] = 2;
    	            break;
    	            case 419:
    	            $this->browser['appVersion'] = '2.0.4';
    	            $this->browser['appVersionInteger'] = 2;
    	            break;
    	            case 522:
    	            $this->browser['appVersion'] = '3.0';
    	            $this->browser['appVersionInteger'] = 3;
    	            break;
    	            case 525:
    	            $this->browser['appVersion'] = '3.1.1';
    	            $this->browser['appVersionInteger'] = 3;
    	            break;
    	        }
    	    }else if($this->isFirefox()){
    	        preg_match('/Firefox\/(\d[\d\.]+\d+)/i', $this->userAgent, $matches);
    	        $this->browser['appVersion'] = $matches[1];
    	        $this->browser['appVersionInteger'] = (int) $matches[1]{0};
    	    }else if($this->isCamino()){
        	    preg_match('/Camino\/(\d[\d\.]+\d+)/i', $this->userAgent, $matches);
        	    $this->browser['appVersion'] = $matches[1];
        	    $this->browser['appVersionInteger'] = (int) $matches[1]{0};
        	    // echo $this->userAgent;
        	}
    	}
	    
	    return $this->browser['appVersion'];
	}
	
	public function getAppVersionInteger(){
	    $this->getAppVersion();
	    return $this->browser['appVersionInteger'];
	}
	
	public function getRenderingEngineName(){
	    
	    if(!isset($this->browser['engine'])){
	    
	        // set engine
            if (stripos($this->userAgent, 'Gecko')) { // rendering engines
                
                if (stripos($this->userAgent, 'KHTML') === FALSE) {
                    $this->browser['engine'] = 'Gecko';
                }else{
                    if(stripos($this->userAgent, 'AppleWebKit') === FALSE){
                        $this->browser['engine'] = 'KHTML';
                    }else{
                        $this->browser['engine'] = 'AppleWebKit';
                    }
                }
                
            }else{
                if($this->isExplorer()){
                    if($this->isMacintosh()){
                        $this->browser['engine'] = 'MSIE for MAC';
                    }else{
                        $this->browser['engine'] = 'MSIE';
                    }
                }
            }
        }
        
        return $this->browser['engine'];
        
	}
    
	// platforms
	public function isMacintosh(){
		return $this->getPlatform() == "Macintosh" ? true : false;
	}
	
	public function isWindows(){
		return $this->getPlatform() == "Windows" ? true : false;
	}
	
	public function isLinux(){
		return $this->getPlatform() == "GNU/Linux" ? true : false;
	}
	
	public function isUnix(){
		return ($this->getPlatform() == "Unix" /* || ($this->isMacintosh() && $this->) */) ? true : false;
	}
	
	// rendering engines
	function isGecko(){
		return $this->getRenderingEngineName() == "Gecko" ? true : false;
	}
	
	// browsers 
	public function isSafari(){
		return $this->getAppName() == "Safari" ? true : false;
	}
	
	public function isExplorer(){
		return $this->getAppName() == "Explorer" ? true : false;
	}
	
	public function isFirefox(){
		return $this->getAppName() == "Firefox" ? true : false;
	}
	
	public function isCamino(){
		return $this->getAppName() == "Camino" ? true : false;
	}
	
	public function getLanguage(){
		return $this->browser['language'];
	}
	
	public function getSimpleClientSideObject(){
	    if(is_object($this->simpleObject)){
	        return $this->simpleObject;
	    }else{
	        $this->simpleObject = new SmartestClientSideUserAgentObject;
	        $this->simpleObject->appName = $this->getAppName();
	        $this->simpleObject->appVersion = $this->getAppVersion();
	        $this->simpleObject->appVersionInteger = $this->getAppVersionInteger();
	        $this->simpleObject->platform = $this->getPlatform();
	        $this->simpleObject->engine = $this->getRenderingEngineName();
	        $this->simpleObject->isGecko = $this->isGecko();
	        $this->simpleObject->language = $this->browser['language'];
	        return $this->simpleObject;
	    }
	}
	
	public function __toArray(){
	    $array = array();
	    $array['appName'] = $this->getAppName();
	    $array['appVersion'] = $this->getAppVersion();
	    $array['appVersionInteger'] = $this->getAppVersionInteger();
	    $array['platform'] = $this->getPlatform();
	    $array['engine'] = $this->getRenderingEngineName();
	    $array['isGecko'] = $this->isGecko();
	    $array['language'] = $this->browser['language'];
	    return $array;
	}
	
	public function getSimpleClientSideObjectAsJson(){
	    return json_encode($this->getSimpleClientSideObject());
	}

}

// Safari: Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/418.8 (KHTML, like Gecko) Safari/419.3
// full list of user agents at: http://www.pgts.com.au/pgtsj/pgtsj0208c.html