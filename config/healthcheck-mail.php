<?php

declare(strict_types=1);

return [
    /*
     * Whether the mail connectivity is required.
     * If true, the check will fail if unreachable.
     * If false, it will only result in a warning.
     */
    'required' => env('HEALTH_MAIL_REQUIRED', false),
    'timeout_seconds' => env('HEALTH_MAIL_TIMEOUT_SECONDS', 3),
];
