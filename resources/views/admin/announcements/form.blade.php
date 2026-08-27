@php
    $announcement = $announcement ?? null;
@endphp

<div>
    <label for="title" class="mb-1.5 block text-sm font-medium">Title</label>
    <input id="title" name="title" type="text" value="{{ old('title', $announcement?->title) }}" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
    @error('title')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="status" class="mb-1.5 block text-sm font-medium">Status</label>
    <select id="status" name="status" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">
        <option value="published" @selected(old('status', $announcement?->status ?? 'published') === 'published')>Published</option>
        <option value="draft" @selected(old('status', $announcement?->status) === 'draft')>Draft</option>
    </select>
    @error('status')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>

<div>
    <label for="body" class="mb-1.5 block text-sm font-medium">Message</label>
    <textarea id="body" name="body" rows="6" required class="w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm outline-none ring-teal-700/20 focus:border-teal-700 focus:ring-4">{{ old('body', $announcement?->body) }}</textarea>
    @error('body')<p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>@enderror
</div>
