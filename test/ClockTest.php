<?php declare(strict_types=1);

namespace Clock;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use PHPUnit\Framework\TestCase;

use function date_default_timezone_get;

final class ClockTest extends TestCase
{
    public function testInstanceOfClockInterface(): void
    {
        $clock = new Clock;

        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertInstanceOf(\Psr\Clock\ClockInterface::class, $clock);
    }

    /**
     * @throws ClockExceptionInterface
     * @throws Exception
     */
    public function testWithProvidedTimezone(): void
    {
        $timezone = new DateTimeZone('Europe/Vatican');
        $clock = new Clock(timeZone: $timezone);

        $past = new DateTimeImmutable('now', $timezone);
        $now = $clock->now();
        $future = new DateTimeImmutable('now', $timezone);

        self::assertEquals($timezone, $now->getTimezone());
        self::assertGreaterThanOrEqual($past, $now);
        self::assertLessThanOrEqual($future, $now);
    }

    /**
     * @throws ClockExceptionInterface
     */
    public function testCreate(): void
    {
        $clock = Clock::create(timeZone: 'Europe/Vatican');
        self::assertInstanceOf(ClockInterface::class, $clock);
        self::assertSame('Europe/Vatican', $clock->now()->getTimezone()->getName());
    }

    /**
     * @dataProvider withDataProvider
     * @throws ClockExceptionInterface
     */
    public function testWith(
        DateTimeInterface|string|int $withA,
        DateTimeInterface|string|int $withB,
        bool                         $diffTZ = false
    ): void
    {
        $clock = new Clock;

        self::assertSame($clock, $clock->with(ClockInterface::NOW)->withUTC());

        $aClock = $clock->with($withA);
        $bClock = $clock->with($withB);

        self::assertNotSame($aClock, $bClock);

        self::assertSame($aClock, $aClock->with($withA));
        self::assertSame($bClock, $bClock->with($withB));

        if ($diffTZ) {
            self::assertNotSame($aClock->now()->getTimezone(), $bClock->now()->getTimezone());
        }
    }


    /**
     * @throws Exception
     */
    #[ArrayShape([
        'with-string-date' => "string[]",
        'with-string-datetime' => "string[]",
        'with-timestamp-0' => "int[]",
        'with-timestamp-1' => "int[]",
        'with-timestamp-2' => "int[]",
        'with-datetime' => "\DateTime[]",
        'with-datetimeImmutable' => "\DateTimeImmutable[]",
        'with-datetimeImmutable-with-TZ' => "array",
        'with-onlyTZ' => "\DateTimeImmutable[]",
        'with-same-but-NOT ;)' => "\DateTimeImmutable[]"
    ])]
    public static function withDataProvider(): array
    {
        return [
            'with-string-date' => ['2022-01-01', '2022-02-01'],
            'with-string-datetime' => ['2022-01-01 00:00:00', '2022-02-01 00:00:00'],
            'with-timestamp-0' => [1683903310, 1683903316],
            'with-timestamp-1' => [1683903310, 1683903310],
            'with-timestamp-2' => [1, 1],
            'with-datetime' => [new DateTime('2022-01-01'), new DateTime('2022-02-01')],
            'with-datetimeImmutable' => [
                new DateTimeImmutable('2022-01-01'),
                new DateTimeImmutable('2022-02-01')
            ],
            'with-datetimeImmutable-with-TZ' => [
                new DateTimeImmutable('2022-01-01'),
                new DateTimeImmutable('2022-02-01', new DateTimeZone('Europe/Vatican')),
                true
            ],
            'with-onlyTZ' => [
                new DateTimeImmutable('2022-01-01', new DateTimeZone('Europe/Budapest')),
                new DateTimeImmutable('2022-01-01', new DateTimeZone('Europe/Vatican')),
                true
            ],
            'with-same-but-NOT ;)' => [
                new DateTimeImmutable('2022-01-01', new DateTimeZone('Europe/Budapest')),
                new DateTimeImmutable('2022-01-01', new DateTimeZone('Europe/Budapest')),
            ]
        ];
    }

    /**
     * @throws ClockExceptionInterface
     */
    public function testWithDateTimeZone(): void
    {
        $clock = new Clock(timeZone: new DateTimeZone('Europe/Vatican'));

        self::assertNotEquals('Europe/Budapest', $clock->now()->getTimezone()->getName());

        $clockWithDtz = $clock->withDateTimeZone(new DateTimeZone('Europe/Budapest'));
        self::assertEquals('Europe/Budapest', $clockWithDtz->now()->getTimezone()->getName());

        self::assertNotSame($clock, $clockWithDtz);
        self::assertSame($clock, $clock->withDateTimeZone(new DateTimeZone('Europe/Vatican')));
        self::assertSame($clockWithDtz, $clockWithDtz->withDateTimeZone(new DateTimeZone('Europe/Budapest')));
    }

    /**
     * @throws ClockExceptionInterface
     */
    public function testWithUTC(): void
    {
        $clock = new Clock(timeZone: new DateTimeZone('Europe/Vatican'));

        self::assertNotEquals(ClockInterface::UTC, $clock->now()->getTimezone()->getName());

        $clockWithUTC = $clock->withUTC();
        self::assertEquals(ClockInterface::UTC, $clockWithUTC->now()->getTimezone()->getName());

        self::assertNotSame($clock, $clockWithUTC);
        self::assertSame($clockWithUTC, $clockWithUTC->withUTC());
    }

    /**
     * @throws ClockExceptionInterface
     */
    public function testWithSystemTimezone(): void
    {
        $clock = new Clock(timeZone: new DateTimeZone('Europe/Vatican'));

        self::assertNotEquals(date_default_timezone_get(), $clock->now()->getTimezone()->getName());

        $clockWithSysTz = $clock->withSystemTimezone();
        self::assertEquals(date_default_timezone_get(), $clockWithSysTz->now()->getTimezone()->getName());

        self::assertNotSame($clock, $clockWithSysTz);
        self::assertSame($clockWithSysTz, $clockWithSysTz->withSystemTimezone());
    }

    public function testNowException(): void
    {
        $this->expectException(ClockExceptionInterface::class);
        $this->expectExceptionMessage('Failed to parse time string');

        $clock = new Clock('unknown datetime');
        $clock->now();
    }

    public function testWithException(): void
    {
        $this->expectException(ClockExceptionInterface::class);
        $this->expectExceptionMessage('Failed to parse time string');

        $clock = new Clock;
        $clock->with('unknown datetime')->now();
    }

    public function testWithDateTimeZoneException(): void
    {
        $this->expectException(ClockExceptionInterface::class);
        $this->expectExceptionMessage('Unknown or bad timezone');

        $clock = new Clock;
        $clock->withDateTimeZone('unknown datetime zone')->now();
    }

    /**
     * @dataProvider badTimezonesProvider
     * @param string $timezone
     * @return void
     * @throws ClockExceptionInterface
     */
    public function testTzExceptions(string $timezone): void
    {
        $this->expectException(ClockExceptionInterface::class);
        $this->expectExceptionMessage('Unknown or bad timezone');

        if ($timezone) {
            $this->expectExceptionMessage($timezone);
        }

        new Clock(timeZone: $timezone);
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
        $this->expectExceptionMessage('Unknown or bad timezone');
        if ($timezone) {
            $this->expectExceptionMessage($timezone);
        }
        Clock::create(timeZone: $timezone);
    }

    public static function badTimezonesProvider(): array
    {
        return [
            [''],
            ['qwerty'],
            ['Unknown'],
        ];
    }
}

