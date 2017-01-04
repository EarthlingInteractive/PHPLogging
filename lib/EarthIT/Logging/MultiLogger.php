<?php

class EarthIT_Logging_MultiLogger
{
	protected $loggers;
	
	public function __construct( array $loggers ) {
		$this->loggers = $loggers;
	}
	
	public function __invoke( $thing  ) {
		foreach( $this->loggers as $logger ) {
			call_user_func($logger, $thing);
		}
	}
}
