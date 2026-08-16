<section aria-labelledby="trend-heading">
    <h2 id="trend-heading" class="mb-4 text-lg font-semibold">Traffic Trend</h2>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        @if ($trend->isEmpty())
            <p class="p-6 text-sm text-slate-500">No traffic recorded for this date range.</p>
        @else
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Date</th>
                        <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Page Views</th>
                        <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php($max = max(1, (int) $trend->max('total')))
                    @foreach ($trend as $row)
                        <tr>
                            <td class="px-4 py-3">{{ $row->date }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format($row->total) }}</td>
                            <td class="px-4 py-3">
                                <div class="h-2 rounded-full bg-slate-100">
                                    <div
                                        class="h-2 rounded-full bg-slate-700"
                                        style="width: {{ round(($row->total / $max) * 100) }}%"
                                    ></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</section>
