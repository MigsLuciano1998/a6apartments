<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium">Payable</th>
                    <th class="px-4 py-3 font-medium">Amount</th>
                    <th class="px-4 py-3 font-medium">Paid</th>
                    <th class="px-4 py-3 font-medium">Method</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Cashier</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($expenses as $expense)
                    <tr class="bg-white">
                        <td class="px-4 py-3">{{ $expense->expense_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('admin.expenses.show', $expense) }}" class="text-teal-800 hover:underline">{{ $expense->payable?->title ?? '—' }}</a>
                        </td>
                        <td class="px-4 py-3 tabular-nums">{{ number_format((float) $expense->amount, 2) }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ number_format($expense->amountPaid(), 2) }}</td>
                        <td class="px-4 py-3">{{ $expense->paymentMethodLabel() }}</td>
                        <td class="px-4 py-3">
                            @if ($expense->isPartial())
                                <span class="block text-sm font-medium text-amber-800">{{ $expense->statusLabel() }}</span>
                                <span class="mt-0.5 block text-xs text-slate-500">Remaining {{ number_format($expense->amountRemaining(), 2) }}</span>
                            @else
                                <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-800">{{ $expense->statusLabel() }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $expense->cashierUser?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.expenses.edit', $expense) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                                <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                            @if ($search)
                                No expenses match “{{ $search }}”.
                            @else
                                No expenses recorded yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($expenses->hasPages())
    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
@endif
