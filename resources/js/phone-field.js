document.querySelectorAll('input[name="phone"]').forEach((input) => {
    const digitsOnly = () => {
        input.value = input.value.replace(/\D/g, '');
    };

    input.addEventListener('input', digitsOnly);
    input.addEventListener('paste', () => {
        requestAnimationFrame(digitsOnly);
    });
});
