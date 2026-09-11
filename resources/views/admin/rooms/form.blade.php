@php
    $room = $room ?? null;
@endphp

<div>
    <label for="room_number" class="mb-1.5 block text-sm font-medium">Room number</label>
    <input id="room_number" name="room_number" type="text" value="{{ old('room_number', $room?->room_number) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    @error('room_number')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="room_name" class="mb-1.5 block text-sm font-medium">Room name</label>
    <input id="room_name" name="room_name" type="text" value="{{ old('room_name', $room?->room_name) }}"  class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    @error('room_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="status" class="mb-1.5 block text-sm font-medium">Status</label>
    <select id="status" name="status" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        <option value="active" @selected(old('status', $room?->status ?? 'active') === 'active')>Active</option>
        <option value="inactive" @selected(old('status', $room?->status) === 'inactive')>Inactive</option>
    </select>
    @error('status')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="remarks" class="mb-1.5 block text-sm font-medium">Remarks <span class="font-normal text-slate-400">optional</span></label>
    <textarea id="remarks" name="remarks" rows="3" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">{{ old('remarks', $room?->remarks) }}</textarea>
    @error('remarks')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>
