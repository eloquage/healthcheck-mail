<?php

declare(strict_types=1);

use IllumaLaw\HealthCheckMail\HealthcheckMailServiceProvider;
use Illuminate\Support\ServiceProvider;

it('registers the config publish path under the correct tag', function () {
    $provider = app()->getProvider(HealthcheckMailServiceProvider::class);
    if (! ($provider instanceof HealthcheckMailServiceProvider)) {
        throw new RuntimeException('The package service provider must be registered.');
    }

    $source = dirname(__DIR__).'/src/../config/healthcheck-mail.php';

    expect(ServiceProvider::pathsToPublish($provider::class, 'healthcheck-mail-config'))
        ->toBe([$source => config_path('healthcheck-mail.php')]);
});

it('loads config with the correct default', function () {
    expect(config('healthcheck-mail.required'))->toBeFalse()
        ->and(config('healthcheck-mail.timeout_seconds'))->toBe(3);
});
