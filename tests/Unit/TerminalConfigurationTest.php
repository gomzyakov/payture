<?php

namespace Gomzyakov\Payture\InPayClient\Tests\Unit;

use Gomzyakov\Payture\InPayClient\PaytureOperation;
use Gomzyakov\Payture\InPayClient\TerminalConfiguration;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * @covers \Gomzyakov\Payture\InPayClient\TerminalConfiguration
 */
final class TerminalConfigurationTest extends TestCase
{
    public function test_valid_config(): void
    {
        $config = new TerminalConfiguration('secret', 'pass', 'https://nowhere.payture.com');

        $this->assertInstanceOf(TerminalConfiguration::class, $config);

        $this->assertEquals('secret', $config->getKey());
        $this->assertEquals('https://nowhere.payture.com/', $config->getUrl());
        $this->assertEquals('pass', $config->getPassword());
    }

    /**
     * @dataProvider notValidConfigVariants
     *
     * @param array  $options
     * @param string $exception
     * @param string $message
     */
    public function test_not_valid_config(array $options, string $exception, string $message): void
    {
        $this->expectExceptionMessage($message);
        $this->expectException($exception);

        new TerminalConfiguration($options['key'], $options['password'], $options['url']);
    }

    public function notValidConfigVariants(): array
    {
        return [
            // required validation
            [
                [
                    'key'      => '',
                    'url'      => 'test',
                    'password' => 'test',
                ],
                InvalidArgumentException::class,
                'Empty terminal Key provided',
            ],
            [
                [
                    'key'      => 'test',
                    'url'      => '',
                    'password' => 'test',
                ],
                InvalidArgumentException::class,
                'Invalid URL provided',
            ],
            [
                [
                    'key'      => 'test',
                    'url'      => 'test',
                    'password' => '',
                ],
                InvalidArgumentException::class,
                'Empty terminal Password provided',
            ],
            // format validation
            [
                [
                    'key'      => 'secret',
                    'url'      => 'payture.com',
                    'password' => 'pass',
                ],
                InvalidArgumentException::class,
                'Invalid URL provided',
            ],
        ];
    }

    /**
     * @dataProvider getOperationUrlProviders
     *
     * @param PaytureOperation $operation
     * @param array            $parameters
     * @param string           $expected_url
     */
    public function test_building_operation_url(PaytureOperation $operation, array $parameters, string $expected_url): void
    {
        $configuration = new TerminalConfiguration('MerchantKey', 'MerchantPassword', 'https://nowhere.payture.com/');
        $url           = $configuration->buildOperationUrl($operation, 'apim', $parameters);

        self::assertSame($expected_url, $url);
    }

    public function getOperationUrlProviders(): array
    {
        return [
            [
                PaytureOperation::Init,
                ['Key' => 'MerchantKey', 'Data' => 'SomeData'],
                'https://nowhere.payture.com/apim/Init?Key=MerchantKey&Data=SomeData',
            ],
            [
                PaytureOperation::Pay,
                ['SessionId' => 'external-id'],
                'https://nowhere.payture.com/apim/Pay?SessionId=external-id',
            ],
            [
                PaytureOperation::Charge,
                ['Key' => 'MerchantKey', 'Data' => 'SomeData'],
                'https://nowhere.payture.com/apim/Charge?Key=MerchantKey&Data=SomeData',
            ],
            [
                PaytureOperation::Refund,
                ['Key' => 'MerchantKey', 'Data' => 'SomeData'],
                'https://nowhere.payture.com/apim/Refund?Key=MerchantKey&Data=SomeData',
            ],
            [
                PaytureOperation::Unblock,
                ['Key' => 'MerchantKey', 'Data' => 'SomeData'],
                'https://nowhere.payture.com/apim/Unblock?Key=MerchantKey&Data=SomeData',
            ],
            [
                PaytureOperation::PayStatus,
                ['Key' => 'MerchantKey', 'Data' => 'SomeData'],
                'https://nowhere.payture.com/apim/PayStatus?Key=MerchantKey&Data=SomeData',
            ],
            [
                PaytureOperation::GetState,
                ['Key' => 'MerchantKey', 'Data' => 'SomeData'],
                'https://nowhere.payture.com/apim/GetState?Key=MerchantKey&Data=SomeData',
            ],
        ];
    }
}
