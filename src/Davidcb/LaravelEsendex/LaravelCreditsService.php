<?php

namespace Davidcb\LaravelEsendex;

use \Esendex\Model\Api;
use \Esendex\Model\Account;

class LaravelCreditsService extends \Esendex\AccountService {

    private $authentication;
    private $httpClient;
    private $parser;

    const SERVICE = "credits";
    const SERVICE_VERSION = "v1.0";

    public function __construct(\Esendex\Authentication\IAuthentication $authentication,
        \Esendex\Http\IHttp $httpClient = null)
    {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new \Esendex\Http\HttpClient(true);
    }

    public function move($fromReference, $toReference, $quantity)
    {
        $uri = \Esendex\Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            'move',
            $this->httpClient->isSecure()
        );

        $jsonRequest = $this->encodePostRequest($fromReference, $toReference, $quantity);

        $result = $this->httpClient->post(
                         $uri,
                         $this->authentication,
                         $jsonRequest
                     );

        return $result;
    }

    private function encodePostRequest($fromReference, $toReference, $quantity)
    {
        if (strlen($fromReference) < 1) {
            throw new ArgumentException("From reference is invalid");
        }
        if (strlen($toReference) < 1) {
            throw new ArgumentException("To reference is invalid");
        }
        if (strlen($quantity) < 1 || $quantity < 1) {
            throw new ArgumentException("Quantity is invalid");
        }

        $args = ['from' => $fromReference, 'to' => $toReference, 'quantity' => $quantity, 'orderby' => 'expiry_asc'];

        return json_encode($args);
    }

    private function parseDateTime($value)
    {
        $value = (strlen($value) < 20)
            ? $value . "Z"
            : substr($value, 0, 19) . "Z";
        return \DateTime::createFromFormat(\DateTime::ISO8601, $value);
    }

}
