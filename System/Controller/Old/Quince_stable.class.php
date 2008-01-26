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
  * Based on the original PHP-Controller API, but rewritten from the ground up!
  *
  * @category   Controller
  * @package    Quince
  * @license    Lesser GNU Public License
  * @author     Marcus Gilroy-Ware <marcus@visudo.com>
  * @author     Eddie Tejeda <eddie@visudo.com>
  * @copyright  2006 Visudo LLC
  * @version    0.8
  */

include_once 'PEAR.php';
include_once 'XML/Unserializer.php'; 
include_once 'XML/Serializer.php'; 

class Quince{

	var $xmlDataArray;
	var $domain;
	var $domainPath;
	var $request;
	var $templateName;
	var $getVariables = null;
	var $methodName;
	var $className;
	var $modules;
	var $moduleName;
	var $moduleIndex;
	var $moduleDirectories;
	var $aliases;
	var $formForwards;
	var $suffix = ".class.php";
	var $defaultModule;
	var $defaultModuleIndex;
	var $requestIsUsingAlias = false;
	var $activeRequestArray;
	var $activeRequestArrayName;
	var $aliasMatch;
	var $aliasDestination;
	var $startTime;
	var $prepareTime;
	var $postActionTime;
	var $prepareTimeTaken;
	var $postActionTimeTaken;
	var $ajaxModules;
	var $log;
	var $errors;
	
	/* A transitional feature - allows users to replace <module>, <modules> and <domain-path>
	with the older <page>, <pages>, and <application-path> respectively */
	var $useOldTerms = false;
	
	// Deprecated:
	var $sectionName;
	var $domainName;

	function Quince($filename = "controller.xml", $log="", $automatic=true, $use_cache=true){
		
		$this->_log("Instantiating controller...");
		
		$this->startTime = microtime(true);
		
		// Firstly, check that the data from the XML file is present:
		
		if(isset($_SESSION["xmlDataHash"]) && $_SESSION["xmlDataArray"]){ // If the xml controller file is already cached in the session
			
			if($_SESSION["xmlDataHash"] == md5_file($filename)){
				// LOG: Loaded XML Data From Session
				$this->_log("Quince XML file unchanged. Loaded controller data from session.");
				$this->xmlDataArray = $_SESSION["xmlDataArray"];
				
			}else{
				if($_SESSION["xmlDataArray"] = $this->loadXmlControllerFile($filename)){
					$this->_log("Quince XML file has been modified.");
					$this->_log("Loaded XML data From file: $filename");
					$_SESSION["xmlDataHash"] = md5_file($filename);
					$this->xmlDataArray = $_SESSION["xmlDataArray"];
					
				}else{
					// file not loaded
					// ERROR
					$this->_error("Quince XML file was not found or could not be loaded: $filename");
				}
			}
		}else{ // try and load the xml controller file
		
			// LOG: XML Data Not Cached
			$this->_log("Quince XML data not found in session cache");
			if($_SESSION["xmlDataArray"] = $this->loadXmlControllerFile($filename)){
				$this->_log("Loaded XML data From file: $filename");
				// file successfully loaded
				$_SESSION["xmlDataHash"] = md5_file($filename);
				$this->xmlDataArray = $_SESSION["xmlDataArray"];
				
			}else{
				// file not loaded
				// ERROR
			}
		}
		
		// echo $_SESSION["xmlDataHash"];
		
		$this->activeRequestArray = count($_POST) ? $_POST : $_GET;
		$this->activeRequestArrayName = count($_POST) ? "POST" : "GET";
		
		if($use_cache == false){
			$this->_purge();
		}
		
		if($automatic == true){
			$this->dispatch();
		}
		
	}
	
	function dispatch(){
		// The order these functions are called in is very important
		$this->_log("Beginning dispatch...");
		$this->request = $this->getControllerUrlRequest();
		$this->setDomain();
		$this->moduleDirectories = $this->getModuleDirectories();
		$this->modules = $this->getModules();
		$this->aliases = $this->getAliases();
		$this->formForwards = $this->getformForwards();
		$this->setModule();
		$this->setTemplate();
		$this->setClass();
		$this->setMethod();
	}
	
	function loadXmlControllerFile($filename){
		
		// load xml controller
		// LOG: Loading Controller XML file
		$this->_log("Attempting to load principal Quince XML file: $filename");
    	$option = array('complexType' => 'array', 'parseAttributes' => TRUE);
    	$unserialized = new XML_Unserializer($option);
    	$result = $unserialized->unserialize($filename, true);
    
    	if (PEAR::isError($result)) {
			// die($result->getMessage());
			// ERROR: XML file could not be parsed: PEAR said "$result->getMessage()"
			return false;
    	}else{
    		// load contents from xml file
    		$data = $unserialized->getUnserializedData();
    		return $data;
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
		
		return $request;
		
	}
	
	function setDomain(){
		$this->domain = "http://".$_SERVER["HTTP_HOST"]."/".$this->domainPath;
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
			return array();
		}
	}
	
	function getModules(){
		
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
			
			foreach($modules as $key=>$module){
			
				$modules[$key]['class_file'] = "";
				$modules[$key]['class_file_found'] = false;
				
				if(isset($modules[$key]['ajax']) && strtolower($modules[$key]['ajax']) == 'true'){
					$this->ajaxModules[] = $modules[$key]['name'];
					$modules[$key]['ajax'] = true;
				}else{
					$modules[$key]['ajax'] = false;
				}

				if((isset($modules[$key]['default-module']) || isset($modules[$key]['default-page'])) && !isset($this->defaultModule)){
					$this->defaultModule = $modules[$key]['name'];
					$this->defaultModuleIndex = $key;
				}
				
				foreach($this->moduleDirectories as $directory){
					// echo $directory;
					if($modules[$key]['class_file_found'] != true){ // if the file hasn't already been found...
						
						// try one of the other module directories
						if(is_file($directory.$module['class'].$this->suffix)){
							
							$modules[$key]['class_file'] = $directory.$module['class'].$this->suffix;
							$modules[$key]['class_file_found'] = true;
							$modules[$key]['has_own_directory'] = false;
							$modules[$key]['directory'] = $directory;
						
						}else if(is_file($directory.$module['class']."/".$module['class'].$this->suffix)){
							
							$modules[$key]['class_file'] = $directory.$module['class']."/".$module['class'].$this->suffix;
							$modules[$key]['class_file_found'] = true;
							$modules[$key]['has_own_directory'] = true;
							$modules[$key]['directory'] = $directory.$module['class']."/";
							
							if(is_file($modules[$key]['directory']."module.xml")){
								$modules[$key]['module_xml_file'] = $modules[$key]['directory']."module.xml";
							}
							
						}else if(is_file($directory.$module['name']."/".$module['class'].$this->suffix)){
							
							$modules[$key]['class_file'] = $directory.$module['name']."/".$module['class'].$this->suffix;
							$modules[$key]['class_file_found'] = true;
							$modules[$key]['has_own_directory'] = true;
							$modules[$key]['directory'] = $directory.$module['name']."/";
							
							if(is_file($modules[$key]['directory']."module.xml")){
								$modules[$key]['module_xml_file'] = $modules[$key]['directory']."module.xml";
							}
						}else{
							$modules[$key]['has_own_directory'] = false;
						}
					}
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
				
			}
			
			// print_r($modules);
			return $modules;
			
		}else{
			// ERROR: No modules defined
			return array();
		}
	}
	
	function getAliases(){
	
		$this->_log("Retrieving aliases...");
		
		$aliases = array();
		$i = 0;
		
		foreach($this->modules as $moduleKey => $module){
			if(isset($module['alias']) && is_array($module['alias'])){
				if(isset($module['alias']['match'])){
					$aliases[$i] = $module['alias'];
					$aliases[$i]['module'] = $module['name'];
					$aliases[$i]['module_key'] = $moduleKey;
					$i++;
				}else{
					foreach($module['alias'] as $alias){
						$aliases[$i] = $alias;
						$aliases[$i]['module'] = $module['name'];
						$aliases[$i]['module_key'] = $moduleKey;
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
	
		if(strlen($this->request) > 0){
		
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
						return;
					}else{
						// the actual file containing the class was not found
						$this->moduleName = $this->defaultModule;
						$this->sectionName =& $this->moduleName; // deprecated - for backwards compatibility
						$this->moduleIndex = $this->defaultModuleIndex;
						$this->aliasMatch = $alias['match'];
						$this->aliasDestination = $alias['_content'];
						$this->requestIsUsingAlias = true;
						$this->_log("Request matched alias to existing module, but file {$this->modules[$alias['module_key']]['class_file']} was not found. Loading default module.");
						// LOG: 
						return;
					}
				}
			}
		
			// an alias is not being used
			if($matched = preg_match('/^([\w\._]+)(\/([\w\._]+))?/i', strtolower($this->request), $matches)){
				
				foreach($this->modules as $key=>$module){
					if($matches[1] == strtolower($module['name'])){
						if($this->modules[$key]['class_file_found'] == true){
							$this->moduleName = $module['name'];
							$this->sectionName =& $this->moduleName; // deprecated - for backwards compatibility
							$this->moduleIndex = $key;
							$this->_log("Found module {$this->modules[$this->moduleIndex]['name']}");
							return;
						}else{
							$this->moduleName = $this->defaultModule;
							$this->sectionName =& $this->moduleName; // deprecated - for backwards compatibility
							$this->moduleIndex = $this->defaultModuleIndex;
							$this->_log("Request matched alias to existing module, but file {$this->modules[$this->moduleIndex]['class_file']} was not found. Loading default module.");
							return;
						}
					}
				}
				
				if(!$this->moduleName){
					$this->_log("Request not recognised. Loading default module and default method");
					$this->moduleName = $this->defaultModule;
					$this->sectionName =& $this->moduleName;
					$this->moduleIndex = $this->defaultModuleIndex;
					return;
				}
				
			}
		
		}else{
			
			$this->_log("Request not recognised. Loading default module and default method");
			$this->moduleName = $this->defaultModule;
			$this->sectionName =& $this->moduleName;
			$this->moduleIndex = $this->defaultModuleIndex;
			return;
	
		}
		
	}
	
	function setTemplate(){
	
		// echo $this->moduleIndex;
		
		if(isset($this->moduleIndex)){
			$this->templateName = $this->modules[$this->moduleIndex]['template'];
		}
	}
	
	function setClass(){
		if(isset($this->moduleIndex)){
			$this->className = $this->modules[$this->moduleIndex]['class'];
		}
	}
	
	function setMethod(){
		if($this->requestIsUsingAlias == true){
			
			if(isset($this->modules[$this->moduleIndex]['alias']['match'])){
				$aliases[0] = $this->modules[$this->moduleIndex]['alias'];
			}else{
				$aliases = $this->modules[$this->moduleIndex]['alias'];
				if(!is_array($aliases)){
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
			$default_method = $this->modules[$this->moduleIndex]['default-method'];
		}else{
			preg_match('/^([\w_]{2,})\/([\w_]+)/i', $this->request, $matches);
			
			if(isset($matches[2])){
				$method = $matches[2];
			}
			
			$default_method = $this->modules[$this->moduleIndex]['default-method'];
		}
		
		if(is_file($this->modules[$this->moduleIndex]['class_file'])){
			if(@include_once $this->modules[$this->moduleIndex]['class_file']){
				$available_methods = get_class_methods($this->className);
				if(in_array($method, $available_methods)){
					$this->methodName = $method;
				}else{
					if(in_array($default_method, $available_methods)){
						$this->methodName = $default_method;
					}else{
						// ERROR: requested method not found. default method not implemented.
					}
				}
			}else{
				// ERROR: could not load class file: $this->modules[$this->moduleIndex]['class_file']
			}
		}
	}	
	
	function performAction(){
		if(@include_once $this->modules[$this->moduleIndex]['class_file']){
			
			$this->getVariables = $this->getGetVariables();
			
			//if($destination = $this->requestIsForward()){
				// do some forwarding
				
			//}else{
				
				$args = array("get" => $this->getVariables, "post"=> $_POST, "cookie" => $_COOKIE, "url"=> $this->currentUrl );
				
				$this->prepareTime = microtime(true);
				$this->prepareTimeTaken = number_format(($this->prepareTime - $this->startTime)*1000, 2, ".", ",");
				
				$user_object = new $this->className;
				
				// LOG: Calling $this->className::$this->methodName();
				$this->_log("Calling {$this->className}::{$this->methodName}()");
				$this->result = @call_user_func_array(array(&$user_object, $this->methodName), $args);
				
				$this->postActionTime = microtime(true);
				$this->postActionTimeTaken = number_format(($this->postActionTime - $this->startTime)*1000, 2, ".", ",");
				
				if($destination = $this->requestIsForward()){
					// handle form forwards
					$destination = $this->getFormForwardDestination($destination);
					header("location:".$this->domain.$destination);
					exit;
				}
			//}
		}else{
			// ERROR: could not load class file: $this->modules[$this->moduleIndex]['class_file'];
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
					return false;
				}
			}
		}
		
		// Add some code to check for ordinary forwarding here, once it is implemented.
		
		return false;
	}
	
	function getFormForwardDestination($destination){
		
		foreach($this->activeRequestArray as $key => $value){
			$destination = str_replace('$'.$key, $value, $destination);
		}
		
		// delete undefined variables
		$destination = preg_replace('/\$[\w_]+/i', '', $destination);
		return $destination;
	}
	
	// deprecated
	function getDomainName(){
		// LOG: "Deprecated Function Used: getDomainName() ";
		return $this->getDomain();
	}
	
	function getDomain(){
		return $this->domain;
	}
	
	function getTemplateName(){
		return $this->templateName;
	}
	
	function getGetVariables(){
		
		if(!$this->getVariables){
		
			$variables = array();
		
			if($this->requestIsUsingAlias == true){
			
				preg_match_all('/\$([\w_]+)/i', $this->aliasMatch, $vars, PREG_SET_ORDER);
				preg_match_all('/([\w_]+)=\$[\w_]+/i', $this->aliasDestination, $varnames, PREG_SET_ORDER);
			
				$alias_regexp = $this->getUrlRegExp($this->aliasMatch);
			
				preg_match($alias_regexp, $this->request, $values);
			
				$i = 0;
			
				foreach($vars as $variable){
					$variables[$varnames[$i][1]] = $values[$i+1];
					$i++;
				}
			
			}
			
			$getVars = array_merge($_GET, $variables);
			// echo "didvars";
			/// print_r($getVars);
			return $getVars;
		
		}else{
		
			return $this->getVariables;
		
		}
	}
	
	function getMethodName(){
		return $this->methodName;
	}
	
	function getClassName(){
		return $this->className;
	}
	
	// deprecated
	function getSectionName(){
		// LOG: "Deprecated Function Used: getSectionName() ";
		return $this->getModuleName();
	}
	
	function getModuleName(){
		return $this->moduleName;
	}
	
	function getModuleDirectory(){
		if(isset($this->moduleIndex)){
			return $this->modules[$this->moduleIndex]['directory'];
		}
	}
	
	function isPrivilegedUser(){
		
	}

	function getAjaxModules(){
		return $this->ajaxModules;
	}
	
	// deprecated
	function getAlias(){
		// LOG: "Deprecated Private Function Used: getAlias() ";
	}
	
	function getContent(){
		$this->_log("Returning result of {$this->className}::{$this->methodName}()");
		return $this->_stripSlashesFromArray($this->result);
	}
	
	function getDebugContent(){
		return $this->debugContent;
	}
	
	function getNavigationState(){
		return $this->navigationState;
	}
	
	function getMetaData(){
		return $this->metaData;
	}
	
	function setDebugLevel($level=0){
		
	}
	
	function setCache(){
		
	}
	
	function enableXmlEditor(){
		
	}
	
	function _purge(){
		unset($_SESSION["xmlDataArray"]);
	}
	
	function _debug(){
		
	}
	
	function _log($message){
		$time = number_format(microtime(true)*100000, 0, ".", "");
		$this->log[$time] = "Controller: ".$message;
	}
	
	function _error($message){
		$time = number_format(microtime(true)*100000, 0, ".", "");
		$this->errors[$time] = $message;
	}
	
	function getUrlRegExp($url){
		
		// echo $url." <br />";
		
		// escape special characters (/\|[]{}^.)
		$regexp = str_replace('/', '\/', $url);
		$regexp = str_replace('|', '\|', $regexp);
		$regexp = str_replace('[', '\[', $regexp);
		$regexp = str_replace(']', '\]', $regexp);
		$regexp = str_replace('{', '\{', $regexp);
		$regexp = str_replace('}', '\}', $regexp);
		$regexp = str_replace('.', '\.', $regexp);
		
		// replace every $variable with "([^\/\s]+)"
		$regexp = preg_replace('/[:\$][\w_-]+/', '([^\/\s\.]+)', $regexp);
		
		return "/^".$regexp."\/?$/i";
	}
	
	function _execTime(){
		$microTime = microtime(); 
		$microTime = explode(" ",$microTime); 
		$microTime = $microTime[1] + $microTime[0]; 
		return $microTime;
	}
	
	function _stripSlashesFromArray($value){
		return is_array($value) ? array_map(array('Quince','_stripSlashesFromArray'), $value) : stripslashes($value);
	}
	
	function _redirect($url){
		header('Location: '.$url);
		exit;
	}
	

}

?>