<section aria-labelledby="status-heading">
    <h2 id="status-heading" class="mb-4 text-lg font-semibold">Response Status Breakdown</h2>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        @if ($statuses->isEmpty())
            <p class="p-6 text-sm text-slate-500">No response statuses recorded for this date range.</p>
        @else
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                        <th scope="col" class="px-4 py-3 text-right font-medium text-slate-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($statuses as $status)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $status->status_code }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($status->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</section>
