document.addEventListener('click', (event) => {
    const openButton = event.target.closest('[data-confirm-open]');

    if (openButton !== null) {
        const dialogId = openButton.dataset.confirmOpen;
        const dialog = document.getElementById(dialogId);

        if (dialog === null) {
            return;
        }

        resetConfirmationDialog(dialog);
        dialog.showModal();
        return;
    }

    const closeButton = event.target.closest('[data-confirm-close]');

    if (closeButton !== null) {
        const dialog = closeButton.closest('dialog');

        if (dialog === null) {
            return;
        }

        dialog.close();
        return;
    }

    if (event.target.classList.contains('admin-confirm-dialog')) {
        event.target.close();
    }
});

document.addEventListener('input', (event) => {
    const input = event.target.closest('[data-confirm-input]');

    if (input === null) {
        return;
    }

    const form = input.closest('[data-confirm-name]');

    if (form === null) {
        return;
    }

    const submitButton = form.querySelector('[data-confirm-submit]');
    const expectedName = form.dataset.confirmName;

    if (submitButton === null || expectedName === undefined) {
        return;
    }

    submitButton.disabled = input.value !== expectedName;
});

function resetConfirmationDialog(dialog) {
    const form = dialog.querySelector('[data-confirm-name]');

    if (form === null) {
        return;
    }

    const input = form.querySelector('[data-confirm-input]');
    const submitButton = form.querySelector('[data-confirm-submit]');

    if (input !== null) {
        input.value = '';
    }

    if (submitButton !== null) {
        submitButton.disabled = true;
    }
}