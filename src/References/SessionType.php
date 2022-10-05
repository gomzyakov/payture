<?php

namespace Gomzyakov\Payture\InPayClient\References;

/**
 * Enum which determine session types in payture gateway.
 *
 * TODO Move to References
 *
 * @see https://payture.com/api#inpay_init_
 */
enum SessionType
{
    /**
     * TODO Add description.
     */
    case Pay;

    case Block;
}
