<?php

/**
 * Formats log events as text and outputs to a writer
 */
class EarthIT_Logging_TextLogger
{
	protected $writer;
	protected $groupStack;
	public $groupIndent = "  ";
	
	/**
	 * @param callable $writer a function to call for each piece of text to be output
	 */
	public function __construct( $writer ) {
		$this->writer = $writer;
	}
	
	protected function getCurrentIndent() {
		return str_repeat($this->groupIndent, count($this->groupStack));
	}
	
	public function __invoke( $thing ) {
		$mdStrings = [];
		if( $thing instanceof EarthIT_Logging_AnnotatedEvent ) {
			$annotated = $thing;
			$thing = $annotated->event;
			
			if( ($level = $annotated->level) !== null ) $mdStrings[] = EarthIT_Logging::logLevelName($level).':';
			if( $annotated->componentClassName ) $mdStrings[] = $annotated->componentClassName.':';
		} else $annotated = null;
		
		$mdStr = implode(' ',$mdStrings);
		$bodyStr = trim((string)$thing);
		$boats = array_filter(array($mdStr, $bodyStr));
		
		if( strpos($bodyStr, "\n") === false and strlen($mdStr)+strlen($bodyStr)+4 < 80 ) {
			$boatSep = " ";
		} else {
			$boatSep = "\n";
		}
		
		if( $annotated and $annotated->closesGroupId ) {
			if( count($this->groupStack) == 0 ) throw new Exception("Unbalanced log event group!");
			$opener = array_pop($this->groupStack);
			$interval = $annotated->endTime - $opener->beginTime;
			$boats[] = "(group took ".sprintf("%0.6f",$interval)." seconds)";
		}
		
		$indent = $this->getCurrentIndent();
		call_user_func( $this->writer, $indent."-- ".str_replace("\n","\n{$indent}",implode($boatSep, $boats))."\n" );
		
		if( $annotated and $annotated->opensGroupId ) {
			$this->groupStack[] = $annotated;
		}
	}
}
