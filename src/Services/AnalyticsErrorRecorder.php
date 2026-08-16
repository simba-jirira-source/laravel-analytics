<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use LaravelAnalytics\LaravelAnalytics\Contracts\ErrorRecorder;
use LaravelAnalytics\LaravelAnalytics\Models\AnalyticsError;
use LaravelAnalytics\LaravelAnalytics\Support\ErrorFingerprintGenerator;
use LaravelAnalytics\LaravelAnalytics\Support\RequestExclusionChecker;
use LaravelAnalytics\LaravelAnalytics\Support\SafeExceptionMetadataExtractor;
use Throwable;

class AnalyticsErrorRecorder implements ErrorRecorder
{
    public function __construct(
        protected RequestExclusionChecker $exclusionChecker,
        protected ErrorFingerprintGenerator $fingerprintGenerator,
        protected SafeExceptionMetadataExtractor $metadataExtractor,
    ) {}

    public function record(Throwable $throwable, Request $request): void
    {
        if (! $this->exclusionChecker->shouldRecordError($request, $throwable)) {
            return;
        }

        $metadata = $this->metadataExtractor->extract($throwable, $request);
        $fingerprint = $this->fingerprintGenerator->generate($throwable);
        $occurredAt = Carbon::now();

        $error = AnalyticsError::query()->firstOrNew([
            'fingerprint' => $fingerprint,
        ]);

        if (! $error->exists) {
            $error->fill([
                ...$metadata,
                'fingerprint' => $fingerprint,
                'first_occurred_at' => $occurredAt,
                'last_occurred_at' => $occurredAt,
                'occurrence_count' => 1,
            ])->save();

            return;
        }

        $error->forceFill([
            'last_occurred_at' => $occurredAt,
            'occurrence_count' => $error->occurrence_count + 1,
            'message' => $metadata['message'],
            'status_code' => $metadata['status_code'],
        ])->save();
    }
}
