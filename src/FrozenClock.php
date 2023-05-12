<?php declare(strict_types=1);

namespace Clock;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Psr\Clock\ClockInterface;

class FrozenClock implements ClockInterface
{
    /**
     * @throws Exception
     */
    public function __construct(
        private DateTimeImmutable $now = new DateTimeImmutable('now', new DateTimeZone('UTC'))
    ) {}

    public function set(DateTimeImmutable $now): void
    {
        $this->now = $now;
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }

    /**
     * @throws Exception
     */
    public static function create(DateTimeImmutable $now): ClockInterface
    {
        return new self($now);
    }

    /**
     * @throws Exception
     */
    public static function fromUTC(): ClockInterface
    {
        return new self(Clock::fromUTC()->now());
    }

    /**
     * @throws Exception
     */
    public static function fromSystemTimezone(): ClockInterface
    {
        return new self(Clock::fromSystemTimezone()->now());
    }
}
