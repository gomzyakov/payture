<?php

namespace Gomzyakov\Payture\InPayClient;

/**
 * Operations that payture accepts.
 */
interface PaytureOperation
{
    public const INIT = 'Init';
    public const PAY = 'Pay';
    public const CHARGE = 'Charge';
    public const UNBLOCK = 'Unblock';
    public const REFUND = 'Refund';
    /**
     * @deprecated
     * @see PaytureOperation::GET_STATE
     */
    public const PAY_STATUS = 'PayStatus';
    public const GET_STATE = 'GetState';
}
