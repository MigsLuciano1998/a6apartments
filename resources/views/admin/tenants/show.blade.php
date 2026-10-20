@extends('layouts.app')

@section('title', $tenant->name)
@section('description', 'Tenant profile, room assignment, and billing for '.$tenant->name.' at A6 Apartments.')
@section('eyebrow', 'Tenants')
@section('heading', 'Tenant info')

@section('content')
    @php
        $tenantTypeLabel = $tenant->tenant_type === 'bills_only' ? 'Bills Only Tenant' : 'Regular Tenant';
        $currentRooms = $tenant->currentAssignments->map->room->filter();
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
                    <h3 class="text-lg font-semibold">Balances</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        @if ($currentRooms->isNotEmpty())
                            Totals for
                            {{ $currentRooms->pluck('room_number')->join(', ') }}.
                        @else
                            Assign this tenant to a room to see payable totals.
                        @endif
                    </p>
                </div>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-end">
                    <div class="px-5 py-3 sm:min-w-48 sm:text-right">
                        <p class="text-xs font-medium uppercase tracking-wider" style="color: #000">Total to be paid</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums text-rose-700">{{ peso($amountDue) }}</p>
                    </div>
                    <div class="px-5 py-3 sm:min-w-48 sm:text-right">
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Previous balance</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums {{ $previousBalance > 0 ? 'text-rose-700' : 'text-slate-700' }}">{{ peso($previousBalance) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h3 class="text-lg font-semibold">Room assignments</h3>
            <p class="mt-1 text-sm text-slate-500">All rooms assigned to this tenant.</p>
            <x-live-table param="assignments_q" table="assignments" page-name="assignments_page" :value="$assignmentSearch" placeholder="Room or status" class="mt-4">
                @include('admin.tenants.assignment-results')
            </x-live-table>
        </div>
    </section>
@endsection
