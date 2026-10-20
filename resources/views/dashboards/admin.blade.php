@extends('layouts.app')

@section('title', $roleLabel.' dashboard')
@section('description', 'Staff dashboard for A6 Apartments with outstanding balances, collections, expenses, and due payments.')
@section('eyebrow', 'Staff workspace')
@section('heading', $roleLabel.' dashboard')

@section('content')
    <div class="space-y-8">
        <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="text-sm font-medium text-teal-700">Hello, {{ auth()->user()->first_name }}</p>
            <h2 class="mt-2 text-3xl font-semibold">Operations overview</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $month->format('F Y') }}</p>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <a href="{{ route('admin.payments.index') }}" class="rounded-[28px] border border-rose-200 bg-rose-50 p-6 shadow-sm transition hover:border-rose-400">
                <p class="text-xs font-medium uppercase tracking-wider text-rose-700/70">Outstanding Balance</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-rose-800">{{ peso($outstanding) }}</p>
                <p class="mt-1 text-sm text-rose-800/60">All remaining unpaid charges</p>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="rounded-[28px] border border-teal-200 bg-teal-50 p-6 shadow-sm transition hover:border-teal-500">
                <p class="text-xs font-medium uppercase tracking-wider text-teal-700/70">Collections</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-teal-900">{{ peso($collections) }}</p>
                <p class="mt-1 text-sm text-teal-800/70">Payments recorded this month</p>
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="rounded-[28px] border border-amber-200 bg-amber-50 p-6 shadow-sm transition hover:border-amber-400">
                <p class="text-xs font-medium uppercase tracking-wider text-amber-800/70">Expenses</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-amber-950">{{ peso($expenses) }}</p>
                <p class="mt-1 text-sm text-amber-900/60">Paid expenses this month</p>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="rounded-[28px] border border-orange-200 bg-orange-50 p-6 shadow-sm transition hover:border-orange-400">
                <p class="text-xs font-medium uppercase tracking-wider text-orange-800/70">Due Payments Amount</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-orange-900">{{ peso($duePayments) }}</p>
                <p class="mt-1 text-sm text-orange-800/60">Remaining this month</p>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="rounded-[28px] border border-sky-200 bg-sky-50 p-6 shadow-sm transition hover:border-sky-400">
                <p class="text-xs font-medium uppercase tracking-wider text-sky-800/70">Total Unpaid Bills</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-sky-950">{{ peso($unpaidBills) }}</p>
                <p class="mt-1 text-sm text-sky-800/60">Non-rent remaining this month</p>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="rounded-[28px] border border-violet-200 bg-violet-50 p-6 shadow-sm transition hover:border-violet-400">
                <p class="text-xs font-medium uppercase tracking-wider text-violet-800/70">Total Unpaid Rent</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-violet-950">{{ peso($unpaidRent) }}</p>
                <p class="mt-1 text-sm text-violet-800/60">Rent remaining this month</p>
            </a>
        </section>

        <section class="grid gap-4 sm:grid-cols-2">
            <a href="{{ route('admin.rooms.index') }}" class="rounded-[28px] border border-stone-200 bg-[#fbf8f3] p-6 shadow-sm transition hover:border-teal-700">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Rooms</p>
                <p class="mt-2 text-2xl font-semibold">{{ $roomCount }}</p>
            </a>
            <a href="{{ route('admin.tenants.index') }}" class="rounded-[28px] border border-cyan-200 bg-cyan-50 p-6 shadow-sm transition hover:border-cyan-400">
                <p class="text-xs font-medium uppercase tracking-wider text-cyan-800/70">Tenants</p>
                <p class="mt-2 text-2xl font-semibold text-cyan-950">{{ $tenantCount }}</p>
            </a>
        </section>
    </div>
@endsection
