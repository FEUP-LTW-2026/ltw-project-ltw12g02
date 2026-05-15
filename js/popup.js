document.addEventListener('DOMContentLoaded', () => {
  const openButtons = document.querySelectorAll('[data-dialog-target]');
  const closeButtons = document.querySelectorAll('[data-dialog-close]');

  openButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const dialogId = button.dataset.dialogTarget;
      const dialog = document.getElementById(dialogId);

      if (dialog) {
        dialog.showModal();
      }
    });
  });

  closeButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const dialog = button.closest('dialog');

      if (dialog) {
        dialog.close();
      }
    });
  });

  document.querySelectorAll('dialog').forEach((dialog) => {
    dialog.addEventListener('click', (event) => {
      if (event.target === dialog) {
        dialog.close();
      }
    });
  });
});