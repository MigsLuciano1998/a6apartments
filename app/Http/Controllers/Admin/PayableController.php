<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PayableController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $payables = $this->filteredPayables($search);

        if ($request->ajax()) {
            return view('admin.payables.results', compact('payables', 'search'));
        }

        return view('admin.payables.index', compact('payables', 'search'));
    }

    public function create(): View
    {
        return view('admin.payables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Payable::query()->create($validated);

        return redirect()
            ->route('admin.payables.index')
            ->with('success', 'Payable created successfully.');
    }

    public function show(Payable $payable): View
    {
        $payable->load([
            'roomPayables' => fn ($query) => $query->latest('start_date'),
            'roomPayables.room',
        ]);

        return view('admin.payables.show', compact('payable'));
    }

    public function edit(Payable $payable): View
    {
        return view('admin.payables.edit', compact('payable'));
    }

    public function update(Request $request, Payable $payable): RedirectResponse
    {
        $validated = $request->validate($this->rules($payable->id));

        $payable->update($validated);

        return redirect()
            ->route('admin.payables.index')
            ->with('success', 'Payable updated successfully.');
    }

    public function destroy(Payable $payable): RedirectResponse
    {
        $payable->delete();

        return redirect()
            ->route('admin.payables.index')
            ->with('success', 'Payable deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $payableId = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('payables', 'title')->ignore($payableId)],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ];
    }

    private function filteredPayables(string $search)
    {
        return Payable::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }
}
