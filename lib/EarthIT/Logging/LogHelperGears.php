<?php

/**
 * Import into a class where $this->logger is accessible,
 * and from within that class you can use these handy functions.
 */
trait EarthIT_Logging_LogHelperGears
{
	protected function _log( $level, array $stuff ) {
		$thing = "";
		if( count($stuff) > 1 ) {
			$thing = implode("; ", array_map(function($t) {
				if( is_scalar($t) ) {
					return (string)$t;
				} else if( method_exists($t, '__toString') ) {
					return $t->__toString();
				} else if( is_array($t) ) {
					return EarthIT_JSON::prettyEncode($t);
				} else if( $t === null ) {
					return "null";
				} else {
					return "(".gettype($t).")";
				}
			}, $stuff));
		} else foreach( $stuff as $thing );

		$thing = new EarthIT_Logging_AnnotatedEvent( $thing, [
			EarthIT_Logging_AnnotatedEvent::MD_COMPONENT_CLASS_NAME => get_class($this),
			EarthIT_Logging_AnnotatedEvent::MD_LEVEL => $level,
			EarthIT_Logging_AnnotatedEvent::MD_TIME => microtime(true),
		] );
		
		call_user_func($this->logger, $thing, $level);
	}
	
	// These are defined with a single $event argument
	// for interface compatibility with other logging functions that may happen to be defined.
	// They will log all given arguments, though.
	
	protected function debug( $event ) {
		$this->_log( EarthIT_Logging::LEVEL_DEBUG, func_get_args() );
	}
	
	protected function log( $event ) {
		$this->_log( EarthIT_Logging::LEVEL_INFO, func_get_args() );
	}
	
	protected function warn( $event ) {
		$this->_log( EarthIT_Logging::LEVEL_WARN, func_get_args() );
	}
}
