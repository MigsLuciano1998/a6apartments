<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OutstandingBalanceService;
use App\Support\LiveTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function __construct(
        private OutstandingBalanceService $outstandingBalanceService,
    ) {}
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $tenants = $this->filteredTenants($search);

        if ($request->ajax()) {
            return view('admin.tenants.results', compact('tenants', 'search'));
        }

        return view('admin.tenants.index', compact('tenants', 'search'));
    }

    public function create(): View
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        User::query()->create([
            ...$validated,
            'role' => 'tenant',
        ]);

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    public function show(Request $request, User $tenant): View
    {
        $this->ensureTenant($tenant);

        $month = now();
        $assignmentSearch = LiveTable::search($request, 'assignments_q');

        $tenant->load([
            'currentAssignments.room',
            'currentAssignments.roomBillings.payments',
            'currentAssignments.roomBillings.roomPayable.payable',
            'roomAssignments.room',
            'roomAssignments.roomBillings.payments',
            'roomAssignments.roomBillings.roomPayable.payable',
        ]);

        $assignments = LiveTable::paginate(
            $tenant->roomAssignments()
                ->with(['room', 'roomBillings.payments', 'roomBillings.roomPayable.payable'])
                ->when($assignmentSearch !== '', function ($query) use ($assignmentSearch) {
                    $query->where(function ($query) use ($assignmentSearch) {
                        $query->where('status', 'like', "%{$assignmentSearch}%")
                            ->orWhereHas('room', function ($query) use ($assignmentSearch) {
                                $query->where('room_number', 'like', "%{$assignmentSearch}%")
                                    ->orWhere('room_name', 'like', "%{$assignmentSearch}%");
                            });
                    });
                })
                ->latest('start_date'),
            $request,
            'assignments_page',
        );

        $assignmentBalances = $assignments->getCollection()->mapWithKeys(fn ($assignment) => [
            $assignment->id => [
                'current' => $this->outstandingBalanceService->currentRemainingForAssignment($assignment),
                'previous' => $this->outstandingBalanceService->previousRemainingForAssignment($assignment),
            ],
        ]);

        if (LiveTable::isAjaxFor($request, 'assignments')) {
            return view('admin.tenants.assignment-results', compact('assignments', 'assignmentBalances', 'assignmentSearch'));
        }

        $amountDue = $this->outstandingBalanceService->currentRemainingForTenant($tenant);
        $previousBalance = $this->outstandingBalanceService->previousBalanceForTenant($tenant, $month);

        return view('admin.tenants.show', compact(
            'tenant',
            'amountDue',
            'previousBalance',
            'assignmentBalances',
            'assignments',
            'assignmentSearch',
        ));
    }

    public function edit(User $tenant): View
    {
        $this->ensureTenant($tenant);

        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, User $tenant): RedirectResponse
    {
        $this->ensureTenant($tenant);

        $validated = $request->validate($this->rules($tenant->id));

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $tenant->update($validated);

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    public function destroy(User $tenant): RedirectResponse
    {
        $this->ensureTenant($tenant);

        $tenant->delete();

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $tenantId = null): array
    {
        $password = $tenantId
            ? ['nullable', 'confirmed', Password::min(8)]
            : ['required', 'confirmed', Password::min(8)];

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($tenantId)],
            'phone' => ['required', 'digits_between:1,30'],
            'tenant_type' => ['required', 'in:regular,bills_only'],
            'status' => ['required', 'in:active,inactive'],
            'password' => $password,
        ];
    }

    private function ensureTenant(User $tenant): void
    {
        abort_unless($tenant->role === 'tenant', 404);
    }

    private function filteredTenants(string $search)
    {
        return User::query()
            ->where('role', 'tenant')
            ->with(['currentAssignment.room', 'currentAssignments.room'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('suffix', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('tenant_type', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('currentAssignments.room', function ($query) use ($search) {
                            $query->where('room_number', 'like', "%{$search}%")
                                ->orWhere('room_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }
}
