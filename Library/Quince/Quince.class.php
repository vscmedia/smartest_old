<?php

/**
  * Quince (formerly "PHP Controller")
  *
  * This library is free software; you can redistribute it and/or
  * modify it under the terms of the GNU Lesser General Public
  * License as published by the Free Software Foundation; either
  * version 2.1 of the License, or (at your option) any later version.
  * 
  * This library is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  * Lesser General Public License for more details.
  * 
  * You should have received a copy of the GNU Lesser General Public
  * License along with this library; if not, write to the Free Software
  * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  *
  * Quince(TM) PHP Controller
  * Based on the original PHP-Controller API, but rewritten from the ground up, and about 10ms faster!
  *
  * Typical runtime about 12-13ms on a good server
  * We are always looking to make this faster
  *
  * @category   Controller
  * @package    Quince
  * @license    Lesser GNU Public License
  * @author     Marcus Gilroy-Ware <marcus@visudo.com>
  * @author     Eddie Tejeda <eddie@visudo.com>
  * @copyright  Marcus Gilroy-Ware & Eddie Tejeda 2006
  * @version    0.8
  */
  
  // Pear, Xml_serializer, and XML_Unserializer must all be included

class Quince{
	// settings
	
	// prefix methods with a string, for instance "execute"
	var $methodPrefix = null;
	
	// the suffix used on class files. The default is the standard .class.php
	var $suffix = ".class.php";
	
	// should the controller check class files to make sure the method is implemented? default is true
	var $checkClassForMethod = true;
	
	// use dot syntax? modulename.methodname?
	var $dotSyntaxEnabled = false;
	
	// the contents of the mmain xml file as an array
	var $xmlDataArray;
	
	var $domain;
	var $domainPath;
	
	var $request;
	var $templateName;
	
	var $getVariables = null;
	var $readyToPerformAction = false;
	var $methodName;
	var $actionName;
	
	var $className;
	var $moduleNamesMap = array();
	var $moduleIndicesMap = array();
	
	var $user_object;
	var $object_stack = array();
	
	var $modules;
	var $moduleName;
	var $moduleIndex;
	var $moduleDirectories;
	var $defaultModule;
	var $defaultModuleIndex;
	var $ajaxModules = array();
	var $ajaxModulesIndex = 0;
	
	var $aliases;
	var $aliasMatch;
	var $aliasDestination;
	var $formForwards;
	var $options = array();
	
	var $requestIsUsingAlias = false;
	var $contentRetrieved = false;
	var $activeRequestArray;
	var $activeRequestArrayName;
	
	var $startTime;
	var $prepareTime;
	var $postActionTime;
	var $prepareTimeTaken;
	var $postActionTimeTaken;
	
	var $namespace = "";
	var $namespaceArray = array();
	var $initialNamespace = "";
	var $namespaces;
	var $defaultNamespace;
	
	var $log;
	var $errors;
	var $halt = false;
	var $result = null;
	var $cache_dir = './';
	
	var $num_forwards = 0;
	
	const FAIL = '__QUINCE_FAILED_ACTION';
	const NODISPLAY = '__QUINCE_NO_UI_FEEDBACK';
	
	const E_ALL = 100;
	const E_NOTICE = 101;
	const E_ERROR = 102;
	
	const NS_NONE     = 0;
	const NS_DOMAIN   = 1;
	const NS_METHOD   = 2;
	const NS_CLASS    = 4;
	const NS_TEMPLATE = 8;
	const NS_ALL      = 15;
	
	const CLASS_CHECK_ALWAYS = 2;
	const CLASS_CHECK_NON_ALIAS = 1;
	const CLASS_CHECK_NEVER = false;
	
	/* A transitional feature - allows users to replace <module>, <modules> and <domain-path>
	with the older <page>, <pages>, and <application-path> respectively */
	var $useOldTerms = false;
	
	// Deprecated:
	var $sectionName;
	var $domainName;

	function Quince($filename = "controller.xml", $automatic=true, $use_cache=true){
		
		$this->_log('Instantiating the controller...');
		$this->_log('User has IP '.$_SERVER['REMOTE_ADDR'].'; Time is '.date("l jS F Y h:i:s a"));
		
		if(is_array($filename)){
			// If the first argument is an array, it is an array containing all the configuration info
			$this->options = $filename;
			if(isset($this->options['filename'])){$filename = $this->options['filename'];}else{$filename = "controller.xml";}
			if(isset($this->options['auto'])){$automatic = $this->options['auto'];}
		}else{
			$this->options = array();
			$this->options['filename'] = $filename;
			$this->options['auto'] = $automatic;
		}
		
		$this->checkClassForMethod = self::CLASS_CHECK_ALWAYS;
		
		$this->startTime = microtime(true);
		
		// Firstly, check that the data originally from the XML file is present, either in the disk cache or the session:
		
		if($hash = $this->getXmlDataHash() && $xmldata = $this->getXmlDataArray()){ // If the xml controller file is already cached in the session
			
			// var_dump($hash);
			
			// if the file hasn't changed and the hashes are the same
			if($hash == md5_file($filename)){
			
				// LOG: Loaded XML Data From Session
				$this->_log('Quince XML file unchanged. Loaded controller data from cache.');
				$this->xmlDataArray = $xmldata;
				
			}else{ // the file has changed, so the hashes are different
				
				// get the data from the XML
				if($data = $this->loadXmlControllerFile($filename)){
					
					$this->_log('Quince XML file has been modified.');
					$this->_log('Loaded XML data From file: '.$filename);
					
					$this->setXmlDataHash(md5_file($filename));
					$this->setXmlDataArray($data);
					
					// $_SESSION["xmlDataHash"] = md5_file($filename);
					$this->xmlDataArray = $data;
					
				}else{
					// file not loaded
					// ERROR
					
					$this->_error('Quince XML file was not found or could not be loaded: '.$filename, true);
				}
			}
			
		}else{ // try and load the xml controller file, since it wasn't cached at all
		
			// LOG: XML Data Not Cached
			$this->_log('Quince XML data not found in session cache');
			
			if($data = $this->loadXmlControllerFile($filename)){
				$this->_log('Loaded XML data From file: '.$filename);
				
				// file successfully loaded
				$hash = md5_file($filename);
				
				$this->setXmlDataHash($hash);
				$this->setXmlDataArray($data);
				
				$this->xmlDataArray = $data;
				
			}else{
				// file not loaded
				// ERROR
				$this->_error('Controller data needed, but Quince XML file could not be loaded', true);
			}
		}
		
		// echo $_SESSION["xmlDataHash"];
		
		$this->activeRequestArray = count($_POST) ? $_POST : $_GET;
		$this->activeRequestArrayName = count($_POST) ? "POST" : "GET";
		
		if($use_cache == false){
			$this->_purge();
		}
		
		if($automatic == true){
			$this->_log("Dispatch is going to begin automatically.");
			$this->dispatch();
		}
		
	}
	
	function getXmlDataHash(){
		// looks in the cache file on disk. if it's not there, it looks in the session. if not there either it returns false.
		if($data = $this->loadFromDiskCache('quincexmlhash')){
			return $data;
		}else if(array_key_exists('xmlDataHash', $_SESSION) && is_array($_SESSION["xmlDataHash"])){
			return $_SESSION["xmlDataHash"];
		}else{
			return false;
		}
	}
	
	function setXmlDataHash($data){
		// writes to a cache file, or if that isn't available, to the session
		if($this->saveToDiskCache('quincexmlhash', $data)){
			return true;
		}else{
			$_SESSION["xmlDataHash"] = $data;
			return true;
		}
	}
	
	function getXmlDataArray(){
		// looks in the cache file on disk. if it's not there, it looks in the session. if not there either it returns false.
		if($data = $this->loadFromDiskCache('quincexmldata')){
			return $data;
		}else if(array_key_exists('xmlDataArray', $_SESSION) && is_array($_SESSION["xmlDataArray"])){
			return $_SESSION["xmlDataArray"];
		}else{
			return false;
		}
	}
	
	function setXmlDataArray($data){
		// writes to a cache file, or if that isn't available, to the session
		if($this->saveToDiskCache('quincexmldata', $data)){
			return true;
		}else{
			$_SESSION["xmlDataArray"] = $data;
			return true;
		}
	}
	
	function dispatch(){
		// The order these functions are called in is very important
		$this->_log("Beginning dispatch...");
		$this->request = $this->getControllerUrlRequest();
		$this->namespaces = $this->setNamespaces();
		$this->setDomain();
		$this->moduleDirectories = $this->getModuleDirectories();
		$this->modules = $this->setModules();
		$this->aliases = $this->setAliases();
		$this->formForwards = $this->getformForwards();
		$this->setModule();
		$this->setTemplate();
		$this->setClass();
		$this->setMethod();
		$this->readyToPerformAction = true;
	}
	
	function loadXmlControllerFile($filename){
		
		// load xml controller
		// LOG: Loading Controller XML file
		$this->_log("Attempting to load principal Quince XML file: $filename");
		
		if(class_exists("XML_Unserializer")){
    		
    		$option = array('complexType' => 'array', 'parseAttributes' => TRUE);
    		$unserialized = new XML_Unserializer($option);
    		$result = $unserialized->unserialize($filename, true);
    
    		if (PEAR::isError($result)) {
				// ERROR: XML file could not be parsed: PEAR said "$result->getMessage()"
				$this->_error("XML file could not be parsed: PEAR XML_Unserializer said '{$result->getMessage()}'", true);
				return false;
    		}else{
    			// load contents from xml file
    			$data = $unserialized->getUnserializedData();
    			return $data;
    		}
    	}else{
    		$this->_error("XML file could not be parsed because the PEAR XML_Unserializer library could not be found.", true);
    	}
	}
	
	function getControllerUrlRequest(){
	
		// determine the url:
		
		$url = reset(explode("?", $_SERVER["REQUEST_URI"]));
		
		if($url{0} == "/"){
			$url = substr($url, 1);
		}
		
		if($this->useOldTerms == true){
			$path_key = "application-path";
		}else{
			$path_key = "domain-path";
		}
		
		if(@strlen($this->xmlDataArray[$path_key]) > 1){
			
			$domain_path = $this->xmlDataArray[$path_key];
			$last_char = strlen($domain_path) - 1;
			
			if($domain_path{$last_char} != "/"){
				$domain_path .= "/";
				$this->domainPath = $domain_path;
			}else{
				$this->domainPath = $domain_path;
			}
			
			if(strpos($url, $domain_path) === 0){
				// $domain path
				$request = str_replace($domain_path, '', $url);
			}else{
				$request = $url;
			}
		}else{
			
			$request = $url;
			
		}
		
		// check for namespace
		if(preg_match('/^(([^:]+):)[^:]+$/', reset(explode("/", $request)), $matches)){
			$this->initialNamespace = $matches[2];
			$this->_log("The namespace \"{$this->initialNamespace}\" was detected in the URL request, and will be checked.");
			$request = substr($request, strlen($matches[1]));
		}
		
		if($request){
			$this->_log("The URL request was detected as \"$request\".");
		}else{
			$this->_log("The URL request is empty. This is the top level of the site or application.");
		}
		
		return $request;
		
	}
	
	function setDomain(){
		$protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";
		$this->domain = $protocol.$_SERVER["HTTP_HOST"]."/".$this->domainPath;
	}
	
	function getModuleDirectories(){
	
		if($this->useOldTerms == true){
			$modules_key = "pages";
		}else{
			$modules_key = "modules";
		}
	
		if($this->xmlDataArray[$modules_key]){
			$directories = explode(":", $this->xmlDataArray[$modules_key]);
			
			foreach($directories as $key=>$directory){
				
				$last_char = strlen($directory) - 1;
				
				if(strlen($directory) > 1){
					if($directory{$last_char} != "/"){
						$directories[$key] .= "/";
					}
				}
				
				if(!is_dir($directories[$key])){
					unset($directories[$key]);
				}
				
			}
			
			return array_values($directories);
			
		}else{
			// ERROR: No modules directories defined
			$this->_error("No modules directories defined");
			return array();
		}
	}
	
	function setNamespaces(){
		$this->_log("Checking namespace options...");
		
		// $this->initialNamespace;
		
		if(isset($this->xmlDataArray['namespace']) && is_array($this->xmlDataArray['namespace'])){
			
			if(isset($this->xmlDataArray['namespace']['name'])){
				$namespaces = array();
				$namespaces[0] = $this->xmlDataArray['namespace'];
			}else{
				$namespaces = $this->xmlDataArray['namespace'];
			}
		
			foreach($namespaces as $key=>$namespace){
			
				if(!isset($namespace['name'])){
					$this->_log("A &lt;namespace&gt; tag was found without the required &lt;name&gt; tag.");
					continue;
				}
			
				if(isset($namespace['default-namespace']) && ($namespace['default-namespace'] != false && strtolower($namespace['default-namespace']) != "false")){
					$this->defaultNamespace = $namespace;
				}
				
				if($this->initialNamespace && isset($namespace['name']) && $namespace['name'] == $this->initialNamespace){
					$this->namespace = $namespace['name'];
					$this->namespaceArray = $namespace;
					$this->_log("The namespace specified in the URL was matched to one in the XML file. The namespace was set as '{$this->namespace}'.");
				}
				
			}
			
			if(!$this->namespace){
				if($this->defaultNamespace){
					$this->namespace = $this->defaultNamespace['name'];
					$this->namespaceArray = $this->defaultNamespace;
					
					if($this->initialNamespace){
						$this->_log("The namespace specified in the URL '{$this->initialNamespace}' didn't match any of those in the XML file, so the default namespace, '{$this->namespace}' was assumed.");
					}else{
						$this->_log("No namespace was specified in the URL - not a problem - so the default namespace, '{$this->namespace}' was assumed.");
					}
					
				}else{
					$this->_log("No namespace was specified in the URL and no default was specified in the URL file, so namespaces are disabled for this request.");
				}
			}
			
		}else{
			if($this->initialNamespace){
				$this->_log("The namespace {$this->initialNamespace} was requested, but no namespaces were set in the Quince XML file...");
				$this->namespace = "";
			}else{
				$this->namespace = "";
			}
		}
		
		// print_r($this->namespaceArray);
		
		if(isset($this->namespaceArray['mode'])){
						
			$this->namespaceArray['_affect_domain'] = false;
			$this->namespaceArray['_affect_method'] = false;
			$this->namespaceArray['_affect_class'] = false;
			$this->namespaceArray['_affect_templates'] = false;
			
			// echo $namespace['mode']."<br />";
			
			if(preg_match('/^(NS_(NONE|DOMAIN|METHOD|CLASS|TEMPLATE|ALL))\s?\^\s?(NS_(NONE|DOMAIN|METHOD|CLASS|TEMPLATE|ALL))$/', $this->namespaceArray['mode'], $modes)){
				
				// echo "subtract<br />";
				$level = constant("self::".$modes['1']) - constant("self::".$modes['3']);
				// print_r($modes);
				// echo $level;
				
			}else if(preg_match('/^(NS_(NONE|DOMAIN|METHOD|CLASS|TEMPLATE|ALL)\|?)+$/', $this->namespaceArray['mode'], $modes)){
				
				$level = 0;
				
				// echo "add<br />";
				foreach(explode("|", $this->namespaceArray['mode']) as $constant_name){
					$level += constant("self::".$constant_name);
					// echo "adding ".constant("self::".$constant_name)."<br />";
				}
				
				// echo $level;
			}else{
				$level = 0;
			}
			
			// echo $level;
			
			switch($level){
				case 1:
				case 9:
					$this->namespaceArray['_affect_domain'] = true;
					break;
				case 2:
				case 10:
					$this->namespaceArray['_affect_method'] = true;
					break;
				case 3:
				case 11:
					$this->namespaceArray['_affect_domain'] = true;
					$this->namespaceArray['_affect_method'] = true;
					break;
				case 4:
				case 14:
					$this->namespaceArray['_affect_class'] = true;
					break;
				case 5:
				case 13:
					$this->namespaceArray['_affect_domain'] = true;
					$this->namespaceArray['_affect_class'] = true;
					break;
				case 6:
				case 14:
					$this->namespaceArray['_affect_method'] = true;
					$this->namespaceArray['_affect_class'] = true;
					break;
				case 7:
				case 15:
					$this->namespaceArray['_affect_domain'] = true;
					$this->namespaceArray['_affect_method'] = true;
					$this->namespaceArray['_affect_class'] = true;
					break;
			}
			
			if($level > 7){
				$this->namespaceArray['_affect_templates'] = true;
			}else{
				$this->namespaceArray['_affect_templates'] = false;
			}
			
			// print_r($this->namespaceArray);
			
		}else{
			$this->_log("The current namespace doesn't have the required &lt;mode&gt; tag, and so won't have any effect.");
		}
		
	}
	
	function setModules(){
		
		$this->_log("Retrieving modules...");
		
		if($this->useOldTerms == true){
			$module_key = "page";
		}else{
			$module_key = "module";
		}
		
		if(is_array($this->xmlDataArray[$module_key])){
			
			if(isset($this->xmlDataArray[$module_key]['name'])){
				$modules = array();
				$modules[0] = $this->xmlDataArray[$module_key];
			}else{
				$modules = $this->xmlDataArray[$module_key];
			}
			
			foreach($modules as $key=>&$module){
			
				$modules[$key]['class_file'] = "";
				$modules[$key]['class_file_found'] = false;
				
				if($this->namespaceArray['_affect_class'] && $this->namespaceArray['prefix']){
					$modules[$key]['class'] = $this->namespaceArray['prefix'].$module['class'];
				}
				
				$this->moduleNamesMap[$module['name']] = $key;
				$this->moduleIndicesMap[$key] = $module['name'];
				
				foreach($this->moduleDirectories as $directory){
					// echo $directory;
					if($modules[$key]['class_file_found'] != true){ // if the file hasn't already been found...
						
						// echo $modules[$key]['class']."<br />";
						
						// try one of the other module directories
						if(is_file($directory.$module['class'].$this->suffix)){
							
							$modules[$key]['class_file'] = $directory.$modules[$key]['class'].$this->suffix;
							$modules[$key]['class_file_found'] = true;
							$modules[$key]['has_own_directory'] = false;
							$modules[$key]['directory'] = $directory;
							
							if(isset($module['label'])){
							    $modules[$key]['label'] = $module['label'];
							}
							
							$this->_log("Found module \"{$module['name']}\".");
						
						}else if(is_file($directory.$module['class']."/".$modules[$key]['class'].$this->suffix)){
							
							$modules[$key]['class_file'] = $directory.$module['class']."/".$module['class'].$this->suffix;
							$modules[$key]['class_file_found'] = true;
							$modules[$key]['has_own_directory'] = true;
							$modules[$key]['directory'] = $directory.$module['class']."/";
							
							if(isset($module['label'])){
							    $modules[$key]['label'] = $module['label'];
							}
							
							if(is_file($modules[$key]['directory']."module.xml")){
								$modules[$key]['module_xml_file'] = $modules[$key]['directory']."module.xml";
							}
							
							$this->_log("Found module \"{$module['name']}\".");
							
						}else if(is_file($directory.$module['name']."/".$modules[$key]['class'].$this->suffix)){
							
							$modules[$key]['class_file'] = $directory.$module['name']."/".$modules[$key]['class'].$this->suffix;
							$modules[$key]['class_file_found'] = true;
							$modules[$key]['has_own_directory'] = true;
							$modules[$key]['directory'] = $directory.$module['name']."/";
							
							if(isset($module['label'])){
							    $modules[$key]['label'] = $module['label'];
							}
							
							if(is_file($modules[$key]['directory']."module.xml")){
								$modules[$key]['module_xml_file'] = $modules[$key]['directory']."module.xml";
							}
							
							$this->_log("Found module \"{$module['name']}\".");
							
						}else{
							$modules[$key]['has_own_directory'] = false;
						}
					}
				}
				
				if((isset($modules[$key]['default-module']) || isset($modules[$key]['default-page'])) && !isset($this->defaultModule)){
					$this->_log("The default module has been set as \"{$modules[$key]['name']}\".");
					$this->defaultModule = $modules[$key]['name'];
					$this->defaultModuleIndex = $key;
				}
				
				if(isset($modules[$key]['module_xml_file'])){
					if(md5_file($modules[$key]['module_xml_file']) != $_SESSION['moduleXmlHash'][$modules[$key]['name']]){
						// If the module xml file has changed
						$this->_log("Attempting to load Quince module XML file: ".$modules[$key]['module_xml_file']);
    					$option = array('complexType' => 'array', 'parseAttributes' => TRUE);
    					$unserialized = new XML_Unserializer($option);
    					$result = $unserialized->unserialize($modules[$key]['module_xml_file'], true);
    					$_SESSION['moduleXmlData'][$modules[$key]['name']] = $unserialized->getUnserializedData();
    					$_SESSION['moduleXmlHash'][$modules[$key]['name']] = md5_file($modules[$key]['module_xml_file']);
    					$data = $_SESSION['moduleXmlData'][$modules[$key]['name']];
    				}else{
    					// If the module xml file is the same
    					$this->_log($modules[$key]['module_xml_file']." has not changed since last pageload. Loading data from session.");
    					$data = $_SESSION['moduleXmlData'][$modules[$key]['name']];
    				}
    				
    				if(is_array($data['alias'])){
    				
    					if(isset($data['alias']['match'])){
    						$data['alias'][0] = $data['alias'];
    					}
    					
    					if(is_array($modules[$key]['alias'])){
    					
    						if(isset($modules[$key]['alias']['match'])){
    							$modules[$key]['alias'][0] = $modules[$key]['alias'];
    						}
    						
    						$modules[$key]['alias'] = array_merge($modules[$key]['alias'], $data['alias']);
    						
    					}
    				}
    				
    				if(is_array($data['form-forward'])){
    				
    					if(isset($data['form-forward']['method-name'])){
    						$data['form-forward'][0] = $data['form-forward'];
    					}
    					
    					if(is_array($modules[$key]['form-forward'])){
    					
    						if(isset($modules[$key]['form-forward']['method-name'])){
    							$modules[$key]['form-forward'][0] = $modules[$key]['form-forward'];
    						}
    						
    						$modules[$key]['form-forward'] = array_merge($modules[$key]['form-forward'], $data['form-forward']);
    						
    					}
    				}
    				
    				if(isset($data['label']) && strlen($data['label']) > 0){
    					$modules[$key]['label'] = $data['label'];
    				}
    				
    				if(isset($data['description']) && strlen($data['description']) > 0){
    					$modules[$key]['description'] = $data['description'];
    				}
    				
				}
				
				if(isset($modules[$key]['ajax']) && strtolower($modules[$key]['ajax']) == 'true'){
					$this->ajaxModules[$this->ajaxModulesIndex]['name'] = $modules[$key]['name'];
					$this->ajaxModules[$this->ajaxModulesIndex]['class'] = $modules[$key]['class'];
					$this->ajaxModules[$this->ajaxModulesIndex]['directory'] = $modules[$key]['directory'];
					$this->_log("The module \"{$modules[$key]['name']}\" was marked as an AJAX-enabled module.");
					$modules[$key]['ajax'] = true;
					$this->ajaxModulesIndex++;
				}else{
					$modules[$key]['ajax'] = false;
				}
				
			}
			
			// print_r($modules);
			return $modules;
			
		}else{
			// ERROR: No modules defined
			$this->_error("No modules defined", true);
			return array();
		}
	}
	
	function setAliases(){
	
		$this->_log("Retrieving aliases...");
		
		$aliases = array();
		$i = 0;
		
		foreach($this->modules as $moduleKey => $module){
			if(isset($module['alias']) && is_array($module['alias'])){
				if(isset($module['alias']['match'])){
					$aliases[$i] = $module['alias'];
					$aliases[$i]['module'] = $module['name'];
					$aliases[$i]['module_key'] = $moduleKey;
					$aliases[$i]['method'] = reset(explode("?", $module['alias']['_content']));
					$i++;
				}else{
					foreach($module['alias'] as $alias){
						$aliases[$i] = $alias;
						$aliases[$i]['module'] = $module['name'];
						$aliases[$i]['module_key'] = $moduleKey;
						$aliases[$i]['method'] = reset(explode("?", $alias['_content']));
						$i++;
					}
				}
			}
		}
		
		return $aliases;
		
	}
	
	function getFormForwards(){
	
		$this->_log("Retrieving form forwards...");
		
		$formForwards = array();
		$i = 0;
		
		foreach($this->modules as $moduleKey => $module){
			if(isset($module['form-forward']) && is_array($module['form-forward'])){
				if(isset($module['form-forward']['method-name'])){
					$formForwards[$i] = $module['form-forward'];
					$formForwards[$i]['module'] = $module['name'];
					$formForwards[$i]['module_key'] = $moduleKey;
					$i++;
				}else{
					foreach($module['form-forward'] as $formForward){
						$formForwards[$i] = $formForward;
						$formForwards[$i]['module'] = $module['name'];
						$formForwards[$i]['module_key'] = $moduleKey;
						$i++;
					}
				}
			}
		}
		
		return $formForwards;
	}
	
	function setModule(){
		
		/// echo 'hello';
		
		if(strlen($this->request) > 0){
		    
		    // echo 'hello';
		    
			// check aliases first
			foreach($this->aliases as $alias){
			
				if(preg_match($this->getUrlRegExp($alias['match']), $this->request)){
					if($this->modules[$alias['module_key']]['class_file_found'] == true){
						$this->moduleName = $alias['module'];
						$this->sectionName =& $this->moduleName; // deprecated - for backwards compatibility
						$this->moduleIndex = $alias['module_key'];
						$this->aliasMatch = $alias['match'];
						$this->aliasDestination = $alias['_content'];
						$this->requestIsUsingAlias = true;
						// echo "hello";
						// return;
					}else{
						// the actual file containing the class was not found
					    // echo 'hello';
						$this->moduleName = $this->defaultModule;
						$this->sectionName =& $this->moduleName; // deprecated - for backwards compatibility
						$this->moduleIndex = $this->defaultModuleIndex;
						$this->aliasMatch = $alias['match'];
						$this->aliasDestination = $alias['_content'];
						$this->requestIsUsingAlias = true;
						$this->_log("Request matched alias to existing module, but file {$this->modules[$alias['module_key']]['class_file']} was not found. Loading default module.");
						// return;
					}
				}
			}
		
			// an alias is not being used
			
			if($this->dotSyntaxEnabled == true){
				$regexp = '/^([\w_]+)([\/\.]([\w\._]+))?/i';
			}else{
				$regexp = '/^([\w\._]+)([\/]([\w\._]+))?/i';
			}
			
			if($matched = preg_match($regexp, strtolower($this->request), $matches)){

				foreach($this->modules as $key=>$module){
					
					if($matches[1] == strtolower($module['name'])){
						if($this->modules[$key]['class_file_found'] == true){
							$this->moduleName = $module['name'];
							$this->sectionName =& $this->moduleName; // deprecated - for backwards compatibility
							$this->moduleIndex = $key;
							// return;
						}else{
							$this->moduleName = $this->defaultModule;
							$this->sectionName =& $this->moduleName; // deprecated - for backwards compatibility
							$this->moduleIndex = $this->defaultModuleIndex;
							$this->_log("Request matched alias to existing module, but file {$this->modules[$this->moduleIndex]['class_file']} was not found. Loading default module.");
							// return;
						}
					}
				}
				
				if(!$this->moduleName){
					
					$this->_log("The URL request was not recognised. Falling back on to the default module.");
					$this->moduleName = $this->defaultModule;
					$this->sectionName =& $this->moduleName;
					$this->moduleIndex = $this->defaultModuleIndex;
					// return;
				}
				
			}
		
			
		
		}else{
			
			$this->_log("Request not recognised. Falling back on to the default module.");
			$this->moduleName = $this->defaultModule;
			$this->sectionName =& $this->moduleName;
			$this->moduleIndex = $this->defaultModuleIndex;
			// return;
	
		}
		
		$this->_log("The module has been set as \"{$this->moduleName}\".");
		
	}
	
	function setTemplate(){
	
		// echo $this->moduleIndex;
		
		if(isset($this->moduleIndex)){
			$this->templateName = $this->modules[$this->moduleIndex]['template'];
			$this->_log("The template set has been set as \"{$this->templateName}\".");
			
			if($this->namespaceArray['_affect_templates'] && $this->namespaceArray['prefix']){
				$this->templateName = $this->namespaceArray['prefix'].$this->templateName;
				$this->_log("The template set has been modified by the namespace to \"{$this->templateName}\".");
			}
		}
	}
	
	function setClass(){
		if(isset($this->moduleIndex)){
			$this->className = $this->modules[$this->moduleIndex]['class'];
			$this->_log("The class has been set as \"{$this->className}\" ({$this->modules[$this->moduleIndex]['class_file']}).");
			
			if($this->namespaceArray['_affect_class'] && $this->namespaceArray['prefix']){
				// $this->className = $this->namespaceArray['prefix'].$this->className;
			}
			
		}
	}
	
	function setMethod(){
		
		if($this->requestIsUsingAlias == true){
			
			$this->_log("Request has been recognised as an alias.");
			$this->_log("Attempting to determine which method should be called...");
			
			if(isset($this->modules[$this->moduleIndex]['alias']['match'])){
				$aliases[0] = $this->modules[$this->moduleIndex]['alias'];
			}else{
				
				if(isset($this->modules[$this->moduleIndex]['alias'])){
					$aliases = $this->modules[$this->moduleIndex]['alias'];
				}
				
				if(!isset($aliases) || !is_array($aliases)){
					$aliases = array();
				}
			}
			
			foreach($aliases as $alias){
				
				if(preg_match($this->getUrlRegExp($alias['match']), $this->request)){
					$aliasContent = $alias['_content'];
					break;
				}
			
			}
			
			preg_match('/^([\w_]+)/i', $aliasContent, $matches);
			$method = $matches[1];
			
			$this->_log("This URL alias is mapped to a method called $method().");
			
			if($this->namespaceArray['_affect_method'] && isset($this->namespaceArray['prefix']) && strlen($this->namespaceArray['prefix'])){
				$method = $this->namespaceArray['prefix'].$method;
				$this->_log("The method prefix specified in the current namespace has modified the method to be $method().");
			}
			
			$default_method = $this->modules[$this->moduleIndex]['default-method'];
			
		}else{
			
			$this->_log("The URL request does not match any of the aliases for this module.");
			$this->_log("Attempting to determine which method should be called...");
			
			if($this->dotSyntaxEnabled == true){
				$regexp = '/^([\w_]{2,})[\/\.]([\w_]+)/i';
			}else{
				$regexp = '/^([\w_]{2,})\/([\w_]+)/i';
			}
			
			preg_match($regexp, $this->request, $matches);
			
			$default_method = $this->modules[$this->moduleIndex]['default-method'];
			
			if(isset($matches[2])){
				$method = $matches[2];
				if($this->checkClassForMethod){
					$this->_log("The method has been recognised as ".$matches[2]."(), but may not be implemented.");
				}
			}else{
				$this->_log("The method has not been specified. The default method for this module is $default_method().");
			}
			
			if($this->namespaceArray['_affect_method'] && isset($this->namespaceArray['prefix']) && strlen($this->namespaceArray['prefix'])){
				$method = $this->namespaceArray['prefix'].$method;
				$this->_log("The method prefix specified in the current namespace has modified the method to be $method().");
			}
			
		}
		
		// If the checking of classes is switched on... (it is by default)
		if($this->checkClassForMethod){
		
			// make sure the file exists
			if(is_file($this->modules[$this->moduleIndex]['class_file'])){
			
				$this->_log("Checking file \"".$this->modules[$this->moduleIndex]['class_file']."\"");
				
				if(@include_once $this->modules[$this->moduleIndex]['class_file']){
					if(class_exists($this->modules[$this->moduleIndex]['class'])){
				
						$this->_log("The class \"".$this->modules[$this->moduleIndex]['class']."\" was found.");
					
						$available_methods = get_class_methods($this->className);
					
						if(in_array(@$method, $available_methods)){
							$this->_log("The method has been set as $method().");
							$this->methodName = $method;
						}else{
							if(in_array($default_method, $available_methods)){

								if(isset($method)){
									$this->_log("The method $method() is not implemented, but the default method, $default_method(), is.");
								}else{
									$this->_log("The default method, $default_method(), is implemented in this class.");
								}

								$this->methodName = $default_method;
								$this->_log("The method has been set as ".$this->methodName."().");
							}else{
								// ERROR: requested method not found. default method not implemented.
								$this->_error("The requested method was not found. Tried to load the default (".$default_method.") method but it wasn't implemented either.", true);
							}
						}
					}else{
						$this->_error("The class does not exist.", true);
					}
				}else{
					// ERROR: could not load class file: $this->modules[$this->moduleIndex]['class_file']
					$this->_error("Could not load class file: {$this->modules[$this->moduleIndex]['class_file']}", true);
				}
			}
		
		}else{
			$this->methodName = $method;
			$this->_log("Not bothering to check class file \"{$this->modules[$this->moduleIndex]['class_file']}\" for method.");
			$this->_log("The method has been set as ".$this->methodName."().");
		}
	}
	
	function addProperty($name, $object){
		if(is_object($this->user_object)){
			$this->user_object->$name = $object;
		}else{
			$this->object_stack[$name] = $object;
		}
	}
	
	function performAction(){
		
		if($this->readyToPerformAction){
		
			if(@include_once $this->modules[$this->moduleIndex]['class_file']){
				
				$this->getVariables = $this->getGetVariables();
				
				//if($destination = $this->requestIsForward()){
					// do some forwarding
					
				//}else{
					
				$args = array("get" => $this->getVariables, "post"=> $_POST, "cookie" => $_COOKIE, "namespace"=> $this->namespace );
					
				$this->prepareTime = microtime(true);
				$this->prepareTimeTaken = number_format(($this->prepareTime - $this->startTime)*1000, 2, ".", ",");
				
				if(!is_object($this->user_object)){
				    $this->user_object = new $this->className;
			    }
			    
				// echo get_class($this->user_object);
			    $this->user_object->controller =& $this;
			        
			    if(count($this->object_stack)){
			    	foreach($this->object_stack as $name => $value){
			    		$this->user_object->$name = $value;
						// echo '$this->'.$name.' = '.print_r($value, true).'<br />';
			    	}
			    }
			        
			    // echo $this->methodName;
					
				// LOG: Calling $this->className::$this->methodName();
				$this->_log('Calling user action: '.$this->className.'::'.$this->methodName.'()');
				// var_dump($this->methodName);
				$this->result = call_user_func_array(array(&$this->user_object, $this->methodName), $args);
					
				$this->postActionTime = microtime(true);
				$this->postActionTimeTaken = number_format(($this->postActionTime - $this->startTime)*1000, 2, ".", ",");
					
				if($destination = $this->requestIsForward()){
					// handle form forwards
					if($this->result == Quince::FAIL){
						// action failed
						$destination = $this->getFormForwardDestination($destination);
						header("location:".$this->domain.$destination);
						exit;
					}else{
						// action succeeded
						$destination = $this->getFormForwardDestination($destination);
						header("location:".$this->domain.$destination);
						exit;
					}
				}
				//}
			}else{
				// ERROR: could not load class file: $this->modules[$this->moduleIndex]['class_file'];
				$this->_error("Could not load class file: {$this->modules[$this->moduleIndex]['class_file']}");
			}
		}else{
			if($this->options['auto']){
				$this->_error("Quince wasn't ready to go ahead with the action.", true);
			}else{
				$this->_log("Quince wasn't ready to go ahead with the action. Please call Quince::dispatch() first.");
			}
		}
	}
	
	function requestIsForward(){
		// print_r($this->formForwards);
		
		foreach($this->formForwards as $formForward){
			
			if($this->moduleName == $formForward['module'] && $this->methodName == $formForward['method-name']){
				if($this->activeRequestArrayName == $formForward['method-type']){
					return $formForward['_content'];
				}else{
					// ERROR: form forwarding using wrong request type
					$this->_log("Form forwarding using wrong request type. Request is {$this->activeRequestArrayName} but should be {$formForward['method-type']}.");
					return $formForward['_content'];
				}
			}
		}
		
		// Add some code to check for ordinary forwarding here, once it is implemented.
		
		return false;
	}
	
	function getFormForwardDestination($destination){
		
		// print_r($this->activeRequestArray);
		
		foreach($this->activeRequestArray as $key => $value){
			if(!is_array($value)){
				$destination = str_replace('$'.$key, $value, $destination);
			}
		}
		
		// var_dump($destination);
		// var_dump($value);
		// var_dump($key);
		
		// delete undefined variables
		$destination = preg_replace('/\$[\w_]+/i', '', $destination);
		return $destination;
	}
	
	// deprecated
	function getDomainName(){
		// LOG: "Deprecated Function Used: getDomainName() ";
		$this->_log("Deprecated Function Used: getDomainName(). Use getDomain()");
		return $this->getDomain();
	}
	
	function getDomain(){
		if($this->namespaceArray['_affect_domain']){
			return $this->domain.$this->namespace.":";
		}else{
			return $this->domain;
		}
	}
	
	function getTemplateName(){
		return $this->templateName;
	}
	
	function getGetVariables(){
		
		if(!$this->getVariables){
		
			$variables = array();
		
			if($this->requestIsUsingAlias == true){
			
				preg_match_all('/[:|\$]([\w_]+)/i', $this->aliasMatch, $vars, PREG_SET_ORDER);
				preg_match_all('/([\w_]+)=\$[\w_]+/i', $this->aliasDestination, $varnames, PREG_SET_ORDER);
			
				$alias_regexp = $this->getUrlRegExp($this->aliasMatch);
			
				preg_match($alias_regexp, $this->request, $values);
			
				$i = 0;
			
				foreach($vars as $variable){
					$variables[$varnames[$i][1]] = $values[$i+1];
					$this->_log("Added variable to GET array from dynamic alias: \${$varnames[$i][1]} recognised with value of '{$values[$i+1]}'");
					$i++;
				}
			
			}
			
			$getVars = array_merge($_GET, $variables);
			
			return $getVars;
		
		}else{
		
			return $this->getVariables;
		
		}
	}
	
	function getPostVariables(){
		// TODO: Make this filter the post vars
		return $_POST;
	}
	
	function getRequestVariables(){
		return array_merge($this->getGetVariables(), $this->getPostVariables());
	}
	
	function send($name, $value){
		
		if(is_array($this->result) && !isset($this->result[$name])){
			$this->result[$name] = $value;
		}
		
		if($this->contentRetrieved){
			$this->_log("Trying to amend result after it has already been retrieved. This probably won't have any effect.");
		}
	}
	
	function getUrlFor($moduleName, $method='', $args=''){
			
		if(is_array($this->modules[$this->moduleNamesMap[$moduleName]])){
	
			if(!$method){
				$method = $this->modules[$this->moduleNamesMap[$moduleName]]['default-method'];
			}
		
			if(defined('QUINCE_USER_URL_'.strtoupper($moduleName).'_'.strtoupper($method))){
			
				// do some stuff here to get query string
			
				return constant('QUINCE_USER_URL_'.strtoupper($moduleName).'_'.strtoupper($method));
			
			}else{
		
				$separator = $this->dotSyntaxEnabled ? "." : "/";
				$foundAlias = false;
				
				// look to see if an alias exists in the specified module for the specified action
				foreach($this->aliases as $alias){
					if(strtolower($alias['module']) == strtolower($moduleName) && strtolower($alias['method']) == strtolower($method)){
						$path = $this->getDomain().$alias['match'];
						$foundAlias = true;
						break;
					}
				}
			
				if($foundAlias){
					// if not, return the default configuration
				
					$hasUrlVars = preg_match_all('/(:|\$)([\w_]+)/', $path, $vars);
					
					if($hasUrlVars){
						
						foreach($vars[2] as $key=>$varname){
							if(isset($args[$varname])){
								// echo "replace ".$vars[1][$key].$varname." with ".$args[$varname];
								$path = str_replace($vars[1][$key].$varname, $args[$varname], $path);
							}else{
								$this->_error("getUrlFor() - required URL parameter \"$varname\" not supplied.");
								$path = str_replace($vars[1][$key].$varname, '', $path);
							}
						}
					}
					
					$getVars = array();
					
					if(is_array($args)){
						
						if($hasUrlVars){
							foreach($args as $key=>$value){
								if(!in_array($key, $vars[2])){
									$getVars[$key] = $value;
								}
							}
						}
						
						$qs = count($getVars) ? "?".$this->getQueryString($getVars) : '';
						
					}else{
					
						$qs = '';
						
					}
					
					// if the url itself doesn't have any dynamic parts, save it as a constant for quicker retrieval next time
					if(!$hasUrlVars){
						define('QUINCE_USER_URL_'.strtoupper($moduleName).'_'.strtoupper($method), $path);
					}
					
					return $path.$qs;
				
				}else{
				
					if(is_array($args)){
						$qs = "?".$this->getQueryString($args);
					}else{
						$qs = "";
					}
			
					$path = $this->getDomain().$this->getModuleName().$separator.$this->getMethodName();
					define('QUINCE_USER_URL_'.strtoupper($moduleName).'_'.strtoupper($method), $path);
					
					return $path.$qs;
				}
			
			}
		
		}else{
			$this->_log("Quince::getUrlFor() - the specified module \"$moduleName\" doesn't exist.");
		}
	}
	
	function getMethodName(){
		return $this->methodName;
	}
	
	function getClassName(){
		return $this->className;
	}
	
	function getClassFilePath(){
		return $this->modules[$this->moduleIndex]['class_file'];
	}
	
	// deprecated
	function getSectionName(){
		// LOG: "Deprecated Function Used: getSectionName() ";
		$this->_log("Deprecated Function Used: getSectionName(). Use getModuleName()");
		return $this->getModuleName();
	}
	
	function getModuleName(){
		return $this->moduleName;
	}
	
	function getNamespace(){
		return $this->namespace;
	}
	
	function getModuleDirectory(){
		if(isset($this->moduleIndex)){
			return $this->modules[$this->moduleIndex]['directory'];
		}
	}
	
	function isPrivilegedUser(){
		$this->_log("Deprecated Function Used: isPrivilegedUser()");
	}
	
	function getQueryString(array $args){
		
		$string = '';
		$i = 0;
		
		foreach($args as $key=>$value){
			
			if($i>0){
				$string .= '&';
			}
			
			$string .= $key.'='.$value;
		}
		
		return $string;
	}
	
	function getModules(){
		return $this->modules;
	}
	
	function getModuleNames(){
		$names = array();
		foreach($this->modules as $module){
			$names[] = $module['name'];
		}
		return $names;
	}

	function getAjaxModules(){
		return $this->ajaxModules;
	}
	
	function getRequest(){
		return $this->request;
	}
	
	// deprecated
	function getAlias(){
		$this->_log("Deprecated Function Used: getAlias(). Use getRequest()");
		if($this->requestIsUsingAlias == true){
			return $this->request;
		}
	}
	
	function getContent(){
		if($this->result){
			$this->_log("Returning result of {$this->className}::{$this->methodName}()");
			$this->contentRetrieved = true;
			return $this->_stripSlashesFromArray($this->result);
		}else{
			$this->_log("You have to execute the action with Quince::performAction() before you can get any content, silly.");
			return null;
		}
	}
	
	function getDebugContent($type = 100){
	
		$events = array();
		
		switch($type){
			case 100:
			if(is_array($this->errors)){
				$all_events = array_merge($this->errors, $this->log);
			}else{
				$all_events = $this->log;
			}
			break;
			
			case 101:
			$all_events = $this->log;
			break;
			
			case 102:
			if(is_array($this->errors)){
				$all_events = $this->errors;
			}else{
				$all_events = array();
			}
			break;
		}
		
		ksort($all_events);
		$i = 0;
		
		foreach($all_events as $key=>$message){
			
			if($i == 0){
				$start_time = $key;
			}
			
			$time = ($key - $start_time)/100;
			
			$events[$i]['time'] = number_format($time, 2)." ms";
			$events[$i]['message'] = $message;
			
			$i++;
		}
		
		return $events;
	}
	
	function getIsAlias(){
		return $this->requestIsUsingAlias;
	}
	
	function getNavigationState(){
		$this->_log("Deprecated Function Used: getNavigationState()");
		return $this->navigationState;
	}
	
	function getMetaData(){
		$this->_log("Deprecated Function Used: getMetaData()");
		return $this->metaData;
	}
	
	function setDebugLevel($level=0){
		$this->_log("Deprecated Function Used: setDebugLevel()");
	}
	
	function setCache(){
		$this->_log("Deprecated Function Used: setCache()");
	}
	
	function enableXmlEditor(){
		$this->_log("Deprecated Function Used: enableXmlEditor()");
	}
	
	function _purge(){
		$_SESSION["xmlDataArray"] = array();
	}
	
	function _debug($type = 100){
		// print_r($this->getDebugContent());
		foreach($this->getDebugContent($type) as $event){
			echo '<div style="float:left;width:100%;background-color:#fff;color:#111;border-bottom:1px solid #ddd">'.$event['time']." - ".$event['message']."</div>";
		}
	}
	
	function _log($message){
		$time = number_format(microtime(true)*100000, 0, ".", "");
		$this->log[$time] = "Controller: ".$message;
		// echo $message."<br />";
	}
	
	function _error($message, $halt = false){
		$time = number_format(microtime(true)*100000, 0, ".", "");
		$this->errors[$time] = $message;
		// echo $message."<br />";
		if($halt == true){
			$this->_log("Fatal error reported. Unable to continue. Tell my wife I...");
			$this->halt = true;
		}
	}
	
	function getUrlRegExp($url){
		
		// escape special characters (/\|[]{}^.)
		$regexp = str_replace('/', '\/', $url);
		$regexp = str_replace('|', '\|', $regexp);
		$regexp = str_replace('[', '\[', $regexp);
		$regexp = str_replace(']', '\]', $regexp);
		$regexp = str_replace('{', '\{', $regexp);
		$regexp = str_replace('}', '\}', $regexp);
		$regexp = str_replace('.', '\.', $regexp);
		
		// replace every $variable with "([^\/\s]+)"
		$regexp = preg_replace('/\$[\w_-]+/', '([^\/\s\.]+)', $regexp);
		$regexp = preg_replace('/:[\w_-]+/', '(\d+)', $regexp);
		
		return "/^".$regexp."\/?$/i";
	}
	
	function fileGetContents($file_name){
		$fh = fopen($file_name, 'r', true);
		$contents = fread($fh, filesize($file_name));
		fclose($fh);
		return $contents;
	}
	
	function filePutContents($file_name, $data){
		if(!$fh = fopen($file_name, 'w', true)){
			return false;
		}else{
			if(fwrite($fh, $data) === FALSE){
				return false;
			}else{
				fclose($fh);
				return true;
			}
		}
	}
	
	// in PHP5 this would be a private function
	function loadFromDiskCache($data_name){
		if(file_exists($this->cache_dir.$data_name.'.tmp')){
			if($contents = $this->fileGetContents($this->cache_dir.$data_name.'.tmp')){
				$this->_log("Found and loaded data in disk cache: ".$this->cache_dir.$data_name.'.tmp');
				$data = unserialize($contents);
				return $data;
			}else{
				return false;
			}
		}
	}
	
	// so would this
	function saveToDiskCache($data_name, $data){
		if(strlen($data_name)){
			if($this->filePutContents($this->cache_dir.$data_name.'.tmp', serialize($data))){
				$this->_log("Successfully wrote data to disk: ".$this->cache_dir.$data_name.'.tmp');
			}else{
				$this->_log("Writing data to disk failed.");
			}
		}
	}
	
	// this would be public
	function setCacheDirectory($new_directory){
		if(strlen($new_directory)){
			$this->cache_dir = $new_directory;
		}
	}
	
	function _execTime(){
		$microTime = microtime(); 
		$microTime = explode(" ",$microTime); 
		$microTime = $microTime[1] + $microTime[0]; 
		return $microTime;
	}
	
	function _stripSlashesFromArray($value){
		return is_array($value) ? array_map(array('Quince','_stripSlashesFromArray'), $value) : utf8_encode(stripslashes($value));
	}
	
	function _redirect($url){
		header('Location: '.$url);
		exit;
	}
}

?>
