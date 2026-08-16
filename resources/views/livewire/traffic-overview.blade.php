<section aria-labelledby="overview-heading">
    <h2 id="overview-heading" class="mb-4 text-lg font-semibold">Overview</h2>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([
            'page_views' => 'Page Views',
            'unique_visitors' => 'Unique Visitors',
            'visits' => 'Visits',
            'errors' => 'Errors',
            'active_bans' => 'Active IP Bans',
        ] as $key => $label)
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-semibold">{{ number_format($metrics[$key] ?? 0) }}</p>
            </div>
        @endforeach
    </div>
</section>
