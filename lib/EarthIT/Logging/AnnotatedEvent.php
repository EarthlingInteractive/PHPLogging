<?php

class EarthIT_Logging_AnnotatedEvent
{
	const MD_LEVEL = 'level';
	const MD_COMPONENT_CLASS_NAME = 'componentClassName';
	const MD_BEGIN_TIME = 'beginTime';
	const MD_END_TIME = 'endTime';
	
	protected $event;
	protected $metadata;
	public function __construct( $event, array $metadata ) {
		$this->event = $event;
		$this->metadata = $metadata;
	}
	
	public function getEvent() { return $this->event; }
	public function getMetadata() { return $this->metadata; }
	public function __toString() { return (string)$this->event; }
	
	protected function getMd($key) {
		return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
	}
	
	public function __get($k) {
		switch( $k ) {
		case 'beginTime': return $this->getMd(self::MD_BEGIN_TIME);
		case 'endTime': return $this->getMd(self::MD_END_TIME);
		case 'event': return $this->getEvent();
		case 'level': return $this->getMd(self::MD_LEVEL);
		case 'componentClassName': return $this->getMd(self::MD_COMPONENT_CLASS_NAME);
		default:
			return $this->getMd($k);
		}
	}
}
