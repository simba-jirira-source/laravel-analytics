<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface AnalyticsRecorder
{
    public function record(Request $request, Response $response, int $durationMs): void;
}
