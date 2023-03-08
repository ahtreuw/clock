<?php declare(strict_types=1);

namespace Vulpes\Clock;

use Psr\Clock\ClockInterface;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

use function date_default_timezone_get;

final class SystemClock implements ClockInterface
{
    private DateTimeZone $timeZone;

    /**
     * @param DateTimeZone|string $timeZone
     * @throws ClockExceptionInterface
     */
    public function __construct(DateTimeZone|string $timeZone = 'UTC')
    {
        try {
            $this->timeZone = is_string($timeZone) ? new DateTimeZone($timeZone) : $timeZone;
        } catch (Exception $e) {
            throw new ClockException('Unknown or bad timezone (' . $timeZone . ')');
        }
    }

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timeZone);
    }

    /**
     * @throws ClockExceptionInterface
     */
    public static function create(DateTimeZone|string $timeZone = 'UTC'): ClockInterface
    {
        return new self($timeZone);
    }

    public static function fromUTC(): ClockInterface
    {
        return new self('UTC');
    }

    public static function fromSystemTimezone(): ClockInterface
    {
        return new self(date_default_timezone_get());
    }
}
