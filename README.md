# Healthcheck mail for Laravel

[![Tests](https://github.com/eloquage/healthcheck-mail/actions/workflows/run-tests.yml/badge.svg)](https://github.com/eloquage/healthcheck-mail/actions)
[![Packagist License](https://img.shields.io/badge/Licence-MIT-blue)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://img.shields.io/packagist/v/illuma-law/healthcheck-mail?label=Version)](https://packagist.org/packages/illuma-law/healthcheck-mail)

A focused mail health check for Spatie's [Laravel Health](https://spatie.be/docs/laravel-health/v1/introduction) package.

This package provides a simple, direct health check to verify that your application's SMTP server is reachable and accepting TCP connections.

## Features

- **Connectivity Check:** Verifies that your Laravel application can successfully connect to the configured SMTP host and port.
- **Latency Monitoring:** Measures the response time of the SMTP connection.
- **Smart Skipping:** Automatically skips the check if the default mailer is set to `log`, `array`, `fail`, or `null`.

## Installation

Require this package with composer:

```shell
composer require illuma-law/healthcheck-mail
```

## Usage & Integration

Register the check inside your application's health service provider (e.g. `AppServiceProvider` or a dedicated `HealthServiceProvider`), alongside your other Spatie Laravel Health checks:

### Basic Registration

```php
use IllumaLaw\HealthCheckMail\MailConnectivityCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    MailConnectivityCheck::new(),
]);
```

### Expected Result States

The check interacts with the Spatie Health dashboard and JSON endpoints using these states:

- **Ok:** SMTP server is reachable and accepting connections.
- **Skipped:** The default mailer is not an SMTP-based driver.
- **Failed:** SMTP server was unreachable, or the mailer configuration is missing.

## Testing

Run the test suite:

```shell
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
