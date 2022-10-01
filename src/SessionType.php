<?php

namespace Gomzyakov\Payture\InPayClient;

/**
 * Enum which determine session types in payture gateway.
 *
 * @see https://payture.com/api#inpay_init_
 */
interface SessionType
{
    const PAY = 'Pay';
    const BLOCK = 'Block';
}
