<?php declare(strict_types=1);

namespace Clock;

use Psr\Clock\ClockInterface;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

use function date_default_timezone_get;

final class Clock implements ClockInterface
{
    public const TIMEZONE_UTC = 'UTC';

    private DateTimeZone $dateTimeZone;

    /**
     * @param DateTimeZone|string $timeZone
     */
    public function __construct(DateTimeZone|string $timeZone = self::TIMEZONE_UTC)
    {
        $this->dateTimeZone = $timeZone instanceof DateTimeZone ? $timeZone : new DateTimeZone($timeZone);
    }

    /**
     * @throws Exception
     */
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->dateTimeZone);
    }

    /**
     * @throws Exception
     */
    public static function create(DateTimeZone|string $timeZone = self::TIMEZONE_UTC): ClockInterface
    {
        return new self($timeZone);
    }

    public static function fromUTC(): ClockInterface
    {
        return new self;
    }

    /**
     * @throws Exception
     */
    public static function fromSystemTimezone(): ClockInterface
    {
        return new self(date_default_timezone_get());
    }
}
