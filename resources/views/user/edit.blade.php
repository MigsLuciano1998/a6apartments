@extends('layouts.app')

@section('title', 'User')
@section('description', 'Update your A6 Apartments account information and password.')
@section('eyebrow', 'Account')
@section('heading', 'User')

@section('content')
    <section class="mx-auto max-w-3xl space-y-8">
        <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-semibold">Your information</h2>
            <p class="mt-1 text-sm text-slate-500">These details appear on your account and billing records.</p>

            <form method="POST" action="{{ route('user.update') }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-1.5 block text-sm font-medium">First name <span class="text-rose-600">*</span></label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                        @error('first_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="last_name" class="mb-1.5 block text-sm font-medium">Last name <span class="text-rose-600">*</span></label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                        @error('last_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="middle_name" class="mb-1.5 block text-sm font-medium">Middle name <span class="font-normal text-slate-400">optional</span></label>
                        <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name', $user->middle_name) }}" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    </div>
                    <div>
                        <label for="suffix" class="mb-1.5 block text-sm font-medium">Suffix <span class="font-normal text-slate-400">optional</span></label>
                        <input id="suffix" name="suffix" type="text" value="{{ old('suffix', $user->suffix) }}" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    </div>
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium">Email <span class="text-rose-600">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    @error('email')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-medium">Phone <span class="text-rose-600">*</span></label>
                    <input id="phone" name="phone" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="30" value="{{ old('phone', $user->phone) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    @error('phone')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">
                        Save information
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-[28px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-semibold">Password</h2>
            <p class="mt-1 text-sm text-slate-500">Enter your current password, then choose a new accepted password.</p>

            <form method="POST" action="{{ route('user.password') }}" data-password-form class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="mb-1.5 block text-sm font-medium">Current password <span class="text-rose-600">*</span></label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    @error('current_password')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium">New password <span class="text-rose-600">*</span></label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-stone-200">
                        <div data-password-meter class="h-full w-0 rounded-full bg-stone-200 transition-all"></div>
                    </div>
                    <p data-password-message class="mt-1.5 text-xs text-slate-500">Complete every item below. The password is accepted when all checks turn green.</p>
                    @error('password')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirm new password <span class="text-rose-600">*</span></label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
                    <p data-password-match class="mt-1.5 text-sm text-slate-500">Re-enter the same password.</p>
                </div>

                @include('partials.password-rules')

                <div class="flex justify-end">
                    <button type="submit" data-password-submit disabled class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-50">
                        Update password
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
