@extends('layouts.app')

@section('title', 'Room '.$room->room_number)
@section('description', 'Room '.$room->room_number.' occupancy, tenants, and billing details at A6 Apartments.')
@section('eyebrow', 'Rooms')
@section('heading', 'Room info')

@section('content')
    <section class="mx-auto max-w-4xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">
                    {{ $room->room_number }}
                    <span class="mx-2 font-normal text-slate-300">|</span>
                    {{ $room->room_name }}
                    <span class="mx-2 font-normal text-slate-300">|</span>
                    <span class="inline-flex align-middle rounded-full px-2.5 py-0.5 text-xs font-medium {{ $room->status === 'active' ? 'bg-teal-50 text-teal-800' : 'bg-stone-100 text-slate-600' }}">{{ $room->status }}</span>
                </h2>
                <p class="mt-2 text-sm text-slate-600">{{ $room->remarks ?: '—' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.rooms.edit', $room) }}" class="rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                <a href="{{ route('admin.rooms.index') }}" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Back to rooms</a>
            </div>
        </div>

        <div class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Current tenant</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        @if ($room->currentAssignment)
                            Occupied by
                            @if ($room->currentAssignment->user?->role === 'tenant')
                                <a href="{{ route('admin.tenants.show', $room->currentAssignment->user) }}" class="font-medium text-teal-800 hover:underline">{{ $room->currentAssignment->user->name }}</a>
                            @else
                                {{ $room->currentAssignment->user?->name ?? 'a tenant' }}
                            @endif
                            since {{ $room->currentAssignment->start_date?->format('M d, Y') }}.
                        @else
                            This room has no tenant assigned.
                        @endif
                    </p>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-end">
                    @if ($room->currentAssignment)
                        <div class="sm:text-right" style="min-width: 11rem">
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Remaining this month</p>
                            <p class="mt-1 text-xl font-semibold tabular-nums {{ $currentRemaining > 0 ? 'text-rose-700' : 'text-slate-700' }}">{{ number_format($currentRemaining, 2) }}</p>
                        </div>
                        <div class="sm:text-right" style="min-width: 11rem; margin-left: 2.5rem">
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Previous balance</p>
                            <p class="mt-1 text-xl font-semibold tabular-nums {{ $previousBalance > 0 ? 'text-rose-700' : 'text-slate-700' }}">{{ number_format($previousBalance, 2) }}</p>
                        </div>
                    @elseif ($previousBalance > 0)
                        <div class="sm:text-right" style="min-width: 11rem">
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Previous balance</p>
                            <p class="mt-1 text-xl font-semibold tabular-nums text-rose-700">{{ number_format($previousBalance, 2) }}</p>
                        </div>
                    @endif

                    @if ($room->currentAssignment)
                        <form method="POST" action="{{ route('admin.rooms.remove-tenant', $room) }}" onsubmit="return confirm('Remove this tenant from the room?')">
                            @csrf
                            <button type="submit" class="rounded-2xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-50">
                                Remove tenant
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.rooms.assign-tenant', $room) }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                            @csrf
                            <select name="user_id" required class="min-w-56 rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                                <option value="">Select tenant</option>
                                @foreach ($availableTenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" @disabled($availableTenants->isEmpty()) class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-50">
                                Assign tenant
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if (! $room->currentAssignment && $availableTenants->isEmpty())
                <p class="mt-3 text-sm text-slate-500">There are no available tenants to assign. Add a tenant first, or remove one from another room.</p>
            @endif

            <h3 class="mt-8 text-lg font-semibold">Assignment history</h3>
            <div class="mt-4 overflow-hidden rounded-2xl border border-stone-200">
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
                        @forelse ($room->roomAssignments as $assignment)
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
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">No assignments for this room yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Room payables</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Assign bill types from Payables to this room. Recurring bills are monthly; one-time bills are charged once.
                    </p>
                </div>
                <a href="{{ route('admin.payables.create') }}" class="text-sm font-medium text-teal-800 hover:underline">Manage payable types</a>
            </div>

            @if ($editingRoomPayable)
                <form method="POST" action="{{ route('admin.rooms.payables.update', [$room, $editingRoomPayable]) }}" class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="payables_id" class="mb-1.5 block text-sm font-medium">Payable</label>
                        <select id="payables_id" name="payables_id" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                            @foreach ($availablePayables as $payable)
                                <option value="{{ $payable->id }}" @selected((int) old('payables_id', $editingRoomPayable->payables_id) === $payable->id)>{{ $payable->title }}</option>
                            @endforeach
                        </select>
                        @error('payables_id')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="amount" class="mb-1.5 block text-sm font-medium">Amount</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="0" value="{{ old('amount', $editingRoomPayable->amount) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                        @error('amount')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="frequency" class="mb-1.5 block text-sm font-medium">Billing</label>
                        <select id="frequency" name="frequency" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                            <option value="recurring" @selected(old('frequency', $editingRoomPayable->frequency) === 'recurring')>Recurring (monthly)</option>
                            <option value="one_time" @selected(old('frequency', $editingRoomPayable->frequency) === 'one_time')>One time only</option>
                        </select>
                        @error('frequency')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="start_date" class="mb-1.5 block text-sm font-medium">Start date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $editingRoomPayable->start_date?->format('Y-m-d')) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                        @error('start_date')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="end_date" class="mb-1.5 block text-sm font-medium">End date <span class="font-normal text-slate-400">optional</span></label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $editingRoomPayable->end_date?->format('Y-m-d')) }}" class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                        @error('end_date')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Update</button>
                        <a href="{{ route('admin.rooms.show', $room) }}" class="rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Cancel</a>
                    </div>
                </form>
            @else
                <form method="POST" action="{{ route('admin.rooms.payables.store', $room) }}" class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @csrf
                    <div>
                        <label for="payables_id" class="mb-1.5 block text-sm font-medium">Payable</label>
                        <select id="payables_id" name="payables_id" required @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
                            <option value="">Select payable</option>
                            @foreach ($availablePayables as $payable)
                                <option value="{{ $payable->id }}" @selected((int) old('payables_id') === $payable->id)>{{ $payable->title }}</option>
                            @endforeach
                        </select>
                        @error('payables_id')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="amount" class="mb-1.5 block text-sm font-medium">Amount</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="0" value="{{ old('amount') }}" required @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
                        @error('amount')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="frequency" class="mb-1.5 block text-sm font-medium">Billing</label>
                        <select id="frequency" name="frequency" required @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
                            <option value="recurring" @selected(old('frequency', 'recurring') === 'recurring')>Recurring (monthly)</option>
                            <option value="one_time" @selected(old('frequency') === 'one_time')>One time only</option>
                        </select>
                        @error('frequency')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="start_date" class="mb-1.5 block text-sm font-medium">Start date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" required @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
                        @error('start_date')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="end_date" class="mb-1.5 block text-sm font-medium">End date <span class="font-normal text-slate-400">optional</span></label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50">
                        @error('end_date')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-end">
                        <button type="submit" @disabled($availablePayables->isEmpty()) class="w-full rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-50">
                            Add payable
                        </button>
                    </div>
                </form>
                @if ($availablePayables->isEmpty())
                    <p class="mt-3 text-sm text-slate-500">No payable types yet. Add a payable type first.</p>
                @endif
            @endif

            <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-[#fbf8f3] text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Payable</th>
                            <th class="px-4 py-3 font-medium">Amount</th>
                            <th class="px-4 py-3 font-medium">Billing</th>
                            <th class="px-4 py-3 font-medium">Start</th>
                            <th class="px-4 py-3 font-medium">End</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($room->roomPayables as $roomPayable)
                            <tr>
                                <td class="px-4 py-3 font-medium">
                                    @if ($roomPayable->payable)
                                        <a href="{{ route('admin.payables.show', $roomPayable->payable) }}" class="text-teal-800 hover:underline">{{ $roomPayable->payable->title }}</a>
                                    @else
                                        —
                                    @endif
                                    @if ($roomPayable->isCurrent())
                                        <span class="ml-2 inline-flex rounded-full bg-teal-50 px-2 py-0.5 text-xs font-medium text-teal-800">Current</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ number_format((float) $roomPayable->amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $roomPayable->frequencyLabel() }}</td>
                                <td class="px-4 py-3">{{ $roomPayable->start_date?->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $roomPayable->end_date?->format('M d, Y') ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.rooms.show', ['room' => $room, 'edit_payable' => $roomPayable->id]) }}" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                                        @if ($roomPayable->isCurrent())
                                            <form method="POST" action="{{ route('admin.rooms.payables.end', [$room, $roomPayable]) }}" onsubmit="return confirm('End this payable for the room?')">
                                                @csrf
                                                <button type="submit" class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">End</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.rooms.payables.destroy', [$room, $roomPayable]) }}" onsubmit="return confirm('Delete this payable from the room?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">No payables assigned to this room yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($room->roomPayables->isNotEmpty())
                        <tfoot class="border-t border-stone-200 bg-[#fbf8f3]">
                            <tr>
                                <td class="px-4 py-3 font-semibold">Total to be paid</td>
                                <td class="px-4 py-3 font-semibold">{{ number_format($room->amountToBePaid(), 2) }}</td>
                                <td colspan="4" class="px-4 py-3 text-sm text-slate-500">Current payables only (recurring monthly + one-time).</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </section>
@endsection
