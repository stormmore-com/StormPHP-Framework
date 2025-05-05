<?php

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Mail\Mail;
use Stormmore\Framework\Mail\MailBuilder;
use Stormmore\Framework\Mail\Senders\IMailSender;

class MailBuilderTest extends TestCase
{
    private MailBuilder $mailBuilder;

    public function testSenderEmailValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withSender("invalidemail");
    }

    public function testRecipientEmailValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withRecipient("invalidemail");
    }

    public function testCcValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withCc("invalidemail");
    }

    public function testBccValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withBcc("invalidemail");
    }

    public function testReplyToValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withReplyTo("invalidemail");
    }

    public function testSenderValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withRecipient("recipient@valid.com");
        $this->mailBuilder->withSubject("subject");
        $this->mailBuilder->withContent("content");

        $this->mailBuilder->build();
    }

    public function testRecipientValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withSender("sender@valid.com");
        $this->mailBuilder->withSubject("subject");
        $this->mailBuilder->withContent("content");

        $this->mailBuilder->build();
    }

    public function testSubjectValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withSender("sender@valid.com");
        $this->mailBuilder->withRecipient("recipient@valid.com");
        $this->mailBuilder->withContent("content");

        $this->mailBuilder->build();
    }

    public function testContentValidation(): void
    {
        $this->expectException(Exception::class);

        $this->mailBuilder->withSender("sender@valid.com");
        $this->mailBuilder->withRecipient("recipient@valid.com");
        $this->mailBuilder->withSubject("subject");

        $this->mailBuilder->build();
    }

    public function testValidEmail(): void
    {
        $this->mailBuilder->withRecipient("recipient@valid.com");
        $this->mailBuilder->withSender("sender@valid.com");
        $this->mailBuilder->withSubject("subject");
        $this->mailBuilder->withContent("content");

        $mail = $this->mailBuilder->build();

        $this->assertEquals("recipient@valid.com", $mail->recipients[0]->email);
        $this->assertEquals("sender@valid.com", $mail->sender->email);
        $this->assertEquals("subject", $mail->subject);
        $this->assertEquals("content", $mail->content);
    }

    public function setUp(): void
    {
        $this->mailBuilder = new MailBuilder($this->getMockBuilder(IMailSender::class)->getMock());
    }
}