<?php

namespace Gomzyakov\Payture\InPayClient;

/**
 * Enum which determine session types in payture gateway.
 *
 * @see https://payture.com/api#inpay_init_
 */
interface SessionType
{
    public const PAY = 'Pay';

    public const BLOCK = 'Block';
}
