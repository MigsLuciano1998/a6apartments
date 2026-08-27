@php
    $expense = $expense ?? null;
    $status = old('status', $expense?->status ?? 'full_paid');
@endphp

<div>
    <label for="expense_date" class="mb-1.5 block text-sm font-medium">Date</label>
    <input id="expense_date" name="expense_date" type="date" value="{{ old('expense_date', $expense?->expense_date?->toDateString() ?? now()->toDateString()) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    @error('expense_date')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="payables_id" class="mb-1.5 block text-sm font-medium">Payable</label>
    <select id="payables_id" name="payables_id" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        <option value="">Select payable</option>
        @foreach ($payables as $payable)
            <option value="{{ $payable->id }}" @selected((int) old('payables_id', $expense?->payables_id) === $payable->id)>{{ $payable->title }}</option>
        @endforeach
    </select>
    @error('payables_id')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="amount" class="mb-1.5 block text-sm font-medium">Amount</label>
    <input id="amount" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount', $expense?->amount) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    @error('amount')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="payment_method" class="mb-1.5 block text-sm font-medium">Payment method</label>
    <select id="payment_method" name="payment_method" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        <option value="cash" @selected(old('payment_method', $expense?->payment_method ?? 'cash') === 'cash')>Cash</option>
        <option value="online" @selected(old('payment_method', $expense?->payment_method) === 'online')>Online</option>
    </select>
    @error('payment_method')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="status" class="mb-1.5 block text-sm font-medium">Status</label>
    <select id="status" name="status" data-expense-status required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        <option value="full_paid" @selected($status === 'full_paid')>Fully paid</option>
        <option value="partial_payment" @selected($status === 'partial_payment')>Partial payment</option>
    </select>
    @error('status')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div class="{{ $status === 'partial_payment' ? '' : 'hidden' }}" data-partial-amount-wrap>
    <label for="partial_payment_amount" class="mb-1.5 block text-sm font-medium">Partial payment amount</label>
    <input
        id="partial_payment_amount"
        name="partial_payment_amount"
        type="number"
        step="0.01"
        min="0.01"
        value="{{ old('partial_payment_amount', $expense?->partial_payment_amount) }}"
        data-partial-payment-amount
        @disabled($status !== 'partial_payment')
        class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4"
    >
    <p class="mt-1.5 text-xs text-slate-500">Must be less than the full amount. The monthly total uses this paid amount.</p>
    @error('partial_payment_amount')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="cashier" class="mb-1.5 block text-sm font-medium">Cashier</label>
    <select id="cashier" name="cashier" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        @foreach ($cashiers as $cashier)
            <option value="{{ $cashier->id }}" @selected((int) old('cashier', $expense?->cashier ?? auth()->id()) === $cashier->id)>{{ $cashier->name }}</option>
        @endforeach
    </select>
    @error('cashier')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="remarks" class="mb-1.5 block text-sm font-medium">Remarks <span class="font-normal text-slate-400">optional</span></label>
    <textarea id="remarks" name="remarks" rows="3" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">{{ old('remarks', $expense?->remarks) }}</textarea>
    @error('remarks')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>
