<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Payable</th>
                    <th class="px-4 py-3 font-medium">Balance</th>
                    <th class="px-4 py-3 font-medium">Reading</th>
                    <th class="px-4 py-3 font-medium">Due date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($billings as $billing)
                    @php
                        $isMetered = (bool) $billing->roomPayable?->payable?->isMetered();
                        $reading = $isMetered && $billing->current_reading !== null
                            ? number_format((float) $billing->current_reading, 2)
                            : 'N/A';
                    @endphp
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium">{{ $billing->roomPayable?->payable?->title ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold tabular-nums {{ $billing->amountDue() > 0 ? 'text-rose-700' : '' }}">{{ peso($billing->amountDue()) }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ $reading }}</td>
                        <td class="px-4 py-3">{{ $billing->due_date?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                            @if ($search)
                                No payables match “{{ $search }}”.
                            @else
                                No published bills for this room yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($billings->hasPages())
    <div class="mt-4">
        {{ $billings->links() }}
    </div>
@endif
