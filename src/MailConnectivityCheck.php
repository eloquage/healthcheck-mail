<?php

declare(strict_types=1);

namespace IllumaLaw\HealthCheckMail;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Health\Enums\Status;

final class MailConnectivityCheck extends Check
{
    public function run(): Result
    {
        $mailer = config('mail.default', 'log');
        $mailer = is_string($mailer) ? $mailer : 'log';

        if (in_array($mailer, ['log', 'array', 'fail', 'null'], true)) {
            return (new Result(Status::skipped(), "Mailer [{$mailer}] does not use SMTP; connectivity probe skipped."))
                ->shortSummary('Skipped');
        }

        $config = config("mail.mailers.{$mailer}");

        if (! is_array($config)) {
            return Result::make()->failed("Mailer configuration [{$mailer}] is missing.");
        }

        $host = $config['host'] ?? '';
        $host = is_string($host) ? $host : '';
        $port = $config['port'] ?? 587;
        $port = is_numeric($port) ? (int) $port : 587;

        if ($host === '') {
            return Result::make()->failed('SMTP host is not configured.');
        }

        $startedAt = microtime(true);
        $socket = @fsockopen($host, $port, $errno, $errstr, 3.0);
        $ms = (int) round((microtime(true) - $startedAt) * 1000);

        $meta = [
            'mailer' => $mailer,
            'host' => $host,
            'port' => $port,
            'response_time_ms' => $ms,
        ];

        if ($socket === false) {
            return Result::make()
                ->meta($meta)
                ->failed("SMTP is unreachable at {$host}:{$port} ({$errno} {$errstr}).");
        }

        fclose($socket);

        return Result::make()
            ->meta($meta)
            ->shortSummary("{$ms}ms")
            ->ok('SMTP accepts TCP connections.');
    }
}
