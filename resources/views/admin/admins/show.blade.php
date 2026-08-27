@extends('layouts.app')

@section('title', $admin->name)
@section('description', 'Staff profile and access details for '.$admin->name.' at A6 Apartments.')
@section('eyebrow', 'Admins')
@section('heading', 'Admin info')

@section('content')
    <section class="mx-auto max-w-4xl">
        <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-teal-700">Admin user</p>
                    <h2 class="mt-1 text-2xl font-semibold">{{ $admin->name }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.admins.edit', $admin) }}" class="rounded-2xl border border-stone-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Edit</a>
                    <a href="{{ route('admin.admins.index') }}" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Back to admins</a>
                </div>
            </div>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">First name</dt>
                    <dd class="mt-1 font-medium">{{ $admin->first_name }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Last name</dt>
                    <dd class="mt-1 font-medium">{{ $admin->last_name }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Middle name</dt>
                    <dd class="mt-1 font-medium">{{ $admin->middle_name ?: '—' }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Suffix</dt>
                    <dd class="mt-1 font-medium">{{ $admin->suffix ?: '—' }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Email</dt>
                    <dd class="mt-1 font-medium">{{ $admin->email }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Phone</dt>
                    <dd class="mt-1 font-medium">{{ $admin->phone }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Role</dt>
                    <dd class="mt-1 font-medium capitalize">{{ $admin->role }}</dd>
                </div>
                <div class="rounded-2xl bg-[#fbf8f3] p-4">
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $admin->status === 'active' ? 'bg-teal-50 text-teal-800' : 'bg-stone-100 text-slate-600' }}">
                            {{ $admin->status }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </section>
@endsection
