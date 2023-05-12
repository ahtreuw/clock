# Clock

This repository contains the [PHP FIG PSR-20] Clock implementation.

## Install

Via Composer Package is available on [Packagist], you can install it using [Composer].

``` bash
$ composer require vulpes/clock
```
## Details
`Clock\Clock` and `Clock\FrozenClock` are instance of `Clock\ClockInterface` and `Psr\Clock\ClockInterface`.


### Clock
The clock works from a dateTime string (default: `now`), so it can take any value that `strtotime()` can handle, 
and that `DateTimeImmutable` in its constructor can handle when calling `Clock::now()` function.

### FrozenClock
The FrozenClock works from a `DateTimeImmutable` object, so its value never changes, 
it will always return the same value within a timezone when calling the `FrozenClock::now()` function.

### Usage

```php
use Clock\ClockInterface; 
use Clock\ClockExceptionInterface; 
use Clock\Clock;
use Clock\FrozenClock;

// with default parameters
$clock = new Clock(
    /* string */              dateTime: ClockInterface::NOW,
    /* DateTimeZone|string */ timeZone: ClockInterface::UTC
);

// with default parameters
$clock = new FrozenClock(
    /* DateTimeInterface|string|int|null */ dateTime: 
    new DateTimeImmutable(ClockInterface::NOW, new DateTimeZone(ClockInterface::UTC))
);

// Clock and FrozenClock are identical in behavior below in this section

$clock->now() // DateTimeImmutable
$clock->now()->format('P'); // +01:00
$clock->now()->getTimezone()->getName(); // UTC

// Clock used UTC default, so here $clock will be the same as $utcClock
$utcClock = $clock->withUTC();
$utcClock->now()->getTimezone()->getName() // UTC

$systemTimezoneClock = $clock->withSystemTimezone();
$systemTimezoneClock->now()->getTimezone()->getName() // (system-timezone)

$withCustomTZ = $clock->withDateTimeZone('Europe/Vatican');
$withCustomTZ = $clock->withDateTimeZone(new DateTimeZone('Europe/Vatican'));
$withCustomTZ->now()->getTimezone()->getName() // Europe/Vatican

$with = $clock->with(new DateTime("1989-01-13"));          // $with->now()->format("Y-m-d") > "1989-01-13"
$with = $clock->with(new DateTimeImmutable("2011-01-13")); // $with->now()->format("Y-m-d") > "2011-01-13"
$with = $clock->with('2022-02-02'); // $with->now()->format("Y-m-d") >  "2022-02-02"
$with = $clock->with(1643756400);   // $with->now()->format("Y-m-d") > ~"2022-02-02"
$with = $clock->with(1111111111);   // $with->now()->getTimestamp() > 1111111111

$withCustomTZ = $clock->withDateTimeZone(new DateTimeZone('Europe/Vatican'));
$withCustomTZ->now()->getTimezone()->getName() // Europe/Vatican

try {
    $clock = new Clock(dateTime: 'unknown-or-bad-timezone');
    $clock->now();
    // ...
} catch (ClockExceptionInterface) {
    // Failed to parse time string...
}

try {
    $clock = new Clock(timeZone: 'bad-timezone');
} catch (ClockExceptionInterface) {
    // Unknown or bad timezone (unknown datetime zone: "bad-timezone")
}
```

### Differences between Clock and FrozenClock in usage

```php
use Clock\Clock;
use Clock\FrozenClock;

$systemClock = new Clock('now');
$frozenClock = new FrozenClock('now');

$systemClock->now()->format('i:s') // 10:02
$frozenClock->now()->format('i:s') // 10:02

// five minutes and 30 seconds later
$systemClock->now()->format('i:s') // 15:32
$frozenClock->now()->format('i:s') // 10:02

// BUT if you set Clock::dateTime with a timestamp, it will retain
// its value as a string from then on and behave like FrozenClock.
$frozenSystemClock = $systemClock->with($frozenClock)
$frozenSystemClockc // 10:02

// The clock works from a dateTime string (default: `now`), so it can take any value that `strtotime()` can handle, 
// and that `DateTimeImmutable` in its constructor can handle when calling `Clock::now()` function.

$alwaysTomorrow = $systemClock->with('+1 day'); // on 2021-01-01
$alwaysTomorrow->now()->format('Y-m-d') // 2021-01-02

// two days later (on 2021-01-03) - sleep(60 * 60 * 24 * 2)
$alwaysTomorrow->now()->format('Y-m-d') // 2021-01-04
```
[PHP FIG PSR-20]: https://www.php-fig.org/psr/psr-20/
[Packagist]: http://packagist.org/packages/vulpes/clock
[Composer]: http://getcomposer.org
