<?php
/**
 * Implements file upload http://us3.php.net/manual/en/features.file-upload.php#54258
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    "freely distributable"
 * @author     Suri Bala <suri@suribala.com>
 */


class FileUpload{

	var $upload_tmp_dir = "/tmp/";  // leading and trailing slash required
	var $file_upload_flag = "off";
	var $upload_max_filesize = "100";
	var $allowable_upload_base_dirs = array("/tmp/", "/var/visudo/smartest/upload");
	var $allowable_upload_tmp_dirs = array( "/tmp/");
	var $upload_dir= "/tmp/";  // leading and trailing slash required
	var $upload_file_name;

	function FileUpload($name) {
		
		if( is_null($_FILES[$name]) )  {
			echo "Specified file <strong> ".$name." </strong> does not exist in the FILES array. Please check if it exists";
			echo "Exiting...";
			exit;
		}
		
		$this->getConfigurationSettings();
		
		if( $this->file_upload_flag == "off" ) {
			echo "File upload capability in the configuration file is turned <strong> off </strong> . Please update the php.ini file.";
			exit;
		}
		
		$this->upload_file_name = $name;
	}

	function getConfigurationSettings() {
		$this->file_upload_flag = ini_get('file_uploads');
		$this->upload_tmp_dir = ini_get('upload_tmp_dir');
		$this->upload_max_filesize = ini_get('upload_max_filesize');
		$this->upload_max_filesize = preg_replace('/M/', '000000', $this->upload_max_filesize);
	}

	function getErrors() {
		return $_FILES[$this->upload_file_name]['error'];
	}

	function getFileSize() {
		return $_FILES[$this->upload_file_name]['size'];
	}

	function getFileName() {
		return $_FILES[$this->upload_file_name]['name'];
	}

	function getTmpName() {
		return $_FILES[$this->upload_file_name]['tmp_name'];
	}

	function setUploadDir($upload_dir) {
		trim($upload_dir);
		
		if( $upload_dir[strlen($upload_dir)-1] != "/" ) $upload_dir .= "/"; // add trailing slash
		
		$can_upload = false;
		
		foreach( $this->allowable_upload_base_dirs as $dir ) {
			if( $dir == $upload_dir ) {
				$can_upload = true;
				break;
			}
		
		}
		
		if( !$can_upload ) {
			echo "Cannot upload to the dir ->".$upload_dir;
			return;
		}else{
			$this->upload_dir = $upload_dir;
			echo $this->upload_dir;
		}
	}

	function setTmpUploadDir($upload_tmp_dir) {
		
		trim($upload_tmp_dir);
		
		if( $upload_tmp_dir[strlen($upload_tmp_dir)-1] != "/" ) $upload_tmp_dir .= "/"; // add trailing slash
		
		$can_upload = false;
		
		foreach( $this->allowable_upload_base_dirs as $dir ) {
			if( $dir == $upload_tmp_dir ) {
				$can_upload = true;
				return;
			}
		}
		
		if( !$can_upload ) {
			echo "Cannot upload to the dir ->".$uplaod_tmp_dir;
			return;
		}
		
		$this->upload_tmp_dir = $upload_dir;
	}

	function uploadFile() {
		if( $this->checkMaxMemorySizeLimit() ) {
			echo "File size of ".$this->getFileSize()." greater than allowable limit of ".$this->upload_max_filesize."Please change the configuration setting.";
			return;
		}else{
			if( !move_uploaded_file($this->getTmpName(), $this->upload_dir.$this->getFileName()) ) {
				echo "Failed to upload file ".$this->getTmpName();
			}
		}
	}

	function checkMaxMemorySizeLimit() {
		if( $this->getFileSize() >  $this->upload_max_filesize ) {
			return true;
		}else{
			return false;
		}
	}

}