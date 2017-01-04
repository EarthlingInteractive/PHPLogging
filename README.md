## EarthIT_Logging

This is an exceedingly simple framework for logging arbitrary events.

A logger is simply a function that is called with a single argument,
which is a representation of the event to be logged.
The only constraint is that said log event should be either a string or an object that supports __toString.

One event class is predefined, EarthIT_Logging_AnnotatedEvent.
This wraps another log event and associated metadata such as log level or begin and end times,
which your logging function may look at.

A `LogHelperGears` trait is defind that makes it easier to
create and log EarthIT_Logging_AnnotatedEvents by calling
```$this->debug(...)```, ```$this->log(...)```, or ```$this->warn(...)```.

A few basic logging classes are provided, also.
