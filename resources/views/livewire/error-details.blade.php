<div class="space-y-6">
    <div>
        <a
            href="{{ route(config('analytics.dashboard.route_prefix').'dashboard') }}"
            class="text-sm font-medium text-slate-600 underline hover:text-slate-900"
        >
            Back to dashboard
        </a>
    </div>

    <header>
        <h1 class="text-2xl font-semibold">{{ class_basename($error->exception_class) }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ $error->message }}</p>
    </header>

    <dl class="grid gap-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Path</dt>
            <dd class="mt-1 font-mono text-sm">{{ $error->path ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Route</dt>
            <dd class="mt-1 text-sm">{{ $error->route_name ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Status</dt>
            <dd class="mt-1 text-sm">{{ $error->status_code ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Occurrences</dt>
            <dd class="mt-1 text-sm">{{ number_format($error->occurrence_count) }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">First Occurred</dt>
            <dd class="mt-1 text-sm">{{ $error->first_occurred_at?->toDateTimeString() ?? '—' }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Last Occurred</dt>
            <dd class="mt-1 text-sm">{{ $error->last_occurred_at?->toDateTimeString() ?? '—' }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Location</dt>
            <dd class="mt-1 font-mono text-xs">{{ $error->file }}:{{ $error->line }}</dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Fingerprint</dt>
            <dd class="mt-1 break-all font-mono text-xs">{{ $error->fingerprint }}</dd>
        </div>
    </dl>
</div>
