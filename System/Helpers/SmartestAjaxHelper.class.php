<?php

SmartestHelper::register('Ajax');

class SmartestAjaxHelper extends SmartestHelper{
	
	static function createAutoServerClass(){
		
		$ajaxModules[0]['class'] = "Sets";
		$ajaxModules[1]['class'] = "Settings";

		$autoGenerateClass = "
		class AutoServer extends HTML_AJAX_Server {
		// this flag must be set for your init methods to be used
			var \$initMethods = true;
			";
			
		foreach($ajaxModules as $module){
			$autoGenerateClass .= "
			// init method for my class
				function init".$module['class']."() {
					require_once \"Modules/".$module['class'].".class.php\";
					\$module =& new ".$module['class']."();
					\$this->registerClass(\$module);
				}";
		}
	
		$autoGenerateClass .= "}	
		\$server =& new AutoServer();	
		\$server->handleRequest();
		";
		
	}

}