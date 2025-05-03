<?php

namespace Stormmore\Framework\Mail\Senders;

use Exception;
use Stormmore\Framework\Mail\Mail;

class SmtpSender implements IMailSender
{
    public function __construct(private string $host = "localhost",
                                private int $port = 25,
                                private $security = false,
                                private string $user = "",
                                private string $password = "")
    {
    }

    public function send(Mail $mail): void
    {
        try {
            if (!($socket = fsockopen(($this->security ? $this->security . "://" : "")
                . $this->host, $this->port, $errno, $errstr, 15)))
                throw new Exception("Could not connect to SMTP host ".
                    "'" . $this->host . "' ($errno) ($errstr)\n");

            $this->waitForPositiveCompletionReply($socket);

            fwrite($socket, "EHLO " . gethostname() . "\r\n");
            $this->waitForPositiveCompletionReply($socket);

            if ($this->user != "" && $this->password != "") {
                fwrite($socket, "AUTH LOGIN"."\r\n");
                $this->waitForPositiveIntermediateReply($socket);

                fwrite($socket, base64_encode($this->user)."\r\n");
                $this->waitForPositiveIntermediateReply($socket);

                fwrite($socket, base64_encode($this->password)."\r\n");
                $this->waitForPositiveCompletionReply($socket);
            }

            fwrite($socket, "MAIL FROM: <" . $mail->sender . ">"."\r\n");
            $this->waitForPositiveCompletionReply($socket);

            fwrite($socket, "RCPT TO: <" . $mail->recipient . ">" . "\r\n");
            $this->waitForPositiveCompletionReply($socket);

            fwrite($socket, "DATA"."\r\n");
            $this->waitForPositiveIntermediateReply($socket);

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
            $multiPartMessage .= "Content-Transfer-Encoding: quoted-printable" . "\r\n";
            $multiPartMessage .= "\r\n";
            $multiPartMessage .= quoted_printable_encode($mail->content) . "\r\n";
            $multiPartMessage .= "\r\n";

            foreach($mail->attachments as $file) {
                $multiPartMessage .= "--" . $mimeBoundary . "\r\n";
                $multiPartMessage .= "Content-Type: "
                    . $file->getMimeType()
                    . ";" . "\r\n";
                $multiPartMessage .= "	name=\"" . $file->getFilename()
                    . "\"" . "\r\n";
                $multiPartMessage .= "Content-Transfer-Encoding: base64"
                    . "\r\n";
                $multiPartMessage .= "Content-Description: "
                    . $file->getFilename() . "\r\n";
                $multiPartMessage .= "Content-Disposition: attachment;"
                    . "\r\n";
                $multiPartMessage .= "	filename=\""
                    . $file->getFilename() . "\""
                    . "\r\n";
                $multiPartMessage .= "\r\n";
                $multiPartMessage .= $file->getContent(). "\r\n";
                $multiPartMessage .= "\r\n";
            }

            $multiPartMessage .= "--" . $mimeBoundary . "--" . "\r\n";

            fwrite($socket, "Subject: " . $mail->subject . "\r\n");
            fwrite($socket, "To: <" . $mail->recipient . ">\r\n");
            fwrite($socket, "From: <" . $mail->sender . ">\r\n");
            fwrite($socket, $multiPartMessage . "\r\n");

            fwrite($socket, "."."\r\n");
            $this->waitForPositiveCompletionReply($socket);

            fwrite($socket, "QUIT"."\r\n");
            fclose($socket);
        } catch (Exception $e) {
            echo "Error while sending email. Reason : \n" . $e->getMessage();
        }
    }

    /** Verify if server responds with a positive preliminary (1xx) status code
     */
    protected function waitForPositivePreliminaryReply($socket) {
        try {
            $this->_serverRespondedAsExpected($socket, 1);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /** Verify if server responds with a positive completion (2xx) status code
     */
    protected function waitForPositiveCompletionReply($socket) {
        try {
            $this->_serverRespondedAsExpected($socket, 2);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /** Verify if server responds with a positive intermediate (3xx) status code
     */
    protected function waitForPositiveIntermediateReply($socket) {
        try {
            $this->_serverRespondedAsExpected($socket, 3);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /** Verify if server responds with a transient negative completion (4xx)
    status code
     */
    protected function waitForTransientNegativeCompletionReply($socket) {
        try {
            $this->_serverRespondedAsExpected($socket, 4);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /** Verify if server responds with a permanent negative completion (5xx)
    status code
     */
    protected function waitForPermanentNegativeCompletionReply($socket) {
        try {
            $this->_serverRespondedAsExpected($socket, 5);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /** Check if the received response is the expected one.
    Should not be called directly, use thes waitFor...() methods instead
     */
    private function _serverRespondedAsExpected($socket,
                                                $expectedStatusCode) {
        $serverResponse = "";

        //SMTP server can send multiple response.
        //For example several 250 status code after EHLO
        while (substr($serverResponse, 3, 1) != " ") {
            $serverResponse = fgets($socket, 256);
//			echo $serverResponse;
            if (!($serverResponse))
                throw new Exception("Couldn\'t get mail server response codes."
                    . " Please contact an administrator.");
        }

        $statusCode = substr($serverResponse, 0, 3);
        $statusMessage = substr($serverResponse, 4);
        if (!(is_numeric($statusCode)
            && (int)($statusCode / 100) == $expectedStatusCode)) {
            throw new Exception($statusMessage);
        }
    }
}