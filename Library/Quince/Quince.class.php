<?php

class QuinceAction{
    
    protected $_request;
    protected $_use_checking;
    protected $_action_object;
    protected $_methods;
    protected $_action;
    protected $_class;
    protected $_module_info;
    
    public function __construct($use_checking=true){
        $this->_use_checking = $use_checking;
    }
    
    public function assignRequest($r){
        $this->_request = $r;
    }
    
    public function prepareActionObject(){
        
        if($this->_module_info = QuinceUtilities::cacheGet('module_config_'.$this->_request->getModule())){
	        
	        if(!$this->_request->getAction()){
	            $this->_request->setAction($this->_module_info['default_action']);
	        }
	        
	        // Set the class and action names, accounting for any modifications from the namespace
            if($this->_request->getNamespace() != 'default' || isset($this->_module_info['namespaces']['default'])){
                if(isset($this->_module_info['namespaces'][$this->_request->getNamespace()]['affect'])){
                    $affect = $this->_module_info['namespaces'][$this->_request->getNamespace()]['affect'];
                    $this->_class = ($affect == 'class') ? $this->_request->getNamespace().'_'.$this->_module_info['class'] : $this->_module_info['class'];
                    $this->_action = ($affect == 'action') ? $this->_request->getNamespace().'_'.$this->_request->getAction() : $this->_request->getAction();
                    $default_action = ($affect == 'action') ? $this->_request->getNamespace().'_'.$this->_module_info['default_action'] : $this->_module_info['default_action'];
                }else{
                    $this->_class = $this->_module_info['class'];
                    $this->_action = $this->_request->getAction();
                    $default_action = $this->_module_info['default_action'];
                }
            }else{
                $this->_class = $this->_module_info['class'];
                $this->_action = $this->_request->getAction();
                $default_action = $this->_module_info['default_action'];
            }

            $this->_class = $this->_module_info['class'];
            $this->_action = $this->_request->getAction();
	        
	        // Make sure the actual class file exists
	        if(!$this->_use_checking || ($this->_use_checking && is_file($this->_module_info['directory'].$this->_class.'.class.php'))){
	            
	            if(!class_exists($this->_class)){
	                include($this->_module_info['directory'].$this->_class.'.class.php');
                }
                
                // Check that the module's class exists by seeing whether, once the file has been included, the class is defined
                if(!$this->_use_checking || ($this->_use_checking && class_exists($this->_class))){
	                
	                // set up charsets and content types
                    if($this->_request->getNamespace() == 'default' && !isset($this->_module_info['namespaces']['default'])){
                        $this->_request->setCharset(Quince::$default_charset);
                        $this->_request->setContentType(Quince::$default_content_type);
                    }else{
                        if(isset($this->_module_info['namespaces'][$this->_request->getNamespace()])){
                            $this->_request->setCharset(Quince::$default_charset);
                            $this->_request->setContentType(Quince::$default_content_type);
                            if(isset($this->_module_info['namespaces'][$this->_request->getNamespace()]['charset']) && $this->_module_info['namespaces'][$this->_request->getNamespace()]['charset']) $this->_request->setCharset($this->_module_info['namespaces'][$this->_request->getNamespace()]['charset']);
                            if(isset($this->_module_info['namespaces'][$this->_request->getNamespace()]['content_type']) && $this->_module_info['namespaces'][$this->_request->getNamespace()]['content_type']) $this->_request->setContentType($this->_module_info['namespaces'][$this->_request->getNamespace()]['content_type']);
                        }else{
                            $this->_request->setCharset(Quince::$default_charset);
                            $this->_request->setContentType(Quince::$default_content_type);
                        }
                    }

                    // pass on extra info in the module config
                    $this->_request->setMetas($this->_module_info['meta']);
                    if(isset($this->_module_info['namespaces'][$this->_request->getNamespace()]['meta']) && is_array($this->_module_info['namespaces'][$this->_request->getNamespace()]['meta'])){
                        foreach($this->_module_info['namespaces'][$this->_request->getNamespace()]['meta'] as $k => $v){
                            $this->_request->setMeta($k, $v);
                        }
                    }

                    $this->_request->setMeta('_module_longname', $this->_module_info['longname']);
                    $this->_request->setMeta('_module_identifier', $this->_module_info['identifier']);
                    $this->_request->setMeta('_module_dir', $this->_module_info['directory']);
                    $this->_request->setMeta('_module_php_class', $this->_class);
	                
	                // Check that the method is implemented, but only if use checking is enabled
	                if($this->_use_checking){
	                    
	                    $this->_methods = get_class_methods($this->_class);
	                    
	                    if(!in_array($this->_action, $this->_methods)){
	                        
	                        if($this->_action == $default_action){
	                            throw new QuinceException("Class '".$this->_class."' does not contain required action: ".$this->_action);
                            }
                            
                            if(!in_array($default_action, $this->_methods)){
                                throw new QuinceException("Class '".$this->_class."' does not contain required action: ".$this->_action.", nor the module default action: ".$default_action.".");
                            }else{
                                $this->_request->setAction($default_action);
                            }
	                    }
	                    
	                }
	                
	                // Instantiate the class
	                $this->_action_object = new $this->_class($this->_request);
	                
	                // Make the action object available in the request data
	                $this->_request->setUserActionObject($this->_action_object);
	                
	            }else{
	                throw new QuinceException("File ".$this->_class.".class.php does not contain required class: ".$this->_class);
	            }
	        }else{
	            throw new QuinceException("Module '{$this->_request->getModule()}' does not contain required class file: ".$this->_module_info['directory'].$this->_class.'.class.php');
	        }
	        
	    }else{
	        throw new QuinceException("Could not retrieve module info for module '{$this->_request->getModule()}' from cache.");
	    }
        
    }
    
    public function execute(){
        
        // make sure the method is callable
        if($this->_use_checking){
        
            if(!is_callable(array($this->_action_object, $this->_action))){
                if($this->_action == $this->_module_info['default_action']){
                    throw new QuinceException("Method '".$this->_action."' of class '".$this->_class."' is either private or protected, and cannot be called.");
                }else{
                    if(is_callable(array($this->_action_object, $this->_module_info['default_action']))){
                        // the current action is not the default action
                        $this->_action = $this->_module_info['default_action'];
                    }else{
                        throw new QuinceException("Method '".$this->_action."' of class '".$this->_class."' is either private or protected, and cannot be called. The module's default action was not callable either");
                    }
                }
            }
        
        }
        
        // make the request object available to the action itself
        if(!$this->_use_checking || ($this->_use_checking && in_array('setCurrentRequest', $this->_methods))){
            $this->_action_object->setCurrentRequest($this->_request);
        }
        
        // $args is to enable easy migration from earlier Quince versions
        $vars = $this->_request->getRequestParameters();
        $args = array('get'=>$vars, 'post'=>$_POST);
        
        try{
            
            $this->_request->setUserActionObject($this->_action_object);
            
            // call the module's pre-action function
            $this->_action_object->__pre();
            
            // now call the function. If it forwards, this will be the last line that gets executed
            $result = call_user_func_array(array($this->_action_object, $this->_action), $args);
            return $result;
            
        }catch(Exception $e){
            throw $e;
        }
        
    }

}

class QuinceRequest{
    
    protected $_module;
    protected $_action;
    protected $_domain;
    protected $_namespace;
    protected $_request_string;
    protected $_content_type = null;
    protected $_charset = null;
    protected $_is_alias = false;
    protected $_request_params = array();
    protected $_metas = array();
    protected $_result = null;
    protected $_user_action_object = null;
    
    final public function getModificationsEnabled(){
        return $this->_modifications_allowed;
    }
    
    final public function setModificationsEnabled($m){
        $this->_modifications_enabled = (bool) $m;
    }
    
    final public function getModule(){
        return $this->_module;
    }
    
    final public function setModule($m){
        $this->_module = $m;
    }
    
    final public function getAction(){
        return $this->_action;
    }
    
    final public function setAction($a){
        $this->_action = $a;
    }
    
    final public function getDomain(){
        return $this->_domain;
    }
    
    final public function getHyperlinkDomain(){
        if($this->_namespace == 'default'){
            return $this->_domain;
        }else{
            return $this->_domain.$this->_namespace.':';
        }
    }
    
    final public function setDomain($d){
        $this->_domain = $d;
    }
    
    final public function getRequestString(){
        return $this->_request_string;
    }
    
    final public function getHyperlinkRequestString(){
        if($this->_namespace == 'default'){
            return $this->_request_string;
        }else{
            if($this->_request_string){
                return $this->_request_string;
            }else{
                return 'index';
            }
        }
    }
    
    final public function setRequestString($r){
        
        // check for namespace
        if(Quince::$use_namespaces){
            if(preg_match('/^(([^:]+):)[^:]+$/', reset(explode("/", $r)), $matches)){
    			$r = substr($r, strlen($matches[1]));
    			$this->_namespace = $matches[2];
    		}else{
    		    $this->_namespace = 'default';
    		}
	    }
	
		if($r == 'index'){
		    $r = '';
		}
	
		$this->_request_string = $r;
    }
    
    final public function getContentType(){
        return $this->_content_type;
    }
    
    final public function setContentType($c){
        $this->_content_type = $c;
    }
    
    final public function getCharset(){
        return $this->_charset;
    }
    
    final public function setCharset($c){
        $this->_charset = $c;
    }
    
    final public function getNamespace(){
        return $this->_namespace;
    }
    
    final public function setNamespace($n){
        $this->_namespace = $n;
    }
    
    final public function hasRequestParameter($n){
        return isset($this->_request_params[$n]);
    }
    
    final public function getRequestParameter($n){
        return isset($this->_request_params[$n]) ? $this->_request_params[$n] : null;
    }
    
    final public function setRequestParameter($n, $v){
        $this->_request_params[$n] = $v;
    }
    
    final public function getRequestParameters(){
        return $this->_request_params;
    }
    
    final public function getIsAlias(){
        return $this->_is_alias;
    }
    
    final public function setIsAlias($b){
        $this->_is_alias = (bool) $b;
    }
    
    final public function isReady(){
        return isset($this->_module);
    }
    
    final public function getResult(){
        return $this->_result;
    }
    
    final public function setResult($result){
        $this->_result = $result;
    }
    
    final public function getMetas(){
        return $this->_metas;
    }
    
    final public function setMetas($m){
        $this->_metas = $m;
    }
    
    final public function getMeta($m){
        return $this->_metas[$m];
    }
    
    final public function setMeta($name, $value){
        $this->_metas[$name] = $value;
    }
    
    final public function getClass(){
        return $this->_metas['_module_php_class'];
    }
    
    final public function getUserActionObject(){
        return $this->_user_action_object;
    }
    
    final public function setUserActionObject($o){
        $this->_user_action_object = $o;
    }
    
}

// This class should be extended by the modules' main classes
class QuinceBase{
    
    protected $_request;
    
    final public function __construct($r){
        $this->setCurrentRequest($r);
        $this->__moduleConstruct();
    }
    
    protected function forward($module, $action){
        
        $e = new QuinceForwardException('');
        $e->setModule($module);
        $e->setAction($action);
        throw $e;
        
    }
    
    public function give($name, $data){
        
        if($name == '_request'){
            throw new QuinceException("Cannot overwrite QuinceBase->_request with QuinceBase->give()");
        }
        
        $this->$name = $data;
    }
    
    protected function redirect($to, $http_code=303, $exit=true){
        
        if(!$to){
			
			$destination = $this->_request->getDomain();
			
		}else if($to{0} == "/"){
		    
		    if($this->_request->getDomain() == '/' || substr($to, 0, strlen($this->_request->getDomain())) == $this->_request->getDomain()){
		        $destination = $to;
	        }else{
	            $destination = $this->_request->getDomain().substr($to, 1);
	        }
	        
	    }else if(preg_match('/^@(\w+):(\w+)(\?(.*))?$/', $to, $matches)){
            
            $r = new QuinceRouter;
            $r->setRequest($this->_request);
            
            if($r->routeExists($matches[1].':'.$matches[2])){
                $destination = $r->fetchRouteUrl($to, $matches);
            }
            
		}else if(preg_match('/^@(\w+)(\?(.*))?$/', $to, $matches)){
            $r = new QuinceRouter;
            $r->setRequest($this->_request);
            if($r->routeExists($this->_request->getModule().':'.$matches[1])){
                $destination = $r->fetchRouteUrl('@'.$this->_request->getModule().':'.$matches[1].$matches[2]);
            }
		}else{
		    $destination = $to;
		}
		
		throw new QuinceRedirectException($destination);
		
    }
    
    public function setCurrentRequest($r){
        $this->_request = $r;
    }
    
    protected function getRequest(){
        return $this->_request;
    }
    
    protected function __moduleConstruct(){}
    public function __pre(){}
    
}

class QuinceUtilities{
    
    public static $quince;
    
    public static function cacheGet($name){
        $fn = Quince::$cache_dir.md5($name).'.tmp';
        if(file_exists($fn)){
            return unserialize(file_get_contents($fn));
        }else{
            return false;
        }
    }
    
    public static function cacheSet($name, $data){
        $fn = Quince::$cache_dir.md5($name).'.tmp';
        return file_put_contents($fn, serialize($data));
    }
    
    public static function cacheHas($name){
        $fn = Quince::$cache_dir.md5($name).'.tmp';
        return file_exists($fn);
    }
    
    public static function cacheClear($name){
        $fn = Quince::$cache_dir.md5($name).'.tmp';
        if(file_exists($fn)){
            return unlink($fn);
        }else{
            return false;
        }
    }
    
    public static function fetchConfig($config_file){
	    $config = self::yamlLoad($config_file);
	    return $config['quince'];
	}
	
	public static function fetchModuleConfig($config_file){
	    $config = self::yamlFastLoad($config_file);
        return $config['module'];
	}
	
	public static function yamlLoad($file_name){
	    if(is_file($file_name)){
            $spyc = new Spyc;
            $array = $spyc->loadFile($file_name);
            return $array;
        }else{
            // error
            // throw new QuinceException("QuinceUtilities tried to load non-existent YAML file: ".$file_name);
            self::$quince->handleException(new QuinceException("QuinceUtilities tried to load non-existent YAML file: ".$file_name));
        }
    }
    
    public static function yamlFastLoad($file_name){
        if(is_file($file_name)){
            if(self::cacheGet('syhh_'.crc32($file_name)) == md5_file($file_name)){
                return self::cacheGet('syhc_'.crc32($file_name), true);
            }else{
                self::cacheSet('syhh_'.crc32($file_name), md5_file($file_name));
                $content = self::yamlLoad($file_name);
                self::cacheSet('syhc_'.crc32($file_name), $content);
                return $content;
            }
        }else{
            throw new QuinceException("QuinceUtilities tried to load non-existent YAML file: ".$file_name);
        }
    }
    
    public static function dirContents($dir, $t=0){
        
        $files = array();

	    $res = opendir($dir);
	    $str = '';

		while (false !== ($file = readdir($res))) {

    		if($file{0} != '.'){
    		    
    		    $files[] = is_dir($dir.$file) ? $dir.utf8_encode($file).'/' : $dir.utf8_encode($file);
    		    
    		}

		}
		
		closedir($res);
        return $files;
        
    }
    
    public static function excapeRegexCharacters($s){
        
        $regexp = str_replace('/', '\/', $s);
		$regexp = str_replace('|', '\|', $regexp);
		$regexp = str_replace('[', '\[', $regexp);
		$regexp = str_replace(']', '\]', $regexp);
		$regexp = str_replace('{', '\{', $regexp);
		$regexp = str_replace('}', '\}', $regexp);
		$regexp = str_replace('.', '\.', $regexp);
        
        return $regexp;
        
    }
    
    public static function stripSlashesFromArray($value){
		return is_array($value) ? array_map(array('QuinceUtilities','stripSlashesFromArray'), $value) : utf8_encode(stripslashes($value));
	}
	
	public static function getAliasUrlRequiredArguments($alias_url){
	    preg_match_all('/:([\w_]+)/', $alias_url, $matches);
	    return $matches[1];
	}
	
	public static function buildQueryString($vars){
	    
	    $frags = array();
	    
	    foreach($vars as $k => $v){
	        $frags[] = $k.'='.$v;
	    }
	    
	    return implode('&', $frags);
	}
    
}

class QuinceRouter{
    
    protected $_request;
    
    public function setRequest($r){
        $this->_request = $r;
    }
    
    public function routeExists($route){
        return array_key_exists($route, Quince::$routes);
    }
    
    public function fetchRouteUrl($rc, $matches=''){
        
        if(!is_array($matches)){
            if (!preg_match('/^@(\w+):(\w+)(\?(.*))?$/', $rcd, $matches)){
                if(preg_match('/^@(\w+)(\?(.*))?$/', $rcd, $f_matches)){
                    $matches = array();
                    $matches[1] = $this->_request->getModule();
                    $matches[2] = $f_matches[1];
                    $matches[3] = $f_matches[2];
                    $matches[4] = $f_matches[3];
                }
            }
        }
        
        $k = $matches[1].':'.$matches[2];
        
        if(isset(Quince::$routes[$k])){
            
            $route = Quince::$routes[$k];
            $params = $route['params'];
        
            if(strlen($matches[3])){
                $new_params = array();
                parse_str($matches[4], $new_params);
                $params = array_merge($params, $new_params);
            }
        
            if(isset($params['namespace'])){
                $namespace = $params['namespace'];
                unset($params['namespace']);
            }else{
                $namespace = 'default';
            }
        
            if(isset($route['url'])){
            
                if($namespace == 'default'){
                    $url = $this->_request->getDomain().substr($route['url'], 1);
                }else{
                    $url = $this->_request->getDomain().$namespace.'+_NSS+'.substr($route['url'], 1);
                }
            
                $route_required_params = QuinceUtilities::getAliasUrlRequiredArguments($route['url']);
            
                foreach($route_required_params as &$rp){
                    if(isset($params[$rp])){
                        $url = str_replace(':'.$rp, $params[$rp], $url);
                        unset($params[$rp]);
                        unset($rp);
                    }else{
                        throw new QuinceException('Route @'.$route['module'].':'.$route['name'].' requires a missing \''.$rp.'\' parameter.');
                    }
                }
            
                return str_replace('+_NSS+', ':', $url);
            
            }else{
            
                // No url is specified for this route, so just return the ordinary /module/action?args type of URL
                if($namespace == 'default'){
                    $url = $this->_request->getDomain().$route['module'].'/'.$route['action'];
                }else{
                    $url = $this->_request->getDomain().$namespace.'+_NSS+'.$route['module'].'/'.$route['action'];
                }
            
                if(strlen($matches[3])){
                    $url.='?'.QuinceUtilities::buildQueryString($params);
                }
            
                return $url;
            }
            
        }else{
            
            throw new QuinceException('Route @'.$route['module'].':'.$route['name'].' is not defined.');
            
        }
    }
    
}

class QuinceException extends Exception{
    // this is done so that Exception functionality can be added with minimal disruption
}

class QuinceForwardException extends QuinceException{

    protected $_module;
    protected $_action;
    
    public function getModule(){
        return $this->_module;
    }
    
    public function setModule($m){
        $this->_module = $m;
    }
    
    public function getAction(){
        return $this->_action;
    }
    
    public function setAction($a){
        $this->_action = $a;
    }

}

class QuinceRedirectException extends QuinceException{

    protected $_redirectUrl = null;
    protected $_status_codes = array(301=>"Moved Permanently", 302=>"Found", 303=>"See Other", 304=>"Not Modified", 305=>"Use Proxy", 307=>"Temporary Redirect");
    
    const PERMANENT = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;
    const TEMPORARY = 307;
    
    public function __construct($url=false){
        if(strlen($url)){
            $this->_redirectUrl = $url;
        }
    }
    
    public function setRedirectUrl($url){
	    $this->_redirectUrl = $url;
	}
	
	public function getRedirectUrl(){
	    return $this->_redirectUrl;
	}
	
	public function redirect($sc=303, $exit=true){
	    
	    header("HTTP/1.1 ".$sc." ".$this->_status_codes[$sc]);
        header("Location: ".$this->getRedirectUrl());
        
        if($exit){
            // exit;
        }
	    
	}

}

class Quince{
    
    const CURRENT_URL = '___QUINCE_CURRENT_URL';
    const CURRENT_DIR = '___QUINCE_CURRENT_DIR';
    const VERSION = '2.0 beta';
    
    protected $_home_dir;
    protected $_cache_dir;
	protected $_module_dirs = array();
	protected $_module_conf;
	protected $_default_module_name;
	protected $_use_checking;
	protected $_request_class;
	protected $_exception_handling;
	protected $_existing_modules;
	protected $_num_forwards = 0;
	protected $_num_redirects = 0;
	protected $_current_request;
	protected $_current_action;
	
	public $module_shortnames;
	
	public static $modules;
	public static $raw_aliases;
	public static $alias_shortcuts;
	public static $routes;
	public static $home_dir;
	public static $cache_dir;
	public static $default_charset;
	public static $default_content_type;
	public static $use_namespaces;
	
    public function __construct($home_dir="___QUINCE_CURRENT_DIR", $config_file='quince.yml'){
        
        // this value is always checked as it's 110% essential
        if($home_dir == "___QUINCE_CURRENT_DIR"){
            $this->_home_dir = getcwd().'/';
        }else if(!is_dir(dirname($home_dir))){
            $this->handleException(new QuinceException('The specified $home_dir:'.$home_dir.' does not exist.'));
        }else{
            $this->_home_dir = $home_dir;
        }
        
        // make some values available to other classes and beyond, as constants
        self::$home_dir = $this->_home_dir;
        
        if(!is_file($this->_home_dir.$config_file)){
            throw new QuinceException('Quince configuration file not found: '.self::$this->_home_dir.$config_file);
        }
        
        // load quince configuration
        $config = QuinceUtilities::fetchConfig($this->_home_dir.$config_file);
        // var_dump(is_file($this->_home_dir.$config_file));
        $this->_exception_handling = $config['exception_handling'];
        $this->_module_dirs = $config['modules']['storage'];
        $this->_module_conf = $config['modules']['config'];
        $this->_request_class = $config['request_class'];
        $this->_default_module_name = $config['default_module'];
        $this->_use_checking = $config['use_checking'];
        $this->_use_namespaces = $config['use_namespaces'];
        $this->_cache_dir = realpath($this->_home_dir.$config['cache_dir']).'/';
        
        // again, make this available for other classes, not least of which QuinceUtilities
        self::$cache_dir = $this->_cache_dir;
        self::$default_charset = $config['default_charset'];
        self::$default_content_type = $config['default_content_type'];
        self::$use_namespaces = $config['use_namespaces'];
        
    }
    
    private function removeQueryString($url){
	    
	    $hash = md5($url);
	    
	    if(!isset($this->_non_query_urls[$hash])){
	        if($nq = strpos($url, '?')){
    	        $url = substr($url, 0, $nq);
    	    }
    	    
    	    $this->_non_query_urls[$hash] = $url;
    	    
	    }
	    
	    return $this->_non_query_urls[$hash];
	    
	}
    
    public function processRequest($url){
	    
	    $r = new $this->_request_class;
	    
	    // MultiViews support: look for URLS like index.php/module/action
		$fc_filename = basename($_SERVER['SCRIPT_FILENAME']).'/';
		$fc_filename_len = strlen($fc_filename);
		
	    // $ulength = strlen($url.'/');
	    
	    // echo getcwd().' ';
	    // echo $_SERVER["DOCUMENT_ROOT"].'/ ';
	    // echo $url;
	    
	    /* 
	    // If the end of the url and the file path are the same
	    if(substr(getcwd().'/', $ulength*-1, $ulength) == $url.'/'){
	        $r->setRequestString('');
	        $r->setDomain($url.'/');
	        return $r;
	    }
	    */
	    
	    // TAKE ALL THE BITS OF THE REQUEST_URI THAT AREN'T IN THE DOCUMENT_ROOT AND MAKE THEM THE DOMAIN
	    
	    $test_url = $url{(strlen($url)-1)} == '/' ? substr($url, 0, -1) : $url;
	    
	    // Calculate the domain
	    // $hdp = explode('/', getcwd());
	    // $hdp = explode('/', $test_url);
        // array_shift($hdp);
        // array_pop($hdp);
        // print_r($hdp);
        // $reverse = array_reverse($hdp);
        // $possible_dir = implode('/', $hdp).'/';
        
        // echo $possible_dir;
        
        // array_shift($hdp);
        
        // $cwd = getcwd();
        
        // echo $dr.'/'.$possible_dir.' ';
        // echo $dr;
        // $f = strpos($dr, $cwd);
        
        $dr = realpath($_SERVER["DOCUMENT_ROOT"]).'/';
        $hdp = explode('/', $test_url);
        array_shift($hdp);
        array_pop($hdp);
        $possible_dir = implode('/', $hdp).'/';
        
        while(!is_dir($dr.$possible_dir)){
            array_pop($hdp);
            $possible_dir = implode('/', $hdp).'/';
        }
        
        if($possible_dir == '/'){
            $r->setDomain('/');
            $r->setRequestString(substr($url, 1));
        }else{
            $r->setDomain('/'.$possible_dir);
            $r->setRequestString(substr($url, strlen($possible_dir)+1));
        }
        
        return $r;
        
        /* if(is_dir($dr.$possible_dir)){
            $r->setDomain('/'.$possible_dir);
            $r->setRequestString(substr($url, strlen($possible_dir)+1));
        }else{
            $r->setDomain('/');
            $r->setRequestString(substr($url, 1));
        } */
        
        // print_r($r);
        
        
        
        /* echo $dr.' * ';
        echo $test_url;
        
        if($f === false){
            throw new QuinceException("Domain could not be calculated: Document root not found in current working directory");
        }else{
            if($f > 0){
                $docroot = substr($cwd, 0, $f).$_SERVER["DOCUMENT_ROOT"];
            }else{
                $docroot = $_SERVER["DOCUMENT_ROOT"];
            }
        } */
        
        /* if(){
            
        } */
        
        /* if(strlen($cwd) == strlen($docroot)){
            
            $r->setRequestString('');
	        $r->setDomain($url.'/');
	        return $r;
	        
        }else if(strlen($cwd) > strlen($docroot)){
            
            $possible_domain = substr($cwd, strlen($docroot));
            $start = strlen($possible_domain)+1;
            
            if(substr($_SERVER['REQUEST_URI'], 0, $start) == $possible_domain.'/'){
                $r->setDomain($possible_domain.'/');
                $r->setRequestString(substr($url, $start));
                return $r;
            }
            
        } */
        
        // echo $cwd.' '.$docroot;
        
        // $num_folders = count($hdp);
        
        /* for($i=0;$i<$num_folders;$i++){
            $try_path = '/'.implode('/', $hdp).'/';
            // echo $try_path.' ';
            $substr_start = strlen($try_path)*-1;
            // print_r($hdp);
            $request = array_pop($hdp).'/'.$request;
            // echo $request;
            
        }
        
        $argnum = 1;
        $count = (count($hdp)-1);
        $ds = array();
        
        for($i=0;$i<$count;++$i){
            $ds[] = '/'.implode('/', array_reverse(array_slice($hdp, 0, ($argnum * -1)))).'/';
            ++$argnum;
        }
        
        $ds = array_reverse($ds);
        $r->setRequestString(substr($url, 1));
        $r->setDomain('/');
        
        // Loop through the directory paths until one matches
        foreach($ds as $try){
            
            $dlen = strlen($try);
            
            if(substr($url, 0, $dlen) == $try){
                
                $r->setRequestString(substr($url, $dlen));
                $r->setDomain($try);
                
                if(substr($r->getRequestString(), 0, $fc_filename_len) == $fc_filename){
        		    $r->setDomain($r->getDomain().$fc_filename);
        		    $r->setRequestString(substr($r->getRequestString(), $fc_filename_len));
        		}
                
                return $r;
            }
        } */
        
        return $r;
	}
	
	public function getNewModulesList(){
	    
	    $all_modules = array();
        
        foreach($this->_module_dirs as $m){
            $dirs = QuinceUtilities::dirContents($this->_home_dir.$m);
            $all_modules = array_merge($all_modules, $dirs);
        }
        
        return $all_modules;
        
	}
	
	protected function scanModules(){
	    
	    // first, find modules.
        $this->_existing_modules = is_array($this->_existing_modules) ? $this->_existing_modules : $this->getNewModulesList();
        
        // now a tricky bit - the cache
        
        $new_hash = md5(implode(':', $this->_existing_modules));
        $old_hash = QuinceUtilities::cacheGet('all_modules_hash');
        
        if(!$old_hash || $old_hash != $new_hash){
            
            QuinceUtilities::cacheClear('all_modules_config_hash');
            QuinceUtilities::cacheSet('all_modules_hash', $new_hash);
            
        }
        
        // Modules have changed, try a shallow traverse of modules, just checking hashes of config files
        $amch = '';
        
        foreach($this->_existing_modules as $m){
            $cf = $m.$this->_module_conf;
            if(is_file($cf)){
                $amch.=md5_file($cf);
            }
        }
        
        $new_mcoh = md5($amch);
        $old_mcoh = QuinceUtilities::cacheGet('all_modules_config_hash');
        
        if(!$old_mcoh || $old_mcoh != $new_mcoh){
            
            // somewhere the module configs have changed. A deep traversal is in order.
            
            $aliases = array();
            $routes = array();
            $modules = array();
            $module_shortnames = array();
            $module_names = array();
            
            foreach($this->_existing_modules as $m){
                
                $cf = $m.$this->_module_conf;
                if(is_file($cf)){
                    
                    $conf = QuinceUtilities::fetchModuleConfig($cf);
                    $conf['directory'] = $m;
                    $modules[] = $conf;
                    
                    if(!isset($module_names[$conf['shortname']])){
                    
                        // cache whole module conf
                        QuinceUtilities::cacheSet('module_config_'.$conf['shortname'], $conf);
                        
                        // add module shortname to list, so we know it's real
                        $module_shortnames[] = $conf['shortname'];
                    
                        // get aliases
                        if(isset($conf['aliases']) && is_array($conf['aliases'])){
                            foreach($conf['aliases'] as &$a){
                                $a['module'] = $conf['shortname'];
                            }
                            $aliases = array_merge($aliases, $conf['aliases']);
                        }
                        
                        // get routes
                        if(isset($conf['routes']) && is_array($conf['routes'])){
                            
                            $fake_route_aliases = array();
                            
                            foreach($conf['routes'] as $k => &$r){
                                
                                $r['module'] = $conf['shortname'];
                                $r['name'] = $k;
                                $routes[$conf['shortname'].':'.$k] = $r;
                                
                                // add routes that specify a url to aliases
                                if(isset($r['url'])){
                                    $fra = array();
                                    $fra['action'] = $r['action'];
                                    $fra['module'] = $conf['shortname'];
                                    $fra['url'] = $r['url'];
                                    $fra['params'] = $r['params'];
                                    $fake_route_aliases[] = $fra;
                                }
                            }
                            
                            $aliases = array_merge($aliases, $fake_route_aliases);
                            
                        }
                        
                        
                        
                    }
                }
            }
            
            // write one long list of the aliases to cache
            QuinceUtilities::cacheSet('all_aliases', $aliases);
            self::$raw_aliases = $aliases;
            
            // write one long list of the aliases to cache
            QuinceUtilities::cacheSet('module_shortnames', $module_shortnames);
            $this->module_shortnames = $module_shortnames;
            
            // as configs have changed, alias shortcuts need to be refreshed
            QuinceUtilities::cacheClear('alias_url_shortcuts');
            self::$alias_shortcuts = array();
            
            // write one long list of the routes to cache
            QuinceUtilities::cacheSet('routes', $routes);
            self::$routes = $routes;
            
            // save list of all modules to cache
            QuinceUtilities::cacheSet('all_modules', $modules);
            self::$modules = $modules;
            
            // as configs have changed, route shortcuts need to be refreshed
            // QuinceUtilities::cacheClear('route_shortcuts');
            // self::$route_shortcuts = array();
            
            // write the new hash of the module configs to cache
            QuinceUtilities::cacheSet('all_modules_config_hash', $new_mcoh);
            
        }else{
            
            self::$modules = QuinceUtilities::cacheGet('all_modules');
            self::$raw_aliases = QuinceUtilities::cacheGet('all_aliases');
            self::$alias_shortcuts = QuinceUtilities::cacheGet('alias_url_shortcuts');
            self::$routes = QuinceUtilities::cacheGet('routes');
            
            $this->module_shortnames = QuinceUtilities::cacheGet('module_shortnames');
        }
	    
	    if(!in_array($this->_default_module_name, $this->module_shortnames)){
            $this->handleException(new QuinceException("The specified default module, '{$this->_default_module_name}', does not exist."));
        }
	    
	}
	
	public function doAction(){
	    
	    try{
	        $result = $this->_current_action->execute();
	        return $result;
	    // Catch forwards
	    }catch(QuinceForwardException $e){
	        if($this->_num_forwards < 10){
                $this->_current_request->setModule($e->getModule());
                $this->_current_request->setAction($e->getAction());
                ++$this->_num_forwards;
                
                // if the next action is also a forward, nothing will be returned, and so on.
                // Something is returned only once an action is called that doesn't forward, but the whole cycle still triggered from here so we still have to use 'return'
                $a = new QuinceAction($this->_use_checking);
        	    $a->assignRequest($this->_current_request);
        	    $a->prepareActionObject();

        	    $this->_current_action = $a;
        	    return $this->doAction();
                
            }else{
                $this->handleException(new QuinceException("Quince has detected too many forwards. The maximum number is 10."));
            }
        
        }catch(QuinceRedirectException $e){
            if($this->_num_redirects < 10){
                ++$this->_num_redirects;
                $e->redirect();
            }else{
                $this->handleException(new QuinceException("Quince has detected too many redirects. The maximum number is 10."));
            }
        // Catch other "real" exceptions
        }catch(Exception $e){
            $this->handleException($e);
        }
	    
	}
	
	public function prepare($url='___QUINCE_CURRENT_URL'){
	    
	    if($url == "___QUINCE_CURRENT_URL"){
	        $url = $_SERVER['REQUEST_URI'];
	        $url_is_current_request = true;
	    }else{
	        $url_is_current_request = false;
	    }
	    
	    $url = $this->removeQueryString($url);
	    
	    try{
	        $this->_current_request = $this->processRequest($url);
        }catch(QuinceException $e){
            $this->handleException($e);
        }
	    
	    // Scan the modules
	    try{
	        $this->scanModules();
        }catch(QuinceException $e){
            $this->handleException($e);
        }
	    
	    $rs = $this->_current_request->getRequestString();
	    
	    // Next, is the request an alias?
	    // if so, give the request object its associated module/action
	    // First, check the alias shortcuts cache
	    if(isset(self::$alias_shortcuts[$rs])){
	        
	        $this->_current_request->setModule(self::$alias_shortcuts[$rs]['module']);
	        $this->_current_request->setAction(self::$alias_shortcuts[$rs]['action']);
	        $this->_current_request->setIsAlias(true);
	        
	        // if(is_array(self::$alias_shortcuts[$rs]['params'])){
	        //    foreach(self::$alias_shortcuts[$rs]['params'] as $n => $v){
    	    //        $this->_current_request->setRequestParameter($n, $v);
    	    //    }
	        // }
	        
	        if(is_array(self::$alias_shortcuts[$rs]['url_vars'])){
	            foreach(self::$alias_shortcuts[$rs]['url_vars'] as $n => $v){
	                $this->_current_request->setRequestParameter($n, $v);
	            }
            }
	        
	    }
	    
	    // the request might still be an alias - aliases might just not be cached
	    foreach(self::$raw_aliases as $alias){
	        
	        // Aliases that do not use url vars
	        if($alias['url'] == '/'.$rs){
	            
	            $this->_current_request->setModule($alias['module']);
	            $this->_current_request->setAction($alias['action']);
	            $this->_current_request->setIsAlias(true);
	            
	            self::$alias_shortcuts[$rs] = array();
	            self::$alias_shortcuts[$rs]['module'] = $alias['module'];
	            self::$alias_shortcuts[$rs]['action'] = $alias['action'];
	            self::$alias_shortcuts[$rs]['url_vars'] = array();
	            
	            if(isset($alias['params'])){
	                foreach($alias['params'] as $n => $v){
	                    $this->_current_request->setRequestParameter($n, $v);
	                }
	                self::$alias_shortcuts[$rs]['params'] = $alias['params'];
	            }
	            
	            break;
	        }
	        
	        // Aliases that do:
	        $regex = '/^'.preg_replace('/\/(:|\$)([\w_]+)/i', "/([^\/]+)", QuinceUtilities::excapeRegexCharacters($alias['url'])).'\/?$/';
	        
	        if(preg_match($regex, '/'.$rs, $matches)){
	            
	            preg_match_all('/\/(:|\$)([\w_]+)/i', $alias['url'], $arg_matches);
    	        $argnames = $arg_matches[2];
	            
	            array_shift($matches);
	            $argvalues = ($matches);
	            
	            $this->alias_shortcuts[$rs] = array();
	            $this->alias_shortcuts[$rs]['module'] = $alias['module'];
	            $this->alias_shortcuts[$rs]['action'] = $alias['action'];
	            $this->alias_shortcuts[$rs]['url_vars'] = array();
	            
	            $this->_current_request->setModule($alias['module']);
	            $this->_current_request->setAction($alias['action']);
	            $this->_current_request->setIsAlias(true);
	            
	            if(isset($alias['params'])){
                    foreach($alias['params'] as $n => $v){
                        $this->_current_request->setRequestParameter($n, $v);
                    }
                    self::$alias_shortcuts[$rs]['params'] = $alias['params'];
                }
	            
	            foreach($argnames as $i => $n){
    	            $this->_current_request->setRequestParameter($n, $argvalues[$i]);
    	            $this->alias_shortcuts[$rs]['url_vars'][$n] = $argvalues[$i];
    	        }
    	        
    	        break;
    	        
	        }
	    }
	    
	    // if not, then see if the url maps directly onto a module/action
	    if(!$this->_current_request->isReady()){
	        $u = explode('/', $rs);
	        if(count($u) < 3){
	            $module = $u[0];
	            if(in_array($module, $this->module_shortnames)){
	                $this->_current_request->setModule($module);
	                if(isset($u[1])){
	                    $this->_current_request->setAction($u[1]);
                    }
                }else{
                    // default module, default action
                    $this->_current_request->setModule($this->_default_module_name);
                }
            }else{
                // default module, default action
                $this->_current_request->setModule($this->_default_module_name);
            }
	    }
	    
	    // Next quickly add any GET and POST variables to the request object
	    if(count($_GET)){
	        foreach($_GET as $n => $v){
	            $this->_current_request->setRequestParameter($n, $v);
	        }
	    }
	    
	    if(count($_POST)){
	        foreach($_POST as $n => $v){
	            $this->_current_request->setRequestParameter($n, $v);
	        }
	    } 
	    
	    $a = new QuinceAction($this->_use_checking);
	    $a->assignRequest($this->_current_request);
	    $a->prepareActionObject();
	    
	    $this->_current_action = $a;
	    
	}
	
	public function getCurrentRequest(){
	    return $this->_current_request;
	}
	
	public function dispatch($url='___QUINCE_CURRENT_URL', $prepare=true){
        
        if($prepare || !$this->_current_action){
            $this->prepare($url);
        }
	    
	    try{
            $result = $this->doAction();
            $this->getCurrentRequest()->setResult($result);
        }catch(QuinceException $e){
            $this->handleException($e);
        }
        
        QuinceUtilities::cacheSet('alias_url_shortcuts', $this->alias_shortcuts);
        
        return $this->_current_request;
	    
	}
	
	public function handleException(Exception $e){
	    
	    switch($this->_exception_handling){
	        case 'die':
	        die($e->getMessage());
	        case 'return':
	        return $e;
	        case 'ignore':
	        return;
	        case 'throw':
	        default:
	        throw $e;
	    }
	    
	}
	
	public function cleanUp(){
	    
	    unset($this->module_shortnames);
	    unset(self::$alias_shortcuts);
	    unset(self::$raw_aliases);
	    unset($this->_default_module_name);
	    
	}
	
}

class QuinceLegacy extends Quince{
    
    protected $_request;
    protected $_content;
    protected $_module;
    
    public function __construct($ignore, $dispatch_now=true){
        // $ignore, as its name would suggest, is ignored
        parent::__construct(Quince::CURRENT_DIR, 'quince.yml');
        if($dispatch_now){
            $this->dispatch();
        }
    }
    
    public function dispatch(){
        $this->_request = parent::dispatch(Quince::CURRENT_URL);
        $this->_module = QuinceUtilities::cacheGet('module_config_'.$this->_request());
    }
    
    public function performAction(){
        $this->_content = $this->doAction($this->_request);
    }
    
    public function getContent(){
        return $this->_content;
    }
    
    public function getMethodName(){
        return $this->_request->getAction();
    }
    
    public function getSectionName(){
        return $this->_request->getAction();
    }
    
    public function getNameSpace(){
        return $this->_request->getNamespace();
    }
    
    public function getClassName(){
        return $this->_module['class'];
    }
    
    public function getIsAlias(){
        return $this->_request->getIsAlias();
    }
    
}