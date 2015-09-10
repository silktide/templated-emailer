[![Build Status](https://travis-ci.org/silktide/templated-emailer.svg?branch=master)](https://travis-ci.org/silktide/templated-emailer)
[![Code Climate](https://codeclimate.com/github/silktide/templated-emailer/badges/gpa.svg)](https://codeclimate.com/github/silktide/templated-emailer)
[![Test Coverage](https://codeclimate.com/github/silktide/templated-emailer/badges/coverage.svg)](https://codeclimate.com/github/silktide/templated-emailer/coverage)

# Templated emailer
This library simply ties together Symfony's powerful templating engine with the flexibility of Swiftmailer, allowing you to send templated emails easily.

## Setup

### Without DI
 
If you're not using DI (or don't know what DI is) there is a helpful factory to create the mailer:

    $emailer = \Silktide\TemplatedEmailer\TemplatedEmailerFactory::create('/path/to/templates');
    
The only required argument is the path to the folder containing your email templates (see Creating a template below).
    
By default, the factory will set up the class to use PHP's mail() function to send your mail and Symfony's PhpEngine for templating.
You can optionally provide a [Swiftmailer transport class](http://swiftmailer.org/docs/sending.html) as the second argument
if you want to use a different transport, e.g. SMTP.  Here's an example of using an alternative transport:

    $transport = \Swift_SmtpTransport::newInstance('smtp.example.org', 25)
                ->setUsername('your username')
                ->setPassword('your password');
    $emailer =  \Silktide\TemplatedEmailer\TemplatedEmailerFactory::create('/example', $transport);
    

### Dependency injection / manual instantiation

The TemplatedEmailer class just requires two arguments, a Symfony template engine and an instance of Swift_Mailer:
    
    /**
    * @var \Symfony\Component\Templating\EngineInterface
    */
    $templateEngine;
    
    /**
    * @var \Swift_Mailer
    */
    $emailClient;
    
    $emailer = new \Silktide\TemplatedEmailer\TemplatedEmailer($templateEngine, $emailClient);
    
## Creating a template

Templates are using [Symfony's well documented template library](http://symfony.com/doc/current/components/templating/introduction.html).  Here's a very basic example:

    <h1>An email from myApp</h1>
    <p>Dear <?php echo $recipientName; ?></p>
    <p><?php echo $message; ?>

The variables in the message are passed through as an array to the send() method (see usage below).
    
## Usage

Before any emails can be sent, you must set a sender:

    $emailer->setSender('pat@postman.com', 'Postman Pat');
    
Sending an email now just requires a recipient, subject, template filename and context:

    $this->mailer->send(
        'mrsgoggins@greendalepo.com',
        'Jess the black and white cat',
        'anEmail.php',
        [
            'recipientName' => 'Mrs Goggins',
            'message' => 'Hello!'
        ]
    );

The recipient can also have a name set by using an array in the format ['email@host.tld' => 'Friendly Name']:

    $this->mailer->send(
        ['mrsgoggins@greendalepo.com' => 'Mrs Goggins'],
        'Jess the black and white cat',
        'anEmail.php',
        [
            'recipientName' => 'Mrs Goggins',
            'message' => 'Hello!'
        ]
    );
