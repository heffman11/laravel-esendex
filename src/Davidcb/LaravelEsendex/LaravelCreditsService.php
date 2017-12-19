<?php

namespace Davidcb\LaravelEsendex;

use \Esendex\Model\Api;
use \Esendex\Model\Account;

class LaravelCreditsService extends \Esendex\AccountService {

    private $authentication;
    private $httpClient;

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

        $uri = str_replace('api', 'admin.api', $uri);

        $jsonRequest = $this->encodePostRequest($fromReference, $toReference, $quantity);

        $result = $this->httpClient->postJson(
                         $uri,
                         $this->authentication,
                         $jsonRequest
                     );

        if ($result instanceof BadRequestException) {
            return false;
        }

        return true;
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

        $args = ['from' => $fromReference, 'to' => $toReference, 'quantity' => $quantity];

        return json_encode($args);
    }

}
