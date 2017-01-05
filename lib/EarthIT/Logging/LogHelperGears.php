<?php

/**
 * Import into a class where $this->logger is accessible,
 * and from within that class you can use these handy functions.
 */
trait EarthIT_Logging_LogHelperGears
{
	// Leave these unset by default
	// so they pollute whatever class inherits them less.
	protected $_logGroupMd;
	protected $_logGroupIdStack;
	
	// {open|close}LogGroup affect the next call to a logging function.
	
	protected function openLogGroup() {
		$groupId = uniqid();
		if( !isset($this->_logGroupIdStack) ) $this->_logGroupIdStack = array();
		$this->_logGroupIdStack[] = $groupId;
		if( !isset($this->_logGroupMd) ) $this->_logGroupMd = array();
		if( isset($this->_logGroupMd[EarthIT_Logging_AnnotatedEvent::MD_OPENS_GROUP_ID]) ) {
			// TODO: Could just generate a blank event and carry on.
			throw new Exception("Can't open more than one group per event!");
		}
		$this->_logGroupMd[EarthIT_Logging_AnnotatedEvent::MD_OPENS_GROUP_ID] = $groupId;
		return $this;
	}
	
	protected function closeLogGroup() {
		if( !isset($this->_logGroupIdStack) or count($this->_logGroupIdStack) == 0 ) {
			throw new Exception("LogHelperGears: Group ID stack underflow!");
		}
		if( !isset($this->_logGroupMd) ) $this->_logGroupMd = array();
		if( isset($this->_logGroupMd[EarthIT_Logging_AnnotatedEvent::MD_CLOSES_GROUP_ID]) ) {
			// TODO: Could just generate a blank event and carry on.
			throw new Exception("Can't close more than one group per event!");
		}
		$closedGroupId = array_pop($this->_logGroupIdStack);
		$this->_logGroupMd[EarthIT_Logging_AnnotatedEvent::MD_CLOSES_GROUP_ID] = $closedGroupId;
		return $this;
	}

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

		$groupMd = isset($this->_logGroupMd) ? $this->_logGroupMd : array();
		$thing = new EarthIT_Logging_AnnotatedEvent( $thing, array(
			EarthIT_Logging_AnnotatedEvent::MD_COMPONENT_CLASS_NAME => get_class($this),
			EarthIT_Logging_AnnotatedEvent::MD_LEVEL => $level,
			EarthIT_Logging_AnnotatedEvent::MD_TIME => microtime(true),
		) + $groupMd );
		
		call_user_func($this->logger, $thing, $level);
		
		unset($this->_logGroupMd);
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
