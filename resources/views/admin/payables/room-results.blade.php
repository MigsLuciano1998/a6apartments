<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Amount</th>
                    <th class="px-4 py-3 font-medium">Billing</th>
                    <th class="px-4 py-3 font-medium">Start</th>
                    <th class="px-4 py-3 font-medium">End</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($roomPayables as $roomPayable)
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            @if ($roomPayable->room)
                                <a href="{{ route('admin.rooms.show', $roomPayable->room) }}" class="text-teal-800 hover:underline">{{ $roomPayable->room->room_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ peso($roomPayable->amount) }}</td>
                        <td class="px-4 py-3">{{ $roomPayable->frequencyLabel() }}</td>
                        <td class="px-4 py-3">{{ $roomPayable->start_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $roomPayable->end_date?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            @if ($roomSearch)
                                No rooms match “{{ $roomSearch }}”.
                            @else
                                This payable is not assigned to any room yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($roomPayables->hasPages())
    <div class="mt-4">
        {{ $roomPayables->links() }}
    </div>
@endif
