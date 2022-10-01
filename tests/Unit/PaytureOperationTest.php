<?php

namespace Gomzyakov\Payture\InPayClient\Tests\Unit;

use Gomzyakov\Payture\InPayClient\PaytureOperation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gomzyakov\Payture\InPayClient\PaytureOperation
 */
final class PaytureOperationTest extends TestCase
{
    public function testToStringReturnsOperationName(): void
    {
        $operation = PaytureOperation::INIT();
        self::assertSame('Init', (string) $operation);
    }
}
