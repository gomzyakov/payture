<?php

namespace Gomzyakov\Payture\InPayClient\GuzzleHttp;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Gomzyakov\Payture\InPayClient\Exception\TransportException;
use Gomzyakov\Payture\InPayClient\PaytureOperation;
use Gomzyakov\Payture\InPayClient\TerminalConfiguration;
use Gomzyakov\Payture\InPayClient\TransportInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class GuzzleHttpPaytureTransport implements TransportInterface
{
    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    /**
     * @var TerminalConfiguration
     */
    private TerminalConfiguration $config;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var GuzzleHttpOptionsBag
     */
    private GuzzleHttpOptionsBag $optionsBag;

    /**
     * @param ClientInterface           $client
     * @param TerminalConfiguration     $config
     * @param GuzzleHttpOptionsBag|null $optionsBag
     * @param LoggerInterface|null      $logger
     */
    public function __construct(
        ClientInterface $client,
        TerminalConfiguration $config,
        ?GuzzleHttpOptionsBag $optionsBag = null,
        ?LoggerInterface $logger = null
    ) {
        $this->client     = $client;
        $this->config     = $config;
        $this->optionsBag = $optionsBag ?: new GuzzleHttpOptionsBag();
        $this->logger     = $logger ?: new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function request(PaytureOperation $operation, string $interface, array $parameters): string
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info(
                'Executing Payture InPay operation',
                [
                    'interface'  => $interface,
                    'url'        => $this->config->getUrl(),
                    'operation'  => $operation->name,
                    'url_params' => $parameters,
                ]
            );
        }

        try {
            return $this->client->request(
                'GET',
                $this->config->buildOperationUrl($operation, $interface, $parameters),
                $this->optionsBag->getOperationOptions($operation)
            )->getBody();
        } catch (GuzzleException $e) {
            throw TransportException::becauseUnderlyingTransportFailed($e);
        }
    }
}
