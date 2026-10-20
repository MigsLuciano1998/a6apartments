<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium">Tenant</th>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Payable</th>
                    <th class="px-4 py-3 font-medium">Method</th>
                    <th class="px-4 py-3 font-medium">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($paymentsMade as $payment)
                    <tr class="bg-white">
                        <td class="px-4 py-3">{{ $payment->payment_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-medium">
                            @if ($payment->roomBilling?->roomAssignment?->user?->role === 'tenant')
                                <a href="{{ route('admin.tenants.show', $payment->roomBilling->roomAssignment->user) }}" class="text-teal-800 hover:underline">{{ $payment->roomBilling->roomAssignment->user->name }}</a>
                            @else
                                {{ $payment->roomBilling?->roomAssignment?->user?->name ?? '—' }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($payment->roomBilling?->roomAssignment?->room)
                                <a href="{{ route('admin.rooms.show', $payment->roomBilling->roomAssignment->room) }}" class="text-teal-800 hover:underline">{{ $payment->roomBilling->roomAssignment->room->room_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $payment->roomBilling?->roomPayable?->payable?->title ?? '—' }}</td>
                        <td class="px-4 py-3">
                            {{ $payment->paymentMethodLabel() }}
                            @if ($payment->payment_method === 'online' && $payment->reference_number)
                                <span class="block text-xs text-slate-400">{{ $payment->reference_number }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold tabular-nums">{{ peso($payment->amount) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                            @if ($paymentsSearch)
                                No payments match “{{ $paymentsSearch }}”.
                            @else
                                No payments recorded this month yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($paymentsMade->hasPages())
    <div class="mt-4">
        {{ $paymentsMade->links() }}
    </div>
@endif
