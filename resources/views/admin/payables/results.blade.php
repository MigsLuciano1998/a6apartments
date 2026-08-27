<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Description</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($payables as $payable)
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('admin.payables.show', $payable) }}" class="text-teal-800 hover:underline">{{ $payable->title }}</a>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $payable->status === 'active' ? 'bg-teal-50 text-teal-800' : 'bg-stone-100 text-slate-600' }}">
                                {{ $payable->status }}
                            </span>
                        </td>
                        <td class="max-w-xs truncate px-4 py-3 text-slate-500">{{ $payable->description ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.payables.edit', $payable) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                                <form method="POST" action="{{ route('admin.payables.destroy', $payable) }}" onsubmit="return confirm('Delete this payable? Rooms using it will lose this bill type.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                            @if ($search)
                                No payables match “{{ $search }}”.
                            @else
                                No payables yet. Add the first payable to get started.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($payables->hasPages())
    <div class="mt-4">
        {{ $payables->links() }}
    </div>
@endif
