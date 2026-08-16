<?php

declare(strict_types=1);

use SimbaJirira\LaravelAnalytics\Support\SensitiveMessageRedactor;

it('redacts sensitive key value pairs from messages', function () {
    $redactor = new SensitiveMessageRedactor;

    expect($redactor->redact('Invalid token: abc123-secret'))
        ->toBe('Invalid token=[redacted]')
        ->and($redactor->redact('cookie=session-id-123 failed'))
        ->toBe('cookie=[redacted] failed');
});
