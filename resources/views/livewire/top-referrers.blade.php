<section aria-labelledby="top-referrers-heading">
    <h2 id="top-referrers-heading" class="mb-4 text-lg font-semibold">Top Referrers</h2>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        @if ($referrers->isEmpty())
            <p class="p-6 text-sm text-slate-500">No referrers recorded for this date range.</p>
        @else
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Referrer</th>
                        <th scope="col" class="px-4 py-3 text-right font-medium text-slate-600">Views</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($referrers as $referrer)
                        <tr>
                            <td class="px-4 py-3">{{ $referrer->referrer_host }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($referrer->views) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</section>
