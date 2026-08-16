<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Support;

use Throwable;

class ErrorFingerprintGenerator
{
    public function __construct(
        protected SensitiveMessageRedactor $messageRedactor,
    ) {}

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
        return $this->messageRedactor->redact($message);
    }
}
