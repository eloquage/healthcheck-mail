<?php

declare(strict_types=1);

use IllumaLaw\HealthCheckMail\HealthcheckMailServiceProvider;
use Illuminate\Support\ServiceProvider;

it('registers the config publish path under the correct tag', function () {
    $provider = app()->getProvider(HealthcheckMailServiceProvider::class);
    $source = dirname((new ReflectionClass($provider))->getFileName()).'/../config/healthcheck-mail.php';

    expect(ServiceProvider::pathsToPublish($provider::class, 'healthcheck-mail-config'))
        ->toBe([$source => config_path('healthcheck-mail.php')]);
});

it('loads config with the correct default', function () {
    expect(config('healthcheck-mail.required'))->toBeFalse();
});
