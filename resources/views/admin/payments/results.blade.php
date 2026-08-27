<div class="overflow-hidden rounded-2xl border border-stone-200">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Tenant</th>
                    <th class="px-4 py-3 font-medium">Room</th>
                    <th class="px-4 py-3 font-medium">Rent due</th>
                    <th class="px-4 py-3 font-medium">Bills due</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($tenants as $tenant)
                    @php
                        $billings = $tenant->currentAssignment?->roomBillings ?? collect();
                        $unpaid = $billings->filter(fn ($billing) => ! $billing->isPaid());
                        $rentDue = (float) $unpaid->filter(fn ($billing) => $billing->isRent())->sum(fn ($billing) => $billing->amountDue());
                        $billsDue = (float) $unpaid->reject(fn ($billing) => $billing->isRent())->sum(fn ($billing) => $billing->amountDue());
                    @endphp
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
                        <td class="px-4 py-3 tabular-nums">{{ number_format($rentDue, 2) }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ number_format($billsDue, 2) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    data-open-payment
                                    data-tenant-id="{{ $tenant->id }}"
                                    @disabled(! $tenant->currentAssignment)
                                    class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    Add Payment
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                            @if ($search)
                                No tenants match “{{ $search }}”.
                            @else
                                No tenants yet.
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
