<?php declare(strict_types=1);

namespace Clock;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

use function date_default_timezone_get;

class Clock implements ClockInterface
{
    private string $dateTime;
    private DateTimeZone $dateTimeZone;

    /**
     * @throws ClockExceptionInterface
     */
    public function __construct(
        string              $dateTime = ClockInterface::NOW,
        DateTimeZone|string $timeZone = ClockInterface::UTC
    )
    {
        try {
            $this->dateTime = $dateTime;
            $this->dateTimeZone = $timeZone instanceof DateTimeZone ? $timeZone : new DateTimeZone($timeZone);
        } catch (Exception $exception) {
            $message = 'Unknown or bad timezone (unknown datetime zone: "%s")';
            throw new ClockException(sprintf($message, $timeZone), 0, $exception);
        }
    }

    public function now(): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($this->dateTime, $this->dateTimeZone);
        } catch (Exception $exception) {
            throw new ClockException($exception);
        }
    }

    public function with(DateTimeInterface|string|int $dateTime): ClockInterface
    {
        $timeZone = $this->createTimeZone($dateTime);
        $dateTime = $this->createDateTime($dateTime);

        if ($this->dateTime === $dateTime && $this->dateTimeZone->getName() === $timeZone->getName()) {
            return $this;
        }

        $new = clone $this;
        $new->dateTime = $dateTime;
        $new->dateTimeZone = $timeZone;
        return $new;
    }


    public function withDateTimeZone(DateTimeZone|string $timeZone): ClockInterface
    {
        if ($timeZone instanceof DateTimeZone) {

            if ($timeZone->getName() === $this->dateTimeZone->getName()) {
                return $this;
            }

            $new = clone $this;
            $new->dateTimeZone = $timeZone;
            return $new;
        }

        if ($timeZone === $this->dateTimeZone->getName()) {
            return $this;
        }

        try {
            $new = clone $this;
            $new->dateTimeZone = new DateTimeZone($timeZone);
            return $new;
        } catch (Exception $exception) {
            $message = 'Unknown or bad timezone (unknown datetime zone: "%s")';
            throw new ClockException(sprintf($message, $timeZone), 0, $exception);
        }
    }

    public function withUTC(): ClockInterface
    {
        return $this->withDateTimeZone(ClockInterface::UTC);
    }

    public function withSystemTimezone(): ClockInterface
    {
        return $this->withDateTimeZone(date_default_timezone_get());
    }

    public static function create(
        string              $dateTime = ClockInterface::NOW,
        DateTimeZone|string $timeZone = ClockInterface::UTC
    ): ClockInterface
    {
        return new self(dateTime: $dateTime, timeZone: $timeZone);
    }

    private function createDateTime(DateTimeInterface|int|string $dateTime): string
    {
        if (is_string($dateTime)) {
            return $dateTime;
        }
        if (is_int($dateTime)) {
            return date(DateTimeInterface::RFC3339, $dateTime);
        }
        return $dateTime->format(DateTimeInterface::RFC3339_EXTENDED);
    }

    private function createTimeZone(DateTimeInterface|string|int $dateTime): DateTimeZone
    {
        if ($dateTime instanceof DateTimeInterface) {
            return $dateTime->getTimezone() ?: $this->dateTimeZone;
        }
        return $this->dateTimeZone;
    }
}
