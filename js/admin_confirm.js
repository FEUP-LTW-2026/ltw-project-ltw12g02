document.addEventListener('DOMContentLoaded', () => {
  const openButtons = document.querySelectorAll('[data-confirm-open]');
  const closeButtons = document.querySelectorAll('[data-confirm-close]');
  const confirmationForms = document.querySelectorAll('[data-confirm-name]');

  for (const button of openButtons) {
    button.addEventListener('click', () => {
      const dialogId = button.dataset.confirmOpen;
      const dialog = document.getElementById(dialogId);

      if (dialog === null) {
        return;
      }

      resetConfirmationDialog(dialog);
      dialog.showModal();
    });
  }

  for (const button of closeButtons) {
    button.addEventListener('click', () => {
      const dialog = button.closest('dialog');

      if (dialog === null) {
        return;
      }

      dialog.close();
    });
  }

  for (const form of confirmationForms) {
    const input = form.querySelector('[data-confirm-input]');
    const submitButton = form.querySelector('[data-confirm-submit]');
    const expectedName = form.dataset.confirmName;

    if (
      input === null ||
      submitButton === null ||
      expectedName === undefined
    ) {
      continue;
    }

    input.addEventListener('input', () => {
      submitButton.disabled = input.value !== expectedName;
    });
  }

  const dialogs = document.querySelectorAll('.admin-confirm-dialog');

  for (const dialog of dialogs) {
    dialog.addEventListener('click', (event) => {
      if (event.target === dialog) {
        dialog.close();
      }
    });
  }
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