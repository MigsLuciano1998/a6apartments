<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Payable</th>
                    <th class="px-4 py-3 font-medium">Amount</th>
                    <th class="px-4 py-3 font-medium">Method</th>
                    <th class="px-4 py-3 font-medium">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($payments as $payment)
                    <tr class="bg-white">
                        <td class="px-4 py-3">{{ $payment->payment_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">{{ $payment->roomBilling?->roomAssignment?->room?->room_number ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium">{{ $payment->roomBilling?->roomPayable?->payable?->title ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold tabular-nums">{{ peso($payment->amount) }}</td>
                        <td class="px-4 py-3">
                            {{ $payment->paymentMethodLabel() }}
                            @if ($payment->payment_method === 'online' && $payment->reference_number)
                                <span class="block text-xs text-slate-400">{{ $payment->reference_number }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $payment->remarks ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                            @if ($search)
                                No payments match “{{ $search }}”.
                            @else
                                No payments recorded yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($payments->hasPages())
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
@endif
