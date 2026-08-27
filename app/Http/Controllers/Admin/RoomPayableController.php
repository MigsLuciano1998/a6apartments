<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use App\Models\Room;
use App\Models\RoomPayable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomPayableController extends Controller
{
    public function store(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $payable = Payable::query()->findOrFail($validated['payables_id']);

        if ($payable->status !== 'active') {
            return back()->withInput()->with('error', 'Select an active payable.');
        }

        if ($validated['frequency'] === 'recurring' && $this->recurringPayableCurrentlyAssigned($room, $payable->id)) {
            return back()->withInput()->with('error', 'That recurring payable is already assigned to this room.');
        }

        $room->roomPayables()->create($validated);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', $payable->title.' was added to this room.');
    }

    public function update(Request $request, Room $room, RoomPayable $roomPayable): RedirectResponse
    {
        $this->ensureBelongsToRoom($room, $roomPayable);

        $validated = $request->validate($this->rules());

        if ($validated['frequency'] === 'recurring' && $this->recurringPayableCurrentlyAssigned($room, (int) $validated['payables_id'], $roomPayable->id)) {
            return back()->withInput()->with('error', 'That recurring payable is already assigned to this room.');
        }

        $roomPayable->update($validated);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Room payable updated successfully.');
    }

    public function end(Room $room, RoomPayable $roomPayable): RedirectResponse
    {
        $this->ensureBelongsToRoom($room, $roomPayable);

        if (! $roomPayable->isCurrent()) {
            return back()->with('error', 'This payable is already ended.');
        }

        $roomPayable->update([
            'end_date' => now()->toDateString(),
        ]);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Payable was ended for this room.');
    }

    public function destroy(Room $room, RoomPayable $roomPayable): RedirectResponse
    {
        $this->ensureBelongsToRoom($room, $roomPayable);

        $roomPayable->delete();

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Payable was removed from this room.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'payables_id' => ['required', 'exists:payables,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'frequency' => ['required', Rule::in(['recurring', 'one_time'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    private function ensureBelongsToRoom(Room $room, RoomPayable $roomPayable): void
    {
        abort_unless($roomPayable->room_number_id === $room->id, 404);
    }

    private function recurringPayableCurrentlyAssigned(Room $room, int $payableId, ?int $ignoreId = null): bool
    {
        return $room->roomPayables()
            ->current()
            ->where('frequency', 'recurring')
            ->where('payables_id', $payableId)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
