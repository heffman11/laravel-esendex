<?php

namespace Davidcb\LaravelEsendex;

use \Esendex\Model\Api;

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

    public function create($customerId, $label)
    {
        $uri = \Esendex\Http\UriBuilder::serviceUri(
            self::SERVICE_VERSION,
            self::SERVICE,
            null,
            $this->httpClient->isSecure()
        );

        $xmlRequest = $this->encodePostRequest($customerId, $label);

        $xmlResult = $this->httpClient->post(
                         $uri,
                         $this->authentication,
                         $xmlRequest
                     );

                     dd($xmlResult);

        return $this->parser->parsePostResponse($xmlResult);
    }

    public function encodePostRequest($customerId, $label)
    {
        if (strlen($customerId) < 1) {
            throw new ArgumentException("Customer ID is invalid");
        }
        if (strlen($label) < 1) {
            throw new ArgumentException("Label is invalid");
        }

        $doc = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><account />", 0, false, Api::NS);
        $doc->addAttribute("xmlns", Api::NS);
        $child = $doc->addChild("customerid", $customerId);
        $child = $doc->addChild("label", $label);

        return $doc->asXML();
    }

}
