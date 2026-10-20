<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Start</th>
                    <th class="px-4 py-3 font-medium">End</th>
                    <th class="px-4 py-3 font-medium">Total current balance</th>
                    <th class="px-4 py-3 font-medium">Total previous balance</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($assignments as $assignment)
                    @php
                        $currentBalance = (float) ($assignmentBalances[$assignment->id]['current'] ?? 0);
                        $roomPreviousBalance = (float) ($assignmentBalances[$assignment->id]['previous'] ?? 0);
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            @if ($assignment->room)
                                <a href="{{ route('admin.rooms.show', $assignment->room) }}" class="text-teal-800 hover:underline">{{ $assignment->room->room_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $assignment->start_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">{{ $assignment->end_date?->format('M d, Y') ?: '—' }}</td>
                        <td class="px-4 py-3 font-semibold tabular-nums {{ $currentBalance > 0 ? 'text-rose-700' : '' }}">{{ peso($currentBalance) }}</td>
                        <td class="px-4 py-3 font-semibold tabular-nums {{ $roomPreviousBalance > 0 ? 'text-rose-700' : '' }}">{{ peso($roomPreviousBalance) }}</td>
                        <td class="px-4 py-3 capitalize">{{ $assignment->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                            @if ($assignmentSearch)
                                No assignments match “{{ $assignmentSearch }}”.
                            @else
                                No room assignment yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($assignments->hasPages())
    <div class="mt-4">
        {{ $assignments->links() }}
    </div>
@endif
