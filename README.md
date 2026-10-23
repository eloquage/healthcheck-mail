# Healthcheck Mail

[![Tests](https://github.com/eloquage/healthcheck-mail/actions/workflows/run-tests.yml/badge.svg)](https://github.com/eloquage/healthcheck-mail/actions)
[![Latest Version](https://img.shields.io/packagist/v/illuma-law/healthcheck-mail)](https://packagist.org/packages/illuma-law/healthcheck-mail)
[![License](https://img.shields.io/packagist/l/illuma-law/healthcheck-mail)](LICENSE.md)

![SMTP connection health check illustration](art/banner.png)

A Laravel Health check that tests whether your configured SMTP host accepts a TCP connection.

## Requirements

- PHP 8.3 or later
- Laravel 11, 12, or 13
- [Spatie Laravel Health](https://spatie.be/docs/laravel-health)

## Install

```bash
composer require illuma-law/healthcheck-mail
```

## Configure

Use an SMTP mailer in your Laravel mail configuration, for example:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
```

The check skips non-SMTP transports such as `log` and `ses`. Publish its settings if you need to change the defaults:

```bash
php artisan vendor:publish --tag=healthcheck-mail-config
```

In `config/healthcheck-mail.php`:

- `required`: when `true`, missing or unreachable SMTP configuration fails the check; otherwise it warns. Default: `false`.
- `timeout_seconds`: connection timeout in seconds. Default: `3`.

Environment variables are `HEALTH_MAIL_REQUIRED` and `HEALTH_MAIL_TIMEOUT_SECONDS`.

## Use

Register the check with your Laravel Health checks:

```php
use IllumaLaw\HealthCheckMail\MailConnectivityCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    MailConnectivityCheck::new(),
]);
```

The probe opens and closes a TCP connection. It does not test SMTP authentication, TLS negotiation, server greeting, or email delivery. A successful result confirms that the configured host and port accepted the connection.

## Tests

```bash
composer install
composer test
composer analyse
```

## License

MIT. See [LICENSE.md](LICENSE.md).
