<?php declare(strict_types=1);

namespace Clock;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;

use Psr\Clock\ClockInterface;
use function date_default_timezone_get;

final class ClockTest extends TestCase
{
    public function testInstanceOfClockInterface(): void
    {
        $clock = new Clock;

        self::assertInstanceOf(ClockInterface::class, $clock);
    }

    /**
     * @throws Exception
     */
    public function testWithProvidedTimezone(): void
    {
        $timezone = new DateTimeZone('Europe/Vatican');
        $clock = new Clock($timezone);

        $past = new DateTimeImmutable('now', $timezone);
        $now = $clock->now();
        $future = new DateTimeImmutable('now', $timezone);

        self::assertEquals($timezone, $now->getTimezone());
        self::assertGreaterThanOrEqual($past, $now);
        self::assertLessThanOrEqual($future, $now);
    }

    /**
     * @throws Exception
     */
    public function testCreate(): void
    {
        $clock = Clock::create('Europe/Vatican');

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame('Europe/Vatican', $clock->now()->getTimezone()->getName());
    }

    public function testFromUTC(): void
    {
        $clock = Clock::fromUTC();

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame('UTC', $clock->now()->getTimezone()->getName());
    }

    /**
     * @throws Exception
     */
    public function testFromSystemTimezone(): void
    {
        $clock = Clock::fromSystemTimezone();

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame(date_default_timezone_get(), $clock->now()->getTimezone()->getName());
    }

    /**
     * @dataProvider badTimezonesProvider
     * @param string $timezone
     * @return void
     * @throws Exception
     */
    public function testExceptions(string $timezone): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Unknown or bad timezone (' . $timezone . ')');

        new Clock($timezone);
    }

    /**
     * @dataProvider badTimezonesProvider
     * @param string $timezone
     * @return void
     * @throws Exception
     */
    public function testExceptionsOnCreate(string $timezone): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Unknown or bad timezone (' . $timezone . ')');

        Clock::create($timezone);
    }

    public function badTimezonesProvider(): array
    {
        return [
            [''],
            ['qwerty'],
            ['Unknown'],
        ];
    }
}

