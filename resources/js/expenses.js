function expenseForm() {
    const form = document.querySelector('[data-expense-form]');

    if (!form) {
        return;
    }

    const status = form.querySelector('[data-expense-status]');
    const wrap = form.querySelector('[data-partial-amount-wrap]');
    const amountInput = form.querySelector('[data-partial-payment-amount]');

    const syncPartial = () => {
        const isPartial = status?.value === 'partial_payment';

        wrap?.classList.toggle('hidden', !isPartial);

        if (amountInput) {
            amountInput.disabled = !isPartial;
            amountInput.required = isPartial;

            if (!isPartial) {
                amountInput.value = '';
            }
        }
    };

    status?.addEventListener('change', syncPartial);
    syncPartial();
}

document.addEventListener('DOMContentLoaded', () => expenseForm());
