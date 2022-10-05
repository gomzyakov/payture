<?php

namespace Gomzyakov\Payture\InPayClient\Tests\EndToEnd;

use Gomzyakov\Payture\InPayClient\References\SessionType;
use Gomzyakov\Payture\InPayClient\TerminalResponse;

/**
 * @coversNothing
 */
final class TwoStepPaymentTerminalTest extends AbstractTerminalTestCase
{
    private const ORDER_PRICE = 10000;

    /**
     * Keep the test suite with deprecated payStatus.
     */
    public function test_payture_in_pay_api_with_pay_status(): void
    {
        $orderId = self::generateOrderId();

        $response  = $this->initPayment($orderId);
        $sessionId = $response->getSessionId();

        $response = $this->getTerminal()->payStatus($orderId);
        self::assertTrue($response->isSuccess());

        $url = $this->getTerminal()->createPaymentUrl($sessionId);

        $this->pay($url, $orderId, self::ORDER_PRICE);
        $response = $this->getTerminal()->payStatus($orderId);
        self::assertTrue($response->isAuthorizedState());

        $response = $this->getTerminal()->charge($orderId, self::ORDER_PRICE);
        self::assertTrue($response->isSuccess());
        $response = $this->getTerminal()->payStatus($orderId);
        self::assertTrue($response->isChargedState());

        $response = $this->getTerminal()->refund($orderId, self::ORDER_PRICE);
        self::assertTrue($response->isSuccess());
        $response = $this->getTerminal()->payStatus($orderId);
        self::assertTrue($response->isRefundedState());
        self::assertNotEmpty($response->getRrn());
    }

    public function test_payture_in_pay_api(): void
    {
        $orderId = self::generateOrderId();

        $response  = $this->initPayment($orderId);
        $sessionId = $response->getSessionId();

        $url = $this->getTerminal()->createPaymentUrl($sessionId);

        $this->pay($url, $orderId, self::ORDER_PRICE);
        $response = $this->getTerminal()->getState($orderId);
        self::assertNotEmpty($response->getRrn());
        self::assertTrue($response->isAuthorizedState());

        $response = $this->getTerminal()->charge($orderId, self::ORDER_PRICE);
        self::assertTrue($response->isSuccess());
        $response = $this->getTerminal()->getState($orderId);
        self::assertTrue($response->isChargedState());
        self::assertNotEmpty($response->getRrn());

        $response = $this->getTerminal()->refund($orderId, self::ORDER_PRICE);
        self::assertTrue($response->isSuccess());
        $response = $this->getTerminal()->getState($orderId);
        self::assertTrue($response->isRefundedState());
        self::assertNotEmpty($response->getRrn());
    }

    private function initPayment(string $orderId): TerminalResponse
    {
        return $this->getTerminal()->init(
            SessionType::Block,
            $orderId,
            'Auto Test purchase',
            self::ORDER_PRICE,
            '127.0.0.1',
            'https://github.com/Gomzyakov'
        );
    }
}
