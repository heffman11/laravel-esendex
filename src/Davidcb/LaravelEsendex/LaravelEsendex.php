<?php

namespace Davidcb\LaravelEsendex;

class LaravelEsendex
{
    protected $app;
    protected $authentication;

    public function __construct($app)
    {
        $this->app = $app;
        $this->authenticate();
    }

    /**
     * Gives the ability to authenticate with different credentials if we
     * have multiple accounts on our application. If not, it authenticates
     * with the credentials on the config file.
     * @param  string|null $reference
     * @param  string|null $email
     * @param  string|null $password
     * @return null
     */
    public function authenticate($reference = null, $email = null, $password = null) {
        $this->authentication = $this->getAuthentication($reference, $email, $password);
    }

    /**
     * Sends a message using the Esendex API
     * @param  string $from              Sender's name (max 10 characters)
     * @param  string $recipient         Recipient's telephone number
     * @param  string $text              SMS's body
     * @return \EsendexSdk\Model\ResultItem response
     */
    public function send($from, $recipient, $text)
    {
        $message = new \Esendex\Model\DispatchMessage(
            $from,
            $recipient,
            $text,
            \EsendexSdk\Model\Message::SmsType
        );
        $service = new \EsendexSdk\DispatchService($this->authentication);
        return $service->send($message);
    }

    /**
     * Returns the number of credits on the current account
     * @return int
     */
    public function getCredits()
    {
        $service = new \EsendexSdk\DispatchService($this->authentication);
        return $service->getCredits();
    }

    /**
     * Returns latest inbox messages
     * @param int|null $startIndex
     * @param int|null $count
     * @return \EsendexSdk\Model\InboxPage|null
     */
    public function latest($startIndex = null, $count = null)
    {
        $service = new \EsendexSdk\InboxService($this->authentication);
        return $service->latest($startIndex, $count);
    }

    /**
     * Deletes an inbox message given its identifier
     * @param  string $messageId
     * @return bool
     */
    public function deleteInboxMessage($messageId)
    {
        $service = new \EsendexSdk\InboxService($this->authentication);
        return $service->deleteInboxMessage($messageId);
    }

    /**
     * Update the read status of an inbox message given its identifier
     * @param  string $messageId [description]
     * @param  bool   $read
     * @return bool
     */
    public function updateReadStatus($messageId, $read = true)
    {
        $service = new \EsendexSdk\InboxService($this->authentication);
        return $service->updateReadStatus($messageId, $read);
    }

    /**
     * Returns a given message status
     * @param  string $messageId The message's identifier
     * @return string|null
     */
    public function messageStatus($messageId)
    {
        $headerService = new \EsendexSdk\MessageHeaderService($authentication);
        $message = $headerService->message($messageId);
        return $message ? $message->status() : null;
    }

    /**
     * Returns a given message body
     * @param  string $messageId The message's identifier
     * @return string
     */
    public function getMessageBodyById($messageId)
    {
        $service = new \EsendexSdk\MessageBodyService($authentication);
        return $service->getMessageBodyById($messageId);
    }

    /**
     * Returns latest sent messages
     * @param int|null $startIndex
     * @param int|null $count
     * @return \EsendexSdk\Model\SentMessagesPage|null
     */
    public function latestSent($startIndex = null, $count = null)
    {
        $service = new \EsendexSdk\SentMessagesService($this->authentication);
        return $service->latest($startIndex, $count);
    }

    /**
     * Adds a telephone number to the opt-out list
     * @param string $accountReference
     * @param string $mobileNumber
     * @return \EsendexSdk\Model\OptOut
     */
    public function addToOptOut($accountReference, $mobileNumber)
    {
        $service = new \EsendexSdk\OptOutsService($this->authentication);
        return $service->add($accountReference, $mobileNumber);
    }

    /**
     * Adds a telephone number to the opt-out list
     * @param int $pageNumber
     * @param int $pageSize
     * @return array
     */
    public function getOptOutList($pageNumber = null, $pageSize = null)
    {
        $service = new \EsendexSdk\OptOutsService($this->authentication);
        return $service->get($pageNumber, $pageSize);
    }

    /**
     * Returns the account with the given reference or the authenticated
     * account reference if it exists
     * @param  string $reference The account reference
     * @return \EsendexSdk\Model\Account|null
     */
    public function getAccount($reference = null)
    {
        $service = new \EsendexSdk\AccountService($this->authentication);
        return $service->getAccount($reference);
    }

    /**
     * Returns the accounts within the authenticated account
     * @return \EsendexSdk\Model\Account
     */
    public function getAccounts()
    {
        $service = new \Esendex\AccountService($this->authentication);
        return $service->getAccounts();
    }

    /**
     * Gets the Esendex Authentication object for given credentials
     * @param  string $reference Account reference
     * @param  string $email     Authentication email address
     * @param  string $password  Authentication password
     * @return \EsendexSdk\Authentication\LoginAuthentication
     */
    protected function getAuthentication($reference = null, $email = null, $password = null)
    {
        return new \EsendexSdk\Authentication\LoginAuthentication(
            $reference ?? $this->app['config']['esendex.account_id'],
            $email ?? $this->app['config']['esendex.email'],
            $password ?? $this->app['config']['esendex.password']
        );
    }
}
