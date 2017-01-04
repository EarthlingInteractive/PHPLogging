<?php

class EarthIT_Logging_NullLogger {
	public static function getInstance() { return new self; }
	private function __construct() { }
	
	public function __invoke( $thing ) { }
}
