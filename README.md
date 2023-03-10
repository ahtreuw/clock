# Clock
This repository contains the [PHP FIG PSR-20] Clock implementation.

## Install
Via Composer
Package is available on [Packagist], you can install it using [Composer].
``` bash
$ composer require vulpes/clock
```

## Usage
```php
use Vulpes\Clock\ClockExceptionInterface;
use Vulpes\Clock\FrozenClock;
use Vulpes\Clock\SystemClock;

$timeZone = new DateTimeZone('Europe/Budapest');

// $clock = SystemClock::fromSystemTimezone();
// $clock = SystemClock::fromUTC();
// $clock = SystemClock::create('Europe/Budapest');
// $clock = new SystemClock; // default timezone: UTC
// $clock = new SystemClock('UTC');
$clock = new SystemClock($timeZone);

print $clock->now()->format('P'); // +01:00
print $clock->now()->getTimezone()->getName(); // Europe/Budapest

try {
    new SystemClock('unknown-or-bad-timezone');
} catch (ClockExceptionInterface $e) {
    print $e->getMessage(); // Unknown or bad timezone (unknown-or-bad-timezone)
}

// FrozenClock::create(new DateTimeImmutable);
// FrozenClock::fromUTC();
// FrozenClock::fromSystemTimezone();
$frozenClock = new FrozenClock(new DateTimeImmutable('now', new DateTimeZone('Europe/Budapest')));
print $frozenClock->now()->getTimezone()->getName(); // Europe/Budapest

$frozenClock->setTo(new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin')));
print $frozenClock->now()->getTimezone()->getName(); // Europe/Berlin
```
[PHP FIG PSR-20]: https://www.php-fig.org/psr/psr-20/
[Packagist]: http://packagist.org/packages/vulpes/clock
[Composer]: http://getcomposer.org
