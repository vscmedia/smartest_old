<?php
  
// include any needed files - optional of course.
require_once "%NEEDED_FILE%.php";

// if managers_autoload is turned off in settings but you want to use a manager class, 
// you must include it here as such:
require_once SM_SYSTEM_MANAGERS_DIR."%SAMPLE%Manager.class.php";

// Extend SmartestApplication
// not strictly required if you like to do everything by hand, but no good reason not to.
// Gives access to many aspects of Smartest API that would otherwise be unavailable
// Gives access to Controller values and templating object
class %SAMPLE% extends SmartestApplication{
  
	// declare any vars/constants/whatever, as normal
	var $foo;
	const BAR;

	// SmartestApplication already has a constructor, so if you want your class to have a constructor,
	// put it here called __moduleConstruct() and SmartestApplication will call it.
	function __moduleConstruct(){
		
	}
	
	// no other requirements at all.
	// define your methods as normal and have fun...
	
	// will be accessible in the url via mysite.com/yourapp/foo
	public function foo(){          	
		
	}
	
	// will be accessible in the url via mysite.com/yourapp/bar
	public function bar(){
		
	}
	
	// will not be accessible in the url
	private function foobar(){
	    
	}
	
}