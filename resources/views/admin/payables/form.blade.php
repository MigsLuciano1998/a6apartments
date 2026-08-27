@php
    $payable = $payable ?? null;
@endphp

<div>
    <label for="title" class="mb-1.5 block text-sm font-medium">Title</label>
    <input id="title" name="title" type="text" value="{{ old('title', $payable?->title) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    @error('title')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="status" class="mb-1.5 block text-sm font-medium">Status</label>
    <select id="status" name="status" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        <option value="active" @selected(old('status', $payable?->status ?? 'active') === 'active')>Active</option>
        <option value="inactive" @selected(old('status', $payable?->status) === 'inactive')>Inactive</option>
    </select>
    @error('status')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="description" class="mb-1.5 block text-sm font-medium">Description <span class="font-normal text-slate-400">optional</span></label>
    <textarea id="description" name="description" rows="3" class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">{{ old('description', $payable?->description) }}</textarea>
    @error('description')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>
