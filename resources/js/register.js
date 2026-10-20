function passwordChecks(password) {
    return {
        length: password.length >= 8,
        lower: /[a-z]/.test(password),
        upper: /[A-Z]/.test(password),
        number: /\d/.test(password),
        symbol: /[^A-Za-z0-9]/.test(password),
    };
}

function bindRegisterForm() {
    const form = document.querySelector('[data-register-form]');

    if (!form) {
        return;
    }

    const password = form.querySelector('#password');
    const confirmation = form.querySelector('#password_confirmation');
    const terms = form.querySelector('[name="terms_accepted"]');
    const privacy = form.querySelector('[name="privacy_consent"]');
    const submit = form.querySelector('[data-register-submit]');
    const meter = form.querySelector('[data-password-meter]');
    const message = form.querySelector('[data-password-message]');
    const matchMessage = form.querySelector('[data-password-match]');
    const hint = form.querySelector('[data-register-hint]');
    const modal = document.querySelector('[data-terms-modal]');

    const scoreFromChecks = (checks) => Object.values(checks).filter(Boolean).length;

    const refreshPassword = () => {
        const value = password?.value || '';
        const checks = passwordChecks(value);
        const score = scoreFromChecks(checks);
        const strong = score === 5;

        if (meter) {
            meter.style.width = value === '' ? '0%' : `${(score / 5) * 100}%`;
            meter.className = 'h-full rounded-full transition-all';

            if (value === '') {
                meter.classList.add('bg-stone-200');
            } else if (strong) {
                meter.classList.add('bg-teal-700');
            } else if (score >= 3) {
                meter.classList.add('bg-amber-500');
            } else {
                meter.classList.add('bg-rose-600');
            }
        }

        form.querySelectorAll('[data-password-rule]').forEach((item) => {
            const ok = Boolean(checks[item.dataset.passwordRule]);
            const icon = item.querySelector('[data-rule-icon]');

            item.classList.toggle('text-teal-800', ok);
            item.classList.toggle('text-slate-500', ! ok);

            if (icon) {
                icon.textContent = ok ? '✓' : '–';
                icon.className = ok
                    ? 'flex h-5 w-5 items-center justify-center rounded-full bg-teal-700 text-[10px] font-semibold text-white'
                    : 'flex h-5 w-5 items-center justify-center rounded-full bg-stone-200 text-[10px] font-semibold';
            }
        });

        if (message) {
            if (value === '') {
                message.textContent = 'Complete every item below. The password is accepted when all checks turn green.';
                message.className = 'mt-1.5 text-xs text-slate-500';
            } else if (strong) {
                message.textContent = 'Password is strong and accepted.';
                message.className = 'mt-1.5 text-sm font-medium text-teal-800';
            } else if (score >= 3) {
                message.textContent = 'Almost there. Update the password until every requirement below is green.';
                message.className = 'mt-1.5 text-sm text-amber-700';
            } else {
                message.textContent = 'Password is too weak. Follow the accepted password list below.';
                message.className = 'mt-1.5 text-sm text-rose-600';
            }
        }

        if (password) {
            password.classList.toggle('border-teal-700', strong);
            password.classList.toggle('border-rose-400', value !== '' && ! strong);
        }

        return strong;
    };

    const refreshMatch = () => {
        const value = confirmation?.value || '';
        const matches = value !== '' && value === (password?.value || '');

        if (matchMessage) {
            if (value === '') {
                matchMessage.textContent = 'Re-enter the same password.';
                matchMessage.className = 'mt-1.5 text-sm text-slate-500';
            } else if (matches) {
                matchMessage.textContent = 'Passwords match.';
                matchMessage.className = 'mt-1.5 text-sm font-medium text-teal-800';
            } else {
                matchMessage.textContent = 'Passwords do not match. Type the same password again.';
                matchMessage.className = 'mt-1.5 text-sm text-rose-600';
            }
        }

        if (confirmation) {
            confirmation.classList.toggle('border-teal-700', matches);
            confirmation.classList.toggle('border-rose-400', value !== '' && ! matches);
        }

        return matches;
    };

    const refreshSubmit = () => {
        const ready = refreshPassword()
            && refreshMatch()
            && Boolean(terms?.checked)
            && Boolean(privacy?.checked);

        if (submit) {
            submit.disabled = ! ready;
        }

        if (hint) {
            if (ready) {
                hint.textContent = 'Required fields are complete. You can create your account.';
                hint.className = 'text-xs font-medium text-teal-800';
            } else {
                hint.textContent = 'Complete every required field, use an accepted password, and check both boxes to create your account.';
                hint.className = 'text-xs text-slate-500';
            }
        }
    };

    form.addEventListener('input', refreshSubmit);
    form.addEventListener('change', refreshSubmit);

    form.addEventListener('submit', (event) => {
        refreshSubmit();

        if (submit?.disabled) {
            event.preventDefault();
        }
    });

    document.querySelectorAll('[data-open-terms]').forEach((button) => {
        button.addEventListener('click', () => modal?.showModal());
    });

    document.querySelectorAll('[data-close-terms]').forEach((button) => {
        button.addEventListener('click', () => modal?.close());
    });

    modal?.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.close();
        }
    });

    refreshSubmit();
}

bindRegisterForm();
bindPasswordForm();

function bindPasswordForm() {
    const form = document.querySelector('[data-password-form]');

    if (!form) {
        return;
    }

    const current = form.querySelector('#current_password');
    const password = form.querySelector('#password');
    const confirmation = form.querySelector('#password_confirmation');
    const submit = form.querySelector('[data-password-submit]');
    const meter = form.querySelector('[data-password-meter]');
    const message = form.querySelector('[data-password-message]');
    const matchMessage = form.querySelector('[data-password-match]');

    const scoreFromChecks = (checks) => Object.values(checks).filter(Boolean).length;

    const refreshPassword = () => {
        const value = password?.value || '';
        const checks = passwordChecks(value);
        const score = scoreFromChecks(checks);
        const strong = score === 5;

        if (meter) {
            meter.style.width = value === '' ? '0%' : `${(score / 5) * 100}%`;
            meter.className = 'h-full rounded-full transition-all';

            if (value === '') {
                meter.classList.add('bg-stone-200');
            } else if (strong) {
                meter.classList.add('bg-teal-700');
            } else if (score >= 3) {
                meter.classList.add('bg-amber-500');
            } else {
                meter.classList.add('bg-rose-600');
            }
        }

        form.querySelectorAll('[data-password-rule]').forEach((item) => {
            const ok = Boolean(checks[item.dataset.passwordRule]);
            const icon = item.querySelector('[data-rule-icon]');

            item.classList.toggle('text-teal-800', ok);
            item.classList.toggle('text-slate-500', ! ok);

            if (icon) {
                icon.textContent = ok ? '✓' : '–';
                icon.className = ok
                    ? 'flex h-5 w-5 items-center justify-center rounded-full bg-teal-700 text-[10px] font-semibold text-white'
                    : 'flex h-5 w-5 items-center justify-center rounded-full bg-stone-200 text-[10px] font-semibold';
            }
        });

        if (message) {
            if (value === '') {
                message.textContent = 'Complete every item below. The password is accepted when all checks turn green.';
                message.className = 'mt-1.5 text-xs text-slate-500';
            } else if (strong) {
                message.textContent = 'Password is strong and accepted.';
                message.className = 'mt-1.5 text-sm font-medium text-teal-800';
            } else if (score >= 3) {
                message.textContent = 'Almost there. Update the password until every requirement below is green.';
                message.className = 'mt-1.5 text-sm text-amber-700';
            } else {
                message.textContent = 'Password is too weak. Follow the accepted password list below.';
                message.className = 'mt-1.5 text-sm text-rose-600';
            }
        }

        if (password) {
            password.classList.toggle('border-teal-700', strong);
            password.classList.toggle('border-rose-400', value !== '' && ! strong);
        }

        return strong;
    };

    const refreshMatch = () => {
        const value = confirmation?.value || '';
        const matches = value !== '' && value === (password?.value || '');

        if (matchMessage) {
            if (value === '') {
                matchMessage.textContent = 'Re-enter the same password.';
                matchMessage.className = 'mt-1.5 text-sm text-slate-500';
            } else if (matches) {
                matchMessage.textContent = 'Passwords match.';
                matchMessage.className = 'mt-1.5 text-sm font-medium text-teal-800';
            } else {
                matchMessage.textContent = 'Passwords do not match. Type the same password again.';
                matchMessage.className = 'mt-1.5 text-sm text-rose-600';
            }
        }

        if (confirmation) {
            confirmation.classList.toggle('border-teal-700', matches);
            confirmation.classList.toggle('border-rose-400', value !== '' && ! matches);
        }

        return matches;
    };

    const refreshSubmit = () => {
        const ready = Boolean(current?.value)
            && refreshPassword()
            && refreshMatch();

        if (submit) {
            submit.disabled = ! ready;
        }
    };

    form.addEventListener('input', refreshSubmit);
    form.addEventListener('change', refreshSubmit);

    form.addEventListener('submit', (event) => {
        refreshSubmit();

        if (submit?.disabled) {
            event.preventDefault();
        }
    });

    refreshSubmit();
}
