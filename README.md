# Payture InPay Client

Simple PHP client for [Payture InPay API](https://payture.com/en/api/#inpay_).

**InPay** is an easy way to accept payments without saving cards. Customer’s card details are entered at Payture
gateway web page and all required data security is provided by Payture, saving Merchant efforts and expenses related
to card information protection.

![](https://img.shields.io/github/v/release/gomzyakov/payture)
[![codecov](https://codecov.io/gh/gomzyakov/payture/branch/main/graph/badge.svg?token=Pl2pgKZ5os)](https://codecov.io/gh/gomzyakov/payture)

## Installation

```bash
composer require gomzyakov/payture
```

## Usage

```php
<?php

$configuration = new \Gomzyakov\Payture\TerminalConfiguration(
    'MerchantKey',
    'MerchantPassword',
    'https://sandbox.payture.com/'
);

$transport = new \Gomzyakov\Payture\GuzzleHttp\GuzzleHttpPaytureTransport(
    new \GuzzleHttp\Client(),
    $configuration
);

$terminal = new \Gomzyakov\Payture\PaytureInPayTerminal($configuration, $transport);

$terminal->charge('ORDER_NUMBER_123', 100500);
```

## Tuning

### Client configuration

You can pass 3-rd argument to the GuzzleHttpPaytureTransport with the instance of `\Gomzyakov\Payture\GuzzleHttp\GuzzleHttpOptionsBag`.
Instance is preconfigured with guzzle `\GuzzleHttp\RequestOptions` both global (first constructor argument) and per-operation
(second constructor argument indexed by operation name)

### Logging

You can pass 4-th argument to the GuzzleHttpPaytureTransport with instance of PSR-3 `LoggerInterface`
in order to log operations with parameters.

Also you can configure generic Guzzle [logging middleware](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html).

## Testing

```bash
vendor/bin/phpunit -c phpunit.xml
```

You can test this library against your own test terminal providing `PAYTURE_TEST_MERCHANT_KEY` and `PAYTURE_TEST_MERCHANT_PASSWORD`
environment variables while running the tests.

```bash
 PAYTURE_TEST_MERCHANT_KEY=MerchantKey \
 PAYTURE_TEST_MERCHANT_PASSWORD=MerchantPassword \
 vendor/bin/phpunit -c phpunit.xml
```

These test will run order processing sequence against payture sandbox.
