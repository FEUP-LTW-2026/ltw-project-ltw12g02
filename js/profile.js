document.addEventListener('DOMContentLoaded', () => {
  const profileImageInput = document.querySelector('#profile-image-input');
  const profileImageButtons = document.querySelectorAll('.popup-photo');
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

  const openEditProfileButton = document.querySelector('[data-dialog-target="edit-profile-dialog"]');
  const closeEditProfileButtons = document.querySelectorAll('#edit-profile-dialog [data-dialog-close]');

  if (openEditProfileButton) {
    openEditProfileButton.addEventListener('click', saveOriginalImages);
  }

  closeEditProfileButtons.forEach((button) => {
    button.addEventListener('click', restoreOriginalImages);
  });

  const editProfileDialog = document.querySelector('#edit-profile-dialog');

  if (editProfileDialog) {
    editProfileDialog.addEventListener('click', (event) => {
      if (event.target === editProfileDialog) {
        restoreOriginalImages();
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