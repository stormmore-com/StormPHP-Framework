<?php

namespace Stormmore\Framework\Mail\Senders;

use Exception;
use Stormmore\Framework\Mail\Mail;

readonly class SmtpSender implements IMailSender
{
    public function __construct(private string $host = "localhost",
                                private int    $port = 25,
                                private string $protocol = "",
                                private bool   $authenticate = false,
                                private string $user = "",
                                private string $password = "")
    {
    }

    public function send(Mail $mail): void
    {
        $socket = $this->getConnection();
        $socket or throw new Exception("Could not connect to SMTP host '$this->host' \n");

        $this->verifyResponse($socket);

        fwrite($socket, "EHLO " . gethostname() . "\r\n");
        $this->verifyResponse($socket);

        if ($this->authenticate) {
            fwrite($socket, "AUTH LOGIN"."\r\n");
            $this->verifyResponse($socket);

            fwrite($socket, base64_encode($this->user)."\r\n");
            $this->verifyResponse($socket);

            fwrite($socket, base64_encode($this->password)."\r\n");
            $this->verifyResponse($socket);
        }

        fwrite($socket, "MAIL FROM: <" . $mail->sender->email . ">\r\n");
        $this->verifyResponse($socket);

        foreach($mail->recipients as $recipient) {
            fwrite($socket, "RCPT TO: <" . $recipient->email . ">\r\n");
            $this->verifyResponse($socket);
        }

        fwrite($socket, "DATA\r\n");
        $this->verifyResponse($socket);

        $multiPartMessage = "";
        $mimeBoundary="__NextPart_" . md5(time());

        $multiPartMessage .= "MIME-Version: 1.0\r\n";
        $multiPartMessage .= "Content-Type: multipart/mixed;";
        $multiPartMessage .= " boundary=$mimeBoundary\r\n";
        $multiPartMessage .= "\r\n";
        $multiPartMessage .= "This is a multi-part message in MIME format.\r\n";
        $multiPartMessage .= "\r\n";

        $multiPartMessage .= "--" . $mimeBoundary . "\r\n";
        $multiPartMessage .= "Content-Type: $mail->contentType; charset=\"$mail->charset\"\r\n";
        $multiPartMessage .= "Content-Transfer-Encoding: quoted-printable\r\n";
        $multiPartMessage .= "\r\n";
        $multiPartMessage .= quoted_printable_encode($mail->content) . "\r\n";
        $multiPartMessage .= "\r\n";

        foreach($mail->attachments as $file) {
            $multiPartMessage .= "--" . $mimeBoundary . "\r\n";
            $multiPartMessage .= "Content-Type: $file->getMimeType() ;\r\n";
            $multiPartMessage .= "	name=\"" . $file->getFilename() . "\"\r\n";
            $multiPartMessage .= "Content-Transfer-Encoding: base64\r\n";
            $multiPartMessage .= "Content-Description: $file->getFilename() \r\n";
            $multiPartMessage .= "Content-Disposition: attachment;\r\n";
            $multiPartMessage .= "	filename=\"$file->getFilename()\"\r\n";
            $multiPartMessage .= "\r\n";
            $multiPartMessage .= $file->getContent(). "\r\n";
            $multiPartMessage .= "\r\n";
        }

        $multiPartMessage .= "--" . $mimeBoundary . "--" . "\r\n";

        fwrite($socket, "From: {$mail->sender->name} <" . $mail->sender->email . ">\r\n");
        foreach($mail->recipients as $recipient) {
            fwrite($socket, "To: $recipient->name <" . $recipient->email . ">\r\n");
        }
        fwrite($socket, "Subject: " . $mail->subject . "\r\n");
        foreach($mail->cc as $cc) {
            fwrite($socket, "Cc: $cc->name <" . $cc->email . ">\r\n");
        }
        foreach($mail->bcc as $bcc) {
            fwrite($socket, "Bcc: $bcc->name <" . $bcc->email . ">\r\n");
        }
        foreach($mail->replyTo as $replyTo) {
            fwrite($socket, "Reply-To: $replyTo->name <" . $replyTo->email . ">\r\n");
        }
        fwrite($socket, $multiPartMessage . "\r\n");

        fwrite($socket, "."."\r\n");
        $this->verifyResponse($socket);

        fwrite($socket, "QUIT"."\r\n");
        fclose($socket);
    }

    private function getConnection(): mixed
    {
        $hostname = ($this->protocol ? $this->protocol . "://" : "") . $this->host . ":" . $this->port;
        $socket_context = stream_context_create([]);
        $connection = stream_socket_client(
            $hostname,
            $errno,
            $errstr,
            20,
            STREAM_CLIENT_CONNECT,
            $socket_context
        );
        return $connection;
    }


    private function verifyResponse($socket): void
    {
        $serverResponse = "";
        while (substr($serverResponse, 3, 1) != " ") {
            $serverResponse = fgets($socket, 1024);
            $serverResponse or throw new Exception("Couldn't get mail server response codes.");
        }

        $statusCode = intval(substr($serverResponse, 0, 3));
        $statusMessage = substr($serverResponse, 4);
        $statusCode >= 200 and $statusCode < 400 or throw new Exception($statusMessage);
    }
}