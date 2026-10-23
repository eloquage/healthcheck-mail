<?php

declare(strict_types=1);

return [
    'connected' => 'SMTP accepts TCP connections.',
    'mailer_missing' => 'Mailer configuration [:mailer] is missing.',
    'host_missing' => 'SMTP host is not configured.',
    'invalid_port' => 'SMTP port must be between 1 and 65535.',
    'unreachable' => 'SMTP is unreachable at :host::port (:code :message).',
    'skipped' => 'Mailer [:mailer] does not use SMTP; connectivity probe skipped.',
];
