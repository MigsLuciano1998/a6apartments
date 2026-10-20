<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Payable</th>
                    <th class="px-4 py-3 font-medium">Period</th>
                    <th class="px-4 py-3 font-medium">Amount</th>
                    <th class="px-4 py-3 font-medium">Billing</th>
                    <th class="px-4 py-3 font-medium">Payment</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($roomPayables as $roomPayable)
                    @php
                        $billing = $roomPayable->latestBilling();
                        $paymentStatus = $billing?->paymentStatus();
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            @if ($roomPayable->payable)
                                <a href="{{ route('admin.payables.show', $roomPayable->payable) }}" class="text-teal-800 hover:underline">{{ $roomPayable->payable->title }}</a>
                            @else
                                —
                            @endif
                            @if ($roomPayable->isCurrent())
                                <span class="ml-2 inline-flex rounded-full bg-teal-50 px-2 py-0.5 text-xs font-medium text-teal-800">Current</span>
                            @endif
                            @if ($roomPayable->payable?->isMetered() && $roomPayable->current_reading !== null)
                                <span class="mt-1 block text-xs font-normal text-slate-500">
                                    Reading {{ number_format((float) $roomPayable->previous_reading, 2) }} → {{ number_format((float) $roomPayable->current_reading, 2) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $roomPayable->periodLabel() }}</td>
                        <td class="px-4 py-3">{{ peso($roomPayable->amount) }}</td>
                        <td class="px-4 py-3">{{ $roomPayable->frequencyLabel() }}</td>
                        <td class="px-4 py-3">
                            @if ($paymentStatus === 'paid')
                                <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-800">Paid</span>
                            @elseif ($paymentStatus === 'partial')
                                <span class="block text-sm font-medium text-amber-800">Partial</span>
                                <span class="mt-0.5 block text-xs text-slate-500">Remaining {{ peso($billing->amountDue()) }}</span>
                            @elseif ($paymentStatus === 'unpaid')
                                <span class="inline-flex rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium text-slate-600">Unpaid</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.rooms.show', ['room' => $room, 'edit_payable' => $roomPayable->id]) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                                @if ($roomPayable->isCurrent() && $roomPayable->isRecurring())
                                    <form method="POST" action="{{ route('admin.rooms.payables.end', [$room, $roomPayable]) }}" onsubmit="return confirm('End this payable for the room?')">
                                        @csrf
                                        <button type="submit" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">End</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.rooms.payables.destroy', [$room, $roomPayable]) }}" onsubmit="return confirm('Delete this payable from the room?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                            @if ($payableSearch)
                                No payables match “{{ $payableSearch }}”.
                            @else
                                No payables assigned to this room yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($roomPayables->hasPages())
    <div class="mt-4">
        {{ $roomPayables->links() }}
    </div>
@endif
