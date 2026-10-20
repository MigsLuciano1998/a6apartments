@php
    $editing = $editingRoomPayable ?? null;
    $selectedPayableId = (int) old('payables_id', $editing?->payables_id);
    $selectedPayable = $availablePayables->firstWhere('id', $selectedPayableId);
    $isMetered = (bool) $selectedPayable?->isMetered();
    $defaultFormula = $selectedPayable?->isElectricity()
        ? \App\Services\BillingFormulaEvaluator::ELECTRICITY_FORMULA
        : \App\Services\BillingFormulaEvaluator::WATER_FORMULA;
@endphp

<div>
    <label for="payables_id" class="mb-1.5 block text-sm font-medium">Payable</label>
    <select id="payables_id" name="payables_id" required @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
        @unless ($editing)
            <option value="">Select payable</option>
        @endunless
        @foreach ($availablePayables as $payable)
            @php
                $formula = $editing && (int) $editing->payables_id === $payable->id && $editing->billing_formula
                    ? $editing->billing_formula
                    : ($room->roomPayables->where('payables_id', $payable->id)->sortByDesc('start_date')->first()?->billing_formula ?: $payable->defaultFormula());
            @endphp
            <option
                value="{{ $payable->id }}"
                data-kind="{{ $payable->meterKind() }}"
                data-formula="{{ $formula }}"
                @selected($selectedPayableId === $payable->id)
            >{{ $payable->title }}</option>
        @endforeach
    </select>
    @error('payables_id')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div data-standard-fields class="col-span-full grid gap-3 sm:grid-cols-2 lg:grid-cols-3 {{ $isMetered ? 'hidden' : '' }}">
    <div>
        <label for="amount" class="mb-1.5 block text-sm font-medium">Amount</label>
        <x-peso-field>
            <input id="amount" name="amount" type="number" step="0.01" min="0" value="{{ old('amount', $editing?->amount) }}" @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 py-2.5 pl-10 pr-4 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
        </x-peso-field>
        @error('amount')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="frequency" class="mb-1.5 block text-sm font-medium">Billing</label>
        <select id="frequency" name="frequency" @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
            <option value="recurring" @selected(old('frequency', $editing?->frequency ?? 'recurring') === 'recurring')>Recurring (monthly)</option>
            <option value="one_time" @selected(old('frequency', $editing?->frequency) === 'one_time')>One time only</option>
        </select>
        @error('frequency')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="start_date" class="mb-1.5 block text-sm font-medium">Start date</label>
        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $editing?->start_date?->format('Y-m-d') ?? now()->toDateString()) }}" @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
        @error('start_date')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="end_date" class="mb-1.5 block text-sm font-medium">End date <span class="font-normal text-slate-400">optional</span></label>
        <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $editing?->end_date?->format('Y-m-d')) }}" @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
        @error('end_date')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<div data-meter-fields class="col-span-full {{ $isMetered ? '' : 'hidden' }}">
    <label for="billing_formula" class="mb-1.5 block text-sm font-medium">Formula</label>
    <input id="billing_formula" name="billing_formula" type="text" value="{{ old('billing_formula', $editing && $isMetered ? $editing->billing_formula : $defaultFormula) }}" class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 font-mono text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    <p class="mt-1.5 text-xs text-slate-500">Used on the room billing page to compute water or electricity from the current reading.</p>
    @error('billing_formula')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>
