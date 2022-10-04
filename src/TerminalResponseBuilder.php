<?php

namespace Gomzyakov\Payture\InPayClient;

use Gomzyakov\Payture\InPayClient\Exception\InvalidResponseException;
use SimpleXMLElement;

/**
 * @internal
 */
final class TerminalResponseBuilder
{
    /**
     * @param string           $transport_response
     * @param PaytureOperation $operation
     *
     * @throws InvalidResponseException
     *
     * @return TerminalResponse
     */
    public static function parseTransportResponse(
        string $transport_response,
        PaytureOperation $operation
    ): TerminalResponse {
        $attributes = self::parseAttributesFromXmlResponse($transport_response, $operation->name);

        // TODO Этой проверки быть не должно
        if (! isset($attributes['Success']) || ! is_string($attributes['Success'])) {
            throw InvalidResponseException::becauseUndefinedSuccessAttribute();
        }

        $result = new TerminalResponse($attributes['Success'], $attributes['OrderId'] ?? '');

        if (isset($attributes['SessionId'])) {
            $result->setSessionId((string) $attributes['SessionId']);
        }

        if (isset($attributes['Amount'])) {
            $result->setAmount((int) $attributes['Amount']);
        }

        if (isset($attributes['NewAmount'])) {
            $result->setAmount((int) $attributes['NewAmount']);
        }

        if (isset($attributes['State'])) {
            $result->setState($attributes['State']);
        }

        if (isset($attributes['ErrCode'])) {
            $result->setErrorCode($attributes['ErrCode']);
        }

        if (isset($attributes['RRN'])) {
            $result->setRrn($attributes['RRN']);
        }

        return $result;
    }

    /**
     * @param string $xml
     * @param string $operation
     *
     * @throws InvalidResponseException
     *
     * @return array<mixed>
     */
    private static function parseAttributesFromXmlResponse(string $xml, string $operation): array
    {
        $oldUseInternalXmlErrors = libxml_use_internal_errors(true);
        $rootNode                = simplexml_load_string($xml);
        libxml_use_internal_errors($oldUseInternalXmlErrors);

        if (! $rootNode instanceof SimpleXMLElement) {
            throw InvalidResponseException::becauseInvalidXML();
        }

        if (mb_strtolower($rootNode->getName()) !== mb_strtolower($operation)) {
            throw InvalidResponseException::becauseRootTagMismatch($rootNode->getName(), $operation);
        }

        $data = (array) $rootNode;

        if (! isset($data['@attributes'])) {
            throw InvalidResponseException::becauseEmptyAttributes();
        }

        return $data['@attributes'];
    }
}
