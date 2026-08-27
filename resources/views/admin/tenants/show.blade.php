@extends('layouts.app')

@section('title', $tenant->name)
@section('description', 'Tenant profile, room assignment, and billing for '.$tenant->name.' at A6 Apartments.')
@section('eyebrow', 'Tenants')
@section('heading', 'Tenant info')

@section('content')
    @php
        $roomPayables = $tenant->currentAssignment?->room?->roomPayables ?? collect();
        $currentRoom = $tenant->currentAssignment?->room;
        $currentBillings = ($tenant->currentAssignment?->roomBillings ?? collect())
            ->filter(fn ($billing) => $billing->due_date?->isSameMonth(now()));
        $billingsByPayable = $currentBillings->keyBy('room_payables_id');
        $amountDue = (float) $currentBillings->sum(fn ($billing) => $billing->amountDue());
        $tenantTypeLabel = $tenant->tenant_type === 'bills_only' ? 'Bills Only Tenant' : 'Regular Tenant';
    @endphp

    <section class="mx-auto max-w-4xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-medium text-teal-700">{{ $tenantTypeLabel }}</p>
                <h2 class="mt-1 text-2xl font-semibold">
                    {{ $tenant->name }}
                </h2>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $tenant->phone ?: '—' }}
                    <span class="mx-2 text-slate-300">|</span>
                    {{ $tenant->email }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                <a href="{{ route('admin.tenants.index') }}" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Back to tenants</a>
            </div>
        </div>

        <div class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Payables</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        @if ($currentRoom)
                            Charges for room
                            <a href="{{ route('admin.rooms.show', $currentRoom) }}" class="font-medium text-teal-800 hover:underline">{{ $currentRoom->room_number }}</a>
                        @else
                            Assign this tenant to a room to see payable details.
                        @endif
                    </p>
                </div>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-end">
                    <div class="px-5 py-3 sm:min-w-48 sm:text-right">
                        <p class="text-xs font-medium uppercase tracking-wider" style="color: #000">Total to be paid</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums text-rose-700">{{ number_format($amountDue, 2) }}</p>
                    </div>
                    <div class="px-5 py-3 sm:min-w-48 sm:text-right">
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Previous balance</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums {{ $previousBalance > 0 ? 'text-rose-700' : 'text-slate-700' }}">{{ number_format($previousBalance, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-3 overflow-hidden rounded-2xl border border-stone-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Payable</th>
                            <th class="px-4 py-3 font-medium">Amount</th>
                            <th class="px-4 py-3 font-medium">Payment</th>
                            <th class="px-4 py-3 font-medium">Billing</th>
                            <th class="px-4 py-3 font-medium">Start</th>
                            <th class="px-4 py-3 font-medium">End</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($roomPayables as $roomPayable)
                            @php
                                $billing = $billingsByPayable->get($roomPayable->id);
                                $paymentStatus = $billing?->paymentStatus();
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium">
                                    {{ $roomPayable->payable?->title ?? '—' }}
                                    @if ($roomPayable->isCurrent())
                                        <span class="ml-2 inline-flex rounded-full bg-teal-50 px-2 py-0.5 text-xs font-medium text-teal-800">Current</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ number_format((float) $roomPayable->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    @if ($paymentStatus === 'paid')
                                        <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-800">Fully paid</span>
                                    @elseif ($paymentStatus === 'partial')
                                        <span class="block text-sm font-medium text-amber-800">Partial</span>
                                        <span class="mt-0.5 block text-xs text-slate-500">Remaining {{ number_format($billing->amountDue(), 2) }}</span>
                                    @elseif ($paymentStatus === 'unpaid')
                                        <span class="inline-flex rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium text-slate-600">Unpaid</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $roomPayable->frequencyLabel() }}</td>
                                <td class="px-4 py-3">{{ $roomPayable->start_date?->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $roomPayable->end_date?->format('M d, Y') ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                    @if ($currentRoom)
                                        No payables assigned to this tenant’s room yet.
                                    @else
                                        No payables to show.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($previousBalance > 0)
            @php
                $previousBillings = $tenant->roomAssignments
                    ->flatMap->roomBillings
                    ->filter(fn ($billing) => $billing->due_date?->lt(now()->startOfMonth()) && $billing->amountDue() > 0)
                    ->sortBy('due_date');
            @endphp
            <div class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                <h3 class="text-lg font-semibold">Previous balances</h3>
                <p class="mt-1 text-sm text-slate-500">Remaining unpaid charges from earlier months.</p>
                <div class="mt-4 overflow-hidden rounded-2xl border border-stone-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Period</th>
                                <th class="px-4 py-3 font-medium">Payable</th>
                                <th class="px-4 py-3 font-medium">Remaining</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($previousBillings as $billing)
                                <tr>
                                    <td class="px-4 py-3">{{ $billing->periodLabel() }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $billing->roomPayable?->payable?->title ?? '—' }}</td>
                                    <td class="px-4 py-3 font-semibold tabular-nums text-rose-700">{{ number_format($billing->amountDue(), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h3 class="text-lg font-semibold">Room assignments</h3>
            <div class="mt-4 overflow-hidden rounded-2xl border border-stone-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Room</th>
                            <th class="px-4 py-3 font-medium">Start</th>
                            <th class="px-4 py-3 font-medium">End</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($tenant->roomAssignments as $assignment)
                            <tr>
                                <td class="px-4 py-3 font-medium">
                                    @if ($assignment->room)
                                        <a href="{{ route('admin.rooms.show', $assignment->room) }}" class="text-teal-800 hover:underline">{{ $assignment->room->room_number }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $assignment->start_date?->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $assignment->end_date?->format('M d, Y') ?: '—' }}</td>
                                <td class="px-4 py-3 capitalize">{{ $assignment->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">No room assignment yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
