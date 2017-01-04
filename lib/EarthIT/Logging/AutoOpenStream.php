<?php

class EarthIT_Logging_AutoOpenStream
{
	protected $stream;
	protected $openFunction;
	protected $streamName;
	
	public function __construct( $streamName, $openFunction=null, $autoFlush=true ) {
		$this->streamName = $streamName;
		$this->openFunction = $openFunction;
		$this->autoFlush = $autoFlush;
	}
	
	public function __invoke( $data ) {
		if( $this->stream === null ) {
			if( $this->openFunction !== null ) {
				$this->stream = call_user_func($this->openFunction);
			} else if( $this->streamName !== null ) {
				$this->stream = @fopen($this->streamName, 'ab');
				// Maybe do some exception throwing in here
			} else {
				throw new Exception("No stream name or opener function provided to AutoOpenStream!");
			}
		}
		if( $this->stream === null || $this->stream === false ) {
			throw new Exception("Failed to open stream: {$this->streamName} for writing");
		}
		fwrite( $this->stream, $data );
		if( $this->autoFlush ) fflush( $this->stream );
	}
	
	public function __destruct() {
		if( $this->stream !== null and $this->stream !== false ) fclose($this->stream);		
	}
	
	public function flush() {
		if( $this->stream ) fflush( $this->stream );
	}
}
