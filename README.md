
## Installation

```shell
composer require flinters-php/working-hours
```

## Usage

### Time
```php
use Flinters\WorkingHours\Time;

// Init from string
$time = Time::fromString('10:30');

// Init from Int (630 = 10 * 60 + 30) -> 10:30
$time = Time::fromInt(630);

// Init from hour & time
$time = new Time(10, 30);

```

### Range

```php

use Flinters\WorkingHours\Range;
use Flinters\WorkingHours\Time;

// Init from String
$range = Range::fromString('08:30-17:30');

// Init by constructor
$range = new Range(
    Time::fromString('08:30'),
    Time::fromString('17:30') 
);

// This statement will create a range from -> 08:30-10:30
$range = Range::duration(Time::fromString('08:30'), $duration = 120);

$range->contains(Time::fromString('10:30')); // true
$range->contains(Time::fromString('05:30')); // false

$range->containsRange(Range::fromString('08:30-10:30')); // true
$range->containsRange(Range::fromString('18:30-20:30')); // false

// Range: 08:30-17:30
// [08:30-11:30, 13:30-17:30]
$range->break(Range::fromString('11:30-13:30'));

$range->toString(); // 08:30-17:30
```

### Working Hours

```php

use Flinters\WorkingHours\Time;
use Flinters\WorkingHours\Range;
use Flinters\WorkingHours\WorkingHours;

$dayOff = WorkingHours::dayOff();
$dayOff->isFree(Range::fromString('10:30-12:00')); // false

// Set busy time 
$workingDay = WorkingHours::working(Range::fromString('08:30-17:30'));
$workingDay->busy(Range::fromString('10:30-12:30'));
$workingDay->busyDuration(Time::fromString('10:30'), 30);

$workingDay->isFree(Range::fromString('08:30-10:30')); // true
$workingDay->isFree(Range::fromString('11:00-12:00')); // false
$workingDay->isFree(Range::fromString('18:00-20:00')); // false

```
