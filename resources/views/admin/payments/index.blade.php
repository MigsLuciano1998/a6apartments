@extends('layouts.app')

@section('title', 'Payments')
@section('description', 'Record and review tenant payments for A6 Apartments.')
@section('eyebrow', 'Staff workspace')
@section('heading', 'Payments')

@section('content')
    <div class="space-y-8" data-payments-page data-payables-url="{{ route('admin.payments.index') }}/tenants">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Current month</p>
                <p class="mt-2 text-2xl font-semibold">{{ $month->format('F Y') }}</p>
            </div>
            <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total rent to be paid</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-rose-700">{{ peso($totals['rent']) }}</p>
            </div>
            <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total bills to be paid</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-rose-700">{{ peso($totals['bills']) }}</p>
            </div>
            <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Previous balances</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-rose-700">{{ peso($previousTotal) }}</p>
            </div>
        </section>

        <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold">Tenants</h2>
                    <p class="mt-1 text-sm text-slate-500">Search updates the list as you type. Record rent for this month, plus unpaid water and electricity from any month.</p>
                </div>
                <button type="button" data-open-payment class="inline-flex items-center justify-center rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
                    Add Payment
                </button>
            </div>

            <x-live-table table="tenants" :value="$search" label="Search tenants" placeholder="Name or room number">
                @include('admin.payments.results')
            </x-live-table>
        </section>

        <section class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-semibold">Payments made</h2>
            <p class="mt-1 text-sm text-slate-500">Recorded payments for {{ $month->format('F Y') }}.</p>

            <x-live-table param="payments_q" table="payments" page-name="payments_page" :value="$paymentsSearch" placeholder="Tenant, room, payable, or method">
                @include('admin.payments.made-results')
            </x-live-table>
        </section>

        <section id="previous-balances" class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-semibold">Previous balances</h2>
            <p class="mt-1 text-sm text-slate-500">Remaining unpaid and partial charges, latest 10 records per page.</p>

            <x-live-table param="balances_q" table="balances" page-name="balances_page" :value="$balancesSearch" placeholder="Tenant, room, payable, or period">
                @include('admin.payments.balance-results')
            </x-live-table>
        </section>
    </div>

    <dialog data-payment-modal class="w-[min(32rem,calc(100%-2rem))] rounded-[28px] border border-stone-200 bg-white p-0 shadow-xl backdrop:bg-black/40">
        <form method="POST" action="{{ route('admin.payments.store') }}" class="p-6 sm:p-8">
            @csrf
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-xl font-semibold">Add payment</h3>
                    <p class="mt-1 text-sm text-slate-500" data-payment-subtitle>Select unpaid rent for this month, plus unpaid water and electricity from any month.</p>
                </div>
                <button type="button" data-close-payment class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Close</button>
            </div>

            <div class="mt-5">
                <label for="payment_tenant_id" class="mb-1.5 block text-sm font-medium">Tenant</label>
                <select id="payment_tenant_id" name="tenant_id" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    <option value="">Select tenant</option>
                    @foreach ($payableTenants as $payableTenant)
                        <option value="{{ $payableTenant->id }}">{{ $payableTenant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-5" data-payment-list>
                <p class="text-sm text-slate-500">Select a tenant to load payables.</p>
            </div>

            <div class="mt-5">
                <label for="payment_method" class="mb-1.5 block text-sm font-medium">Payment method</label>
                <select id="payment_method" name="payment_method" required data-payment-method class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    <option value="cash">Cash</option>
                    <option value="online">Online</option>
                </select>
            </div>

            <div class="mt-5 hidden" data-payment-reference-wrap>
                <label for="reference_number" class="mb-1.5 block text-sm font-medium">Reference number</label>
                <input
                    id="reference_number"
                    name="reference_number"
                    type="text"
                    data-payment-reference
                    class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4"
                    placeholder="GCash / bank reference"
                >
            </div>

            <div class="mt-5 space-y-3" data-partial-payment>
                <label data-pay-partial-label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-stone-200 px-4 py-3 has-[:disabled]:cursor-not-allowed has-[:disabled]:opacity-50">
                    <input type="checkbox" name="pay_partial" value="1" data-pay-partial disabled class="h-4 w-4 rounded border-stone-300 text-teal-700 focus:ring-teal-700">
                    <span>
                        <span class="block text-sm font-medium">Pay Partially</span>
                        <span class="block text-xs text-slate-500" data-partial-hint>Select one payable to enter a custom amount.</span>
                    </span>
                </label>
                <div class="hidden" data-partial-amount-wrap>
                    <label for="partial_amount" class="mb-1.5 block text-sm font-medium">Amount to pay</label>
                    <x-peso-field>
                        <input
                            id="partial_amount"
                            name="partial_amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            inputmode="decimal"
                            data-partial-amount
                            disabled
                            class="w-full rounded-2xl border border-stone-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4"
                        >
                    </x-peso-field>
                    <p class="mt-1.5 text-xs text-slate-500" data-partial-balance></p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" data-close-payment class="text-sm font-medium text-slate-500 hover:text-teal-800">Cancel</button>
                <button type="submit" data-pay-submit disabled class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-50">
                    Pay
                </button>
            </div>
        </form>
    </dialog>
@endsection
