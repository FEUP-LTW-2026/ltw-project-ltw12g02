document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('dialog').forEach((dialog) => {
    dialog.addEventListener('click', (event) => {
      if (event.target === dialog) {
        dialog.close();
      }
    });
  });

  document.addEventListener('click', (event) => {
    const openButton = event.target.closest('[data-dialog-target]');
    const closeButton = event.target.closest('[data-dialog-close]');

    if (openButton) {
      const dialogId = openButton.dataset.dialogTarget;
      const dialog = document.getElementById(dialogId);

      if (dialog) {
        dialog.showModal();
      }
    }

    if (closeButton) {
      const dialog = closeButton.closest('dialog');

      if (dialog) {
        dialog.close();
      }
    }
  });
});