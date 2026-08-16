@if ($paginator->hasPages())
    <nav aria-label="Pagination" class="flex items-center justify-between gap-3 text-sm">
        <div class="text-slate-500">
            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
        </div>
        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="rounded-md border border-slate-200 px-3 py-1 text-slate-400">Previous</span>
            @else
                <button type="button" wire:click="previousPage" class="rounded-md border border-slate-300 px-3 py-1 hover:bg-slate-50">Previous</button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage" class="rounded-md border border-slate-300 px-3 py-1 hover:bg-slate-50">Next</button>
            @else
                <span class="rounded-md border border-slate-200 px-3 py-1 text-slate-400">Next</span>
            @endif
        </div>
    </nav>
@endif
