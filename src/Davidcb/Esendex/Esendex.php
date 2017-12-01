<?php

namespace Davidcb\Esendex;

class Esendex
{
    protected $app;
    protected $authentication;

    public function __construct($app)
    {
        $this->app = $app;
        $this->authentication = $this->getAuthentication();
    }

    /**
     * Sends a message using the Esendex API
     * @param  string $from      Sender's name (max 10 characters)
     * @param  string $recipient Recipient's telephone number
     * @param  string $text      SMS's body
     * @return object            response
     */
    public function sendMessage($from, $recipient, $text)
    {
        $message = new \Esendex\Model\DispatchMessage(
            $from,
            $recipient,
            $text,
            \Esendex\Model\Message::SmsType
        );
        $service = new \Esendex\DispatchService($this->authentication);
        return $service->send($message);
    }

    /**
     * Returns inbox messages
     * @return object|null       An object with the inbox messages
     */
    public function inboxMessages()
    {
        $service = new \Esendex\InboxService($this->authentication);
        return $service->latest();
    }

    /**
     * Returns a given message status
     * @param  string $messageId The message's identifier
     * @return string|null       The message's status string
     */
    public function messageStatus($messageId)
    {
        $headerService = new \Esendex\MessageHeaderService($authentication);
        $message = $headerService->message($messageId);
        return $message ? $message->status() : null;
    }

    /**
     * Returns a given message body
     * @param  string $messageId The message's identifier
     * @return string            The message's full body
     */
    public function messageBody($messageId)
    {
        $service = new \Esendex\MessageBodyService($authentication);
        return $service->getMessageBodyById($messageId);
    }

    protected function getAuthentication()
    {
        return new \Esendex\Authentication\LoginAuthentication(
            $this->app['config']['esendex.account_id'],
            $this->app['config']['esendex.email'],
            $this->app['config']['esendex.password']
        );
    }
}
