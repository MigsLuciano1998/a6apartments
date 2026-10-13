function paymentsPage() {
    const page = document.querySelector('[data-payments-page]');
    const modal = document.querySelector('[data-payment-modal]');

    if (!page || !modal) {
        return;
    }

    const tenantSelect = modal.querySelector('#payment_tenant_id');
    const list = modal.querySelector('[data-payment-list]');
    const subtitle = modal.querySelector('[data-payment-subtitle]');
    const payButton = modal.querySelector('[data-pay-submit]');
    const partialToggle = modal.querySelector('[data-pay-partial]');
    const partialHint = modal.querySelector('[data-partial-hint]');
    const partialWrap = modal.querySelector('[data-partial-amount-wrap]');
    const partialAmount = modal.querySelector('[data-partial-amount]');
    const partialBalance = modal.querySelector('[data-partial-balance]');
    const methodSelect = modal.querySelector('[data-payment-method]');
    const referenceWrap = modal.querySelector('[data-payment-reference-wrap]');
    const referenceInput = modal.querySelector('[data-payment-reference]');
    const baseUrl = page.dataset.payablesUrl;

    const selectedPayables = () => [...modal.querySelectorAll('input[name="billing_ids[]"]:checked')];

    const formatAmount = (value) => Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const updatePartialUi = () => {
        const checked = selectedPayables();
        const canPartial = checked.length === 1;

        if (partialToggle) {
            partialToggle.disabled = !canPartial;
            if (!canPartial) {
                partialToggle.checked = false;
            }
        }

        if (partialHint) {
            if (checked.length > 1) {
                partialHint.textContent = 'Partial payment is disabled when more than one payable is checked.';
            } else {
                partialHint.textContent = 'Select one payable to enter a custom amount.';
            }
        }

        const showAmount = Boolean(canPartial && partialToggle?.checked);
        const due = showAmount ? Number(checked[0].dataset.amount || 0) : 0;

        if (partialWrap) {
            partialWrap.classList.toggle('hidden', !showAmount);
        }

        if (partialAmount) {
            partialAmount.disabled = !showAmount;
            partialAmount.required = showAmount;
            if (showAmount) {
                partialAmount.max = String(due);
                if (partialBalance) {
                    partialBalance.textContent = `Remaining balance: ${formatAmount(due)}`;
                }
            } else {
                partialAmount.value = '';
                partialAmount.removeAttribute('max');
                if (partialBalance) {
                    partialBalance.textContent = '';
                }
            }
        }
    };

    const updatePayButton = () => {
        const checked = selectedPayables();
        const payingPartial = Boolean(partialToggle?.checked && checked.length === 1);
        const amount = Number(partialAmount?.value || 0);
        const due = payingPartial ? Number(checked[0].dataset.amount || 0) : 0;

        payButton.disabled =
            checked.length === 0 ||
            (payingPartial && (amount <= 0 || amount > due));
    };

    const refreshMethodUi = () => {
        const isOnline = methodSelect?.value === 'online';

        referenceWrap?.classList.toggle('hidden', !isOnline);

        if (referenceInput) {
            referenceInput.required = isOnline;
            if (!isOnline) {
                referenceInput.value = '';
            }
        }
    };

    const refreshPaymentUi = () => {
        updatePartialUi();
        updatePayButton();
        refreshMethodUi();
    };

    const renderPayables = (data) => {
        if (data.room) {
            subtitle.textContent = `${data.tenant.name} · Room ${data.room}`;
        } else {
            subtitle.textContent = data.tenant.name;
        }

        if (!data.payables?.length) {
            list.innerHTML = `<p class="text-sm text-slate-500">${data.message ?? 'No unpaid payables this month.'}</p>`;
            refreshPaymentUi();
            return;
        }

        list.innerHTML = data.payables
            .map(
                (item) => `
                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-stone-200 px-4 py-3 hover:border-teal-700">
                    <input type="checkbox" name="billing_ids[]" value="${item.billing_id}" data-amount="${item.amount}" class="mt-1 h-4 w-4 rounded border-stone-300 text-teal-700 focus:ring-teal-700">
                    <span class="flex-1">
                        <span class="block text-sm font-medium">${item.title}</span>
                        <span class="block text-xs text-slate-500">${item.frequency}</span>
                    </span>
                    <span class="text-sm font-semibold tabular-nums">${formatAmount(item.amount)}</span>
                </label>
            `,
            )
            .join('');

        refreshPaymentUi();
    };

    const loadPayables = (tenantId) => {
        if (!tenantId) {
            list.innerHTML = '<p class="text-sm text-slate-500">Select a tenant to load payables.</p>';
            subtitle.textContent = 'Select unpaid rent for this month, plus unpaid water and electricity from any month.';
            refreshPaymentUi();
            return;
        }

        list.innerHTML = '<p class="text-sm text-slate-500">Loading payables…</p>';

        window.axios
            .get(`${baseUrl}/${tenantId}/payables`)
            .then((response) => renderPayables(response.data))
            .catch(() => {
                list.innerHTML = '<p class="text-sm text-rose-600">Could not load payables.</p>';
                refreshPaymentUi();
            });
    };

    const openModal = (tenantId = '') => {
        if (tenantSelect) {
            tenantSelect.value = tenantId;
        }
        if (methodSelect) {
            methodSelect.value = 'cash';
        }
        loadPayables(tenantId);
        refreshMethodUi();
        modal.showModal();
    };

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-open-payment]');
        if (opener && page.contains(opener)) {
            openModal(opener.dataset.tenantId || '');
            return;
        }

        if (event.target.closest('[data-close-payment]')) {
            modal.close();
        }
    });

    tenantSelect?.addEventListener('change', () => loadPayables(tenantSelect.value));
    methodSelect?.addEventListener('change', refreshMethodUi);
    modal.addEventListener('change', (event) => {
        if (event.target.matches('input[name="billing_ids[]"], [data-pay-partial]')) {
            refreshPaymentUi();
        }
    });
    modal.addEventListener('input', (event) => {
        if (event.target.matches('[data-partial-amount]')) {
            updatePayButton();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => paymentsPage());
