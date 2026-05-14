document.addEventListener('DOMContentLoaded', () => {
  const editProfileDialog = document.querySelector('#edit-profile-dialog');
  const openEditProfileButton = document.querySelector('#open-edit-profile-dialog');
  const closeEditProfileButton = document.querySelector('#close-edit-profile-dialog');
  const cancelEditProfileButton = document.querySelector('#cancel-edit-profile-dialog');

  const profileImageInput = document.querySelector('#profile-image-input');
  const profileImageButtons = document.querySelectorAll('.edit-profile-photo');
  const profileImagePreviews = document.querySelectorAll('.profile-image-preview');

  let originalImageSources = [];

  function saveOriginalImages() {
    originalImageSources = Array.from(profileImagePreviews).map((image) => image.src);
  }

  function restoreOriginalImages() {
    profileImagePreviews.forEach((image, index) => {
      if (originalImageSources[index]) {
        image.src = originalImageSources[index];
      }
    });

    if (profileImageInput) {
      profileImageInput.value = '';
    }
  }

  if (editProfileDialog && openEditProfileButton) {
    openEditProfileButton.addEventListener('click', () => {
      saveOriginalImages();
      editProfileDialog.showModal();
    });
  }

  if (editProfileDialog && closeEditProfileButton) {
    closeEditProfileButton.addEventListener('click', () => {
      restoreOriginalImages();
      editProfileDialog.close();
    });
  }

  if (editProfileDialog && cancelEditProfileButton) {
    cancelEditProfileButton.addEventListener('click', () => {
      restoreOriginalImages();
      editProfileDialog.close();
    });
  }

  if (editProfileDialog) {
    editProfileDialog.addEventListener('click', (event) => {
      if (event.target === editProfileDialog) {
        restoreOriginalImages();
        editProfileDialog.close();
      }
    });
  }

  profileImageButtons.forEach((button) => {
    button.addEventListener('click', () => {
      if (profileImageInput) {
        profileImageInput.click();
      }
    });
  });

  if (profileImageInput) {
    profileImageInput.addEventListener('change', () => {
      const file = profileImageInput.files[0];

      if (!file) {
        return;
      }

      const reader = new FileReader();

      reader.addEventListener('load', () => {
        profileImagePreviews.forEach((image) => {
          image.src = reader.result;
        });
      });

      reader.readAsDataURL(file);
    });
  }
});