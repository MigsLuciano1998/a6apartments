@extends('layouts.app')

@section('title', $payable->title)
@section('description', $payable->title.' billing details and payment status at A6 Apartments.')
@section('eyebrow', 'Payables')
@section('heading', 'Payable info')

@section('content')
    <section class="mx-auto max-w-4xl space-y-6">
        <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-teal-700">Payable</p>
                    <h2 class="mt-1 text-2xl font-semibold">{{ $payable->title }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.payables.edit', $payable) }}" class="rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                    <a href="{{ route('admin.payables.index') }}" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Back to payables</a>
                </div>
            </div>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Title</dt>
                    <dd class="mt-1 font-medium">{{ $payable->title }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $payable->status === 'active' ? 'bg-teal-50 text-teal-800' : 'bg-stone-100 text-slate-600' }}">
                            {{ $payable->status }}
                        </span>
                    </dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4 sm:col-span-2">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Description</dt>
                    <dd class="mt-1 font-medium">{{ $payable->description ?: '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h3 class="text-lg font-semibold">Assigned rooms</h3>
            <p class="mt-1 text-sm text-slate-500">Rooms that currently use this payable.</p>
            <div class="mt-4 overflow-hidden rounded-2xl border border-stone-200">
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
                        @forelse ($payable->roomPayables as $roomPayable)
                            <tr>
                                <td class="px-4 py-3 font-medium">
                                    @if ($roomPayable->room)
                                        <a href="{{ route('admin.rooms.show', $roomPayable->room) }}" class="text-teal-800 hover:underline">{{ $roomPayable->room->room_number }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ number_format((float) $roomPayable->amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $roomPayable->frequencyLabel() }}</td>
                                <td class="px-4 py-3">{{ $roomPayable->start_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $roomPayable->end_date?->format('M d, Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">This payable is not assigned to any room yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
