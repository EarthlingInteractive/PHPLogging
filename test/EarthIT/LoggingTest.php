<?php

class EarthIT_LoggingTest extends PHPUnit_Framework_TestCase
{
	public function testNullLogger() {
		$logger = EarthIT_Logging::loggerFromConfig(array('typeName'=>'Null'));
		$this->assertInstanceOf(EarthIT_Logging_NullLogger::class, $logger);
		call_user_func($logger, "Hello!");
		// Did it not crash?  Then we're probably good.
	}
	
	public function testMemoryLogger() {
		$logger = EarthIT_Logging::loggerFromConfig(array('typeName'=>'Memory', 'key'=>'test123'));
		$this->assertInstanceOf(EarthIT_Logging_MemoryLogger::class, $logger);
		
		$logger->clear();
		call_user_func($logger, 'pew pew');
		call_user_func($logger, 'pew pew pew');
		$this->assertEquals( 2, count($logger->getEvents()) );

		$logger->clear();
		$this->assertEquals( 0, count($logger->getEvents()) );
	}
	
	public function testTextLogger() {
		$thneed = new EarthIT_Logging_Thneed();
		$logger = new EarthIT_Logging_TextLogger( $thneed );
		$logger('log message one!');
		$logger('log message two!');
		$logger("log message\nwith a newline!");
		$logger(new EarthIT_Logging_AnnotatedEvent("debug message!", array(
			EarthIT_Logging_AnnotatedEvent::MD_LEVEL => EarthIT_Logging::LEVEL_DEBUG
		)));
		$logger(new EarthIT_Logging_AnnotatedEvent("debug message\nwith newline!", array(
			EarthIT_Logging_AnnotatedEvent::MD_LEVEL => EarthIT_Logging::LEVEL_DEBUG
		)));
		$this->assertEquals(
			"-- log message one!\n".
			"-- log message two!\n".
			"-- log message\n".
			"with a newline!\n".
			"-- debug: debug message!\n".
			"-- debug:\ndebug message\nwith newline!\n",
			(string)$thneed
		);
	}
	
	public function testTextLoggerWithGroup() {
		$thneed = new EarthIT_Logging_Thneed();
		$logger = new EarthIT_Logging_TextLogger( $thneed );
		$logger("One");
		$logger(new EarthIT_Logging_AnnotatedEvent("Open group0", array(
			EarthIT_Logging_AnnotatedEvent::MD_OPENS_GROUP_ID => 'group0',
			EarthIT_Logging_AnnotatedEvent::MD_TIME => 1,
		)));
		$logger(new EarthIT_Logging_AnnotatedEvent("Open group1\nAnd also hello!", array(
			EarthIT_Logging_AnnotatedEvent::MD_OPENS_GROUP_ID => 'group1',
			EarthIT_Logging_AnnotatedEvent::MD_TIME => 2,
		)));
		$logger(new EarthIT_Logging_AnnotatedEvent("Close group1", array(
			EarthIT_Logging_AnnotatedEvent::MD_CLOSES_GROUP_ID => 'group1',
			EarthIT_Logging_AnnotatedEvent::MD_TIME => 3,
		)));
		$logger(new EarthIT_Logging_AnnotatedEvent("Close group0", array(
			EarthIT_Logging_AnnotatedEvent::MD_CLOSES_GROUP_ID => 'group0',
			EarthIT_Logging_AnnotatedEvent::MD_TIME => 4,
		)));
		
		$this->assertEquals(
			"-- One\n".
			"-- Open group0\n".
			"  -- Open group1\n".
			"  And also hello!\n".
			"  -- Close group1 (group took 1.000000 seconds)\n".
			"-- Close group0 (group took 3.000000 seconds)\n",
			(string)$thneed
		);
	}
	
	public function testMultiLogger() {
		$tempFile = tempnam(sys_get_temp_dir(), 'test.log');
		
		$logger = EarthIT_Logging::loggerFromConfig(array(
			'typeName' => 'Multi',
			'subLoggers' => array(
				'mem' => array(
					'typeName' => 'Memory',
					'key' => 'test234',
				),
				'file' => array(
					'typeName' => 'File',
					'file' => $tempFile,
				)	
			)
		));
		
		$memLogger = EarthIT_Logging_MemoryLogger::getInstance('test234');
		$memLogger->clear();
		
		$this->assertInstanceOf(EarthIT_Logging_MultiLogger::class, $logger);
		$logger("log message one.");
		$logger("log message two.");
		$this->assertEquals( array("log message one.","log message two."), $memLogger->getEvents() );
		$this->assertTrue( file_exists($tempFile) );
		$this->assertEquals(
			"-- log message one.\n".
			"-- log message two.\n",
			file_get_contents($tempFile)
		);
	}
}
