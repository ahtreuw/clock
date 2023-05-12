<?php declare(strict_types=1);

namespace Clock;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

class FrozenClock implements ClockInterface
{
    private DateTimeImmutable $dateTime;

    /**
     * @throws ClockExceptionInterface
     */
    public function __construct(DateTimeInterface|string|int|null $dateTime = null)
    {
        try {
            $this->dateTime = $this->createDateTimeImmutable(
                dateTime: $dateTime,
                defaultDateTimezone: new DateTimeZone(ClockInterface::UTC)
            );
        } catch (Exception $exception) {
            throw new ClockException($exception);
        }
    }

    public function now(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function with(DateTimeInterface|string|int $dateTime): ClockInterface
    {
        try {
            $dateTime = $this->createDateTimeImmutable(
                dateTime: $dateTime,
                defaultDateTimezone: $this->dateTime->getTimezone()
            );
        } catch (Exception $exception) {
            throw new ClockException($exception);
        }

        if ($this->dateTime->getTimestamp() === $dateTime->getTimestamp() &&
            $this->dateTime->getTimezone()->getName() === $dateTime->getTimezone()->getName()) {
            return $this;
        }

        $new = clone $this;
        $new->dateTime = $dateTime;
        return $new;
    }

    public function withDateTimeZone(DateTimeZone|string $timeZone): ClockInterface
    {
        if ($timeZone instanceof DateTimeZone === false) {
            $timeZone = new DateTimeZone($timeZone);
        }

        if ($timeZone->getName() === $this->dateTime->getTimezone()->getName()) {
            return $this;
        }

        $new = clone $this;
        $new->dateTime = $this->dateTime->setTimezone($timeZone);
        return $new;
    }

    public function withUTC(): ClockInterface
    {
        return $this->withDateTimeZone(ClockInterface::UTC);
    }

    public function withSystemTimezone(): ClockInterface
    {
        return $this->withDateTimeZone(date_default_timezone_get());
    }

    public static function create(DateTimeInterface|int|string|null $now = null): ClockInterface
    {
        return new self($now);
    }

    /**
     * @throws Exception
     */
    private function createDateTimeImmutable(
        DateTimeInterface|int|string|null $dateTime,
        false|DateTimeZone                $defaultDateTimezone
    ): DateTimeImmutable
    {
        if ($dateTime instanceof DateTimeImmutable) {
            return $dateTime;
        }

        if (is_null($dateTime)) {
            return new DateTimeImmutable(ClockInterface::NOW, new DateTimeZone(ClockInterface::UTC));
        }

        if (is_string($dateTime)) {
            return new DateTimeImmutable($dateTime, $defaultDateTimezone ?: null);
        }

        $timeStamp = $dateTime instanceof DateTimeInterface ? $dateTime->getTimestamp() : $dateTime;
        $timeZone = $dateTime instanceof DateTimeInterface ? $dateTime->getTimezone() : $defaultDateTimezone;

        $dateTimeImmutable = new DateTimeImmutable(ClockInterface::NOW, $timeZone ?: null);

        return $dateTimeImmutable->setTimestamp($timeStamp);
    }
}
