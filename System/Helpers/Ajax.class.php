<?php
/**
 * Implements the Ajax server for public classes
 *
 * PHP versions 4/5
 *
 * @category   System
 * @package    Smartest
 * @license    read license file
 * @author     Eddie Tejeda <eddie@visudo.com>
 */
session_start();
    
require_once "HTML/AJAX/Server.php";
    
class AutoServer extends HTML_AJAX_Server {
  // this flag must be set for your init methods to be used
  var $initMethods = true;
    
  // init method for my hello world class
  function initPages() {
    require_once "Pages/Pages.class.php";
    $pages = new Pages();
    $this->registerClass($pages);
  }

  // init method for my hello world class
  function initContent() {
    require_once "Pages/Content.class.php";
    $content = new Content();
    $this->registerClass($pages);
  }

  // init method for my hello world class
  function initStatistics() {
    require_once "Pages/Statistics.class.php";
    $content = new Content();
    $this->registerClass($pages);
  }

  // init method for my hello world class
  function initGallery() {
    require_once "Pages/Gallery.class.php";
    $content = new Content();
    $this->registerClass($pages);
  }

}
    
$server = new AutoServer();
$server->handleRequest();

?>
