<?php declare(strict_types=1);

namespace Clock;

use Exception;
use Throwable;

class ClockException extends Exception implements ClockExceptionInterface
{

    public function __construct(string|Exception $exception, int $code = 0, Throwable $throwable = null)
    {
        if ($exception instanceof Exception) {
            parent::__construct($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
            return;
        }
        parent::__construct($exception, $code, $throwable);
    }
}