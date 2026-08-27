@extends('layouts.app')

@section('title', 'Add room')
@section('eyebrow', 'Rooms')
@section('heading', 'Add room')

@section('content')
    <section class="mx-auto max-w-2xl rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold">New room</h2>
        <p class="mt-1 text-sm text-slate-500">Use a unique room number so assignments and bills stay accurate.</p>

        <form method="POST" action="{{ route('admin.rooms.store') }}" class="mt-6 space-y-5">
            @csrf
            @include('admin.rooms.form')

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Save room</button>
                <a href="{{ route('admin.rooms.index') }}" class="text-sm font-medium text-slate-500 hover:text-teal-800">Cancel</a>
            </div>
        </form>
    </section>
@endsection
