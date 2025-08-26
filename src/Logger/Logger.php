<?php

namespace Stormmore\Framework\Logger;

use Stormmore\Framework\Std\Path;
use Throwable;
use DateTime;
use Stormmore\Framework\AppConfiguration;

class Logger implements ILogger
{
    private string $uuid;
    private array $levels = array(self::DEBUG, self::INFO, self::NOTICE, self::WARNING, self::ERROR, self::FATAL);

    public const DEBUG = "debug";
    public const INFO = "info";
    public const NOTICE = "notice";
    public const WARNING = "warning";
    public const ERROR = "error";
    public const FATAL = "critical";


    public function __construct(private readonly AppConfiguration $configuration)
    {
        $this->uuid = bin2hex(random_bytes(8));
    }

    public function log(string $level, string $message, ?Throwable $t = null): void
    {
        if ($t) {
            $message  .= "\n" . $t->getMessage() . "\n" . $t->getTraceAsString();
        }
        $this->write($level, $message);
    }

    public function logD(string $message, ?Throwable $t = null): void
    {
        $this->log(self::DEBUG, $message, $t);
    }

    public function logI(string $message, ?Throwable $t = null): void
    {
        $this->log(self::INFO, $message, $t);
    }

    public function logN(string $message, ?Throwable $t = null): void
    {
        $this->log(self::NOTICE, $message, $t);
    }

    public function logW(string $message, ?Throwable $t = null): void
    {
        $this->log(self::WARNING, $message, $t);
    }

    public function logE(string $message, ?Throwable $t = null): void
    {
        $this->log(self::ERROR, $message, $t);
    }

    public function logF(string $message, ?Throwable $t = null): void
    {
        $this->log(self::FATAL, $message, $t);
    }

    private function write(string $level, string $line): void
    {
        if (!$this->configuration->isLoggerEnabled()) {
            return;
        }
        $logLevel = strtolower($this->configuration->getLogLevel());
        $minLevelIndex = array_search($logLevel, $this->levels) ?? 0;
        $levelIndex = array_search($level, $this->levels) ?? 0;
        if ($minLevelIndex > $levelIndex) return;

        $date = (new DateTime())->format('Y-m-d H:i:s.u');
        $level = strtoupper($level);
        $line = "$date|{$this->uuid}|$level|$line \n";

        $y = date('Y');
        $m = date('m');
        $d = date('d');
        $dir = Path::resolve_alias($this->configuration->getLoggerDirectory());
        $logDirectory = Path::concatenate_paths($dir, $y, $m, $d);
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }
        $filename = $logDirectory . "/log.log";
        file_put_contents($filename, $line, FILE_APPEND);
    }
}