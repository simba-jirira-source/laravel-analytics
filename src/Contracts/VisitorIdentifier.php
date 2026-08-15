<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Contracts;

use Illuminate\Http\Request;

interface VisitorIdentifier
{
    public function identify(Request $request): string;

    public function hashIp(?string $ip): ?string;
}
