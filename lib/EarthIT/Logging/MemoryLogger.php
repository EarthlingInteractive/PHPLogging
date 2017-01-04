<?php

class EarthIT_Logging_MemoryLogger
{
	protected static $defaultInstances = array();
	
	public static function getInstance($k='default') {
		if( !isset(self::$defaultInstances[$k]) ) self::$defaultInstances[$k] = new self;
		return self::$defaultInstances[$k];
	}

	protected $events = array();
	
	public function __invoke( $event ) {
		$this->events[] = $event;
	}
	
	public function getEvents() { return $this->events; }
	public function clear() { $this->events = array(); }
}
