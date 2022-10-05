<?php

namespace Gomzyakov\Payture;

use Gomzyakov\Payture\Exception\TransportException;

interface TransportInterface
{
    /**
     * @param PaytureOperation     $operation
     * @param string               $interface
     * @param array<string, mixed> $parameters
     *
     * @throws TransportException
     */
    public function request(PaytureOperation $operation, string $interface, array $parameters): string;
}
