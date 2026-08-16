<div class="space-y-8">
    <header class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Analytics Dashboard</h1>
            <p class="mt-1 text-sm text-slate-600">Self-hosted traffic, error, and IP ban insights.</p>
        </div>

        <form wire:submit="applyFilters" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="from" class="block text-xs font-medium uppercase tracking-wide text-slate-500">From</label>
                <input
                    id="from"
                    type="date"
                    wire:model="from"
                    class="mt-1 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                >
                @error('from') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="to" class="block text-xs font-medium uppercase tracking-wide text-slate-500">To</label>
                <input
                    id="to"
                    type="date"
                    wire:model="to"
                    class="mt-1 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-200"
                >
            </div>
            <button
                type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400"
            >
                Apply
            </button>
        </form>
    </header>

    <div wire:loading.class="opacity-60" class="space-y-8 transition-opacity">
        <livewire:analytics.traffic-overview :from="$from" :to="$to" :key="'overview-'.$from.'-'.$to" />
        <livewire:analytics.traffic-chart :from="$from" :to="$to" :key="'chart-'.$from.'-'.$to" />

        <div class="grid gap-6 lg:grid-cols-2">
            <livewire:analytics.top-pages :from="$from" :to="$to" :key="'pages-'.$from.'-'.$to" />
            <livewire:analytics.top-referrers :from="$from" :to="$to" :key="'referrers-'.$from.'-'.$to" />
        </div>

        <livewire:analytics.status-breakdown :from="$from" :to="$to" :key="'status-'.$from.'-'.$to" />
        <livewire:analytics.recent-errors :from="$from" :to="$to" :key="'errors-'.$from.'-'.$to" />
        <livewire:analytics.ip-ban-manager :key="'bans'" />
    </div>
</div>
