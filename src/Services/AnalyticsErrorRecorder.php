<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Services;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        $updated = AnalyticsError::query()
            ->where('fingerprint', $fingerprint)
            ->update([
                'last_occurred_at' => $occurredAt,
                'occurrence_count' => DB::raw('occurrence_count + 1'),
                'message' => $metadata['message'],
                'status_code' => $metadata['status_code'],
            ]);

        if ($updated > 0) {
            return;
        }

        try {
            AnalyticsError::query()->create([
                ...$metadata,
                'fingerprint' => $fingerprint,
                'first_occurred_at' => $occurredAt,
                'last_occurred_at' => $occurredAt,
                'occurrence_count' => 1,
            ]);
        } catch (QueryException $exception) {
            if (! $this->isUniqueConstraintViolation($exception)) {
                throw $exception;
            }

            AnalyticsError::query()
                ->where('fingerprint', $fingerprint)
                ->update([
                    'last_occurred_at' => $occurredAt,
                    'occurrence_count' => DB::raw('occurrence_count + 1'),
                    'message' => $metadata['message'],
                    'status_code' => $metadata['status_code'],
                ]);
        }
    }

    protected function isUniqueConstraintViolation(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;

        if ($sqlState === '23000') {
            return true;
        }

        return ($exception->errorInfo[1] ?? null) === 1062;
    }
}
