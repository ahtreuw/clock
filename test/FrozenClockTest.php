<?php declare(strict_types=1);

namespace Vulpes\Clock;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class FrozenClockTest extends TestCase
{
    public function testInstanceOfClockInterface(): void
    {
        $clock = new FrozenClock(new DateTimeImmutable);

        self::assertInstanceOf(ClockInterface::class, $clock);
    }

    public function testSameDateTimeImmutable(): void
    {
        $object = new DateTimeImmutable;
        $clock = new FrozenClock($object);

        self::assertSame($object, $clock->now());
    }

    public function testSetToChanges(): void
    {
        $objectA = new DateTimeImmutable;
        $objectB = new DateTimeImmutable;

        $clock = new FrozenClock($objectA);
        $clock->setTo($objectB);

        self::assertSame($objectB, $clock->now());
        self::assertNotSame($objectA, $clock->now());
    }

    public function testCreate(): void
    {
        $object = new DateTimeImmutable;
        $clock = FrozenClock::create($object);

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame($object, $clock->now());
    }

    public function testFromUTC(): void
    {
        $clock = FrozenClock::fromUTC();

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame('UTC', $clock->now()->getTimezone()->getName());
    }

    public function testFromSystemTimezone(): void
    {
        $clock = FrozenClock::fromSystemTimezone();

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame(date_default_timezone_get(), $clock->now()->getTimezone()->getName());
    }
}
