<?php declare(strict_types=1);

namespace Clock;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class FrozenClockTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testInstanceOfClockInterface(): void
    {
        $clock = new FrozenClock(new DateTimeImmutable);

        self::assertInstanceOf(ClockInterface::class, $clock);
    }

    /**
     * @throws Exception
     */
    public function testSameDateTimeImmutable(): void
    {
        $object = new DateTimeImmutable;
        $clock = new FrozenClock($object);

        self::assertSame($object, $clock->now());
    }

    /**
     * @throws Exception
     */
    public function testSetToChanges(): void
    {
        $objectA = new DateTimeImmutable;
        $objectB = new DateTimeImmutable;

        $clock = new FrozenClock($objectA);
        $clock->set($objectB);

        self::assertSame($objectB, $clock->now());
        self::assertNotSame($objectA, $clock->now());
    }

    /**
     * @throws Exception
     */
    public function testCreate(): void
    {
        $object = new DateTimeImmutable;
        $clock = FrozenClock::create($object);

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame($object, $clock->now());
    }

    /**
     * @throws Exception
     */
    public function testFromUTC(): void
    {
        $clock = FrozenClock::fromUTC();

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame('UTC', $clock->now()->getTimezone()->getName());
    }

    /**
     * @throws Exception
     */
    public function testFromSystemTimezone(): void
    {
        $clock = FrozenClock::fromSystemTimezone();

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame(date_default_timezone_get(), $clock->now()->getTimezone()->getName());
    }
}
