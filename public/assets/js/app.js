document.addEventListener('DOMContentLoaded', () => {
    const deleteForms = document.querySelectorAll('form[data-confirm]');

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm || 'Вы уверены?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});