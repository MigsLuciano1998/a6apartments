@extends('layouts.app')

@section('title', 'Expenses')
@section('description', 'Track operating expenses for A6 Apartments.')
@section('eyebrow', 'Staff workspace')
@section('heading', 'Expenses')

@section('content')
    <div class="space-y-8" data-expenses-page>
        <section class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Current month</p>
                <p class="mt-2 text-2xl font-semibold">{{ $month->format('F Y') }}</p>
            </div>
            <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Monthly total expenses</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-rose-700">{{ peso($monthlyTotal) }}</p>
            </div>
        </section>

        <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold">Expense records</h2>
                    <p class="mt-1 text-sm text-slate-500">Record building and operating expenses. Partial payments count only the amount paid toward the monthly total.</p>
                </div>
                <a href="{{ route('admin.expenses.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
                    Add expense
                </a>
            </div>

            <x-live-table table="expenses" :value="$search" placeholder="Payable, cashier, method, status, or remarks">
                @include('admin.expenses.results')
            </x-live-table>
        </section>
    </div>
@endsection
