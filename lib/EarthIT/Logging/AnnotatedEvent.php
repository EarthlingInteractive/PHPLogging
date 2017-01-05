<?php

class EarthIT_Logging_AnnotatedEvent
{
	const MD_LEVEL = 'level';
	const MD_COMPONENT_CLASS_NAME = 'componentClassName';
	const MD_TIME = 'time'; // microtime(true) of the event
	const MD_BEGIN_TIME = 'beginTime'; // microtime(true) at which the event began
	const MD_END_TIME = 'endTime'; // microtime(true) at which the event ended
	const MD_OPENS_GROUP_ID = 'beginsGroupId'; // If this event opens a group, this is the group's ID
	const MD_CLOSES_GROUP_ID = 'endsGroupId'; // ID of the group that this event closes.
	
	protected $event;
	protected $metadata;
	public function __construct( $event, array $metadata ) {
		$this->event = $event;
		$this->metadata = $metadata;
	}
	
	public function getEvent() { return $this->event; }
	public function getMetadata() { return $this->metadata; }
	public function __toString() { return (string)$this->event; }
	
	protected function getMd() {
		foreach( func_get_args() as $key ) {
			if( isset($this->metadata[$key]) ) return $this->metadata[$key];
		}
		return null;
	}
	
	public function __get($k) {
		switch( $k ) {
		case 'beginTime': return $this->getMd(self::MD_BEGIN_TIME, self::MD_TIME);
		case 'endTime': return $this->getMd(self::MD_END_TIME, self::MD_TIME);
		case 'opensGroupId' : return $this->getMd(self::MD_OPENS_GROUP_ID);
		case 'closesGroupId' : return $this->getMd(self::MD_CLOSES_GROUP_ID);
		case 'time': return $this->getMd(self::MD_TIME, self::MD_BEGIN_TIME);
		case 'event': return $this->getEvent();
		case 'level': return $this->getMd(self::MD_LEVEL);
		case 'componentClassName': return $this->getMd(self::MD_COMPONENT_CLASS_NAME);
		default:
			return $this->getMd($k);
		}
	}
}
