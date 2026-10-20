@extends('layouts.guest')

@section('title', 'Tenant registration')
@section('description', 'Create a tenant account at A6 Apartments to access your room, bills, payments, and announcements.')

@section('content')
    <p class="text-sm font-medium text-teal-700">Tenant registration</p>
    <h2 class="mt-1 text-xl font-semibold sm:text-2xl">Create your tenant account</h2>
    <p class="mt-2 text-sm text-slate-500">Fill in your details so we can set up your room and billing access. Fields marked with <span class="font-semibold text-rose-600">*</span> are required.</p>

    <form method="POST" action="{{ route('register.store') }}" class="mt-7 space-y-5" data-register-form>
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="first_name" class="mb-1.5 block text-sm font-medium">First name <span class="text-rose-600">*</span></label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required aria-required="true" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
                @error('first_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="last_name" class="mb-1.5 block text-sm font-medium">Last name <span class="text-rose-600">*</span></label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required aria-required="true" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
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
            <label for="email" class="mb-1.5 block text-sm font-medium">Email <span class="text-rose-600">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required aria-required="true" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
            @error('email')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="mb-1.5 block text-sm font-medium">Phone <span class="text-rose-600">*</span></label>
            <input id="phone" name="phone" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="30" value="{{ old('phone') }}" required aria-required="true" placeholder="09XXXXXXXXX" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
            @error('phone')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="tenant_type" class="mb-1.5 block text-sm font-medium">Tenant type <span class="text-rose-600">*</span></label>
            <select
                id="tenant_type"
                name="tenant_type"
                required
                aria-required="true"
                class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm"
            >
                <option value="" @selected(! old('tenant_type'))>Select tenant type</option>
                <option value="regular" @selected(old('tenant_type') === 'regular')>Regular</option>
                <option value="bills_only" @selected(old('tenant_type') === 'bills_only')>Bills only</option>
            </select>
            <p class="mt-1.5 text-xs text-slate-500">Choose Regular for full room and billing access, or Bills only to view and pay assigned bills.</p>
            @error('tenant_type')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium">Password <span class="text-rose-600">*</span></label>
                <input id="password" name="password" type="password" required aria-required="true" autocomplete="new-password" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
                <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-stone-200">
                    <div data-password-meter class="h-full w-0 rounded-full bg-stone-200 transition-all"></div>
                </div>
                <p data-password-message class="mt-1.5 text-xs text-slate-500">Complete every item below. The password is accepted when all checks turn green.</p>
                @error('password')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirm password <span class="text-rose-600">*</span></label>
                <input id="password_confirmation" name="password_confirmation" type="password" required aria-required="true" autocomplete="new-password" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-base outline-none ring-teal-700/20 transition focus:border-teal-700 focus:ring-4 sm:text-sm">
                <p data-password-match class="mt-1.5 text-sm text-slate-500">Re-enter the same password.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-[#fbf8f3] p-4">
            <p class="text-sm font-medium">Accepted password</p>
            <p class="mt-1 text-xs text-slate-500">Update the password until each requirement is marked done.</p>
            <ul class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                <li data-password-rule="length" class="flex items-center gap-2 text-slate-500">
                    <span data-rule-icon class="flex h-5 w-5 items-center justify-center rounded-full bg-stone-200 text-[10px] font-semibold">–</span>
                    At least 8 characters
                </li>
                <li data-password-rule="lower" class="flex items-center gap-2 text-slate-500">
                    <span data-rule-icon class="flex h-5 w-5 items-center justify-center rounded-full bg-stone-200 text-[10px] font-semibold">–</span>
                    One lowercase letter (a–z)
                </li>
                <li data-password-rule="upper" class="flex items-center gap-2 text-slate-500">
                    <span data-rule-icon class="flex h-5 w-5 items-center justify-center rounded-full bg-stone-200 text-[10px] font-semibold">–</span>
                    One uppercase letter (A–Z)
                </li>
                <li data-password-rule="number" class="flex items-center gap-2 text-slate-500">
                    <span data-rule-icon class="flex h-5 w-5 items-center justify-center rounded-full bg-stone-200 text-[10px] font-semibold">–</span>
                    One number (0–9)
                </li>
                <li data-password-rule="symbol" class="flex items-center gap-2 text-slate-500 sm:col-span-2">
                    <span data-rule-icon class="flex h-5 w-5 items-center justify-center rounded-full bg-stone-200 text-[10px] font-semibold">–</span>
                    One symbol (! @ # $ % &amp; *)
                </li>
            </ul>
        </div>

        <div>
            <button type="button" data-open-terms class="text-sm font-semibold text-teal-800 hover:underline">
                Click to view Terms and Conditions
            </button>
        </div>

        <label class="flex items-start gap-3 text-sm leading-6 text-slate-600">
            <input
                type="checkbox"
                name="terms_accepted"
                value="1"
                required
                class="mt-1 h-4 w-4 shrink-0 rounded border-stone-300 text-teal-700 focus:ring-teal-700"
                {{ old('terms_accepted') ? 'checked' : '' }}
            >
            <span>I have read and agree to the Terms and Conditions. <span class="text-rose-600">*</span></span>
        </label>
        @error('terms_accepted')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror

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
                I allow A6 Apartments to use all information I enter on this form in the monitoring system, including for room assignment, billing, payments, and announcements. <span class="text-rose-600">*</span>
            </span>
        </label>
        @error('privacy_consent')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror

        <p data-register-hint class="text-xs text-slate-500">Complete every required field, use an accepted password, and check both boxes to create your account.</p>

        <button type="submit" data-register-submit disabled class="w-full rounded-2xl bg-teal-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-50">
            Create tenant account
        </button>
    </form>

    <dialog data-terms-modal class="w-[min(36rem,calc(100%-2rem))] rounded-[28px] border border-stone-200 bg-white p-0 shadow-xl backdrop:bg-black/40">
        <div class="p-6 sm:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-xl font-semibold">Terms and Conditions</h3>
                    <p class="mt-1 text-sm text-slate-500">Please read these terms before creating an account.</p>
                </div>
                <button type="button" data-close-terms class="rounded-full border border-stone-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-teal-700 hover:text-teal-800">Close</button>
            </div>
            <div class="mt-5 max-h-[min(24rem,60vh)] overflow-y-auto pr-1">
                @include('legal.terms-content')
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" data-close-terms class="rounded-2xl bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-800">Done</button>
            </div>
        </div>
    </dialog>

    <p class="mt-6 text-center text-sm text-slate-500">
        Already registered?
        <a href="{{ route('login') }}" class="font-semibold text-teal-800 hover:underline">Sign in</a>
    </p>
@endsection
