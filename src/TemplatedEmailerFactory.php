<?php
/**
 * Copyright 2013-2015 Silktide Ltd. All Rights Reserved.
 */

namespace Silktide\TemplatedEmailer;


use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\TemplateNameParser;
use Swift_MailTransport;
use Swift_Transport;
use Swift_Mailer;

abstract class TemplatedEmailerFactory
{
    /**
     * For non DI use, create a TemplatedEmailer
     *
     * @param string $basePath
     * @param Swift_Transport|null $transport
     * @return TemplatedEmailer
     */
    public static function create($basePath, Swift_Transport $transport = null)
    {
        // Use PHP's default SendMail if no transport is provided
        if ($transport == null) {
            $transport = Swift_MailTransport::newInstance();
        }

        // Create a mailer with our transport
        $mailer = Swift_Mailer::newInstance($transport);

        // Create templating system
        $loader = new FilesystemLoader($basePath.'%name%');
        $engine = new PhpEngine(new TemplateNameParser(), $loader);

        // Create our instance
        return new TemplatedEmailer($engine, $mailer);
    }
}