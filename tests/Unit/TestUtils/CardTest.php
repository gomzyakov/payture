<?php

namespace Tests\Unit\TestUtils;

use Gomzyakov\Payture\TestUtils\Card;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Gomzyakov\Payture\TestUtils\Card
 */
final class CardTest extends TestCase
{
    public function test_empty_bag(): void
    {
        $card = new Card(
            $card_number      = '1111222233334444',
            $secure_code      = '123',
            $expiration_year  = '2023',
            $expiration_month = '12',
            $card_holder      = 'ALEXANDER GOMZYAKOV'
        );

        self::assertSame($card_number, $card->getCardNumber());
        self::assertSame($secure_code, $card->getSecureCode());
        self::assertSame($expiration_year, $card->getExpirationYear());
        self::assertSame($expiration_month, $card->getExpirationMonth());
        self::assertSame($card_holder, $card->getCardHolder());
    }
}
