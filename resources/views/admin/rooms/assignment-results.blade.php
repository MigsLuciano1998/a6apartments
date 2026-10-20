<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Tenant</th>
                    <th class="px-4 py-3 font-medium">Start</th>
                    <th class="px-4 py-3 font-medium">End</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($assignments as $assignment)
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            @if ($assignment->user?->role === 'tenant')
                                <a href="{{ route('admin.tenants.show', $assignment->user) }}" class="text-teal-800 hover:underline">{{ $assignment->user->name }}</a>
                            @else
                                {{ $assignment->user?->name ?? '—' }}
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $assignment->start_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">{{ $assignment->end_date?->format('M d, Y') ?: '—' }}</td>
                        <td class="px-4 py-3 capitalize">{{ $assignment->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                            @if ($assignmentSearch)
                                No assignments match “{{ $assignmentSearch }}”.
                            @else
                                No assignments for this room yet.
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
