<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Support\LiveTable;
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

    public function show(Request $request, Payable $payable): View
    {
        $roomSearch = LiveTable::search($request, 'rooms_q');

        $roomPayables = LiveTable::paginate(
            $payable->roomPayables()
                ->with('room')
                ->when($roomSearch !== '', function ($query) use ($roomSearch) {
                    $query->where(function ($query) use ($roomSearch) {
                        $query->where('frequency', 'like', "%{$roomSearch}%")
                            ->orWhere('amount', 'like', "%{$roomSearch}%")
                            ->orWhereHas('room', function ($query) use ($roomSearch) {
                                $query->where('room_number', 'like', "%{$roomSearch}%")
                                    ->orWhere('room_name', 'like', "%{$roomSearch}%");
                            });
                    });
                })
                ->latest('start_date'),
            $request,
            'rooms_page',
        );

        if (LiveTable::isAjaxFor($request, 'rooms')) {
            return view('admin.payables.room-results', compact('roomPayables', 'roomSearch'));
        }

        return view('admin.payables.show', compact('payable', 'roomPayables', 'roomSearch'));
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
