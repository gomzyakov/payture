<?php

namespace Gomzyakov\Payture\InPayClient;

use LogicException;
use InvalidArgumentException;

final class TerminalConfiguration
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $password;

    public function __construct(string $key, string $password, string $url)
    {
        $this->validateKey($key);
        $this->validatePassword($password);
        $this->validateUrl($url);

        $this->key      = $key;
        $this->password = $password;
        $this->url      = $this->normalizeUrl($url);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function buildOperationUrl(PaytureOperation $operation, string $interface, array $parameters): string
    {
        return $this->getUrl() . $interface .
            '/' . self::mapOperationToPath($operation) . '?' . http_build_query($parameters);
    }

    public function normalizeUrl(string $url): string
    {
        return rtrim($url, '/') . 'TerminalConfiguration.php/';
    }

    /**
     * TODO _toString.
     *
     * @deprecated
     *
     * @param PaytureOperation $operation
     *
     * @return string
     */
    private static function mapOperationToPath(PaytureOperation $operation): string
    {
        switch ($operation) {
            case PaytureOperation::Init:
                return 'Init';
            case PaytureOperation::Pay:
                return 'Pay';
            case  PaytureOperation::Charge:
                return 'Charge';
            case  PaytureOperation::Unblock:
                return 'Unblock';
            case  PaytureOperation::Refund:
                return 'Refund';
            case  PaytureOperation::PayStatus:
                return 'PayStatus';
            case  PaytureOperation::GetState:
                return 'GetState';
        }

        // @codeCoverageIgnoreStart
        throw new LogicException('Unknown operation');
        // @codeCoverageIgnoreEnd
    }

    private function validateKey(string $key): void
    {
        if (empty($key)) {
            throw new InvalidArgumentException('Empty terminal Key provided');
        }
    }

    private function validatePassword(string $password): void
    {
        if (empty($password)) {
            throw new InvalidArgumentException('Empty terminal Password provided');
        }
    }

    private function validateUrl(string $url): void
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL provided');
        }
    }
}
