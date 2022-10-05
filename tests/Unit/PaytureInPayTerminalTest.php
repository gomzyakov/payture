<?php

namespace Gomzyakov\Payture\InPayClient\Tests\Unit;

use Gomzyakov\Payture\InPayClient\PaytureInPayTerminal;
use Gomzyakov\Payture\InPayClient\References\PaytureOperation;
use Gomzyakov\Payture\InPayClient\References\SessionType;
use Gomzyakov\Payture\InPayClient\TerminalConfiguration;
use Gomzyakov\Payture\InPayClient\TransportInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gomzyakov\Payture\InPayClient\PaytureInPayTerminal
 */
final class PaytureInPayTerminalTest extends TestCase
{
    private TerminalConfiguration $config;

    private $transport;

    /**
     * @var PaytureInPayTerminal
     */
    private PaytureInPayTerminal $terminal;

    protected function setUp(): void
    {
        $this->config    = new TerminalConfiguration('MerchantKey', 'MerchantPassword', 'https://nowhere.payture.com/');
        $this->transport = $this->createMock(TransportInterface::class);

        $this->terminal = new PaytureInPayTerminal($this->config, $this->transport);
    }

    /**
     * @dataProvider getInitSessionTypes
     *
     * @param SessionType $type
     * @param string      $data
     *
     * @throws \Gomzyakov\Payture\InPayClient\Exception\TransportException
     */
    public function test_payment_init(SessionType $type, string $data): void
    {
        $this->transport->expects($this->once())
            ->method('request')
            ->with(
                PaytureOperation::Init,
                'apim',
                [
                    'Key'  => 'MerchantKey',
                    'Data' => $data,
                ]
            )->willReturn('<Init Success="True" SessionId="external-id"/>');

        $response = $this->terminal->init(
            $type,
            'Order-123',
            'The order',
            10000,
            '127.0.0.1',
            'https://redirect-me.back/',
            'template',
            [
                'custom_data' => 'value',
            ]
        );

        self::assertTrue($response->isSuccess());
        self::assertEquals('external-id', $response->getSessionId());
    }

    public function getInitSessionTypes(): array
    {
        return [
            [
                SessionType::Pay,
                'SessionType=Pay;OrderId=Order-123;Amount=10000;IP=127.0.0.1;Product=The+order;Url=https%3A%2F%2Fredirect-me.back%2F;TemplateTag=template;custom_data=value',
            ],
            [
                SessionType::Block,
                'SessionType=Block;OrderId=Order-123;Amount=10000;IP=127.0.0.1;Product=The+order;Url=https%3A%2F%2Fredirect-me.back%2F;TemplateTag=template;custom_data=value',
            ],
        ];
    }

    public function test_payment_charge(): void
    {
        $this->transport->expects($this->once())
            ->method('request')
            ->with(
                PaytureOperation::Charge,
                'apim',
                [
                    'Key'      => 'MerchantKey',
                    'OrderId'  => 'Order-123',
                    'Amount'   => 10000,
                    'Password' => 'MerchantPassword',
                ]
            )->willReturn('<Charge Success="True" Amount="10000"/>');

        $response = $this->terminal->charge('Order-123', 10000);

        self::assertTrue($response->isSuccess());
    }

    public function test_payment_unblock(): void
    {
        $this->transport->expects($this->once())
            ->method('request')
            ->with(
                PaytureOperation::Unblock,
                'apim',
                [
                    'Key'      => 'MerchantKey',
                    'OrderId'  => 'Order-123',
                    'Amount'   => 10000,
                    'Password' => 'MerchantPassword',
                ]
            )->willReturn('<Unblock Success="True"/>');

        $response = $this->terminal->unblock('Order-123', 10000);

        self::assertTrue($response->isSuccess());
    }

    public function test_payment_refund(): void
    {
        $this->transport->expects($this->once())
            ->method('request')
            ->with(
                PaytureOperation::Refund,
                'apim',
                [
                    'Key'      => 'MerchantKey',
                    'OrderId'  => 'Order-123',
                    'Amount'   => 6000,
                    'Password' => 'MerchantPassword',
                ]
            )->willReturn('<Refund Success="True" NewAmount="4000"/>');

        $response = $this->terminal->refund('Order-123', 6000);

        self::assertTrue($response->isSuccess());
        self::assertEquals(4000, $response->getAmount());
    }

    public function test_payment_status(): void
    {
        $this->transport->expects($this->once())
            ->method('request')
            ->with(
                PaytureOperation::PayStatus,
                'apim',
                [
                    'Key'     => 'MerchantKey',
                    'OrderId' => 'Order-123',
                ]
            )->willReturn('<PayStatus Success="True" State="Charged" Amount="10000"/>');

        $response = $this->terminal->payStatus('Order-123');

        self::assertTrue($response->isSuccess());
        self::assertTrue($response->isChargedState());
    }

    public function test_get_state(): void
    {
        $rrn     = '003770024290';
        $orderId = 'Order-123';
        $this->transport->expects($this->once())
            ->method('request')
            ->with(
                PaytureOperation::GetState,
                'apim',
                [
                    'Key'     => 'MerchantKey',
                    'OrderId' => $orderId,
                ]
            )->willReturn('<GetState Success="True" OrderId="' . $orderId . '" State="Refunded"
                Forwarded="False" MerchantContract="Merchant" Amount="12461" RRN="' . $rrn . '"/>');

        $response = $this->terminal->getState($orderId);

        self::assertTrue($response->isSuccess());
        self::assertTrue($response->isRefundedState());
        self::assertEquals($rrn, $response->getRrn());
        self::assertEquals($orderId, $response->getOrderId());
    }

    public function test_creating_payment_url(): void
    {
        self::assertEquals(
            'https://nowhere.payture.com/apim/Pay?SessionId=external-id',
            $this->terminal->createPaymentUrl('external-id')
        );
    }
}
