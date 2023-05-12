<?php declare(strict_types=1);

namespace Clock;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;

class FrozenClockTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testInstanceOfClockInterface(): void
    {
        $clock = new FrozenClock;
        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertInstanceOf(\Psr\Clock\ClockInterface::class, $clock);
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
        $objectA = new DateTimeImmutable('2022-01-01');
        $objectB = new DateTimeImmutable('2022-02-02');

        $clockA = new FrozenClock($objectA);
        $clockB = $clockA->with($objectB);

        self::assertSame($objectA, $clockA->now());
        self::assertSame($objectB, $clockB->now());

        self::assertNotSame($clockA, $clockB);

        self::assertSame($clockA, $clockA->with($objectA));
        self::assertSame($clockB, $clockB->with($objectB));
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
     * @throws ClockExceptionInterface
     */
    public function testWithZero(): void
    {
        $zero = new FrozenClock(0);
        $one = $zero->now()->add(new DateInterval('PT1S'));
        self::assertEquals(0, $zero->now()->getTimestamp());
        self::assertEquals(1, $one->getTimestamp());
    }

    /**
     * @throws Exception
     */
    public function testWithDateTime(): void
    {
        $clock = new FrozenClock;

        $wDateTime = $clock->with(new DateTime($date = '2022-01-01'));

        self::assertEquals($wDateTime->now()->format('Y-m-d'), $date);
        self::assertNotEquals($clock->now()->getTimestamp(), $wDateTime->now()->getTimestamp());
        self::assertNotSame($clock, $wDateTime);
    }

    /**
     * @throws Exception
     */
    public function testWithInt(): void
    {
        $clock = new FrozenClock;

        $date = date('Y-m-d', $timestamp = strtotime('-1 weeks'));

        $wDateTime = $clock->with($timestamp);

        self::assertEquals($date, $wDateTime->now()->format('Y-m-d'));
        self::assertEquals($timestamp, $wDateTime->now()->getTimestamp());

        self::assertNotEquals($clock->now()->getTimestamp(), $wDateTime->now()->getTimestamp());
        self::assertNotSame($clock, $wDateTime);
    }

    /**
     * @throws Exception
     */
    public function testWithDateTimeZone(): void
    {
        $clock = new FrozenClock;

        self::assertNotEquals('Europe/Budapest', $clock->now()->getTimezone()->getName());

        $clockWithDtz = $clock->withDateTimeZone(new DateTimeZone('Europe/Budapest'));
        self::assertEquals('Europe/Budapest', $clockWithDtz->now()->getTimezone()->getName());

        self::assertNotSame($clock, $clockWithDtz);
        self::assertSame($clockWithDtz, $clockWithDtz->withDateTimeZone(new DateTimeZone('Europe/Budapest')));
    }

    /**
     * @throws Exception
     */
    public function testWithUTC(): void
    {
        $clock = new FrozenClock(new DateTimeImmutable('now', new DateTimeZone('Europe/Vatican')));

        self::assertNotEquals(ClockInterface::UTC, $clock->now()->getTimezone()->getName());

        $clockWithUTC = $clock->withUTC();
        self::assertEquals(ClockInterface::UTC, $clockWithUTC->now()->getTimezone()->getName());

        self::assertNotSame($clock, $clockWithUTC);
        self::assertSame($clockWithUTC, $clockWithUTC->withUTC());
    }

    /**
     * @throws Exception
     */
    public function testWithSystemTimezone(): void
    {
        $clock = new FrozenClock(new DateTimeImmutable('now', new DateTimeZone('Europe/Vatican')));

        self::assertNotEquals(date_default_timezone_get(), $clock->now()->getTimezone()->getName());

        $clockWithSysTz = $clock->withSystemTimezone();
        self::assertEquals(date_default_timezone_get(), $clockWithSysTz->now()->getTimezone()->getName());

        self::assertNotSame($clock, $clockWithSysTz);
        self::assertSame($clockWithSysTz, $clockWithSysTz->withSystemTimezone());
    }

    public function testWithException(): void
    {
        $this->expectException(ClockExceptionInterface::class);
        $this->expectExceptionMessage('Failed to parse time string');

        $clock = new FrozenClock;
        $clock->with('unknown datetime')->now();
    }

    public function testConstructException(): void
    {
        $this->expectException(ClockExceptionInterface::class);
        $this->expectExceptionMessage('Failed to parse time string');

        $clock = new FrozenClock('unknown datetime');
        $clock->with('unknown datetime')->now();
    }
}
