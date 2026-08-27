<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $admins = $this->filteredAdmins($search);

        if ($request->ajax()) {
            return view('admin.admins.results', compact('admins', 'search'));
        }

        return view('admin.admins.index', compact('admins', 'search'));
    }

    public function create(): View
    {
        return view('admin.admins.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        User::query()->create([
            ...$validated,
            'role' => 'admin',
            'tenant_type' => null,
        ]);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin user created successfully.');
    }

    public function show(User $adminUser): View
    {
        return view('admin.admins.show', ['admin' => $adminUser]);
    }

    public function edit(User $adminUser): View
    {
        return view('admin.admins.edit', ['admin' => $adminUser]);
    }

    public function update(Request $request, User $adminUser): RedirectResponse
    {
        $validated = $request->validate($this->rules($adminUser->id));

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $adminUser->update([
            ...$validated,
            'role' => 'admin',
            'tenant_type' => null,
        ]);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin user updated successfully.');
    }

    public function destroy(User $adminUser): RedirectResponse
    {
        $adminUser->delete();

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin user deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $adminId = null): array
    {
        $password = $adminId
            ? ['nullable', 'confirmed', Password::min(8)]
            : ['required', 'confirmed', Password::min(8)];

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($adminId)],
            'phone' => ['required', 'string', 'max:30'],
            'status' => ['required', 'in:active,inactive'],
            'password' => $password,
        ];
    }

    private function filteredAdmins(string $search)
    {
        return User::query()
            ->where('role', 'admin')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('suffix', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }
}
