@extends('layouts.guest')

@section('title', 'Tenant registration')
@section('description', 'Create a tenant account at A6 Apartments to access your room, bills, payments, and announcements.')

@section('content')
    <p class="text-sm font-medium text-teal-700">Tenant registration</p>
    <h2 class="mt-1 text-xl font-semibold sm:text-2xl">Create your tenant account</h2>
    <p class="mt-2 text-sm text-slate-500">Fill in your details so we can set up your room and billing access.</p>

    <form method="POST" action="{{ route('register.store') }}" class="mt-7 space-y-5">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="first_name" class="mb-1.5 block text-sm font-medium">First name</label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
                @error('first_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="last_name" class="mb-1.5 block text-sm font-medium">Last name</label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
                @error('last_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="middle_name" class="mb-1.5 block text-sm font-medium">Middle name <span class="font-normal text-slate-400">optional</span></label>
                <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name') }}" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
            </div>
            <div>
                <label for="suffix" class="mb-1.5 block text-sm font-medium">Suffix <span class="font-normal text-slate-400">optional</span></label>
                <input id="suffix" name="suffix" type="text" value="{{ old('suffix') }}" placeholder="Jr, Sr, III" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
            </div>
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
            @error('email')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="mb-1.5 block text-sm font-medium">Phone</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required placeholder="09XXXXXXXXX" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
            @error('phone')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>

        <fieldset>
            <legend class="mb-2 text-sm font-medium">Tenant type</legend>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="cursor-pointer rounded-2xl border p-4 transition has-[:checked]:border-teal-700 has-[:checked]:bg-teal-50 {{ old('tenant_type', 'regular') === 'regular' ? 'border-teal-700 bg-teal-50' : 'border-stone-200' }}">
                    <input type="radio" name="tenant_type" value="regular" class="sr-only" {{ old('tenant_type', 'regular') === 'regular' ? 'checked' : '' }}>
                    <span class="block text-sm font-semibold">Regular</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Full tenant access for room assignment and all billings.</span>
                </label>
                <label class="cursor-pointer rounded-2xl border p-4 transition has-[:checked]:border-teal-700 has-[:checked]:bg-teal-50 {{ old('tenant_type') === 'bills_only' ? 'border-teal-700 bg-teal-50' : 'border-stone-200' }}">
                    <input type="radio" name="tenant_type" value="bills_only" class="sr-only" {{ old('tenant_type') === 'bills_only' ? 'checked' : '' }}>
                    <span class="block text-sm font-semibold">Bills only</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Access limited to viewing and paying assigned bills.</span>
                </label>
            </div>
            @error('tenant_type')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
        </fieldset>

        <label class="flex items-start gap-3 text-sm leading-6 text-slate-600">
            <input
                type="checkbox"
                name="privacy_consent"
                value="1"
                required
                class="mt-1 h-4 w-4 shrink-0 rounded border-stone-300 text-teal-700 focus:ring-teal-700"
                {{ old('privacy_consent') ? 'checked' : '' }}
            >
            <span>
                I allow A6 Apartments to use all information I enter on this form in the monitoring system, including for room assignment, billing, payments, and announcements.
            </span>
        </label>
        @error('privacy_consent')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium">Password</label>
                <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
                @error('password')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
            </div>
        </div>

        <button type="submit" class="w-full rounded-2xl bg-teal-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
            Create tenant account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Already registered?
        <a href="{{ route('login') }}" class="font-semibold text-teal-800 hover:underline">Sign in</a>
    </p>
@endsection
