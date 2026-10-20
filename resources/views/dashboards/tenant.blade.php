@extends('layouts.app')

@section('title', 'Tenant dashboard')
@section('description', 'View your current bill, payment history, and announcements for your A6 Apartments unit.')
@section('eyebrow', 'Tenant workspace')
@section('heading', 'Dashboard')

@section('content')
    <div class="space-y-8">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[28px] border border-cyan-200 bg-cyan-50 p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-cyan-800/70">{{ $rooms->count() === 1 ? 'Current room' : 'Current rooms' }}</p>
                <p class="mt-2 text-2xl font-semibold text-cyan-950">{{ $rooms->pluck('room.room_number')->filter()->join(', ') ?: 'None' }}</p>
                <p class="mt-1 text-sm text-cyan-800/70">{{ $rooms->isEmpty() ? 'No room assigned' : ($rooms->pluck('room.room_name')->filter()->join(', ') ?: '—') }}</p>
            </div>
            <div class="rounded-[28px] border border-orange-200 bg-orange-50 p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-orange-800/70">Current bill to be paid</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-orange-900">{{ peso($currentDue) }}</p>
                <p class="mt-1 text-sm text-orange-800/60">{{ $month->format('F Y') }}</p>
            </div>
            <div class="rounded-[28px] border border-violet-200 bg-violet-50 p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-violet-800/70">Previous months</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-violet-950">{{ peso($previousDue) }}</p>
                <p class="mt-1 text-sm text-violet-800/60">Remaining balances</p>
            </div>
            <div class="rounded-[28px] border {{ $overdueDue > 0 ? 'border-rose-200 bg-rose-50' : 'border-teal-200 bg-teal-50' }} p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider {{ $overdueDue > 0 ? 'text-rose-700/70' : 'text-teal-700/70' }}">Overdue</p>
                @if ($overdueDue > 0)
                    <p class="mt-2 text-2xl font-semibold tabular-nums text-rose-800">{{ peso($overdueDue) }}</p>
                    <p class="mt-1 inline-flex rounded-full bg-white/70 px-2.5 py-1 text-xs font-medium text-rose-700">Overdue</p>
                @else
                    <p class="mt-2 text-2xl font-semibold text-teal-900">None</p>
                    <p class="mt-1 inline-flex rounded-full bg-white/70 px-2.5 py-1 text-xs font-medium text-teal-800">Not overdue</p>
                @endif
            </div>
        </section>

        <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-semibold">Bill breakdown</h2>
            <p class="mt-1 text-sm text-slate-500">Open a room to view its published bills for {{ $month->format('F Y') }}.</p>

            <x-live-table param="rooms_q" table="rooms" page-name="rooms_page" :value="$roomsSearch" placeholder="Room number or name">
                @include('dashboards.tenant-rooms-results')
            </x-live-table>
        </section>

        <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-semibold">Previous months</h2>
            <p class="mt-1 text-sm text-slate-500">Remaining balances from earlier billing periods.</p>

            <x-live-table param="previous_q" table="previous" page-name="previous_page" :value="$previousSearch" placeholder="Period, room, or payable">
                @include('dashboards.tenant-previous-results')
            </x-live-table>
        </section>
    </div>
@endsection
