<?php

namespace Gomzyakov\Payture;

use Gomzyakov\Payture\Exception\TransportException;

interface PaytureInPayTerminalInterface
{
    /**
     * @see https://payture.com/api/#inpay_getstate_
     *
     * @param string $orderId Payment ID in Merchant system
     */
    public function getState(string $orderId): TerminalResponse;

    /**
     * @param string $orderId Payment ID in Merchant system
     *
     * @throws TransportException
     *
     * @see https://payture.com/api#inpay_paystatus_
     * @deprecated
     * @see PaytureInPayTerminalInterface::getState()
     */
    public function payStatus(string $orderId): TerminalResponse;

    /**
     * @see https://payture.com/api#inpay_init_
     *
     * @param string               $orderId     Payment ID in Merchant system
     * @param int                  $amount      Payment amount
     * @param string               $clientIp    User IP address
     * @param string               $url         back URL
     * @param string               $templateTag Used template tag. If empty string - no template tag will be passed
     * @param array<string, mixed> $extra       Payture none requirement extra fields
     * @param SessionType          $sessionType
     * @param string               $product
     *
     * @throws TransportException
     */
    public function init(
        SessionType $sessionType,
        string $orderId,
        string $product,
        int $amount,
        string $clientIp,
        string $url,
        string $templateTag = '',
        array $extra = []
    ): TerminalResponse;

    public function createPaymentUrl(string $sessionId): string;

    /**
     * @see https://payture.com/api#inpay_unblock_
     *
     * @param string $order_id Payment ID in Merchant system
     * @param int    $amount   Amount in kopecks that is to be returned
     *
     * @throws TransportException
     */
    public function unblock(string $order_id, int $amount): TerminalResponse;

    /**
     * @see https://payture.com/api#inpay_charge_
     *
     * @param string $order_id Payment ID in Merchant system
     * @param int    $amount   Charging amount in kopecks
     *
     * @throws TransportException
     */
    public function charge(string $order_id, int $amount): TerminalResponse;

    /**
     * The request is used both in one-step and two-step payment schemes.
     *
     * @see https://payture.com/api#inpay_refund_
     *
     * @param string $orderId Payment ID in Merchant system
     * @param int    $amount  Amount in kopecks that is to be returned
     *
     * @throws TransportException
     */
    public function refund(string $orderId, int $amount): TerminalResponse;
}
