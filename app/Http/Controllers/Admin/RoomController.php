<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\Room;
use App\Models\User;
use App\Services\OutstandingBalanceService;
use App\Support\LiveTable;
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
        $assignmentSearch = LiveTable::search($request, 'assignments_q');
        $payableSearch = LiveTable::search($request, 'payables_q');

        $room->load([
            'currentAssignment.user',
            'currentAssignment.roomBillings.payments',
            'currentAssignment.roomBillings.roomPayable.payable',
            'roomPayables.payable',
            'roomAssignments.roomBillings.payments',
        ]);

        $assignments = LiveTable::paginate(
            $room->roomAssignments()
                ->with('user')
                ->when($assignmentSearch !== '', function ($query) use ($assignmentSearch) {
                    $query->where(function ($query) use ($assignmentSearch) {
                        $query->where('status', 'like', "%{$assignmentSearch}%")
                            ->orWhereHas('user', fn ($query) => $query->nameLike($assignmentSearch));
                    });
                })
                ->latest('start_date'),
            $request,
            'assignments_page',
        );

        $roomPayables = LiveTable::paginate(
            $room->roomPayables()
                ->with(['payable', 'roomBillings.payments'])
                ->when($payableSearch !== '', function ($query) use ($payableSearch) {
                    $query->where(function ($query) use ($payableSearch) {
                        $query->where('frequency', 'like', "%{$payableSearch}%")
                            ->orWhere('amount', 'like', "%{$payableSearch}%")
                            ->orWhereHas('payable', fn ($query) => $query->where('title', 'like', "%{$payableSearch}%"));
                    });
                })
                ->orderByRaw('(end_date is null or end_date > ?) desc', [now()->toDateString()])
                ->latest('start_date'),
            $request,
            'payables_page',
        );

        if (LiveTable::isAjaxFor($request, 'assignments')) {
            return view('admin.rooms.assignment-results', compact('room', 'assignments', 'assignmentSearch'));
        }

        if (LiveTable::isAjaxFor($request, 'payables')) {
            return view('admin.rooms.payable-results', compact('room', 'roomPayables', 'payableSearch'));
        }

        $availableTenants = User::query()
            ->where('role', 'tenant')
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $editingRoomPayable = $room->roomPayables()
            ->with(['payable', 'roomBillings.payments'])
            ->find((int) $request->query('edit_payable'));

        $availablePayables = Payable::query()
            ->where('status', 'active')
            ->orderBy('title')
            ->get();

        if ($editingRoomPayable && $editingRoomPayable->payable && ! $availablePayables->contains('id', $editingRoomPayable->payables_id)) {
            $availablePayables->prepend($editingRoomPayable->payable);
        }

        $currentRemaining = $this->outstandingBalanceService->currentRemainingForAssignment($room->currentAssignment);
        $previousBalance = $this->outstandingBalanceService->previousBalanceForRoom($room);
        $payableTotal = $room->amountToBePaid();

        return view('admin.rooms.show', compact(
            'room',
            'availableTenants',
            'availablePayables',
            'editingRoomPayable',
            'currentRemaining',
            'previousBalance',
            'assignments',
            'assignmentSearch',
            'roomPayables',
            'payableSearch',
            'payableTotal',
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
            'room_name' => ['nullable', 'string', 'max:255'],
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
                        ->when(str_contains(strtolower($search), 'available') && ! str_contains(strtolower($search), 'not'), fn ($query) => $query->orWhere('status', 'active'))
                        ->when(str_contains(strtolower($search), 'not available'), fn ($query) => $query->orWhere('status', 'inactive'))
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
}
