<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Phone</th>
                    <th class="px-4 py-3 font-medium">Type</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($tenants as $tenant)
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-teal-800 hover:underline">{{ $tenant->name }}</a>
                        </td>
                        <td class="px-4 py-3">
                            @if ($tenant->currentAssignment?->room)
                                <a href="{{ route('admin.rooms.show', $tenant->currentAssignment->room) }}" class="text-teal-800 hover:underline">{{ $tenant->currentAssignment->room->room_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $tenant->email }}</td>
                        <td class="px-4 py-3">{{ $tenant->phone }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $tenant->tenant_type) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $tenant->status === 'active' ? 'bg-teal-50 text-teal-800' : 'bg-stone-100 text-slate-600' }}">
                                {{ $tenant->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" onsubmit="return confirm('Delete this tenant?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                            @if ($search)
                                No tenants match “{{ $search }}”.
                            @else
                                No tenants yet. Add a tenant or wait for a registration.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($tenants->hasPages())
    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
@endif
