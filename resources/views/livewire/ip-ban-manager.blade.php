<section aria-labelledby="ip-ban-heading">
    <h2 id="ip-ban-heading" class="mb-4 text-lg font-semibold">IP Ban Management</h2>

    <div class="space-y-6">
        <form wire:submit="banIp" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Ban IP Address</h3>

            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div>
                    <label for="ipAddress" class="block text-sm font-medium text-slate-700">IP Address</label>
                    <input
                        id="ipAddress"
                        type="text"
                        wire:model="ipAddress"
                        placeholder="203.0.113.10"
                        class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                    >
                    @error('ipAddress') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="reason" class="block text-sm font-medium text-slate-700">Reason</label>
                    <input
                        id="reason"
                        type="text"
                        wire:model="reason"
                        class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                    >
                </div>
                <div>
                    <label for="expiresAt" class="block text-sm font-medium text-slate-700">Expires At</label>
                    <input
                        id="expiresAt"
                        type="datetime-local"
                        wire:model="expiresAt"
                        class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                    >
                    @error('expiresAt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <button
                type="submit"
                class="mt-4 rounded-md bg-red-700 px-4 py-2 text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-300"
            >
                Ban IP
            </button>
        </form>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            @if ($bans->isEmpty())
                <p class="p-6 text-sm text-slate-500">No IP bans have been recorded.</p>
            @else
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">IP</th>
                            <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Reason</th>
                            <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                            <th scope="col" class="px-4 py-3 text-left font-medium text-slate-600">Expires</th>
                            <th scope="col" class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($bans as $ban)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ $ban->ip_address }}</td>
                                <td class="px-4 py-3">{{ $ban->reason ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($ban->isCurrentlyActive())
                                        <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">Active</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $ban->expires_at?->toDateTimeString() ?? 'Never' }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if ($ban->is_active)
                                        <button
                                            type="button"
                                            wire:click="unbanIp('{{ $ban->ip_address }}')"
                                            wire:confirm="Remove the active ban for this IP address?"
                                            class="text-sm font-medium text-slate-700 underline hover:text-slate-900"
                                        >
                                            Unban
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $bans->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
