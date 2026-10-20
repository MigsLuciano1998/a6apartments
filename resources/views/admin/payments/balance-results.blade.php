<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Period</th>
                    <th class="px-4 py-3 font-medium">Tenant</th>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Payable</th>
                    <th class="px-4 py-3 font-medium">Remaining</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($previousBalances as $billing)
                    <tr class="bg-white">
                        <td class="px-4 py-3">{{ $billing->periodLabel() }}</td>
                        <td class="px-4 py-3 font-medium">
                            @if ($billing->roomAssignment?->user?->role === 'tenant')
                                <a href="{{ route('admin.tenants.show', $billing->roomAssignment->user) }}" class="text-teal-800 hover:underline">{{ $billing->roomAssignment->user->name }}</a>
                            @else
                                {{ $billing->roomAssignment?->user?->name ?? '—' }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($billing->roomAssignment?->room)
                                <a href="{{ route('admin.rooms.show', $billing->roomAssignment->room) }}" class="text-teal-800 hover:underline">{{ $billing->roomAssignment->room->room_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $billing->roomPayable?->payable?->title ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold tabular-nums text-rose-700">{{ peso($billing->amountDue()) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                            @if ($balancesSearch)
                                No previous balances match “{{ $balancesSearch }}”.
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

@if ($previousBalances->total() > 0)
    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">Showing {{ $previousBalances->firstItem() }}–{{ $previousBalances->lastItem() }} of {{ $previousBalances->total() }}</p>
        @if ($previousBalances->hasPages())
            <div>
                {{ $previousBalances->links() }}
            </div>
        @endif
    </div>
@endif
