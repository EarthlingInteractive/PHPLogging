<?php

/**
 * Formats log events as text and outputs to a writer
 */
class EarthIT_Logging_TextLogger
{
	protected $writer;
	
	/**
	 * @param callable $writer a function to call for each piece of text to be output
	 */
	public function __construct( $writer ) {
		$this->writer = $writer;
	}
	
	public function __invoke( $thing ) {
		$mdStrings = [];
		if( $thing instanceof EarthIT_Logging_AnnotatedEvent ) {
			if( ($level = $thing->level) !== null ) $mdStrings[] = EarthIT_Logging::logLevelName($level).':';
			if( $thing->componentClassName ) $mdStrings[] = $thing->componentClassName.':';
			$thing = $thing->event;
		}
		
		$mdStr = implode(' ',$mdStrings);
		$bodyStr = trim((string)$thing);
		$boats = array_filter(array($mdStr, $bodyStr));
		
		if( strpos($bodyStr, "\n") === false and strlen($mdStr)+strlen($bodyStr)+4 < 80 ) {
			$boatSep = " ";
		} else {
			$boatSep = "\n";
		}
		
		call_user_func( $this->writer, "-- ".implode($boatSep, $boats)."\n" );
	}
}
