<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payable;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $month = now();
        $expenses = $this->filteredExpenses($search);
        $monthlyTotal = $this->monthlyTotal($month);

        if ($request->ajax()) {
            return view('admin.expenses.results', compact('expenses', 'search'));
        }

        return view('admin.expenses.index', compact('expenses', 'search', 'month', 'monthlyTotal'));
    }

    public function create(): View
    {
        return view('admin.expenses.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        Expense::query()->create($this->validated($request));

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense): View
    {
        $expense->load(['payable', 'cashierUser']);

        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        $expense->load(['payable', 'cashierUser']);
        $formData = $this->formData();

        if ($expense->payable && ! $formData['payables']->contains('id', $expense->payables_id)) {
            $formData['payables']->prepend($expense->payable);
        }

        if ($expense->cashierUser && ! $formData['cashiers']->contains('id', $expense->cashier)) {
            $formData['cashiers']->prepend($expense->cashierUser);
        }

        return view('admin.expenses.edit', [
            'expense' => $expense,
            ...$formData,
        ]);
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $expense->update($this->validated($request));

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $isPartial = $request->input('status') === 'partial_payment';

        $validated = $request->validate([
            'payables_id' => ['required', 'exists:payables,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
            'payment_method' => ['required', Rule::in(['cash', 'online'])],
            'status' => ['required', Rule::in(['full_paid', 'partial_payment'])],
            'partial_payment_amount' => [$isPartial ? 'required' : 'nullable', 'numeric', 'min:0.01'],
            'cashier' => ['required', 'exists:users,id'],
            'expense_date' => ['required', 'date'],
        ]);

        if ($isPartial) {
            $partial = round((float) $validated['partial_payment_amount'], 2);
            $amount = round((float) $validated['amount'], 2);

            if ($partial >= $amount) {
                $request->validate([
                    'partial_payment_amount' => ['lt:amount'],
                ], [
                    'partial_payment_amount.lt' => 'Partial payment must be less than the full amount.',
                ]);
            }
        } else {
            $validated['partial_payment_amount'] = null;
        }

        $cashier = User::query()->find($validated['cashier']);

        abort_unless($cashier && $cashier->isStaff(), 422);

        return $validated;
    }

    /**
     * @return array{payables: \Illuminate\Support\Collection, cashiers: \Illuminate\Support\Collection}
     */
    private function formData(): array
    {
        return [
            'payables' => Payable::query()->where('status', 'active')->orderBy('title')->get(),
            'cashiers' => User::query()
                ->whereIn('role', ['admin', 'manager'])
                ->where('status', 'active')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
        ];
    }

    private function filteredExpenses(string $search)
    {
        return Expense::query()
            ->with(['payable', 'cashierUser'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('remarks', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('payable', fn ($query) => $query->where('title', 'like', "%{$search}%"))
                        ->orWhereHas('cashierUser', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('expense_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
    }

    private function monthlyTotal($month): float
    {
        $expenses = Expense::query()
            ->whereDate('expense_date', '>=', $month->copy()->startOfMonth()->toDateString())
            ->whereDate('expense_date', '<=', $month->copy()->endOfMonth()->toDateString())
            ->get();

        return (float) $expenses->sum(fn (Expense $expense) => $expense->amountPaid());
    }
}
