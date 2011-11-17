<?php
  
// include any needed files - optional of course.
// require_once "%NEEDED_FILE%.php";

// Extend SmartestApplication
// not strictly required if you like to do everything by hand, but no good reason not to.
// Gives access to many aspects of Smartest API that would otherwise be unavailable
// Gives access to Controller values and templating object
class Sample_App extends SmartestApplication{
  
	// declare any vars/constants/whatever, as normal
	protected $_foo;
	const FOO = 'BAR';

	// SmartestApplication already has a constructor, so if you want your class to have a constructor,
	// put it here called __moduleConstruct() and SmartestApplication will call it.
	public function __smartestApplicationInit(){
		
	}
	
	// no other requirements at all.
	// define your methods as normal and have fun...
	
	// By convention, the default action for a module is called startPage,
	// but you can make this whatever you like in %module_dir%/Configuration/quince.yml
	public function startPage(){
	    
	}
	
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