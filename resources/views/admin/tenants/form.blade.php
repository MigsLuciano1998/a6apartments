@php
    $tenant = $tenant ?? null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="first_name" class="mb-1.5 block text-sm font-medium">First name</label>
        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $tenant?->first_name) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        @error('first_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="last_name" class="mb-1.5 block text-sm font-medium">Last name</label>
        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $tenant?->last_name) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        @error('last_name')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="middle_name" class="mb-1.5 block text-sm font-medium">Middle name <span class="font-normal text-slate-400">optional</span></label>
        <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name', $tenant?->middle_name) }}" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    </div>
    <div>
        <label for="suffix" class="mb-1.5 block text-sm font-medium">Suffix <span class="font-normal text-slate-400">optional</span></label>
        <input id="suffix" name="suffix" type="text" value="{{ old('suffix', $tenant?->suffix) }}" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    </div>
</div>

<div>
    <label for="email" class="mb-1.5 block text-sm font-medium">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email', $tenant?->email) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    @error('email')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="phone" class="mb-1.5 block text-sm font-medium">Phone</label>
    <input id="phone" name="phone" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="30" value="{{ old('phone', $tenant?->phone) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    @error('phone')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="tenant_type" class="mb-1.5 block text-sm font-medium">Tenant type</label>
        <select id="tenant_type" name="tenant_type" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
            <option value="regular" @selected(old('tenant_type', $tenant?->tenant_type ?? 'regular') === 'regular')>Regular</option>
            <option value="bills_only" @selected(old('tenant_type', $tenant?->tenant_type) === 'bills_only')>Bills only</option>
        </select>
        @error('tenant_type')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="status" class="mb-1.5 block text-sm font-medium">Status</label>
        <select id="status" name="status" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
            <option value="active" @selected(old('status', $tenant?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $tenant?->status) === 'inactive')>Inactive</option>
        </select>
        @error('status')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="password" class="mb-1.5 block text-sm font-medium">
            Password
            @if ($tenant)
                <span class="font-normal text-slate-400">optional</span>
            @endif
        </label>
        <input id="password" name="password" type="password" @required(! $tenant) class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        @error('password')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" @required(! $tenant) class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    </div>
</div>
