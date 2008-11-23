<?php

class TemplatesManager{

		var $database;

	function TemplatesManager(){
		// $this->database = $_SESSION['database'];
        $this->database = SmartestPersistentObject::get('db:main');
	}
	function getTemplateNames($path){
		$directory = opendir($path);
		while($file = readdir($directory)){
			if($file!='.' && $file!='..'){
		$filename[] = $file;
			}
		}
		return $filename;
	}

	function getUniqueFilename($path,$file){

		$file_name = $path.$filename;
		$filename=explode('.',$file);
		$i=1;
		while(file_exists($file_name)){
		$i=$i+1;
		$file=$filename[0].$i.".".$filename[1];
		$file_name = $path.$file;
		}
	return $file ;
	} 

	function getTemplateInUseInDraftPage($template_name){
		$sql="SELECT * from Pages WHERE page_draft_template=$template_name";
		$templates_in_use_draftpage=$this->database->queryToArray($sql);
		return $templates_in_use_draftpage;		
	}
	function getTemplateInUseInLivePage($template_name){
		$sql="SELECT * from Pages WHERE page_live_template='$template_name'";
		$templates_in_use_livepage=$this->database->queryToArray($sql);
		return $templates_in_use_livepage;
	}


}

?>