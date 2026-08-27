@extends('layouts.app')

@section('title', 'Expense')
@section('description', 'View expense details recorded for A6 Apartments operations.')
@section('eyebrow', 'Expenses')
@section('heading', 'Expense info')

@section('content')
    <section class="mx-auto max-w-4xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">
                    {{ $expense->payable?->title ?? 'Expense' }}
                    <span class="mx-2 font-normal text-slate-300">|</span>
                    {{ number_format((float) $expense->amount, 2) }}
                    <span class="mx-2 font-normal text-slate-300">|</span>
                    <span class="inline-flex align-middle rounded-full px-2.5 py-0.5 text-xs font-medium {{ $expense->isPartial() ? 'bg-amber-50 text-amber-800' : 'bg-teal-50 text-teal-800' }}">{{ $expense->statusLabel() }}</span>
                </h2>
                <p class="mt-2 text-sm text-slate-600">{{ $expense->remarks ?: '—' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.expenses.edit', $expense) }}" class="rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                <a href="{{ route('admin.expenses.index') }}" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Back to expenses</a>
            </div>
        </div>

        <div class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Date</dt>
                    <dd class="mt-1 font-medium">{{ $expense->expense_date?->format('M d, Y') }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Payment method</dt>
                    <dd class="mt-1 font-medium">{{ $expense->paymentMethodLabel() }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Amount</dt>
                    <dd class="mt-1 font-medium tabular-nums">{{ number_format((float) $expense->amount, 2) }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Amount paid</dt>
                    <dd class="mt-1 font-medium tabular-nums">{{ number_format($expense->amountPaid(), 2) }}</dd>
                </div>
                @if ($expense->isPartial())
                    <div class="rounded-2xl bg-[#fbf8f3] p-4">
                        <dt class="text-xs uppercase tracking-wider text-slate-400">Remaining</dt>
                        <dd class="mt-1 font-medium tabular-nums text-rose-700">{{ number_format($expense->amountRemaining(), 2) }}</dd>
                    </div>
                @endif
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Cashier</dt>
                    <dd class="mt-1 font-medium">{{ $expense->cashierUser?->name ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </section>
@endsection
