@extends('layouts.app')

@section('title', 'Billing · Room '.$room->room_number)
@section('description', 'Enter and publish this month’s bill for room '.$room->room_number.' at A6 Apartments.')
@section('eyebrow', 'Rooms')
@section('heading', 'Room billing')

@section('content')
    <section class="mx-auto max-w-3xl">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold">
                    Billing Page of room {{ $room->room_number }}
                    <span class="ml-2 inline-flex align-middle rounded-full px-2.5 py-1 text-xs font-semibold {{ $published ? 'bg-teal-50 text-teal-800' : 'bg-amber-50 text-amber-800' }}">
                        {{ $published ? 'Published' : 'Unpublished' }}
                    </span>
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    {{ $month->format('F Y') }}
                    @if ($assignment?->user)
                        · {{ $assignment->user->name }}
                    @else
                        · Assign a tenant before publishing
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.rooms.show', $room) }}" class="rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Back to room</a>
        </div>

        <form method="POST" action="{{ route('admin.rooms.billing.publish', $room) }}" data-room-billing-form class="mt-8 rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf

            @if ($lines->isEmpty())
                <p class="text-sm text-slate-500">No payables are assigned to this room yet. Add them on the room page first.</p>
            @else
                <div class="space-y-5">
                    @foreach ($lines as $line)
                        @php
                            $payable = $line['payable'];
                        @endphp
                        <div class="rounded-2xl border border-stone-200 p-4 sm:p-5" data-billing-line @if ($payable->isMetered()) data-formula="{{ $line['formula'] }}" data-previous-reading="{{ $line['previousReading'] }}" @endif>
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold">{{ $payable->title }}</p>
                                    @if ($payable->isMetered())
                                        <p class="mt-1 text-xs text-slate-500">Previous reading: {{ number_format((float) $line['previousReading'], 2) }}</p>
                                        <label for="reading-{{ $payable->id }}" class="mt-3 mb-1.5 block text-sm font-medium">Current reading</label>
                                        <input
                                            id="reading-{{ $payable->id }}"
                                            name="items[{{ $payable->id }}][current_reading]"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            data-current-reading
                                            value="{{ $line['currentReading'] }}"
                                            class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4"
                                        >
                                        @error('items.'.$payable->id.'.current_reading')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                                    @else
                                        <label for="amount-{{ $payable->id }}" class="mt-3 mb-1.5 block text-sm font-medium">Amount</label>
                                        <div class="flex gap-2">
                                            <input
                                                id="amount-{{ $payable->id }}"
                                                name="items[{{ $payable->id }}][amount]"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                data-amount-input
                                                value="{{ $line['amount'] }}"
                                                class="w-full rounded-2xl border border-stone-200 px-4 py-2.5 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4"
                                            >
                                            <span class="inline-flex items-center rounded-2xl border border-stone-200 px-3 py-2 text-xs font-medium text-slate-500">Edit</span>
                                        </div>
                                        @error('items.'.$payable->id.'.amount')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                                    @endif
                                </div>
                                <div class="sm:w-40 sm:text-right">
                                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Sub total</p>
                                    <p class="mt-1 text-xl font-semibold tabular-nums" data-subtotal>{{ number_format((float) ($line['amount'] ?? 0), 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-stone-200 pt-5">
                    <p class="text-sm font-medium text-slate-500">Total</p>
                    <p class="text-2xl font-semibold tabular-nums" data-billing-total>0.00</p>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button type="submit" @disabled(! $assignment) class="rounded-2xl bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-50">
                        {{ $published ? 'Update published bill' : 'Publish' }}
                    </button>
                    @unless ($assignment)
                        <p class="text-sm text-slate-500">Assign a tenant to publish this bill.</p>
                    @endunless
                </div>
            @endif
        </form>
    </section>
@endsection
