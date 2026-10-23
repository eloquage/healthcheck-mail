<?php

declare(strict_types=1);

use IllumaLaw\HealthCheckMail\MailConnectivityCheck;
use Spatie\Health\Enums\Status;

beforeEach(function () {
    config()->set('healthcheck-mail.required', false);
    config()->set('healthcheck-mail.timeout_seconds', 3);
});

it('skips when the default mailer is log', function () {
    config()->set('mail.default', 'log');

    $result = MailConnectivityCheck::new()->run();

    expect($result->status)->toEqual(Status::skipped())
        ->and($result->shortSummary)->toBe('Skipped');
});

it('skips non-SMTP transports', function () {
    config()->set('mail.default', 'ses');
    config()->set('mail.mailers.ses', ['transport' => 'ses']);

    expect(MailConnectivityCheck::new()->run()->status)->toEqual(Status::skipped());
});

it('warns when mailer configuration is missing by default', function () {
    config()->set('mail.default', 'smtp');
    config()->set('mail.mailers.smtp', null);

    $result = MailConnectivityCheck::new()->run();

    expect($result->status)->toEqual(Status::warning())
        ->and($result->notificationMessage)->toBe('Mailer configuration [smtp] is missing.');
});

it('fails when the missing mailer configuration is required', function () {
    config()->set('mail.default', 'smtp');
    config()->set('mail.mailers.smtp', null);
    config()->set('healthcheck-mail.required', true);

    expect(MailConnectivityCheck::new()->run()->status)->toEqual(Status::failed());
});

it('warns when SMTP host is not configured by default', function () {
    config()->set('mail.default', 'smtp');
    config()->set('mail.mailers.smtp', ['transport' => 'smtp', 'host' => '']);

    expect(MailConnectivityCheck::new()->run()->status)->toEqual(Status::warning());
});

it('warns when SMTP is unreachable by default', function () {
    config()->set('mail.default', 'smtp');
    config()->set('mail.mailers.smtp', [
        'transport' => 'smtp',
        'host' => '127.0.0.1',
        'port' => 9999,
    ]);

    $result = MailConnectivityCheck::new()->run();

    expect($result->status)->toEqual(Status::warning())
        ->and($result->notificationMessage)->toContain('SMTP is unreachable');
});

it('fails when unreachable SMTP is required', function () {
    config()->set('mail.default', 'smtp');
    config()->set('mail.mailers.smtp', [
        'transport' => 'smtp',
        'host' => '127.0.0.1',
        'port' => 9999,
    ]);
    config()->set('healthcheck-mail.required', true);

    expect(MailConnectivityCheck::new()->run()->status)->toEqual(Status::failed());
});

it('succeeds when the SMTP host accepts a TCP connection', function () {
    $server = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);
    if ($server === false) {
        throw new RuntimeException("Could not start the local SMTP probe fixture: {$errorMessage} ({$errorCode})");
    }

    $address = stream_socket_get_name($server, false);
    $port = (int) substr((string) $address, strrpos((string) $address, ':') + 1);

    config()->set('mail.default', 'smtp');
    config()->set('mail.mailers.smtp', [
        'transport' => 'smtp',
        'host' => '127.0.0.1',
        'port' => $port,
    ]);

    $result = MailConnectivityCheck::new()->run();

    fclose($server);

    expect($result->status)->toEqual(Status::ok())
        ->and($result->meta['port'])->toBe($port);
});
