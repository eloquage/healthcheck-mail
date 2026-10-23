# illuma-law/healthcheck-mail

Checks whether the configured SMTP host accepts a TCP connection. It does not send an email.

## Usage

```php
use IllumaLaw\HealthCheckMail\MailConnectivityCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    MailConnectivityCheck::new(),
]);
```

## Configuration

Publish config: `php artisan vendor:publish --tag="healthcheck-mail-config"`

Options in `config/healthcheck-mail.php`:
- `required`: make missing or unreachable SMTP configuration fail; otherwise report a warning.
- `timeout_seconds`: maximum TCP connection time in seconds.
