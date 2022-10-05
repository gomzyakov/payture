<?php

namespace Gomzyakov\Payture;

use InvalidArgumentException;

final class TerminalConfiguration
{
    /**
     * @var string
     */
    private string $url;

    /**
     * @var string
     */
    private string $key;

    /**
     * @var string
     */
    private string $password;

    /**
     * @param string $key      TODO
     * @param string $password TODO
     * @param string $url      TODO
     */
    public function __construct(string $key, string $password, string $url)
    {
        // TODO Remove validation
        $this->validateKey($key);
        $this->validatePassword($password);
        $this->validateUrl($url);

        $this->key      = $key;
        $this->password = $password;
        $this->url      = $this->normalizeUrl($url);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * TODO Заменить все array<mixed> на конкретику.
     *
     * @param PaytureOperation $operation
     * @param string           $interface
     * @param array<mixed>     $parameters
     *
     * @return string
     */
    public function buildOperationUrl(PaytureOperation $operation, string $interface, array $parameters): string
    {
        return $this->getUrl() . $interface . '/' . $operation->name . '?' . http_build_query($parameters);
    }

    public function normalizeUrl(string $url): string
    {
        return rtrim($url, '/') . '/';
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
