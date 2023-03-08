<?php declare(strict_types=1);

namespace Vulpes\Clock;

use DateTimeImmutable;
use JetBrains\PhpStorm\Pure;
use Psr\Clock\ClockInterface;

class FrozenClock implements ClockInterface
{
    public function __construct(private DateTimeImmutable $now) {}

    public function setTo(DateTimeImmutable $now): void
    {
        $this->now = $now;
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }

    #[Pure] public static function create(DateTimeImmutable $now): ClockInterface
    {
        return new self($now);
    }

    public static function fromUTC(): ClockInterface
    {
        return new self(SystemClock::fromUTC()->now());
    }

    public static function fromSystemTimezone(): ClockInterface
    {
        return new self(SystemClock::fromSystemTimezone()->now());
    }
}
