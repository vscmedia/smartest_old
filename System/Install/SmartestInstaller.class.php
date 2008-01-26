<?php
/**
 * Implements the installer file
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Eddie Tejeda <eddie@visudo.com>
 */


class Installer{

  function detectHtaccessFile(){
  		$htcontent = null;
		if(file_exists(".htaccess")){
			$htcontent = file_get_contents(".htaccess");
		}
		if( !file_exists(".htaccess") || !stristr($htcontent, "RewriteEngine on") ){
			
			$error_msg = "<h2>Mod-Rewrite is required. Please enable Mod-Rewrite in the file called <i>.htaccess</i><br /></h2>".
			"Example:<br>".
			"<pre>".
			"RewriteEngine on<br>".
			"RewriteRule !\.(png|gif|jpg)$ ".getcwd()."/index.php".
			"</pre>";
			
			die($error_msg);
		}
  }
}
?>
