<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Remaining this month</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($roomRows as $row)
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium">
                            {{ $row['room']->room_number }}
                            @if ($row['room']->room_name)
                                <span class="block text-xs font-normal text-slate-500">{{ $row['room']->room_name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-semibold tabular-nums {{ $row['currentDue'] > 0 ? 'text-rose-700' : '' }}">{{ peso($row['currentDue']) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end">
                                <a href="{{ route('tenant.rooms.billing', $row['room']) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">
                                    View billing
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-10 text-center text-slate-500">
                            @if ($roomsSearch)
                                No rooms match “{{ $roomsSearch }}”.
                            @else
                                No room assigned yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($roomRows->hasPages())
    <div class="mt-4">
        {{ $roomRows->links() }}
    </div>
@endif
