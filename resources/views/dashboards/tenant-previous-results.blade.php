<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Period</th>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Payable</th>
                    <th class="px-4 py-3 font-medium">Remaining</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($previousBillings as $billing)
                    <tr class="bg-white">
                        <td class="px-4 py-3">{{ $billing->periodLabel() }}</td>
                        <td class="px-4 py-3">{{ $billing->roomAssignment?->room?->room_number ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium">{{ $billing->roomPayable?->payable?->title ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold tabular-nums text-rose-700">{{ peso($billing->amountDue()) }}</td>
                        <td class="px-4 py-3">
                            @if ($billing->isOverdue())
                                <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Overdue</span>
                            @else
                                <span class="inline-flex rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium text-slate-600">Not overdue</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                            @if ($previousSearch)
                                No previous balances match “{{ $previousSearch }}”.
                            @else
                                No previous remaining balances.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($previousBillings->hasPages())
    <div class="mt-4">
        {{ $previousBillings->links() }}
    </div>
@endif
