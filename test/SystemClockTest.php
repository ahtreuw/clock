<?php declare(strict_types=1);

namespace Vulpes\Clock;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

use Psr\Clock\ClockInterface;
use function date_default_timezone_get;

final class SystemClockTest extends TestCase
{
    public function testInstanceOfClockInterface(): void
    {
        $clock = new SystemClock;

        self::assertInstanceOf(ClockInterface::class, $clock);
    }

    public function testWithProvidedTimezone(): void
    {
        $timezone = new DateTimeZone('Europe/Vatican');
        $clock = new SystemClock($timezone);

        $past = new DateTimeImmutable('now', $timezone);
        $now = $clock->now();
        $future = new DateTimeImmutable('now', $timezone);

        self::assertEquals($timezone, $now->getTimezone());
        self::assertGreaterThanOrEqual($past, $now);
        self::assertLessThanOrEqual($future, $now);
    }

    public function testCreate(): void
    {
        $clock = SystemClock::create('Europe/Vatican');

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame('Europe/Vatican', $clock->now()->getTimezone()->getName());
    }

    public function testFromUTC(): void
    {
        $clock = SystemClock::fromUTC();

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame('UTC', $clock->now()->getTimezone()->getName());
    }

    public function testFromSystemTimezone(): void
    {
        $clock = SystemClock::fromSystemTimezone();

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame(date_default_timezone_get(), $clock->now()->getTimezone()->getName());
    }

    /**
     * @dataProvider badTimezonesProvider
     * @param string $timezone
     * @return void
     * @throws ClockExceptionInterface
     */
    public function testExceptions(string $timezone): void
    {
        $this->expectException(ClockExceptionInterface::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Unknown or bad timezone (' . $timezone . ')');

        new SystemClock($timezone);
    }

    /**
     * @dataProvider badTimezonesProvider
     * @param string $timezone
     * @return void
     * @throws ClockExceptionInterface
     */
    public function testExceptionsOnCreate(string $timezone): void
    {
        $this->expectException(ClockExceptionInterface::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Unknown or bad timezone (' . $timezone . ')');

        SystemClock::create($timezone);
    }

    public function badTimezonesProvider(): array
    {
        return [
            [''],
            ['qwerty'],
            ['ÁrvíztűrőTükörfúrógép'],
        ];
    }
}

