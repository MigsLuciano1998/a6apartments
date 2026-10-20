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
