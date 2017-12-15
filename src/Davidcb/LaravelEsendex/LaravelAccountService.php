<?php

namespace Davidcb\LaravelEsendex;

use \Esendex\Model\Api;
use \Esendex\Model\Account;

class LaravelAccountService extends \Esendex\AccountService {

    private $authentication;
    private $httpClient;
    private $parser;

    const SERVICE = "accounts";
    const SERVICE_VERSION = "v1.0";

    public function __construct(\Esendex\Authentication\IAuthentication $authentication,
        \Esendex\Http\IHttp $httpClient = null,
        \Esendex\Parser\AccountXmlParser $parser = null)
    {
        $this->authentication = $authentication;
        $this->httpClient = (isset($httpClient))
            ? $httpClient
            : new \Esendex\Http\HttpClient(true);

        $this->parser = (isset($parser))
            ? $parser
            : new \Esendex\Parser\AccountXmlParser();
    }

    public function create($label)
    {
        $uri = \Esendex\Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            null,
            $this->httpClient->isSecure()
        );

        $xmlRequest = $this->encodePostRequest($label);

        $xmlResult = $this->httpClient->post(
                         $uri,
                         $this->authentication,
                         $xmlRequest
                     );

        return $this->parseAccount($xmlResult);
    }

    private function encodePostRequest($label)
    {
        if (strlen($label) < 1) {
            throw new ArgumentException("Label is invalid");
        }

        $doc = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><account />", 0, false, Api::NS);
        $doc->addAttribute("xmlns", Api::NS);
        $child = $doc->addChild("label", $label);

        return $doc->asXML();
    }

    private function parseAccount($account)
    {
        $account = simplexml_load_string($account);
        $result = new Account();
        $result->id($account["id"]);
        $result->reference($account->reference);
        $result->label($account->label);
        $result->address($account->address);
        $result->alias($account->alias);
        $result->type($account->type);
        $result->messagesRemaining(intval($account->messagesremaining, 10));
        $result->expiresOn($this->parseDateTime($account->expireson));
        $result->defaultDialCode($account->defaultdialcode);
        return $result;
    }

    private function parseDateTime($value)
    {
        $value = (strlen($value) < 20)
            ? $value . "Z"
            : substr($value, 0, 19) . "Z";
        return \DateTime::createFromFormat(\DateTime::ISO8601, $value);
    }

}
