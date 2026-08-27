@extends('layouts.app')

@section('title', 'Tenant dashboard')
@section('eyebrow', 'Tenant workspace')
@section('heading', 'Dashboard')

@section('content')
    <div class="space-y-8">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[28px] border border-cyan-200 bg-cyan-50 p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-cyan-800/70">Current room</p>
                <p class="mt-2 text-2xl font-semibold text-cyan-950">{{ $room?->room_number ?? 'None' }}</p>
                <p class="mt-1 text-sm text-cyan-800/70">{{ $room?->room_name ?? 'No room assigned' }}</p>
            </div>
            <div class="rounded-[28px] border border-orange-200 bg-orange-50 p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-orange-800/70">Current bill to be paid</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-orange-900">{{ number_format($currentDue, 2) }}</p>
                <p class="mt-1 text-sm text-orange-800/60">{{ $month->format('F Y') }}</p>
            </div>
            <div class="rounded-[28px] border border-violet-200 bg-violet-50 p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-violet-800/70">Previous months</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-violet-950">{{ number_format($previousDue, 2) }}</p>
                <p class="mt-1 text-sm text-violet-800/60">Remaining balances</p>
            </div>
            <div class="rounded-[28px] border {{ $overdueDue > 0 ? 'border-rose-200 bg-rose-50' : 'border-teal-200 bg-teal-50' }} p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider {{ $overdueDue > 0 ? 'text-rose-700/70' : 'text-teal-700/70' }}">Overdue</p>
                @if ($overdueDue > 0)
                    <p class="mt-2 text-2xl font-semibold tabular-nums text-rose-800">{{ number_format($overdueDue, 2) }}</p>
                    <p class="mt-1 inline-flex rounded-full bg-white/70 px-2.5 py-1 text-xs font-medium text-rose-700">Overdue</p>
                @else
                    <p class="mt-2 text-2xl font-semibold text-teal-900">None</p>
                    <p class="mt-1 inline-flex rounded-full bg-white/70 px-2.5 py-1 text-xs font-medium text-teal-800">Not overdue</p>
                @endif
            </div>
        </section>

        <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-semibold">Bill breakdown</h2>
            <p class="mt-1 text-sm text-slate-500">Charges for {{ $month->format('F Y') }}.</p>

            <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Payable</th>
                                <th class="px-4 py-3 font-medium">Amount</th>
                                <th class="px-4 py-3 font-medium">Paid</th>
                                <th class="px-4 py-3 font-medium">Remaining</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse ($currentBillings as $billing)
                                <tr class="bg-white">
                                    <td class="px-4 py-3 font-medium">{{ $billing->roomPayable?->payable?->title ?? '—' }}</td>
                                    <td class="px-4 py-3 tabular-nums">{{ number_format($billing->amountBilled(), 2) }}</td>
                                    <td class="px-4 py-3 tabular-nums">{{ number_format($billing->amountPaid(), 2) }}</td>
                                    <td class="px-4 py-3 font-semibold tabular-nums {{ $billing->amountDue() > 0 ? 'text-rose-700' : '' }}">{{ number_format($billing->amountDue(), 2) }}</td>
                                    <td class="px-4 py-3">
                                        @if ($billing->isPaid())
                                            <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-800">Fully paid</span>
                                        @elseif ($billing->isPartiallyPaid())
                                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800">Partial</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium text-slate-600">Unpaid</span>
                                        @endif
                                        @if ($billing->isOverdue())
                                            <span class="ml-1 inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Overdue</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-slate-500">No bills for this month yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-semibold">Previous months</h2>
            <p class="mt-1 text-sm text-slate-500">Remaining balances from earlier billing periods.</p>

            <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Period</th>
                                <th class="px-4 py-3 font-medium">Payable</th>
                                <th class="px-4 py-3 font-medium">Remaining</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse ($previousBillings as $billing)
                                <tr class="bg-white">
                                    <td class="px-4 py-3">{{ $billing->periodLabel() }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $billing->roomPayable?->payable?->title ?? '—' }}</td>
                                    <td class="px-4 py-3 font-semibold tabular-nums text-rose-700">{{ number_format($billing->amountDue(), 2) }}</td>
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
                                    <td colspan="4" class="px-4 py-10 text-center text-slate-500">No previous remaining balances.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
