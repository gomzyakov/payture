<?php

namespace Gomzyakov\Payture\InPayClient\GuzzleHttp;

use Gomzyakov\Payture\InPayClient\PaytureOperation;

/**
 * @deprecated
 */
final class GuzzleHttpOptionsBag
{
    private static ?array $requestOptions = null;

    /**
     * @var array<string, mixed>
     */
    private array $optionsPerOperation;

    /**
     * @var array<string, mixed>
     */
    private array $options;

    public function __construct(array $options = [], array $optionsPerOperation = [])
    {
        $this->options             = $options;
        $this->optionsPerOperation = $optionsPerOperation;
    }

    public function getOperationOptions(PaytureOperation $operation): array
    {
        return array_merge(
            $this->options,
            $this->optionsPerOperation[$operation->name] ?? []
        );
    }
}
