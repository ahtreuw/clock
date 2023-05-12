<?php declare(strict_types=1);

namespace Clock;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

interface ClockInterface extends \Psr\Clock\ClockInterface
{
    public const NOW = 'now';
    public const UTC = 'UTC';

    /**
     * @throws ClockExceptionInterface
     */
    public function now(): DateTimeImmutable;

    /**
     * @throws ClockExceptionInterface
     */
    public function with(DateTimeInterface|string|int $dateTime): ClockInterface;

    /**
     * @throws ClockExceptionInterface
     */
    public function withDateTimeZone(DateTimeZone|string $timeZone): ClockInterface;

    /**
     * @throws ClockExceptionInterface
     */
    public function withUTC(): ClockInterface;

    /**
     * @throws ClockExceptionInterface
     */
    public function withSystemTimezone(): ClockInterface;

    /**
     * @throws ClockExceptionInterface
     */
    public static function create(): ClockInterface;
}