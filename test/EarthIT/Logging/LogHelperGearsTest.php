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

	public function testWithGroup() {
		$this->openLogGroup()->log("Hello");
		$this->log("Group item 1");
		$this->log("Group item 2");
		$this->closeLogGroup()->log("Goodbye");
		
		$events = $this->logger->getEvents();
		$this->assertEquals(4, count($events));
		$this->assertInstanceOf(EarthIT_Logging_AnnotatedEvent::class, $events[0]);
		$this->assertInstanceOf(EarthIT_Logging_AnnotatedEvent::class, $events[3]);
		$this->assertNotNull($events[0]->opensGroupId);
		$this->assertNull(   $events[0]->closeGroupId);
		$this->assertNull(   $events[1]->opensGroupId);
		$this->assertNull(   $events[2]->closesGroupId);
		$this->assertNull(   $events[3]->opensGroupId);
		$this->assertNotNull($events[3]->closesGroupId);
	}
}
