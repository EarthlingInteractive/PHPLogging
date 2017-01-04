<?php

class EarthIT_Logging
{
	private function __construct() { }
	
	const LEVEL_DEBUG = 1;
	const LEVEL_INFO  = 2;
	const LEVEL_LOG   = 3;
	const LEVEL_WARN  = 4;
	const LEVEL_ERROR = 5;

	public static function logLevelName($level) {
		switch($level) {
		case self::LEVEL_DEBUG: return 'debug';
		case self::LEVEL_INFO : return 'info';
		case self::LEVEL_LOG  : return 'log';
		case self::LEVEL_WARN : return 'warn';
		case self::LEVEL_ERROR: return 'error';
		default: return "weird log level $level";
		}
	}
	
	public static function loggerFromConfig( array $conf ) {
		$typeName = 'null';
		if( isset($conf['typeName']) ) $typeName = strtolower($conf['typeName']);
		if( isset($conf['enabled']) && $conf['enabled'] === false ) {
			return EarthIT_Logging_NullLogger::getInstance();
		}
		switch( $typeName ) {
		case 'null': return EarthIT_Logging_NullLogger::getInstance();
		case 'stderr':
			$stream = new EarthIT_Logging_AutoOpenStream('php://stderr');
			return new EarthIT_Logging_TextLogger($stream);
		case 'file':
			if( !isset($conf['file']) ) {
				throw new Exception("'file' logger config does not indicate a 'file'");
			}
			$stream = new EarthIT_Logging_AutoOpenStream($conf['file']);
			return new EarthIT_Logging_TextLogger($stream);
		case 'multi':
			$subLoggers = [];
			foreach( $conf['subLoggers'] as $subConf ) {
				$subLoggers[] = self::loggerFromConfig($subConf);
			}
			return new EarthIT_Logging_MultiLogger($subLoggers);
		case 'memory':
			$k = isset($conf['key']) ? $conf['key'] : 'default';
			return EarthIT_Logging_MemoryLogger::getInstance($k);
		default:
			throw new Exception("Unrecognized logger type: ".$typeName);
		}
	}
}
