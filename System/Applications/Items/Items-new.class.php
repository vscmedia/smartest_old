<?php

require_once 'System/Helpers/ItemsHelper.class.php';
require_once 'Managers/SchemasManager.class.php';
require_once 'Managers/AssetsManager.class.php';

require_once 'XML/Serializer.php'; 

class Items extends ModuleBase{

	var $SchemasManager;
	
	function __moduleConstruct(){
		$this->database = $_SESSION['database'];
		$this->manager = new ItemsHelper();
		$this->SchemasManager = new SchemasManager();
		$this->AssetsManager = new AssetsManager();
	}
	
	function startPage($get){	
		
	}
	
	function getItemClasses(){
	
	}
	
	function getItemClassProperties($get){
	
	}
	
	function itemClassSettings($get){
	
	}
	
	function insertItemClassSettings($get,$post){
	
	}
	
	function getItemClassMembers($get="", $post=""){
	
	}
	
	function getItemXml($get, $post){
	
	}
	
	function getItemClassXml($get){
	
	}
	
	function removeProperty($get, $post){
	
	}
	
	function deleteProperty($get){
	
	}
	
	function deleteItem($get){
	
	}
	
	function deleteItemClass($get){
	
	}
	
	function editItemProperty($get, $post){
	
	}
	
	function editItem($get, $post){
	
	}
	
	function editProperties($get){
	
	}
	
	function updateItem($get, $post){
	
	}
	
	function updateItemClassProperty($get, $post){
	
	}
	
	function addItem($get){
	
	}
	
	function insertItem($get, $post){
	
	}
	
	function insertSettings($get, $post){
	
	}
	
	function addItemClass(){
	
	}
	
	function insertItemClass($get, $post){
		
	}
	
	function insertItemClassProperty($get, $post){
		
	}
	
	function addPropertyToClass($get){
		
	}
	
	function addNewItemClassAction($get, $post){
		
	}
	
	function getXmlTest($get){
		
	}
	
	function importData($get){
		
	}
	
	function importDataAction($get,$post){
		
	}
	
	function insertImportData($get,$post){
		
	}
	
	function duplicateItem($get){
		
	}
	
	function exportData($get){
		
	}
	
	function exportDataXml($get,$post){
		
	}
	
	function addSet($get){
		
	}