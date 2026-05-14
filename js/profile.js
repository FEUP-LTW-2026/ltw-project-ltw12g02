const editProfileDialog = document.querySelector('#edit-profile-dialog');
const openEditProfileButton = document.querySelector('#open-edit-profile-dialog');
const closeEditProfileButton = document.querySelector('#close-edit-profile-dialog');
const cancelEditProfileButton = document.querySelector('#cancel-edit-profile-dialog');

if (editProfileDialog && openEditProfileButton) {
  openEditProfileButton.addEventListener('click', () => {
    editProfileDialog.showModal();
  });
}

if (editProfileDialog && closeEditProfileButton) {
  closeEditProfileButton.addEventListener('click', () => {
    editProfileDialog.close();
  });
}

if (editProfileDialog && cancelEditProfileButton) {
  cancelEditProfileButton.addEventListener('click', () => {
    editProfileDialog.close();
  });
}

if (editProfileDialog) {
  editProfileDialog.addEventListener('click', (event) => {
    if (event.target === editProfileDialog) {
      editProfileDialog.close();
    }
  });
}