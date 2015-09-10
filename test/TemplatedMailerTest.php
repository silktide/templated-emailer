<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\TemplatedEmailer\Test;

use Silktide\TemplatedEmailer\TemplatedEmailer;
use Symfony\Component\Templating\EngineInterface;
use Swift_Mime_SimpleMessage;
use Swift_Mailer;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class TemplatedEmailerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TemplatedEmailer
     */
    protected $mailer;

    /**
     * @var EngineInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateEngine;

    /**
     * @var Swift_Mailer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $mailEngine;

    /**
     * @var Swift_Mime_SimpleMessage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $message;

    /**
     * @var string
     */
    protected $senderEmail = 'mrsgoggins@greendalepo.com';

    /**
     * @var string
     */
    protected $senderName = 'Mrs Goggins';

    /**
     * @var string
     */
    protected $recipientEmail = 'pat@postman.com';

    /**
     * @var string
     */
    protected $recipientName = 'Postman Pat';

    /**
     * @var string
     */
    protected $subject = 'Your email';

    /**
     * @var string
     */
    protected $scriptPath = 'example.php';

    /**
     * @var string[]
     */
    protected $context = ['var' => 'value'];

    /**
     * @var string
     */
    protected $messageBody = "<p>Some message as HTML</p>";

    /**
     * Setup test
     */
    public function setup()
    {
        $this->templateEngine = $this->getMockBuilder('Symfony\Component\Templating\EngineInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailEngine = $this->getMockBuilder('Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();


        $this->message = $this->getMockBuilder('Swift_Message')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailEngine
            ->expects($this->any())
            ->method('createMessage')
            ->willReturn($this->message);

        $this->mailer = new TemplatedEmailer(
            $this->templateEngine,
            $this->mailEngine);
    }

    /**
     * Send with default params, to avoid
     * repeating this code in every function
     */
    protected function defaultSend()
    {
        $this->mailer->send(
            $this->recipientEmail,
            $this->subject,
            $this->scriptPath,
            $this->context
        );
    }

    protected function addDefaultSender()
    {
        $this->mailer->setSender($this->senderEmail);
    }

    /**
     * An exception should be thrown if
     * a message is sent before sender is set
     */
    public function testThrowsExceptionIfNoSender()
    {
        $this->setExpectedException('Exception');
        $this->defaultSend();
    }

    /**
     * Should allow us to set a sender with name
     * in Swift_Message
     */
    public function testAllowsSenderWithName()
    {
        $this->mailEngine
            ->expects($this->atLeastOnce())
            ->method('createMessage')
            ->willReturn($this->message);

        $this->message
            ->expects($this->atLeastOnce())
            ->method('setFrom')
            ->with(
                $this->equalTo([
                    $this->senderEmail => $this->senderName
                ])
            );

        $this->mailer->setSender($this->senderEmail, $this->senderName);

        $this->defaultSend();
    }

    /**
     * Should allow us to set a sender without name
     * in Swift_Message
     */
    public function testAllowsSenderWithoutName()
    {
        $this->mailEngine
            ->expects($this->atLeastOnce())
            ->method('createMessage')
            ->willReturn($this->message);

        $this->message
            ->expects($this->atLeastOnce())
            ->method('setFrom')
            ->with(
                $this->equalTo([
                    $this->senderEmail
                ])
            );

        $this->mailer->setSender($this->senderEmail);

        $this->defaultSend();
    }

    /**
     * Test if render method is called on template engine
     */
    public function testCallsRenderMethodOnTemplateEngine()
    {
        // Should call template engine's render method with the passed in props
        $this->templateEngine
            ->expects($this->atLeastOnce())
            ->method('render')
            ->with(
                $this->equalTo($this->scriptPath),
                $this->equalTo($this->context)
            );

        $this->addDefaultSender();
        $this->defaultSend();
    }

    /**
     * Test if sender is set on Swift_Message without a name
     */
    public function testSetsSenderWithoutName()
    {
        $this->message
            ->expects($this->atLeastOnce())
            ->method('setFrom')
            ->with(
                $this->equalTo([$this->senderEmail])
            );

        $this->addDefaultSender();
        $this->defaultSend();
    }

    /**
     * Test if sender is set on Swift_Message without a name
     */
    public function testSetsRecipient()
    {
        $this->message
            ->expects($this->atLeastOnce())
            ->method('setTo')
            ->with(
                $this->equalTo([$this->recipientEmail])
            );

        $this->addDefaultSender();
        $this->defaultSend();
    }

    /**
     * Test if sender is set on Swift_Message without a name
     */
    public function testSetsSubject()
    {
        $this->message
            ->expects($this->atLeastOnce())
            ->method('setSubject')
            ->with(
                $this->equalTo($this->subject)
            );

        $this->addDefaultSender();
        $this->defaultSend();
    }

    /**
     * Test if sender is set on Swift_Message without a name
     */
    public function testSetsHTMLPart()
    {
        $this->templateEngine
            ->expects($this->any())
            ->method('render')
            ->willReturn($this->messageBody);

        $this->message
            ->expects($this->atLeastOnce())
            ->method('addPart')
            ->with(
                $this->equalTo($this->messageBody),
                $this->equalTo('text/html')
            );

        $this->addDefaultSender();
        $this->defaultSend();
    }

    /**
     * The send method should be called on the transport
     * layer.
     */
    public function testShouldCallSendMethodOnTransport()
    {
        $this->mailEngine
            ->expects($this->atLeastOnce())
            ->method('send')
            ->with(
                $this->equalTo($this->message)
            );

        $this->addDefaultSender();
        $this->defaultSend();
    }
}