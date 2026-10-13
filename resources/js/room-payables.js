function evaluateBillingFormula(formula, currentReading, previousReading) {
    let expression = String(formula || '').toLowerCase();
    const values = {
        current_electricity_reading: Number(currentReading) || 0,
        previous_electricity_reading: Number(previousReading) || 0,
        current_reading: Number(currentReading) || 0,
        previous_reading: Number(previousReading) || 0,
    };

    Object.entries(values).forEach(([name, value]) => {
        expression = expression.replace(new RegExp(`\\b${name}\\b`, 'g'), String(value));
    });

    expression = expression.replace(/\s+/g, '');

    if (!expression || !/^[0-9+\-*/().]+$/.test(expression)) {
        throw new Error('Invalid formula');
    }

    const result = Function(`"use strict"; return (${expression});`)();

    if (!Number.isFinite(result)) {
        throw new Error('Invalid result');
    }

    return Math.round(result * 100) / 100;
}

function formatAmount(value) {
    return Number(value || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function bindRoomPayableForm(form) {
    const payableSelect = form.querySelector('#payables_id');
    const standardFields = form.querySelector('[data-standard-fields]');
    const meterFields = form.querySelector('[data-meter-fields]');
    const formulaInput = form.querySelector('#billing_formula');

    if (!payableSelect || !standardFields || !meterFields) {
        return;
    }

    const selectedOption = () => payableSelect.options[payableSelect.selectedIndex];

    const isMetered = () => {
        const kind = selectedOption()?.dataset.kind;
        return kind === 'water' || kind === 'electricity';
    };

    const setDisabled = (root, disabled) => {
        root.querySelectorAll('input, select, textarea').forEach((field) => {
            if (field.id === 'payables_id') {
                return;
            }

            field.disabled = disabled;
        });
    };

    const syncFields = (resetFormula) => {
        const metered = isMetered();
        const option = selectedOption();

        meterFields.classList.toggle('hidden', !metered);
        standardFields.classList.toggle('hidden', metered);
        setDisabled(standardFields, metered || payableSelect.disabled);
        setDisabled(meterFields, !metered || payableSelect.disabled);

        if (metered && resetFormula && formulaInput && option?.dataset.formula) {
            formulaInput.value = option.dataset.formula;
        }
    };

    payableSelect.addEventListener('change', () => syncFields(true));
    syncFields(false);
}

function bindRoomBillingForm(form) {
    const totalEl = form.querySelector('[data-billing-total]');

    const lineAmount = (line) => {
        const reading = line.querySelector('[data-current-reading]');
        const amountInput = line.querySelector('[data-amount-input]');

        if (reading) {
            if (reading.value === '') {
                return 0;
            }

            try {
                return Math.max(0, evaluateBillingFormula(
                    line.dataset.formula,
                    reading.value,
                    line.dataset.previousReading || 0,
                ));
            } catch {
                return 0;
            }
        }

        return Number(amountInput?.value || 0);
    };

    const refresh = () => {
        let total = 0;

        form.querySelectorAll('[data-billing-line]').forEach((line) => {
            const subtotal = line.querySelector('[data-subtotal]');
            const amount = lineAmount(line);
            total += amount;

            if (subtotal) {
                subtotal.textContent = formatAmount(amount);
            }
        });

        if (totalEl) {
            totalEl.textContent = formatAmount(total);
        }
    };

    form.addEventListener('input', refresh);
    refresh();
}

document.querySelectorAll('[data-room-payable-form]').forEach(bindRoomPayableForm);
document.querySelectorAll('[data-room-billing-form]').forEach(bindRoomBillingForm);
