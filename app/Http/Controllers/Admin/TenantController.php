<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OutstandingBalanceService;
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

    public function show(User $tenant): View
    {
        $this->ensureTenant($tenant);

        $tenant->load('currentAssignment');

        $month = now();

        $tenant->load([
            'roomAssignments' => fn ($query) => $query->latest('start_date'),
            'roomAssignments.room',
            'roomAssignments.roomBillings.payments',
            'roomAssignments.roomBillings.roomPayable.payable',
            'currentAssignment.room.roomPayables' => fn ($query) => $query
                ->orderByRaw('(end_date is null or end_date > ?) desc', [now()->toDateString()])
                ->latest('start_date'),
            'currentAssignment.room.roomPayables.payable',
            'currentAssignment.room.roomPayables.roomBillings.payments',
            'currentAssignment.roomBillings.payments',
        ]);

        $previousBalance = $this->outstandingBalanceService->previousBalanceForTenant($tenant, $month);

        return view('admin.tenants.show', compact('tenant', 'previousBalance'));
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
            'phone' => ['required', 'string', 'max:30'],
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
            ->with('currentAssignment.room')
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
                        ->orWhereHas('currentAssignment.room', function ($query) use ($search) {
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
