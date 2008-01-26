<?php

SmartestHelper::register('Download');

/**
 * undocumented class
 *
 * @package Smartest
 * @author Marcus Gilroy-Ware
 **/
class SmartestDownloadHelper extends SmartestHelper{
    
    const SM_DOWNLOAD_LOCAL_FILE = 1;
    const SM_DOWNLOAD_STRING = 2;
    
    protected $_local_file;
    protected $_mime_type = 'application/force-download';
    protected $_ready_to_send = false;
    protected $_download_filename;
    protected $_type = 1;
    protected $_string = null;
    
    public function __construct($local_file_path){
        
        // echo $local_file_path;
        
        if(file_exists(utf8_decode($local_file_path))){
            // the user is downloading a file
            $this->_local_file = $local_file_path;
            $this->_ready_to_send = true;
            $this->_download_filename = basename($this->_local_file);
            $this->_type = self::SM_DOWNLOAD_LOCAL_FILE;
        }else{
            // the user is just sending a string - they will need to provide a file name
            $this->_ready_to_send = false;
            $this->_type = self::SM_DOWNLOAD_STRING;
            $this->_string = $local_file_path;
        }
    }
    
    public function getLocalFile(){
        return $this->_local_file;
    }
    
    public function setMimeType($mime_type){
        if(preg_match('/[\w]+\/[\w-]+/', $mime_type)){
            $this->_mime_type = $mime_type;
        }
    }
    
    public function getMimeType(){
        return $this->_mime_type;
    }
    
    public function setDownloadFilename($filename){
        // we used basename so that anybody who mistakenly hands it a full path doesn't break it :)
        $this->_download_filename = basename($filename);
    }
    
    public function getDownloadFilename(){
        return $this->_download_filename;
    }
    
    public function getType(){
        return $this->_type;
    }
    
    public function getDowloadableContent(){
        if($this->_type == self::SM_DOWNLOAD_LOCAL_FILE){
            return SmartestFileSystemHelper::load($this->_local_file);
        }else if($this->_type == self::SM_DOWNLOAD_STRING){
            return $this->_string;
        }
    }
    
    public function getDownloadSize(){
        if($this->_type == self::SM_DOWNLOAD_LOCAL_FILE){
            // echo utf8_decode($this->_local_file);
            return (string)(filesize(utf8_decode($this->_local_file)));
        }else if($this->_type == self::SM_DOWNLOAD_STRING){
            return mb_strlen($this->_string);
        }
    }
    
    public function send(){
        header("Cache-Control: public, must-revalidate\r\n");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\r\n");
        header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ). ' GMT'."\r\n");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header("Content-Type: ".$this->_mime_type."; charset=utf-8\r\n");
        // header('Content-Type: text/html; charset=utf-8');
        header("Content-Length: ".$this->getDownloadSize()."\r\n");
        header('Content-Disposition: attachment; filename='.$this->_download_filename." \r\n");
        // readfile($this->_local_file);
        echo $this->getDowloadableContent();
        exit;
    }

} // END class 