<?php

namespace Gomzyakov\Payture\InPayClient\GuzzleHttp;

use GuzzleHttp\RequestOptions;
use Gomzyakov\Payture\InPayClient\PaytureOperation;
use ReflectionClass;
use ReflectionException;
use InvalidArgumentException;

use function count;

final class GuzzleHttpOptionsBag
{
    private static $requestOptions;

    private static $operations;

    /**
     * @var array[]
     */
    private $optionsPerOperation;

    /**
     * @var array
     */
    private $options;

    public function __construct(array $options = [], array $optionsPerOperation = [])
    {
        $this->assertOptionsPerOperation($optionsPerOperation);
        $this->assertOptions($options);

        $this->options             = $options;
        $this->optionsPerOperation = $optionsPerOperation;
    }

    public function getOperationOptions(PaytureOperation $operation): array
    {
        return array_merge(
            $this->options,
            $this->optionsPerOperation[(string) $operation] ?? []
        );
    }

    /**
     * Get operations that Payture can handle.
     *
     * @return string[]
     */
    private static function getAvailableOperations(): array
    {
        if (self::$operations === null) {
            self::$operations = PaytureOperation::getConstList();
        }

        return self::$operations;
    }

    /**
     * Get request options for client.
     *
     * @return string[]
     */
    private static function getAvailableOptions(): array
    {
        if (self::$requestOptions === null) {
            try {
                self::$requestOptions = (new ReflectionClass(RequestOptions::class))->getConstants();
                //@codeCoverageIgnoreStart
            } catch (ReflectionException $e) {
                trigger_error('PaytureClient requires ' . RequestOptions::class . ' to be available for auto-loading');

                self::$requestOptions = [];

                //@codeCoverageIgnoreEnd
            }
        }

        return self::$requestOptions;
    }

    /**
     * Assert that client options are correct.
     *
     * @param array $options
     */
    private function assertOptions(array $options): void
    {
        $this->assertValidFields(
            array_keys($options),
            self::getAvailableOptions(),
            'Invalid option keys: %s.'
        );
    }

    /**
     * Assert that fields include only valid.
     *
     * @param array  $fields
     * @param array  $validFields
     * @param string $message
     */
    private function assertValidFields(array $fields, array $validFields, string $message): void
    {
        $invalidFields = array_diff($fields, $validFields);
        if (! count($invalidFields)) {
            return;
        }

        throw new InvalidArgumentException(sprintf($message, implode(', ', $invalidFields)));
    }

    /**
     * Assert that options per operations have correct options and operations.
     *
     * @param array $optionsPerOperation
     */
    private function assertOptionsPerOperation(array $optionsPerOperation): void
    {
        $this->assertValidFields(
            array_keys($optionsPerOperation),
            self::getAvailableOperations(),
            'Invalid Payture operations: %s.'
        );

        array_map([$this, 'assertOptions'], $optionsPerOperation);
    }
}
