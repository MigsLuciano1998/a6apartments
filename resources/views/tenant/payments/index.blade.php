@extends('layouts.app')

@section('title', 'Payments')
@section('description', 'Review recorded payments for your A6 Apartments account.')
@section('eyebrow', 'Tenant workspace')
@section('heading', 'Payments')

@section('content')
    <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold">Payment records</h2>
        <p class="mt-1 text-sm text-slate-500">Payments recorded by staff for your account.</p>

        <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200">
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
                                <td class="px-4 py-3 font-semibold tabular-nums">{{ number_format((float) $payment->amount, 2) }}</td>
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
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">No payments recorded yet.</td>
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
    </section>
@endsection
