<?php

namespace Gomzyakov\Payture\InPayClient\Tests\Unit\GuzzleHttp;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use Gomzyakov\Payture\InPayClient\GuzzleHttp\GuzzleHttpPaytureTransport;
use Gomzyakov\Payture\InPayClient\PaytureOperation;
use Gomzyakov\Payture\InPayClient\TerminalConfiguration;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Gomzyakov\Payture\InPayClient\GuzzleHttp\GuzzleHttpPaytureTransport
 */
final class GuzzleHttpPaytureTransportTest extends TestCase
{
    public function test_operation_execution(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $config = new TerminalConfiguration('MerchantKey', 'MerchantPassword', 'https://nowhere.payture.com/');

        $transport = new GuzzleHttpPaytureTransport($client, $config, null, $logger);

        $response = '<Init Success="True"/>';
        $client->expects($this->once())
            ->method('request')
            ->with('GET', 'https://nowhere.payture.com/apim/Init?', [])
            ->willReturn(new Response(200, [], $response));

        $logger->expects($this->once())
            ->method('info');

        self::assertEquals($response, $transport->request(PaytureOperation::INIT(), 'apim', []));
    }

    /**
     * @expectedException \Gomzyakov\Payture\InPayClient\Exception\TransportException
     */
    public function test_transport_converts_guzzle_exception_to_transport_exception(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $config = new TerminalConfiguration('MerchantKey', 'MerchantPassword', 'https://nowhere.payture.com/');

        $transport = new GuzzleHttpPaytureTransport($client, $config);

        $client->expects($this->once())
            ->method('request')
            ->with('GET', 'https://nowhere.payture.com/apim/Init?', [])
            ->willThrowException(new TransferException('Request failed'));

        $transport->request(PaytureOperation::INIT(), 'apim', []);
    }
}
