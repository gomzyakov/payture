<?php

namespace Gomzyakov\Payture\InPayClient;

use Gomzyakov\Payture\InPayClient\Exception\InvalidResponseException;
use Gomzyakov\Payture\InPayClient\Exception\TransportException;
use LogicException;

use function count;

final class PaytureInPayTerminal implements PaytureInPayTerminalInterface
{
    private const API_PREFIX = 'apim';

    /**
     * @var TerminalConfiguration
     */
    private $config;

    /**
     * @var TransportInterface
     */
    private $transport;

    public function __construct(TerminalConfiguration $config, TransportInterface $transport)
    {
        $this->config    = $config;
        $this->transport = $transport;
    }

    /**
     * @see https://payture.com/api#inpay_init_
     *
     * @param string      $orderId     Payment ID in Merchant system
     * @param int         $amount      Payment amount
     * @param string      $clientIp    User IP address
     * @param string      $url         back URL
     * @param string      $templateTag Used template tag. If empty string - no template tag will be passed
     * @param array       $extra       Payture none requirement extra fields
     * @param SessionType $sessionType
     * @param string      $product
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
    ): TerminalResponse {
        $data = [
            'SessionType' => self::mapSessionType($sessionType),
            'OrderId'     => $orderId,
            'Amount'      => $amount,
            'IP'          => $clientIp,
            'Product'     => $product,
            'Url'         => $url,
        ];

        if ($templateTag !== '') {
            $data['TemplateTag'] = $templateTag;
        }

        if (count($extra)) {
            $data = array_merge($data, $extra);
        }

        $urlParams = [
            'Key'  => $this->config->getKey(),
            'Data' => http_build_query($data, '', ';'),
        ];

        return $this->sendRequest(PaytureOperation::Init, $urlParams);
    }

    /**
     * @see https://payture.com/api#inpay_charge_
     *
     * @param string $order_id Payment ID in Merchant system
     * @param int    $amount   Charging amount in kopecks
     *
     * @throws TransportException
     */
    public function charge(string $order_id, int $amount): TerminalResponse
    {
        $data = [
            'Key'      => $this->config->getKey(),
            'Password' => $this->config->getPassword(),
            'OrderId'  => $order_id,
            'Amount'   => $amount,
        ];

        return $this->sendRequest(PaytureOperation::Charge, $data);
    }

    /**
     * Perform partial or full amount unblock for Block session type.
     *
     * @see https://payture.com/api#inpay_unblock_
     *
     * @param string $order_id Payment ID in Merchant system
     * @param int    $amount   Amount in kopecks that is to be returned
     *
     * @throws TransportException
     */
    public function unblock(string $order_id, int $amount): TerminalResponse
    {
        $data = [
            'Key'      => $this->config->getKey(),
            'Password' => $this->config->getPassword(),
            'OrderId'  => $order_id,
            'Amount'   => $amount,
        ];

        return $this->sendRequest(PaytureOperation::Unblock, $data);
    }

    /**
     * Create new deal to refund given Amount from the OrderId.
     *
     * @see https://payture.com/api#inpay_refund_
     *
     * @param string $orderId Payment ID in Merchant system
     * @param int    $amount  Amount in kopecks that is to be returned
     *
     * @throws TransportException
     */
    public function refund(string $orderId, int $amount): TerminalResponse
    {
        $data = [
            'Key'      => $this->config->getKey(),
            'Password' => $this->config->getPassword(),
            'OrderId'  => $orderId,
            'Amount'   => $amount,
        ];

        return $this->sendRequest(PaytureOperation::Refund, $data);
    }

    /**
     * @deprecated
     * @see PaytureInPayTerminalInterface::getState()
     * @see https://payture.com/api#inpay_paystatus_
     *
     * @param string $orderId Payment ID in Merchant system
     *
     * @throws TransportException
     */
    public function payStatus(string $orderId): TerminalResponse
    {
        $data = [
            'Key'     => $this->config->getKey(),
            'OrderId' => $orderId,
        ];

        return $this->sendRequest(PaytureOperation::PayStatus, $data);
    }

    /**
     * Returns actual order state.
     *
     * @see https://payture.com/api/#inpay_getstate_
     *
     * @param string $orderId Payment ID in Merchant system
     *
     * @throws TransportException
     */
    public function getState(string $orderId): TerminalResponse
    {
        $data = [
            'Key'     => $this->config->getKey(),
            'OrderId' => $orderId,
        ];

        return $this->sendRequest(PaytureOperation::GetState, $data);
    }

    public function createPaymentUrl(string $sessionId): string
    {
        $data = [
            'SessionId' => $sessionId,
        ];

        return $this->config->buildOperationUrl(PaytureOperation::Pay, self::API_PREFIX, $data);
    }

    private static function mapSessionType(SessionType $sessionType): string
    {
        // TODO Just SessionType __toString
        switch ($sessionType) {
            case SessionType::Pay:
                return 'Pay';
            case SessionType::Block:
                return 'Block';
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Unknown session type');
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param PaytureOperation $operation
     * @param array            $parameters
     *
     * @throws InvalidResponseException
     * @throws TransportException
     *
     * @return TerminalResponse
     */
    private function sendRequest(PaytureOperation $operation, array $parameters): TerminalResponse
    {
        $transportResponse = $this->transport->request($operation, self::API_PREFIX, $parameters);

        return TerminalResponseBuilder::parseTransportResponse($transportResponse, $operation);
    }
}
