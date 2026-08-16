<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Support;

use Throwable;

class ErrorFingerprintGenerator
{
    public function generate(Throwable $throwable): string
    {
        $components = [
            $throwable::class,
            $throwable->getFile(),
            (string) $throwable->getLine(),
            $this->normalizeMessage($throwable->getMessage()),
        ];

        return hash('sha256', implode('|', $components));
    }

    protected function normalizeMessage(string $message): string
    {
        $redacted = preg_replace(
            '/\b(password|token|secret|authorization|api_key|bearer)\s*[:=]\s*\S+/i',
            '$1=[redacted]',
            $message,
        );

        return trim($redacted ?? $message);
    }
}
