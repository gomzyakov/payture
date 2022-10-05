<?php

namespace Gomzyakov\Payture;

/**
 * Operations that payture accepts.
 *
 * @see https://payture.com/en/api/#inpay_
 */
enum PaytureOperation
{
    /**
     * @see https://payture.com/en/api/#inpay_init_
     */
    case Init;

    /**
     * @see https://payture.com/en/api/#inpay_pay_
     */
    case Pay;

    /**
     * @see https://payture.com/en/api/#inpay_charge_
     */
    case Charge;

    /**
     * @see https://payture.com/en/api/#inpay_unblock_
     */
    case Unblock;

    /**
     * @see https://payture.com/en/api/#inpay_refund_
     */
    case Refund;

    /**
     * @see https://payture.com/en/api/#inpay_getstate_
     */
    case PayStatus;

    /**
     * https://payture.com/en/api/#inpay_paystatus_.
     */
    case GetState;
}
