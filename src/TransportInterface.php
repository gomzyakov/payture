<?php

namespace Gomzyakov\Payture\InPayClient;

use Gomzyakov\Payture\InPayClient\Exception\TransportException;

interface TransportInterface
{
    /**
     * @throws TransportException
     */
    public function request(PaytureOperation $operation, string $interface, array $parameters): string;
}
