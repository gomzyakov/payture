<?php

namespace Gomzyakov\Payture\TestUtils;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use DOMDocument;
use DOMXPath;
use LogicException;
use DOMNodeList;
use DOMNode;

use function json_decode;

final class PaymentHelper extends Assert
{
    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $orderNr
     * @param int    $amount
     * @param string $paymentUrl
     * @param Card   $card
     * @param string $sandboxUrl
     *
     * @throws GuzzleException
     */
    public function pay(
        string $orderNr,
        int $amount,
        string $paymentUrl,
        Card $card,
        string $sandboxUrl
    ): void {
        $this->sendPayment(
            $paymentUrl,
            $sandboxUrl,
            array_merge(
                [
                    'OrderId'     => $orderNr,
                    'Amount'      => $amount,
                    'TemplateTag' => 'json',
                ],
                self::formatCard($card)
            )
        );
    }

    /**
     * @param Card $card
     *
     * @return array<string, mixed>
     */
    private static function formatCard(Card $card): array
    {
        return [
            'cardNumber' => $card->getCardNumber(),
            'SecureCode' => $card->getSecureCode(),
            'EYear'      => $card->getExpirationYear(),
            'EMonth'     => $card->getExpirationMonth(),
            'CardHolder' => $card->getCardHolder(),
        ];
    }

    /**
     * @param ResponseInterface $response
     *
     * @return void
     */
    private static function assertPaytureAcceptedCard(ResponseInterface $response): void
    {
        $body = json_decode($response->getBody(), true);

        if (! is_array($body)) {
            throw new LogicException('Can`t decode JSON-string');
        }

        self::assertEquals(200, $response->getStatusCode(), 'Wrong status code.');
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'), 'Wrong content.');
        self::assertEquals(true, $body['Success'], "Payment failure: {$response->getBody()}.");
    }

    /**
     * @param string[]          $names
     * @param ResponseInterface $response
     *
     * @return array<string, string|null>
     */
    private static function getInputValues(ResponseInterface $response, array $names): array
    {
        $html = (string) $response->getBody();
        $dom  = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);
        $xpath = new DOMXPath($dom);

        $values = array_map(
            function (string $name) use ($xpath) {
                $values = $xpath->query('//input[@name="' . $name . '"]/@value');

                if (! $values instanceof DOMNodeList) {
                    throw new LogicException('Not instance of DOMNodeList');
                }

                $item = $values->item(0);
                if (! $item instanceof DOMNode) {
                    throw new LogicException('Not instance of DOMNode');
                }

                return $item->nodeValue;
            },
            $names
        );

        return array_combine($names, $values);
    }

    /**
     * @param string               $paymentUrl
     * @param string               $sandboxUrl
     * @param array<string, mixed> $data
     *
     * @throws GuzzleException
     */
    private function sendPayment(string $paymentUrl, string $sandboxUrl, array $data): void
    {
        $response = $this->client->request('GET', $paymentUrl);

        self::assertEquals($response->getStatusCode(), 200, "Can't open payment page: {$paymentUrl}.");

        // Send request to sandbox with secret key:
        $response = $this->client->request(
            'POST',
            $sandboxUrl,
            [
                RequestOptions::COOKIES => new CookieJar(true, []),
                RequestOptions::HEADERS => [
                    'Referer'          => $paymentUrl,
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
                RequestOptions::FORM_PARAMS => [
                    'Data' => http_build_query(
                        array_merge(
                            $data,
                            self::getInputValues($response, ['Key'])
                        ),
                        '',
                        ';'
                    ),
                    'Json' => 'true',
                ],
            ]
        );

        self::assertPaytureAcceptedCard($response);
    }
}
