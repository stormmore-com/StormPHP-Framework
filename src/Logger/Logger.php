<?php

namespace Stormmore\Framework\Logger;

use DateTime;

class Logger implements ILogger
{
    public const DEBUG = "debug";
    public const INFO = "info";
    public const NOTICE = "notice";
    public const WARNING = "warning";
    public const ERROR = "error";
    public const FATAL = "critical";
    private array $accepted;
    private string $uuid;

    public function __construct(private readonly Configuration $configuration)
    {
        $this->accepted = [];
        $levels = array(self::DEBUG, self::INFO, self::NOTICE, self::WARNING, self::ERROR, self::FATAL);
        $logLevel = strtolower($this->configuration->level);
        $index = array_search($logLevel, $levels);
        if ($index === false) {
            $index = 0;
        }
        foreach($levels as $i => $level) {
            if ($i >= $index) {
                $this->accepted[] = $level;
            }
        }
        $this->uuid = bin2hex(random_bytes(8));
    }

    public function log(string $level, string $message): void
    {
        $this->write($level, $message);
    }

    public function logD(string $message): void
    {
        $this->write(self::DEBUG, $message);
    }

    public function logI(string $message): void
    {
        $this->log(self::INFO, $message);
    }

    public function logN(string $message): void
    {
        $this->log(self::NOTICE, $message);
    }

    public function logW(string $message): void
    {
        $this->log(self::WARNING, $message);
    }

    public function logE(string $message): void
    {
        $this->log(self::ERROR, $message);
    }

    public function logF(string $message): void
    {
        $this->log(self::FATAL, $message);
    }

    private function write(string $level, string $line): void
    {
        if (!$this->configuration->enabled) {
            return;
        }
        $date = (new DateTime())->format('Y-m-d H:i:s.u');
        $level = strtoupper($level);
        $line = "$date|{$this->uuid}|$level|$line \n";
        if (empty($this->configuration->directory)) {
            $this->configuration->directory = '@/.logs/';
        }

        $y = date('Y');
        $m = date('m');
        $d = date('d');
        $dir = resolve_path_alias($this->configuration->directory);
        $logDirectory = concatenate_paths($dir, $y, $m, $d);
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }
        $filename = $logDirectory . "/log.txt";
        file_put_contents($filename, $line, FILE_APPEND);
    }
}