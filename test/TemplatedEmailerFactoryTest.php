<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\TemplatedEmailer\Test;

use Silktide\TemplatedEmailer\TemplatedEmailer;
use Silktide\TemplatedEmailer\TemplatedEmailerFactory;
use Symfony\Component\Templating\EngineInterface;
use Swift_Mime_SimpleMessage;
use Swift_Mailer;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Swift_SmtpTransport;

class TemplatedEmailerFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Should return an instance of TemplatedEmailer
     */
    public function testCanCreateInstanceUsingFactory()
    {
        $emailer =  TemplatedEmailerFactory::create('/example');
        $this->assertInstanceOf('Silktide\TemplatedEmailer\TemplatedEmailer', $emailer);
    }

    /**
     * Should allow use of your own transport
     */
    public function testCanCreateInstanceWithCustomTransport()
    {
        $transport = Swift_SmtpTransport::newInstance('smtp.example.org', 25)
            ->setUsername('your username')
            ->setPassword('your password')
        ;
        $emailer =  TemplatedEmailerFactory::create('/example', $transport);
        $this->assertInstanceOf('Silktide\TemplatedEmailer\TemplatedEmailer', $emailer);
    }
}