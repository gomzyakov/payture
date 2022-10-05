<?php

namespace Gomzyakov\Payture\GuzzleHttp;

use Gomzyakov\Payture\PaytureOperation;

/**
 * @deprecated
 */
final class GuzzleHttpOptionsBag
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $optionsPerOperation;

    /**
     * @var array<string, mixed>
     */
    private array $options;

    /**
     * @param array<string, mixed>                $options
     * @param array<string, array<string, mixed>> $optionsPerOperation
     */
    public function __construct(array $options = [], array $optionsPerOperation = [])
    {
        $this->options             = $options;
        $this->optionsPerOperation = $optionsPerOperation;
    }

    /**
     * @param PaytureOperation $operation
     *
     * @return array<string, mixed>
     */
    public function getOperationOptions(PaytureOperation $operation): array
    {
        return array_merge(
            $this->options,
            $this->optionsPerOperation[$operation->name] ?? []
        );
    }
}
