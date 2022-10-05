<?php

namespace Gomzyakov\Payture\Exception;

use Exception;
use Throwable;

class TransportException extends Exception
{
    public static function becauseUnderlyingTransportFailed(Throwable $exception): self
    {
        return new self(
            sprintf('Payture request failed: [%s] %s', $exception->getCode(), $exception->getMessage()),
            $exception->getCode(),
            $exception
        );
    }
}
