<?php
/**
 * Implements the controller manager
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Eddie Tejeda <eddie@visudo.com>
 */

require_once 'XML/Serializer.php'; 
require_once 'XML/Unserializer.php'; //TODO: Maybe use SimpleXML instead?


class ControllerManager{

  var $controllerXml = null;  
  var $filename;
  
  function ControllerManager($filename = "controller.xml"){
    $this->filename = $filename;
    $option = array('complexType' => 'array', 'parseAttributes' => TRUE);
    $unserialized = new XML_Unserializer($option);
    $result = $unserialized->unserialize($filename, true);
    if (PEAR::isError($result)) {
        die($result->getMessage());
    }
    $this->controllerXml = $unserialized->getUnserializedData();
  }

  function getControllerManager(){
    return $this->controllerXml;
  }
  
  function setBaseDomain($domain){
    $this->controllerXml['domain'] = $domain;
    $this->writeXml();
  }
  
  function setModulePath($path){
    $this->controllerXml['pages'] = $path;
    $this->writeXml();    
  }
  

  function addModule($name, $class, $defaultMethod, $template = "", $isDefaultModule = false, $isEnabled = true){
    
    $newModule['name'] = $name; 
    $newModule['class'] = $class;
    $newModule['default-method'] = $defaultMethod;
    $newModule['template'] = $template;    
    $newModule['default-page'] = ($isDefaultModule != false) ? true: false;
    $newModule['enabled'] = ($isEnabled != true) ? false: true;
    
    
    $this->controllerXml['page'][] = $newModule; //append page to loaded file
    $this->writeXml();
  }
  
  function removeModule($name){
    
    foreach($this->controllerXml['page'] as $key=>$page){
      if($page['name'] == $name){
        unset($this->controllerXml['page'][$key]);
        break;
      }
    }
    
    $this->writeXml();
  }

  function editModule($name, $class="", $defaultMethod="", $template = "", $isDefaultModule = "", $rename= ""){
    foreach($this->controllerXml['page'] as $key=>$page){
      if($page['name'] == $name){
        $this->controllerXml['page'][$key][$name];
        
        $this->writeXml();  
        return true;
      }
    }
    return false;    
  }
  
  function findModule(){
  
  }
  
  function getModule(){
  
  }
  
  function getAllModules(){
  
  }
  
  function addFormForward($pageName, $formFowardName, $formForwardType, $formForwardValue){
  
  }

  function addAlias($page = "", $alias = "", $isEnabled = true){

    $newAlias['page'] = $page;
    $newAlias['alias'] = $alias; //validation?
    $newAlias['enabled'] = ($isEnabled != true) ? false: true;
    
    $this->controllerXml['page'][] = $newAlias; //append alias to loaded file

    $this->writeXml();
  }
  
  
  function deleteAlias(){
  
  }  
  
  
  function editAlias(){
  
  }  

  function writeXml(){
    // An array of serializer options 
    $serializer_options = array ( 
      'addDecl' => TRUE, 
      'encoding' => 'UTF-8', 
      'indent' => '  ', 
      'rootName' => 'controller', 
      'defaultTagName' => 'page', 
    ); 

    // Instantiate the serializer with the options 
    $serializer = &new XML_Serializer($serializer_options); 

    // Serialize the data structure 
    $status = $serializer->serialize($this->controllerXml); 

    // Check whether serialization worked 
    if (PEAR::isError($status)) { 
      die($status->getMessage()); 
    }
    
    file_put_contents($this->filename, $serializer->getSerializedData()); 

  }
}



?>
