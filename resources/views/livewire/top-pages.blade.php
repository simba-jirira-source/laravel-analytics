<section aria-labelledby="top-pages-heading">
    <h2 id="top-pages-heading" class="mb-4 text-lg font-semibold">Top Pages</h2>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        @if ($pages->isEmpty())
            <p class="p-6 text-sm text-slate-500">No page views recorded for this date range.</p>
        @else
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Path</th>
                        <th scope="col" class="px-4 py-3 text-right font-medium text-slate-600">Views</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($pages as $page)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $page->path }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($page->views) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</section>
