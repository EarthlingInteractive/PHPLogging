<?php

class EarthIT_Logging_LogHelperGearsTest extends PHPUnit_Framework_TestCase
{
	use EarthIT_Logging_LogHelperGears;
	
	protected $logger;
	
	public function setUp() {
		$this->logger = new EarthIT_Logging_MemoryLogger();
	}
	
	public function testLog() {
		$this->log("Hello, world!");
		usleep(1000); // because we're going to compare times later
		$this->log("Goodbye, world!", "OH LOOK A ZEBRA!!");
		
		$events = $this->logger->getEvents();
		$this->assertEquals(2, count($events));
		$this->assertInstanceOf(EarthIT_Logging_AnnotatedEvent::class, $events[0]);
		$this->assertInstanceOf(EarthIT_Logging_AnnotatedEvent::class, $events[1]);
		
		$this->assertEquals( "Hello, world!", $events[0]->getEvent() );
		$this->assertTrue( $events[1]->time > $events[0]->time );
	}
}
