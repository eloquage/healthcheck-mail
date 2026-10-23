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
        $config = config("mail.mailers.{$mailer}");

        if (! is_array($config)) {
            return $this->unavailable(__('healthcheck-mail::messages.mailer_missing', ['mailer' => $mailer]), 'Not configured');
        }

        $configuredTransport = $config['transport'] ?? $config['driver'] ?? $mailer;
        $transport = is_string($configuredTransport) ? strtolower($configuredTransport) : '';

        if ($transport !== 'smtp') {
            return (new Result(Status::skipped(), __('healthcheck-mail::messages.skipped', ['mailer' => $mailer])))
                ->shortSummary('Skipped');
        }

        $configuredHost = $config['host'] ?? '';
        $host = is_string($configuredHost) ? trim($configuredHost) : '';

        if ($host === '') {
            return $this->unavailable(__('healthcheck-mail::messages.host_missing'), 'Not configured');
        }

        $configuredPort = $config['port'] ?? 587;
        $port = is_numeric($configuredPort) ? (int) $configuredPort : 587;

        if ($port < 1 || $port > 65535) {
            return $this->unavailable(__('healthcheck-mail::messages.invalid_port'), 'Invalid port');
        }

        $configuredTimeout = config('healthcheck-mail.timeout_seconds', 3);
        $timeoutSeconds = max(1, is_numeric($configuredTimeout) ? (int) $configuredTimeout : 3);
        $startedAt = microtime(true);
        $socket = @fsockopen($host, $port, $errno, $errorMessage, (float) $timeoutSeconds);
        $responseTimeMs = (int) round((microtime(true) - $startedAt) * 1000);

        $meta = [
            'mailer' => $mailer,
            'host' => $host,
            'port' => $port,
            'response_time_ms' => $responseTimeMs,
        ];

        if ($socket === false) {
            $message = __('healthcheck-mail::messages.unreachable', [
                'host' => $host,
                'port' => $port,
                'code' => $errno,
                'message' => $errorMessage,
            ]);

            return $this->unavailable($message, 'Unreachable')->meta($meta);
        }

        fclose($socket);

        return Result::make()
            ->meta($meta)
            ->shortSummary("{$responseTimeMs}ms")
            ->ok(__('healthcheck-mail::messages.connected'));
    }

    private function unavailable(string $message, string $summary): Result
    {
        $result = Result::make()->shortSummary($summary);

        if ((bool) config('healthcheck-mail.required', false)) {
            return $result->failed($message);
        }

        return $result->warning($message);
    }
}
