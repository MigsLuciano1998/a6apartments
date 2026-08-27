<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">Posted</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Author</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($announcements as $announcement)
                    <tr class="bg-white">
                        <td class="px-4 py-3 font-medium">{{ $announcement->title }}</td>
                        <td class="px-4 py-3">{{ $announcement->created_at?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $announcement->isPublished() ? 'bg-teal-50 text-teal-800' : 'bg-stone-100 text-slate-600' }}">
                                {{ $announcement->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $announcement->author?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                            @if ($search)
                                No announcements match “{{ $search }}”.
                            @else
                                No announcements yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($announcements->hasPages())
    <div class="mt-4">
        {{ $announcements->links() }}
    </div>
@endif
