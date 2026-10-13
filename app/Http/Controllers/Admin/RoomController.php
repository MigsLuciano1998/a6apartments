<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\Room;
use App\Models\RoomAssignment;
use App\Models\User;
use App\Services\OutstandingBalanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function __construct(private OutstandingBalanceService $outstandingBalanceService) {}
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $rooms = $this->filteredRooms($search);

        if ($request->ajax()) {
            return view('admin.rooms.results', compact('rooms', 'search'));
        }

        return view('admin.rooms.index', compact('rooms', 'search'));
    }

    public function create(): View
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Room::query()->create($validated);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function show(Request $request, Room $room): View
    {
        $room->load([
            'currentAssignment.user',
            'currentAssignment.roomBillings.payments',
            'currentAssignment.roomBillings.roomPayable.payable',
            'roomPayables' => fn ($query) => $query
                ->orderByRaw('(end_date is null or end_date > ?) desc', [now()->toDateString()])
                ->latest('start_date'),
            'roomPayables.payable',
            'roomPayables.roomBillings.payments',
            'roomAssignments' => fn ($query) => $query->latest('start_date'),
            'roomAssignments.user',
            'roomAssignments.roomBillings.payments',
            'roomAssignments.roomBillings.roomPayable.payable',
        ]);

        $availableTenants = User::query()
            ->where('role', 'tenant')
            ->where('status', 'active')
            ->whereNotIn('id', $this->currentlyAssignedTenantIds())
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $editingRoomPayable = $room->roomPayables
            ->firstWhere('id', (int) $request->query('edit_payable'));

        $availablePayables = Payable::query()
            ->where('status', 'active')
            ->orderBy('title')
            ->get();

        if ($editingRoomPayable && $editingRoomPayable->payable && ! $availablePayables->contains('id', $editingRoomPayable->payables_id)) {
            $availablePayables->prepend($editingRoomPayable->payable);
        }

        $currentRemaining = $this->outstandingBalanceService->currentRemainingForAssignment($room->currentAssignment);
        $previousBalance = $this->outstandingBalanceService->previousBalanceForRoom($room);

        return view('admin.rooms.show', compact(
            'room',
            'availableTenants',
            'availablePayables',
            'editingRoomPayable',
            'currentRemaining',
            'previousBalance',
        ));
    }

    public function assignTenant(Request $request, Room $room): RedirectResponse
    {
        if ($room->roomAssignments()->current()->exists()) {
            return back()->with('error', 'This room already has a tenant assigned.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $tenant = User::query()->findOrFail($validated['user_id']);

        if ($tenant->role !== 'tenant' || $tenant->status !== 'active') {
            return back()->with('error', 'Select an active tenant.');
        }

        if (RoomAssignment::query()->current()->where('user_id', $tenant->id)->exists()) {
            return back()->with('error', 'That tenant is already assigned to a room.');
        }

        $room->roomAssignments()->create([
            'user_id' => $tenant->id,
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'status' => 'active',
        ]);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', $tenant->name.' was assigned to this room.');
    }

    public function removeTenant(Room $room): RedirectResponse
    {
        $assignment = $room->roomAssignments()->current()->latest('start_date')->first();

        if (! $assignment) {
            return back()->with('error', 'This room has no tenant to remove.');
        }

        $assignment->update([
            'end_date' => now()->toDateString(),
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Tenant was removed from this room.');
    }

    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate($this->rules($room->id));

        $room->update($validated);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $roomId = null): array
    {
        return [
            'room_number' => ['required', 'string', 'max:50', Rule::unique('rooms', 'room_number')->ignore($roomId)],
            'room_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    private function filteredRooms(string $search)
    {
        return Room::query()
            ->with('currentAssignment.user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('room_number', 'like', "%{$search}%")
                        ->orWhere('room_name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('remarks', 'like', "%{$search}%")
                        ->orWhereHas('currentAssignment.user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('suffix', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function currentlyAssignedTenantIds()
    {
        return RoomAssignment::query()
            ->current()
            ->pluck('user_id');
    }
}
