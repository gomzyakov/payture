<?php

namespace Tests\Unit;

use Gomzyakov\Payture\PaytureOperation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gomzyakov\Payture\PaytureOperation
 */
final class PaytureOperationTest extends TestCase
{
    public function test_to_string_returns_operation_name(): void
    {
        $operation = PaytureOperation::Init;
        self::assertSame('Init', $operation->name);
    }
}
