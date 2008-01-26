<?php
/**
  * PHP Controller
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
  * @category   Controller
  * @package    PHP-Controller
  * @license    Lesser GNU Public License
  * @author     Eddie Tejeda <eddie@visudo.com>
  * @copyright  2005 Visudo LLC
  * @version    0.7
  */

$include_path= get_include_path();
if(!is_file($include_path."/PEAR.php") && !is_file($include_path."/XML/Unserializer.php")){
  ini_set('include_path','.:Libraries/pear/:Libraries/SmartestEngine/');
}
require_once 'PEAR.php';
require_once 'XML/Unserializer.php'; 
require_once 'XML/Serializer.php'; 


/**
 * Controller is a class that depends on an XML file that follows 
 * a strict xml schema to organize websites in mini applicationed, defined
 * in classes. This class manages the flow defined in the XML file.
 * @see http://users.visudo.com/eddie/PHP-Controller/controller.xsd
 */
class Controller{

  /**
   * String which is used to store base domain of the application
   * @see Controller::getDomainName
   * @access private
   * @var string contains domain the base as a point of reference for application
   */
  var $domain = null;

  /**
   * @access private
   * @var string contains the base application path after 
   */
  var $domainPath = null;
  
  /**
   * String which stores the path of the modules class in the system
   * @access private
   * @var string contains the path to the web classes
   */
  var $modulePath= null;

  /**
   * @access private
   * @var array contains associative array with modules from controller
   */
  var $modules= null;

  /**
   * @access private
   * @var string contains the working url of the application
   */
  var $currentUrl= null;

  /**
   * @access private
   * @var string contains the original url, before controller mangles it
   */
  var $originalUrl= null;

  /**
   * @access private
   * @var any contains the datastructure of content that will be passed back to application
   */
  var $result= null;

  /**
   * @access private
   * @var any contains the datastructure of navigation information that will be passed back to application
   */
  var $navigationState= null;

  
  /**
   * @access private
   * @var any contains the datastructure of defined by developr that will hold any meta data
   */
  var $metaData = null;

  /**
   * @access private
   * @var array contains an array of the aut
   */
  var $authentication= null;

  /**
   * @access private
   * @var string contains class1 information
   */
  var $request= null;
			
  /**
   * 
   * @access private
   * @var int contains class1 information
   */
  var $debugLevel = 0;	

 /**
   * 
   * @access private
   * @var int contains class1 information
   */
  var $debugContent = array();

 /**
   * 
   * @access private
   * @var int contains class1 information
   */
  var $fatalError = 0;

  /**
   * @access private
   * @var string contains class1 information
   */
  var $startTime= 0;

  /**
   * @access private
   * @var string contains class1 information
   */
  var $endTime= 0;

	/**
   * @access private
   * @var string contains method information
	 */
	var $methodName= null;
	
  /**
   * The contructor looks for controller.xml and checks to see
   * if modrewrite is enabled and loads contents of file. it also
   * gets information about the URL
   * @access public
   * @param string filename controller.xml
   */
  function Controller($filename = 'controller.xml'){
    if($this->fatalError){
			return;
		}
		$this->startTime = $this->_execTime();
 
    if( !file_exists($filename) ){
      $this->fatalError = 1;
			$this->debugContent[] = 'XmlData class constructor did not recieve a file';      
    }
		
    $htcontent = null;
    $htcontent = file_exists(".htaccess") ? file_get_contents(".htaccess") : NULL;
    if(isset($htcontent) && preg_match('/RewriteEngine\s+on/i',$htcontent) ){
      //we do nothing, we are fully and properly configured
    }
    else if(!file_exists(".htaccess") && is_writable(".")){
      $this->debugContent[] = ".htaccess was not found, but was succesfully written";
      $htcontentExample = "AddType application/x-httpd-php *\nphp_value include_path \".:Libraries/Smarty/:Libraries/pear/\"\nDirectoryIndex index.php\nRewriteEngine on\nRewriteRule !^(Resources|\/) index.php\n";      
      if(function_exists('file_put_contents')){
        file_put_contents(".htaccess", $htcontentExample );
      }
      else{
        if (is_writable($filename)) {
          if (!$handle = fopen('.htaccess', 'a')) {
						$this->fatalError = 1;
						$this->debugContent[] = "Cannot open filename: $filename";
          }
          if (fwrite($handle, $htcontentExample) === FALSE) {
						$this->fatalError = 1;
						$this->debugContent[] = "Cannot write to filename: $filename";
          }
          fclose($handle);
        } 
        else {
					$this->fatalError = 1;
					$this->debugContent[] = "The file $filename is not writable";
        }      
      }
    }
    else{
      $errorMessage = "<h2>Mod-Rewrite is required. Please enable Mod-Rewrite in the file called <i>.htaccess</i><br />".
      "or enable write permission on .htaccess file</h2>".
      "Example:<br>".
      "<pre>".
      "AddType application/x-httpd-php *\nphp_value include_path \".:Libraries/Smarty/:Libraries/pear/\"\nDirectoryIndex index.php\nRewriteEngine on\nRewriteRule !^(Resources|\/) index.php\n".
      "</pre>";
			$this->fatalError = 1;
			$this->debugContent[] = $errorMessage;
    }

    
    // load xml controller
    $option = array('complexType' => 'array', 'parseAttributes' => TRUE);
    $unserialized = new XML_Unserializer($option);
    $result = $unserialized->unserialize($filename, true);
    if (PEAR::isError($result)) {
      die($result->getMessage());
    }
    
    // load contents from xml file
    $data = $unserialized->getUnserializedData();
    
    // domain path, required
		/*
    if(isset($data['domain']) && preg_match ( '/^\/[.]+\/$/', $data['domain'])){      
      if( preg_match ( '/^\/', $data['domain']) ){
        echo "<b style=\"color:red\">Warning: Domain should not start with slash</b><br/>";
      }      
      if(strstr($data['domain'],'http://')){
        echo "<b style=\"color:red\">Warning: Remove the domain name from 'domain' tag in controller.xml. 
        This tag now just uses the path of application after base domain. Please see
        http://users.visudo.com/eddie/PHP-Controller/CHANGES Version 0.6 to learn how
        this tag will not exist in future releases or read about the replacement tag 'domain-path'. If this application is installed as the base
        application for domain, just add a forward slash (/) to 'domain'</b>";
      }
      $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";      
      $this->domain = $protocol.$_SERVER['HTTP_HOST']."/".$data['domain'];
    }
    else{
      // application path, required
      if( preg_match ( '/^\//', $data['domain-path']) ){
        echo "<b style=\"color:red\">Warning: Domain should not start with slash</b><br/>";
      }
      if(isset($data['domain-path'])){
        if(preg_match ( '/\/$/', $data['domain-path'])){
          $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";      
          $this->domain = $protocol.$_SERVER['HTTP_HOST']."/".$data['domain-path'];
        }
        else{
          die("&lt;domain-path&gt; does not end in a forward slash (/)");
        }
      }
      else{
        $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";      
        $this->domain = $protocol.$_SERVER['HTTP_HOST']."/"; 
      }
    }
		*/
		if(isset($data['domain-path'])){
      $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";      
      $this->domain = $protocol.$_SERVER['HTTP_HOST']."/".$data['domain-path'];
		}
		else if(!isset($data['domain-path'])){
			//TODO: this should be done with serializer
			$data['domain-path']= substr($_SERVER['REQUEST_URI'], 1);
			$xmlcontent= file_get_contents($filename);
			$xmlcontent =str_ireplace("</modules>", "</modules>\n\t<domain-path>".$data['domain-path']."</domain-path>", $xmlcontent);
			if(!is_writable($filename)){
				$this->fatalError = 1;
				$this->debugContent[] = "Permission denied writing controller.xml";
			}
			file_put_contents($filename,$xmlcontent);
      $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";      
      $this->domain = $protocol.$_SERVER['HTTP_HOST']."/".$data['domain'];			
		}
		else{
			$data['domain-path']='';
			$xmlcontent= file_get_contents($filename);
			$xmlcontent =str_ireplace("</modules>", "</modules>\n\t<domain-path></domain-path>", $xmlcontent);
			file_put_contents($filename,$xmlcontent);
      $protocol = isset($_SERVER['HTTPS']) ? "https://" : "http://";      
      $this->domain = $protocol.$_SERVER['HTTP_HOST']."/".$data['domain-path'];
		}
    
		$this->catchall = isset($data['catch-all']) ? $data['catch-all']=="true" : false;

    // modules path, required
    if(isset($data['modules']) && preg_match ( '/\/$/', $data['modules'])){      
      if(preg_match ( '/^(\/)/', $data['modules'])){
				if(is_dir($data['modules'])){
          $this->modulePath = null;
					$this->modulePath = "/".$data['modules'];
        }
        else{
					$this->fatalError = 1;
					$this->debugContent[] = "The absolute path to the 'Modules' directory, defined in controller.xml, does not exist";
        }      
      }
      else{
        if(is_dir(getcwd()."/".$data['modules'])){
					$this->modulePath = null;
					$this->modulePath = getcwd()."/".$data['modules'];
        }
        else{
					$this->fatalError = 1;
					$this->debugContent[] = "The relative path to the 'Modules' directory, defined in controller.xml, does not exist";
        }
      }
    }
    else{
      $this->fatalError = 1;
			$this->debugContent[] = "&lt;modules&gt; not defined in controller or does not end in a forward slash (/)";
    }

    // authentication, not required
    if(isset($data['authentication']) ){
      $this->authentication = $data['authentication'];
    }

    // pages, required
    if(isset($data['module']) ){
      
      if(isset($data['module']['name'])){
        $name = strtolower($data['module']['name']);
        $this->modules[$name] = $data['module'];
        
        //<!-- aliases
        if(isset($data['module']['alias']) ){  
          if(isset($data['module']['alias']['match'])){
            $name =$data['module']['alias']['match'];        
            $this->aliases[$name] = $data['module']['name']."/".$data['module']['alias']['_content'];
          }
          else{
            for($i = 0; $i < count($data['module']['alias']); $i++ ){
              $name = $data['module'][$i]['alias']['match'];          
              $this->aliases[$name] = $data['module']['name']."/".$data['module'][$i]['alias']['_content'];
            }      
          }       
        }
        //-->
      }
      else{
        
        for($i = 0; $i < count($data['module']); $i++ ){
          $name = strtolower($data['module'][$i]['name']);
          $this->modules[$name] = $data['module'][$i];
          
          //<!-- aliases
          if(isset($data['module'][$i]['alias']) ){  
            if(isset($data['module'][$i]['alias']['match'])){
              $name = $data['module'][$i]['alias']['match'];        
              $this->aliases[$name] = $data['module'][$i]['name']."/".$data['module'][$i]['alias']['_content'];
            }
            else{
              for($j = 0; $j < count($data['module'][$i]['alias']); $j++ ){
                $name = $data['module'][$i]['alias'][$j]['match'];          
                $this->aliases[$name] = $data['module'][$i]['name']."/".$data['module'][$i]['alias'][$j]['_content'];
              }      
            }       
          }
          //-->          
        }      
      }
    }
    else{
			$this->fatalError = 1;
			$this->debugContent[] = "&lt;module&gt; not defined in controller";
    }
		
    
    // THIS CODE WILL BE REWRITTEN WHEN BACKWARDS COMPATABILITY IS NOT LONGER REQUIRED TO ALIASES, eddie march 15,2006
    // aliases, not required so it does not die if it does not find one
    // this code is where old aliases used to be loaded,eddie march 15,2006
    if(isset($data['alias']) ){
      if(!isset($data['alias'][0])){
        $name = $data['alias']['name'];
        $this->aliases[$name] = $data['alias'];
      }
      else{
        for($i = 0; $i < count($data['alias']); $i++ ){ 
          $name = $data['alias'][$i]['name'];
          $this->aliases[$name] = $data['alias'][$i];
        }      
      }
    }
    
    $this->originalUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    
    // the following is URI mangling get the request variables from browser
    $this->currentUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    
    $requestVariables = strstr( $_SERVER['REQUEST_URI'], "?" );
    $domainLen = strlen( $this->domain);
      
    $allRequestSection = substr($this->currentUrl, $domainLen);    
    $getPosition = strpos($allRequestSection, "?");
    $getPosition = ($getPosition > 0) ? $getPosition : strlen($allRequestSection);
    $requestSection = substr($allRequestSection, 0, $getPosition );
    $get = substr($allRequestSection, $getPosition+1 );
    $requestSection = preg_replace('/\/$/', "", $requestSection);
  
    if( $alias = $this->getAlias($requestSection) ){ 
      if( is_array($alias) ){
        // we append the variables from controller to current variables
        $this->currentUrl = $this->domain.$alias['module'];
        $seperator = strpos($alias['module'], "?") > 0 ? "&" : "?";
        $seperator = count($_GET) >  0 ? $seperator : "";        
        $this->currentUrl = $this->domain.$alias['module'].$seperator.$get;
      }
      else{
        // we append the variables from controller to current variables
        $this->currentUrl = $this->domain.$alias;
        $seperator = strpos($alias, "?") > 0 ? "&" : "?";
        $seperator = count($_GET) >  0 ? $seperator : "";        
        $this->currentUrl = $this->domain.$alias.$seperator.$get;
      }
    }
    $this->endTime = $this->_execTime();
  }
	
	

 /**
  * Load the proper class name, execute the default method in that object
  * if no method is defined in url. if method is defined in URL, execute
  * that method and save contents to private variable.
  * @access public
  * @param string filename controller.xml
  */
  function performAction(){
    if($this->fatalError){
			return;
		}

    //$debugContent = null;
		if($this->debugLevel > 0){
			$this->_debug();
    }

    if( $this->isPrivilegedUser() && !(isset($_POST['username']) && isset($_POST['password']) )){
      // detect aliases
      $className =  $this->getClassName();

      $classFile = $this->modulePath.$className.".class.php";
      if(!file_exists($classFile)){
				$this->fatalError = 1;
				$this->debugContent[] = "$className.class.php not found in ".$this->modulePath;      
      }      
      include_once($classFile);
      $page = new $className;

      // this is an optional function a developer can implement if developer wants
      // to keep track of navigation state
      if( method_exists( $page, "navigationState"  )){
        $len = strlen($this->domain);        
        $this->navigationState = $page->navigationState(substr($this->currentUrl, $len, strlen($this->currentUrl)));
      }
      
      // this is an optional function a developer can implement if developer wants
      // in their class to hold meta data about the current page
      if( method_exists( $page, "metaData"  )){
        $len = strlen($this->domain);        
        $this->metaData = $page->metaData();
      }

      // this is the dynamic method
      $webmethod = $this->getMethodName();
			$section = strtolower($this->getSectionName()); 
			
			//catch all
			if(!method_exists( $page, $webmethod) && $this->catchall){
				$webmethod = $this->modules[$section]['default-method'];
				$this->methodName = $webmethod;				
			}
			// echo $webmethod;

			//module enabled
			if(isset($this->modules[$section]['enabled']) && $this->modules[$section]['enabled'] == 'false'){
				$this->fatalError = 1;
				$this->debugContent[] = "Module disabled";
			}

			
			//allow or deny methods
			if(isset($this->modules[$section]['method-access']) && $this->modules[$section]['method-access'] == 'deny'){

				
				if(is_array($this->modules[$section]['method-name'])){
					foreach($this->modules[$section]['method-name'] as $method){
						if($method == $webmethod){
							$this->fatalError = 1;
							$this->debugContent[] = "Restricted Method";
						}
					}
				}
				else if(isset($this->modules[$section]['method-name'])){
					if($this->modules[$section]['method-name'] == $webmethod){
						$this->fatalError = 1;
						$this->debugContent[] ="Restricted Method";
					}
				}				
			}
			else if(isset($this->modules[$section]['method-access']) && $this->modules[$section]['method-access'] == 'allow'){
				$valid = false;
				if(isset($this->modules[$section]['method-name'])){
					if(is_array($this->modules[$section]['method-name'])){
						foreach($this->modules[$section]['method-name'] as $method){
							if($method == $webmethod){
								$valid = true;
							}
						}
					}
					else if(isset($this->modules[$section]['method-name'])){
						if($this->modules[$section]['method-name'] == $webmethod){
							$valid = true;
						}
					}
					if($valid == false){
						$this->fatalError = 1;
						$this->debugContent[] = "Method not allowed";
					}

				}

			}
			else{
				//allow all methods
			}

			//echo "<br>".$className."<br>";
      if( method_exists( $page, $webmethod)){
        $get = $this->getGetVariables(); 
        //$section = $this->getSectionName(); //REMOVE THIS? FOR SPEED SINCE IT IS USED BELOW

        //we make sure that this page is not a form action. if it is it better have POST/GET
        //data as defined in XML file
        // redirect the user if this is a form that has a forward option
        $methodType = null;
        if( isset( $this->modules[$section]['form-forward'] ) ){
          if( isset( $this->modules[$section]['form-forward'][0] ) ){
            for($i = 0; $i < count($this->modules[$section]['form-forward']); $i++){
              if($this->modules[$section]['form-forward'][$i]['method-name'] == $webmethod){
                $methodType = strtoupper($this->modules[$section]['form-forward'][$i]['method-type']);  
                if(!isset($methodType)){
                  //error. method type must be defined
                }

                $forwardto=null;
                if( ($methodType == "GET" && (count($_GET)==0)) || ($methodType == "POST" && (count($_POST)==0) )){
                  //user should not be accessing this page now!!
                  header("Location: ".$this->domain);
                  die();
                }
              }
            }
          }
          else{
            if($this->modules[$section]['form-forward']['method-name'] == $webmethod){
              
              $methodType = strtoupper($this->modules[$section]['form-forward']['method-type']);
              if(!isset($methodType)){
                //error. method type must be defined
              }
              $forwardto=null;
              if( ($methodType == "GET" && (count($_GET)==0)) || ($methodType == "POST" && (count($_POST)==0) )){
                //user should not be accessing this page now!!
                header("Location: ".$this->domain);
                die();
              }
            }
          }
        }
				
        //execute now!
        
        $args = array("get" => $get, "post"=> $_POST, "cookie" => $_COOKIE, "url"=> $this->currentUrl ); 
				$this->result = call_user_func_array(array(&$page, $webmethod),$args);
        if($this->debugLevel == 0){
          //ob_end_flush();
        }
        
        // redirect the user if this is a form that has a forward option
        if( isset( $this->modules[$section]['form-forward'] ) ){
          if( isset( $this->modules[$section]['form-forward'][0] ) ){
            for($i = 0; $i < count($this->modules[$section]['form-forward']); $i++){       
              if($this->modules[$section]['form-forward'][$i]['method-name'] == $webmethod){
                
                $methodType = strtoupper($this->modules[$section]['form-forward'][$i]['method-type']);  
                $forwardto=null;
                if( ($methodType == "GET" && count($_GET)) || ($methodType == "POST" && count( $_POST))){
                  $forwardto=$this->modules[$section]['form-forward'][$i]['_content'];
                  while(preg_match('/\$([\w]+)/', $forwardto , $match)  ){
                    $requestvarname = $match[1];
                    $requestvarvalue = $_REQUEST[$requestvarname];
                    $forwardto=preg_replace('/\$([\w]+)/', $requestvarvalue, $forwardto);
                  }
                  header("Location: ".$this->domain.$forwardto);
                  die();
                }
              }
            }
          }
          else{
            if($this->modules[$section]['form-forward']['method-name'] == $webmethod){
              $methodType = strtoupper($this->modules[$section]['form-forward']['method-type']);
              $forwardto=null;
              if( ($methodType == "GET" && count( $_GET)) || ($methodType == "POST" && count( $_POST))){
                  $forwardto=$this->modules[$section]['form-forward']['_content'];

                  while(preg_match('/\$([\w]+)/', $forwardto , $match)  ){
                    $requestvarname = $match[1];
                    $requestvarvalue = $_REQUEST[$requestvarname];
                    $forwardto=preg_replace('/\$([\w)/', $requestvarvalue, $forwardto);
                   }
                  header("Location: ".$this->domain.$forwardto);
                  die();
              }
            }
          }
        }
      }
      else{
				$this->fatalError = 1;
				$this->debugContent[] = "The specified method is not implemented in this class";        
      }
    }
    // The user is not privilaged, but they are trying to login
    else if(isset($_POST['username']) && isset($_POST['password'])){
      $className = $this->authentication['class'];
      $authMethod = $this->authentication['login-forward']['method-name'];
      $classFile = $this->modulePath.$className.".class.php";
      if(!file_exists($classFile)){
				$this->fatalError = 1;
				$this->debugContent[] = "$className.class.php not found in ".$this->modulePath;      
      }
      require_once($this->modulePath.$className.".class.php");
      
      $page = new $className;     
      
      if( method_exists( $page, $authMethod  )){
        $args = array("post"=> $_POST ); 
        $this->result = call_user_func_array(array(&$page, $authMethod),$args);
        if($this->debugLevel == 0){
          ob_end_flush();
        }
      }
      else{
				$this->fatalError = 1;
				$this->debugContent[] = "Authentication class is required";
      }
      
      if(isset($this->authentication['login-forward'])){
        //we are forwared to the success page even we fail login. the login form will just reappear
        header("Location: ".$this->domain.$this->authentication['login-forward']['_content']);
        die();
      }
      
      $this->fatalError = 1;
			$this->debugContent[] = "A section needs to be defined on successful login";
      
    }      
    else{// we are loading up the login form 

      $className = $this->authentication['class'];
      $authMethod=$this->getMethodName();
      $classFile = $this->modulePath.$className.".class.php";
      if(!file_exists($classFile)){
				$this->fatalError = 1;
				$this->debugContent[] = "$className.class.php not found in ".$this->modulePath;      
      }
      require_once($this->modulePath.$className.".class.php");				
      $page = new $className;     

      //we are loggin out
      if(!PEAR::isError($authMethod) ){
        $args = array();
        if( isset($authMethod) && method_exists( $page, $authMethod  )){
          call_user_func_array(array(&$page, $authMethod),$args);
          header("Location: ".$this->domain.$this->authentication['logout-forward']['_content']);
          die();
        }
      }

      if(isset($_SESSION['role'])){
        if(isset($this->authentication['login-forward']['_content'])){
          // we are forwared to the success page even we fail login. the login form will just reappear
         header("Location: ".$this->domain.$this->authentication['login-forward']['_content']);
         die();
        }
        else{
					$this->fatalError = 1;
					$this->debugContent[] = "A section needs to be defined on successful login";
        }
      }
    }
  }
	
	
 /**
  * @return string base path domain
  * @access public
  */
  function getDomainName(){
    return $this->domain;
  }
  
 /**
  * Checks the current class and returns name of the template defined by controller.xml 
  * for this class 
  * @access public
  * @return string the name of the template defined by controller.xml for this class
  */
  function getTemplateName(){		
    if($this->fatalError){
			return;
		}
		if($this->isPrivilegedUser() ){
      $section = $this->getSectionName();
      $className = $this->getClassName();
        
      if(isset($this->modules[$section]['name'])){
        if($this->modules[$section]['class'] == $className){
          return $this->modules[$section]['template'];
        }
      }
    }
    else{
      return $this->authentication["template"];
    }  
  }
	
	
 /**
  * Collects the GET variables for this class and loads any preexisting 
  * variables that might be defined by alias
  * @access public
  * @return string of GET variables
  */
  function getGetVariables(){
    if($this->fatalError){
			return;
		}
		// extract the GET variables from URI
    $requestArray = isset($_GET) ? $_GET : null;
    
    // extract GET variables from alias tag if defined in controller
    $requests = null;
    if($alias = $this->getAlias( $this->getSectionName() )  ){
      $pos = null;
      $req = null;      
      if(is_array($alias)){
        //REMOVE THIS IN 1.0 release
        $pos = strpos($alias['module'], "?") > 0 ? strpos($alias['module'], "?")+1: strlen($alias['module']);
        $req = substr( $alias['module'], $pos, (strlen($alias['module']) - $pos) );
      }
      else{
        $pos = strpos($alias, "?") > 0 ? strpos($alias, "?")+1: strlen($alias);
        $req = substr( $alias, $pos, (strlen($alias) - $pos) );
      }
      
      $tempRequests = null;
      if(strpos($req, "&")){
        $tempRequests = explode("&",$req);
      }
      
      if(isset($tempRequests) ){
        foreach($tempRequests as $tmp){
          $requests[] = $tmp;
        }
      }
      if(!isset($tempRequests) && isset($req)) {
        $requests[] = $req;
      }
    }
    
    //appending variables from controller to GET
    if(is_array($requests)){
      foreach($requests as $request){
        $var = explode("=",$request);
        if(is_array($var) && isset($var[1]) ){
          $requestArray[$var[0]] = $var[1];
        }
      }    
    }
    return $requestArray;
  }
	
	
	
	/**
	 * Get the method name for the current section. It returns the second item in the
	 * request i.e example.org/className/action?param=true would return "action"
  * @param string optional string to determine the method of this section
	 * @return string method name of current class
	 */    
	function getMethodName($sectionName=""){    
    if($this->fatalError){
			return;
		}
		if(isset($this->methodName) && strlen($this->methodName)){
			return $this->methodName;
		}
		
		// we get sectionName from either a parameter if defined, or get the default sectionName if defined
		$sectionName = (strlen($sectionName) > 0) ? $sectionName : $this->getSectionName();   
		
		$len = strlen( $this->domain); 
		$request = substr($this->currentUrl, $len);
		$start = strpos($request, "/") > 0 ? strpos($request, "/")+1 : strlen($request);

		$end = strpos($request, "?") > 0 ? strpos($request, "?"):  strlen($request);    
		
		// echo $request."-".$start."-".$end;
		
		$methodname = substr($request, $start, ($end-$start));
		
		// echo $methodname;
		
		if(strpos($methodname, "/")){
			$pos = strpos($methodname, "/");
			$methodname = substr($methodname, 0, $pos);
		}
		
			
		if(!$this->isPrivilegedUser()){
			if (isset($this->authentication['logout-forward']['method-name'])){ 
				if($this->authentication['logout-forward']['method-name'] == $methodname ){        
					return $methodname;
				}
				else{
					return false;
				}
			}
			if(isset($this->authentication['login-forward']['method-name'])){
				if($this->authentication['login-forward']['method-name'] == $methodname){
					return $methodname;
				}
				else{
					return false;
				}
			}
			else{
				return false; //NOTICE: is this what i want to return?
			}
		}
	 
		
		
		$className = $this->getClassName();

		$classFile = $this->modulePath.$className.".class.php";
		
		if(!file_exists($classFile)){
      		$this->fatalError = 1;
			$this->debugContent[] = "$className.class.php not found in ".$this->modulePath;      
		}else{
			require_once($classFile);
		}
		
		$methods = get_class_methods ($className);
		
		if(!array_search ( $methodname, $methods) && $this->catchall){
			$webmethod = $this->modules[$section]['default-method'];
			$methodname = $webmethod;
			$this->methodName = $webmethod;				
		}

		//echo "<b>".var_dump($methodname)."</b><br>";
		
		// we have the section name from just looking at the URL
		if( $methodname ){
			return $methodname;
		}
			
		// we are now looking in the xml file
		if(isset($this->modules[$sectionName])){
			if(isset($this->modules[$sectionName]['name']) ){
				return $this->modules[$sectionName]['default-method'];
			}
		}
	
		if(isset($this->aliases)){
			if(is_array($this->aliases[$sectionName]['name'])){
				//REMOVE FOR 1.0
				$start = strpos($this->aliases[$sectionName]['module'], "/") > 0 ? strpos($this->aliases[$sectionName]['module'], "/")+1 : strlen($this->aliases[$sectionName]['module']);
				$end = strpos($this->aliases[$sectionName]['module'], "?") > 0 ? strpos($this->aliases[$sectionName]['module'], "?"):  strlen($this->aliases[$sectionName]['module']);      
				$methodname = substr($this->aliases[$sectionName]['module'], $start, ($end- $start));
				//the following assumes that if no method name is parsed from xml file then we should just use the default methodname
				return strlen($methodname) ? $methodname : $this->getMethodName($this->aliases[$sectionName]['module']);
			}
			else if(strlen($this->aliases[$sectionName])){
				$start = strpos($this->aliases[$sectionName], "/") > 0 ? strpos($this->aliases[$sectionName], "/")+1 : strlen($this->aliases[$sectionName]);
				$end = strpos($this->aliases[$sectionName], "?") > 0 ? strpos($this->aliases[$sectionName], "?"):  strlen($this->aliases[$sectionName]);      
				$methodname = substr($this->aliases[$sectionName], $start, ($end- $start));
	
				//the following assumes that if no method name is parsed from xml file then we should just use the default methodname
				return strlen($methodname) ? $methodname : $this->getMethodName($this->aliases[$sectionName]);
			}
		}
  
      $this->fatalError = 1;
			$this->debugContent[] = "Method does not exists";
  }

	
	/**
	 * Get the class name for the current section. it checks the page names in xml
	 * file then it looks at aliases then looks for a default if it doesn't find
	 * the previous two or false;
	 * @return string the class name without any extension, php or class.php
  */
  function getClassName(){
    if($this->fatalError){
			return;
		}
    $sectionName = $this->getSectionName();		  
    $privileged = $this->isPrivilegedUser();
    

    if($privileged){
      if(strlen( $sectionName) > 0 ){
        if(isset($this->modules[$sectionName]['name'])){
          return $this->modules[$sectionName]['class'];
        }
      }
    
      if(isset($this->aliases)) {
        if( is_array($this->aliases[$sectionName])){
          //Remove in 1.0
          $len = strpos($this->aliases[$sectionName]['module'], "/");
          $len = isset($len) ? strlen($this->aliases[$sectionName]['module']) : $len; 
          return substr( $this->aliases[$sectionName]['module'], 0, $len);
        }
        else if( strlen($this->aliases[$sectionName])){
          $len = strpos($this->aliases[$sectionName], "/");
          $len = isset($len) ? strlen($this->aliases[$sectionName]) : $len; 
          return substr( $this->aliases[$sectionName], 0, $len);
        }
      
      }
      
      if(isset($this->authentication['name'])) {
        if($this->authentication['name'] == $sectionName) {
          return $this->authentication['name'];
        }
      }
    
      //if all fail, show default
      if(isset($sectionName)){
        foreach($this->modules as $page){
          if($page['default-page'] == true ){
            return $page['class'] ;
          }
        }
      }
    }
    else{
      if(isset($this->authentication)){
        if($this->authentication['name']){
          return $this->authentication['class'];
        }
      }
    }
    return false;
  }
	


	/**
	 * Get the section name from the URI
	 * @return string the name of the section we are in (i.e it exists in xml file) or default if none is specified in URI
	 */
  function getSectionName(){
    if($this->fatalError){
			return;
		}
    $requestStart = strlen($this->domain);
    $request = substr($this->currentUrl, $requestStart, strlen($this->currentUrl) );

    $sectionStart = (strpos($request, "/")) ? strpos($request, "/") : strlen($request);
    $sectionName = substr($request, 0, $sectionStart );
		$sectionName = strtolower($sectionName);
    // pages

    if(isset ($sectionName) ){
      if(isset($this->modules) ){
        if(isset($this->modules[$sectionName]['name'])){
          return $sectionName;
        }
      }
    }
    
    // aliases
    if(isset ($sectionName) ){
      if(isset($this->aliases) ){
        if(isset($this->aliases[$sectionName]) && is_array($this->aliases[$sectionName])){
          return $sectionName;
        }
        else if(isset($this->aliases[$sectionName]) && strlen($this->aliases[$sectionName])){
          return $sectionName;
        }
    
      }
    }
    
    // auth forms
    if( isset ( $sectionName) ){
      if(isset($this->authentication) ){
        if($this->authentication['name'] == $sectionName){
          return $sectionName;
        }
      }
    }
    
    
    // regexp forms
    if(isset ($sectionName) ){
      $requestStart = strlen($this->domain);
      $request = substr($this->currentUrl, $requestStart, strlen($this->currentUrl) );
      
      $sectionStart = (strpos($request, "/")) ? strpos($request, "/") : strlen($request);
      $sectionName = substr($request, 0, $sectionStart );
    
      if(isset($this->aliases) ){
        foreach($this->aliases as $pageAliasName=>$pageAliasValue){
          $matches=null;        
          if( preg_match_all('/\$(\w+)/i', $pageAliasName, $matches ) ){
            //escape special characters in URL
            $item_name = preg_replace('/([\~\^\.\?\/\*\+\[\]\(\)\{\}])/', '\\\$1',$pageAliasName);
            //generate an unique regexp for the current alias info (ie. turn variable names into terms we'll be able to search the URL with
            $request_regexp = preg_replace ('/\$(\w+)/', '([^\/\s\?]+)', $item_name); 
            $request_regexp .= '$';          
    
            //with the generated regular expression according to alias, test current URL and see if it matches
            if(preg_match("/$request_regexp/", $request, $variable_matches)){
              $var_values = null;
              $item_reg = $pageAliasValue;
              for($i=0;$i<count($matches[1]);$i++){
                $name= $matches[0][$i];
                $value= $variable_matches[$i+1];
                $item_reg = str_replace($name,$value, $item_reg);
                
                $this->currentUrl = $this->domain.$item_reg;
    
              }
              $requestStart = strlen($this->domain);
              $request = substr($this->currentUrl, $requestStart, strlen($this->currentUrl) );
               
              $sectionStart = (strpos($request, "/")) ? strpos($request, "/") : strlen($request);
              $sectionName = substr($request, 0, $sectionStart );
              return $sectionName;
            }           
          }
        }
      }
    }

    // user specifies a url and we're here, its not found
    //if(strlen($sectionName) > 0){
    //  return false;
    //}
    
    // Now are looking for default "pages" or "aliases" if nothing is specified in URL 
		foreach($this->modules as $page){
      if(isset($page['default-page']) &&  ($page['default-page'] == true) ){
        return $page['name'];
      }
    }      
  
    foreach($this->aliases as $alias){
      if($alias['default-page'] == true){
        echo "<b style=\"color:red\">Please do not set 'default-page' aliases</b>";
      }
    }
  
   return false;
  }

	
 /**
  * Detects the current section and checks if the current user is allowed to view
  * that section. it determines this by looking at the SESSION variable "role" and
  * compares it to the one in the <page> tag in controller. NOTICE: that no role defined
  * in the xml file makes it public by default.
  * @todo: allow support for more then one group per section
  * @return bool true if the SESSION['role'] is set and matches the controller role
  * @return bool false if SESSION['role'] is not set or does match the controller role
  */
  function isPrivilegedUser(){
    if($this->fatalError){
			return;
		}
		$sectionName = $this->getSectionName();
    $page = null;
    
    // look at the pages in xml file
    if(isset($this->modules)){
     if(isset($this->modules[$sectionName]["name"])){
        $page = $this->modules[$sectionName];
      }
    }
    
    // now check aliases... there are more than one alias defined
    if(isset($this->aliases)){
      if(isset($this->aliases[$sectionName]['name']) && is_array($this->aliases[$sectionName]['name'])){
        //REMOVE in 1.0
        $len = strpos($this->aliases[$sectionName]['module'], "/");
        $len = isset($len) ? strlen($this->aliases["$sectionName"]['module']) : $len; 
        $pageName = substr( $this->aliases[$sectionName]['module'], 0, $len);
        $page = $this->modules[$pageName];
      }
      else if(isset($this->aliases[$sectionName]) && strlen($this->aliases[$sectionName])){
        $len = strpos($this->aliases[$sectionName], "/");
        $len = isset($len) ? strlen($this->aliases[$sectionName]) : $len; 
        $pageName = substr( $this->aliases[$sectionName], 0, $len);
        $page = $this->modules[$pageName];
      }
      
    }    
    
    if( $this->authentication['name'] == $sectionName){
      return false; 
    }

    // no role assigned, it's public
    $role = isset($_SESSION['role']) ? $_SESSION['role']: null;
    if ( !isset ($page['role'])){
     return true;
    }
    else if( $page['role'] == $role ){
     return true;
    }
    else{
     return false;    
    }
  }
	

  
  
  
	/**
  * Get the method name from XML filr for the current section we are in
  * @param string sectionName which is the path in URL
  * @return string alias of the page
  * @return bool false if alias is not found
  */ 
  function getAlias($sectionName){		
    if($this->fatalError){
			return;
		}
		$requestStart = strlen($this->domain);
    $request = substr($this->originalUrl, $requestStart, strlen($this->originalUrl) );
    
    $sectionStart = (strpos($request, "?")) ? strpos($request, "?") : strlen($request);
    $requestMark = substr($request, 0, $sectionStart );
    $request= preg_replace('/\/$/',"", $requestMark);
    $requestFromAlias = null;
    // <!-- The bottom is copied directly from getSectionName this should be a method, or streamlined
    if(isset ($request) ){
      if(isset($this->aliases) ){
        foreach($this->aliases as $pageAliasName=>$pageAliasValue){
          $matches=null;        
          if( preg_match_all('/\$(\w+)/i', $pageAliasName, $matches ) ){
            //escape special characters in URL
            $item_name = preg_replace('/([\~\^\.\?\/\*\+\[\]\(\)\{\}])/', '\\\$1',$pageAliasName);
            //generate an unique regexp for the current alias info (ie. turn variable names into terms we'll be able to search the URL with
            $request_regexp = preg_replace ('/\$(\w+)/', '([^\/\s\?]+)', $item_name); 
            $request_regexp .= '$';          
  
            //with the generated regular expression according to alias, test current URL and see if it matches
            if(preg_match("/$request_regexp/", $request, $variable_matches)){
              $var_values = null;
              $item_reg = $pageAliasValue;
              for($i=0;$i<count($matches[1]);$i++){
                $name= $matches[0][$i];
                $value= $variable_matches[$i+1];
                $item_reg = str_replace($name,$value, $item_reg);
                
                $this->currentUrl = $this->domain.$item_reg;
  
              }
              $requestStart = strlen($this->domain);
              $requestFromAlias = substr($this->currentUrl, $requestStart, strlen($this->currentUrl) );               
            }           
          }
        }
      }
    }
    // -->

    if(isset($this->aliases)){
      if(strlen($requestFromAlias)){
        return $requestFromAlias;
      }
      if(isset($this->aliases[$request]) && strlen($this->aliases[$request])){
        return $this->aliases[$request];
      }
      //REMOVE IN 1.0. THIS IS FOR BACKWARDS COMPAT IN VERSION 0.6
      if(isset($this->aliases[$request]['module'])){
        return $this->aliases[$request];
      }
    }
    return false; 
  }
	
 /**
  * @return mixed anycontent, you have to know what the class method results
  */
  function getContent(){
    if($this->fatalError){
			return;
		}
		return $this->_stripSlashesFromArray($this->result);
  }

 /**
  * @return mixed anycontent, you have to know what the class method results
  */
  function getDebugContent($printHtml=1){
    $openingHtml = "<div id=\"javscriptlayer\" style=\"text-align:left;position: absolute; visibility: hidden;\">
    <script language=\"javascript\" type=\"text/javascript\">
    //Thank you quirksmode!
    //http://www.quirksmode.org/js/cookies.html
    function createCookie(name,value,days){
    if (days){
      var date = new Date();
      date.setTime(date.getTime()+(days*24*60*60*1000));
      var expires = \"; expires=\"+date.toGMTString();
    }
    else var expires = \"\";
    document.cookie = name+\"=\"+value+expires+\"; path=/\";
    }
    
    function readCookie(name){
    var nameEQ = name + \"=\";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++){
      var c = ca[i];
      while (c.charAt(0)==' ') c = c.substring(1,c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
    }
    
    function debugStatus(){          
    var status = readCookie('php_controller_debug');
    
    if(status == 'debug_false'){
      document.getElementById('debug_window').style.visibility='hidden';
      document.getElementById('close_window').style.visibility='hidden';
      document.getElementById('show_window').style.visibility='visible';
    }
    else{
      document.getElementById('debug_window').style.visibility='visible';
      document.getElementById('close_window').style.visibility='visible';
    }
    }
    </script>
    </div>
    <div id=\"show_window\" style=\"text-align:left;bottom: 0px;  z-index:100; right: 0px; position: fixed; visibility: hidden; background-color: #ff6\"><a style=\"text-decoration: none;  font-family: courier; font-size: 12px;\" href=\"#\" onclick=\"document.getElementById('debug_window').style.visibility='visible'; document.getElementById('close_window').style.visibility='visible'; document.getElementById('show_window').style.visibility='hidden'; createCookie('php_controller_debug', 'debug_true', 1);\">debug</a></div>
    <div id=\"debug_window\" style=\"text-align:left;opacity: .99; z-index:100; padding-left: 10px; padding-top: 10px; position: fixed; font-size: 10px; width: 600px; bottom: 0px; right: 0px; background-color:#ff6\">
    <div id=\"close_window\" style=\"text-align:left;visibility:visible; float: right; top:0px; right:5px; position:relative;\"><a href=\"#\" style=\"text-decoration: none; font-size: 12px; font-family: courier;\" onclick=\"document.getElementById('close_window').style.visibility='hidden';document.getElementById('debug_window').style.visibility='hidden';document.getElementById('show_window').style.visibility='visible'; createCookie('php_controller_debug', 'debug_false', 1);\">X</a></div>
    <b style=\"text-align:left; color:black; font-size: 18px\">Debugger Enabled</b>\n
    <pre>";

    $closingHtml = "</pre>\n<script language=\"javascript\" type=\"text/javascript\">window.onload=debugStatus();</script></div>\n";
		
		if($printHtml){
			//echo $openingHtml;
			foreach($this->debugContent as $key=>$value){
				echo $key." = ".$value."\n";
			}
			//echo $closingHtml;
		}
		else{
			return $this->debugContent;
		}
  }
	
	/**
	 * @return mixed anycontent, you have to know what the class method results
	 */    
  function getNavigationState(){
    return $this->navigationState;
  }
	
	/**
	 * @return mixed anycontent, you have to know what the class method results
	 */    
  function getMetaData(){
    return $this->metaData;
  }

  /**
   * @see Controller::setDebugLevel
   * @access public
   */
  function setDebugLevel($int){
    $this->debugLevel = $int;
  }
  
  /**
   * @see Controller::setDebugLevel
   * @access public
   */
  function setCache($int){
    $this->cacheLevel = $int;
  }

  /**
   * @see Controller::setDebugLevel
   * @access public
   */
  function enableXmlEditor($int){
  }

  
  /**
   * @see Controller::setDebugLevel
   * @access private
   */
  function _debug(){
		
		// echo $this->debugLevel;
		
		if($this->debugLevel > 0){    
			$this->debugContent["Internal URI"] = $this->currentUrl;
			$this->debugContent["Modules Path"] = $this->modulePath;
      $this->debugContent["Modules Found"] =  (is_dir($this->modulePath)) ? "Valid" : "Modules directory not found";
      $this->debugContent["Section Name"] = $this->getSectionName();
      $this->debugContent["Class Name"] = $this->getClassName();
			$method = $this->getMethodName();
			$method = strlen($method) ? $method : "(none defined)";
      $this->debugContent["Method Name"] = $method;
      
      
      
      if( $alias = $this->getAlias($this->getSectionName())){
				$this->debugContent["Alias"] = "true";
        $this->debugContent["Alias Contents"] = print_r($alias, 1);
      }
      else{
				$this->debugContent["Alias"] = "Not Alias"; 
      }
      
			$this->debugContent["Template Name"] = $this->getTemplateName();		
		}
		
		if($this->debugLevel > 1){
			$totalTime = substr($this->endTime - $this->startTime, 0, 5);
			$this->debugContent["Constructor Overhead"] =  $totalTime;
		}
      
		if($this->debugLevel > 2){
			$status = null;
			if($this->isPrivilegedUser()){
				$status = "true";
			}
			else{
				$status = "false";
			}
				
			$this->debugContent["Privileged User"] = $status;
        
			if(isset( $_SESSION['role'])){
				$this->debugContent["Logged In"]  = "true";
				$this->debugContent["User Role"]  = $_SESSION['role'];
			}
			else{
				$this->debugContent["Logged In"]  = "false";
			}
		}
      
		if($this->debugLevel > 3){
			$this->debugContent["POST Variables"] = print_r($_POST,1);  
			$this->debugContent["GET Variables"] = print_r($this->getGetVariables(),1);
		}
      
		if($this->debugLevel > 4){
			if(isset($this->modules[$this->getSectionName()])){
				$this->debugContent["Module Settings"] = print_r($this->modules[$this->getSectionName()],1 );
			}
			if(isset($this->aliases[$this->getSectionName()])){
				$this->debugContent["Alias Settings"] = print_r($this->aliases[$this->getSectionName()], 1);
			}
		}
  }
  

  /**
   * @see Controller::setDebugLevel
   * @access private
   */
  function _execTime(){
   $microTime = microtime(); 
   $microTime = explode(" ",$microTime); 
   $microTime = $microTime[1] + $microTime[0]; 
   return $microTime; 
  }

  /**
   * @access private
   */
  function _stripSlashesFromArray($value){
    return is_array($value) ? array_map(array('Controller','_stripSlashesFromArray'), $value) : stripslashes($value);  
  }

  /**
   * @access private
   */
  function _redirect($url) {
    header('Location: '.$url);
    die ('Error redirecting. Click URL to continue: <a href="'.$url.'">'.$url.'</a>');
  }
  
}

?>
