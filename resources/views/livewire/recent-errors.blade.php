<section aria-labelledby="errors-heading">
    <h2 id="errors-heading" class="mb-4 text-lg font-semibold">Recent Errors</h2>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        @if ($errors->isEmpty())
            <p class="p-6 text-sm text-slate-500">No errors recorded for this date range.</p>
        @else
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Exception</th>
                        <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Path</th>
                        <th scope="col" class="px-4 py-3 text-right font-medium text-slate-600">Count</th>
                        <th scope="col" class="px-4 py-3 text-right font-medium text-slate-600">Last Seen</th>
                        <th scope="col" class="px-4 py-3 text-right font-medium text-slate-600">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($errors as $error)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ class_basename($error->exception_class) }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($error->message, 80) }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $error->path }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($error->occurrence_count) }}</td>
                            <td class="px-4 py-3 text-right text-xs text-slate-500">{{ $error->last_occurred_at?->toDateTimeString() }}</td>
                            <td class="px-4 py-3 text-right">
                                <a
                                    href="{{ route(config('analytics.dashboard.route_prefix').'errors.show', $error) }}"
                                    class="text-sm font-medium text-slate-700 underline hover:text-slate-900"
                                >
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $errors->links() }}
            </div>
        @endif
    </div>
</section>
