<?php

namespace Tests\Unit\GuzzleHttp;

use Gomzyakov\Payture\GuzzleHttp\GuzzleHttpOptionsBag;
use Gomzyakov\Payture\PaytureOperation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gomzyakov\Payture\GuzzleHttp\GuzzleHttpOptionsBag
 */
final class GuzzleHttpOptionsBagTest extends TestCase
{
    public function test_empty_bag(): void
    {
        $bag = new GuzzleHttpOptionsBag();
        self::assertEmpty($bag->getOperationOptions(PaytureOperation::Init));
    }

    public function test_option_mering(): void
    {
        $bag = new GuzzleHttpOptionsBag(['timeout' => 5], ['Init' => ['timeout' => 15]]);
        self::assertEquals(['timeout' => 5], $bag->getOperationOptions(PaytureOperation::Charge));
        self::assertEquals(['timeout' => 15], $bag->getOperationOptions(PaytureOperation::Init));
    }
}
