<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Room number</th>
                    <th class="px-4 py-3 font-medium">Room name</th>
                    <th class="px-4 py-3 font-medium">Tenant</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Remarks</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($rooms as $room)
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('admin.rooms.show', $room) }}" class="text-teal-800 hover:underline">{{ $room->room_number }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $room->room_name }}</td>
                        <td class="px-4 py-3">
                            @if ($room->currentAssignment?->user?->role === 'tenant')
                                <a href="{{ route('admin.tenants.show', $room->currentAssignment->user) }}" class="text-teal-800 hover:underline">{{ $room->currentAssignment->user->name }}</a>
                            @else
                                {{ $room->currentAssignment?->user?->name ?? '—' }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $room->status === 'active' ? 'bg-teal-50 text-teal-800' : 'bg-stone-100 text-slate-600' }}">
                                {{ $room->status }}
                            </span>
                        </td>
                        <td class="max-w-xs truncate px-4 py-3 text-slate-500">{{ $room->remarks ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.rooms.edit', $room) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                                <a href="{{ route('admin.rooms.billing', $room) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Billing</a>
                                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Delete this room?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                            @if ($search)
                                No rooms match “{{ $search }}”.
                            @else
                                No rooms yet. Add the first room to get started.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($rooms->hasPages())
    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
@endif
