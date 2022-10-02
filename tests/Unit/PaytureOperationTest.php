<?php

namespace Gomzyakov\Payture\InPayClient\Tests\Unit;

use Gomzyakov\Payture\InPayClient\PaytureOperation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gomzyakov\Payture\InPayClient\PaytureOperation
 */
final class PaytureOperationTest extends TestCase
{
    public function test_to_string_returns_operation_name(): void
    {
        $operation = PaytureOperation::Init;
        self::assertSame('Init', (string) $operation);
    }
}
