<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\TemplatedEmailer;

use Symfony\Component\Templating\EngineInterface;
use Swift_Mailer;
use Swift_Message;
use RuntimeException;

class TemplatedEmailer
{
    /**
     * @var EngineInterface
     */
    protected $templateEngine;

    /**
     * @var Swift_Mailer
     */
    protected $mailTransport;

    /**
     * @var string
     */
    protected $sender;

    /**
     * @param EngineInterface $templateEngine
     * @param Swift_Mailer $mailTransport
     */
    public function __construct(EngineInterface $templateEngine, Swift_Mailer $mailTransport)
    {
        $this->templateEngine = $templateEngine;
        $this->mailTransport = $mailTransport;
    }

    /**
     * Set the sender of the email's address
     * and optionally a name
     *
     * @param string $address
     * @param string|null $name
     */
    public function setSender($address, $name = null)
    {
        if (!isset($name)) {
            $this->sender = [$address];
            return;
        }
        $this->sender = [$address => $name];
    }

    /**
     * Send an email to the given recipient with the
     * given subject & template.  Context is an array of
     * values to be substituted into the template.
     *
     * @param string|string[] $recipient
     * @param string $subject
     * @param string $templatePath
     * @param mixed $context
     */
    public function send($recipient, $subject, $templatePath, $context)
    {
        if (!isset($this->sender)) {
            throw new RuntimeException("Cannot send message [{$subject}] until sender has been set using setSender().");
        }

        if (!is_array($recipient)) {
            $recipient = [$recipient];
        }

        $body = $this->renderTemplate($templatePath, $context);
        $message = $this->createMessage();
        $message->setFrom($this->sender);
        $message->setTo($recipient);
        $message->setSubject($subject);
        $message->setBody($this->getPlaintextFromHtml($body));
        $message->addPart($body, 'text/html');

        $this->sendMessage($message);
    }

    /**
     * Send our message
     *
     * @param Swift_Message $message
     */
    protected function sendMessage(Swift_Message $message)
    {
        $this->mailTransport->send($message);
    }

    /**
     * Convert HTML email to plaintext
     *
     * @param string$body
     * @return string
     * @todo This should return plaintext of the message
     */
    protected function getPlaintextFromHtml($body)
    {
        return $body;
    }

    /**
     * Create a Swift_Message object
     *
     * @return Swift_Message
     */
    protected function createMessage()
    {
        return $this->mailTransport->createMessage();
    }

    /**
     * Render our template using the template
     * engine
     *
     * @param string $templatePath
     * @param mixed $context
     * @return string
     */
    protected function renderTemplate($templatePath, $context)
    {
        return $this->templateEngine->render($templatePath, $context);
    }
}