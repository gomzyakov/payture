<?php

namespace Gomzyakov\Payture\InPayClient\Tests\Unit\GuzzleHttp;

use Gomzyakov\Payture\InPayClient\GuzzleHttp\GuzzleHttpOptionsBag;
use Gomzyakov\Payture\InPayClient\PaytureOperation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gomzyakov\Payture\InPayClient\GuzzleHttp\GuzzleHttpOptionsBag
 */
final class GuzzleHttpOptionsBagTest extends TestCase
{
    public function test_empty_bag(): void
    {
        $bag = new GuzzleHttpOptionsBag();
        self::assertEmpty($bag->getOperationOptions(PaytureOperation::INIT()));
    }

    public function test_option_mering(): void
    {
        $bag = new GuzzleHttpOptionsBag(['timeout' => 5], ['Init' => ['timeout' => 15]]);
        self::assertEquals(['timeout' => 5], $bag->getOperationOptions(PaytureOperation::CHARGE()));
        self::assertEquals(['timeout' => 15], $bag->getOperationOptions(PaytureOperation::INIT()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_invalid_option_causes_validation_exception(): void
    {
        new GuzzleHttpOptionsBag(['invalid option' => 5]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_invalid_operation_causes_validation_exception(): void
    {
        new GuzzleHttpOptionsBag([], ['Init' => ['invalid option' => 5]]);
    }
}
