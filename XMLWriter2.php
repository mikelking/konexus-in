<?php

// simple wrapper class which lets us do chaining
// makes example code much cleaner
// feel free to use in production code
class XMLWriter2 
{
	// needed for our simple decorator pattern here
	// as class extension won't work for the purpose
	private $xmlwriter = null;
	
	public function __construct() {
		$this->xmlwriter = new XMLWriter();
	}
	
	// this one needs to be special cased as we don't want to return $this here
	public function outputMemory($flush = TRUE) {
		return $this->xmlwriter->outputMemory($flush);
	}
	
	// Why isn't there a method like this already? :(
	public function addElement($name, $body) {
		$this->xmlwriter->startElement($name);
		$this->xmlwriter->text($body);
		$this->xmlwriter->endElement();
		return $this;
	}
	
	// This handles the "magic" of calling the decorated class's methods
	// But returning $this rather than the pointless bools that it does normally
	public function __call($method, $arguments) {
		if(method_exists($this->xmlwriter, $method)) {
			call_user_func_array(array($this->xmlwriter, $method), $arguments);
			return $this;
		} else {
			throw new Exception("Method undefined!");
		}
	}
}


?>