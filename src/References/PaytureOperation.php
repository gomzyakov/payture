<?php

namespace Gomzyakov\Payture\InPayClient\References;

/**
 * Operations that payture accepts.
 *
 * TODO Move to References
 * TODO Link
 */
enum PaytureOperation
{
    /**
     * TODO Add description.
     */
    case Init;

    case Pay;

    case Charge;

    case Unblock;

    case Refund;

    case PayStatus;

    case GetState;
}
