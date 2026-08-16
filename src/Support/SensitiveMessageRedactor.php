<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Support;

class SensitiveMessageRedactor
{
    private const string PATTERN = '/\b(password|token|secret|authorization|api_key|bearer|cookie|session)\s*[:=]\s*\S+/i';

    public function redact(string $message): string
    {
        $redacted = preg_replace(self::PATTERN, '$1=[redacted]', $message);

        return trim($redacted ?? $message);
    }
}
