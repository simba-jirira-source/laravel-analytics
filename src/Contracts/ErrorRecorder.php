<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics\Contracts;

use Illuminate\Http\Request;
use Throwable;

interface ErrorRecorder
{
    public function record(Throwable $throwable, Request $request): void;
}
